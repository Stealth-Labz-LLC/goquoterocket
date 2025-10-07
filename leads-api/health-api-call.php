<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();
$requestData = !empty($_REQUEST) ? $_REQUEST : '';
// echo '<pre>';
// print_r($requestData);
// die();


// Get the current date and time
$currentDateTime = new DateTime();
$updateTime = $currentDateTime->format('Y-m-d H:i:s');
$preferredtimeofcall = $currentDateTime->format('Y-m-d H:i:s');

if (!empty($requestData['healthApi']) && $requestData['healthApi'] === 'Yes') {
    $_SESSION['prevFormData'] = $requestData;
}


$data =  !empty($_SESSION['prevFormData']) ? $_SESSION['prevFormData'] : $requestData;
//echo '<pre>'; print_r($data); die();

$base_url = $_SERVER['HTTP_REFERER'];
//echo $triggerTrackApi; die();

if (!empty($requestData['healthApi']) && $requestData['healthApi'] === 'Yes') {
    $healthApiResult = healthApiCall($data, $base_url, $updateTime, $preferredtimeofcall);
    if ($healthApiResult) {
        importDataToDripcell($data, $tag1 = 'QR', $tag2 = 'Health');
    }
}

function healthApiCall($data, $base_url, $updateTime, $preferredtimeofcall)
{
    //Insurance api call
    $healthPayload = array(
        'returnjson' => 'yes',
        'campid' => 'MEDICAL-WHITE-LABEL',
        //'sid' => !empty($data['aff_id']) ? $data['aff_id'] : '25293',
        'sid' => '25335',
        'firstname' => $data['given-name'],
        'lastname' => $data['family-name'],
        'phone1' => $data['phone'],
        //'email' => !empty($data['email']) ? $data['email'] : 'N/A',
        'optinurl' => $base_url,
        'optindate' => $updateTime,
        'employmentstatus' => !empty($data['employmentStatus']) ? $data['employmentStatus'] : 'N/A',
        'income' => !empty($data['income']) ? $data['income'] : 'null',
        'age' => !empty($data['age']) ? $data['age'] : 'null',
        'acceptterms' => 'true',
        'aff_sub' => !empty($data['aff_sub']) ? $data['aff_sub'] : 'N/A',
        'aff_sub2' => !empty($data['aff_sub2']) ? $data['aff_sub2'] :  '',
        'aff_sub3' => !empty($data['aff_sub3']) ? $data['aff_sub3'] : 'N/A',
        //'offer_id' => !empty($data['offer_id']) ? $data['offer_id'] : 'N/A',
        'offer_id' => '2926',
        'preferredtimeofcall' => $preferredtimeofcall,
    );
    // echo '<pre>';
    // print_r($healthPayload);
    // die();
    $ageBar = ['25-34', '35-44', '45-55'];
    if ($healthPayload['income'] != 'R0 - R5,000' && in_array($healthPayload['age'], $ageBar) && $healthPayload['employmentstatus'] == 'Employed') {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://returnxdigital.leadbyte.co.uk/api/submit.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($healthPayload),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Access-Control-Allow-Origin: *',
            ),
        ));
        $response = curl_exec($curl);
        //echo curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        curl_close($curl);
        $healthResponseData = json_decode($response);
        // echo '<pre>';
        // print_r($healthResponseData);
        // die();
        if ($healthResponseData->response === 'OK' && !empty($healthResponseData->leadId)) {
            $postback_data = [
                "couponCodes" => [
                    "qr_health"
                ],
                "revenueOriginId" => "ec8a7a14-d6d4-45f0-ac7e-b6ff5b1dbb56",
                "conversionTypeCode" => "lead",
                "amount" => [
                    "value" => "3.00"
                ],
                "externalId" => "$healthResponseData->leadId",
                "customParams" => [
                    "advS1" => !empty($data['aff_sub']) ? $data['aff_sub'] : '0022',
                    "advS2" => !empty($data['aff_sub2']) ? $data['aff_sub2'] : '',
                    "advS3" => !empty($data['aff_sub3']) ? $data['aff_sub3'] : 'organic',
                ]
            ];

            $stlbzCurl = curl_init();
            curl_setopt_array($stlbzCurl, array(
                CURLOPT_URL => 'https://stlbz.trackdesk.com/tracking/conversion/v1',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($postback_data),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Access-Control-Allow-Origin: *',
                ),
            ));
            $postback_response = curl_exec($stlbzCurl);
            curl_close($stlbzCurl);
            $decodedData = json_decode($postback_response, true);
            $sanitizedpostback_response = json_encode($decodedData, JSON_HEX_QUOT | JSON_HEX_APOS);
            // echo "Postback response: " . $sanitizedpostback_response;
            // die();
        }


        if ($healthResponseData->code !== -2) {

            $leadPayload = array(
                'token' => 'm-f7fd694964k8b165ffeb3afc65eb27a4',
                'campid' => 'MEDICAL-WHITE-LABEL',
                'funnel_id' => !empty($data['funnelId']) ? $data['funnelId'] : 'N/A',
                //'sid' => !empty($data['aff_id']) ? $data['aff_id'] : 'N/A',
                'sid' => '25335',
                'api_payload' => json_encode($healthPayload),
                'api_response' => $response,
                'postback_payload' => json_encode($postback_data),
                'postback_response' => $sanitizedpostback_response,
                'city' => !empty($data['shippingCity']) ? $data['shippingCity'] : '',
                'firstname' => $data['given-name'],
                'lastname' => $data['family-name'],
                'phone1' => $data['phone'],
                'email' => !empty($data['email']) ? $data['email'] : 'N/A',
                'optinurl' => $base_url,
                'optindate' => $updateTime,
                'employmentstatus' => !empty($data['employmentStatus']) ? $data['employmentStatus'] : 'N/A',
                'income' => !empty($data['income']) ? $data['income'] : 'null',
                'acceptterms' => 'true',
                'aff_sub' => !empty($data['aff_sub']) ? $data['aff_sub'] : 'N/A',
                'aff_sub2' => !empty($data['aff_sub2']) ? $data['aff_sub2'] : '',
                'aff_sub3' => !empty($data['aff_sub3']) ? $data['aff_sub3'] : 'N/A',
                //'offer_id' => !empty($data['offer_id']) ? $data['offer_id'] : 'N/A',
                'offer_id' => '2926',
                'preferredtimeofcall' => $preferredtimeofcall,
            );
            //echo '<pre>'; print_r($leadPayload); die();
            //lead import api
            $leadCurl = curl_init();
            curl_setopt_array($leadCurl, array(
                CURLOPT_URL => 'https://quoterocket.co.za/leads-import/main/addData',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query($leadPayload),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded',
                    'Access-Control-Allow-Origin: *',
                ),
            ));
            $LeadResponse = curl_exec($leadCurl);
            curl_close($leadCurl);
            echo $response;
            return true;
        } else {
            echo $response;

            die();
        }
    } else {
        $errorResponse = array(
            "status" => "Validation Error",
            "code" => 2,
            "status_message" => 'Validation criteria did not matched.',
        );
        echo json_encode($errorResponse);
    }
}
function importDataToDripcell($data, $tag1, $tag2)
{
    $api_url = 'https://api.dripcel.com/contacts';
    $api_key = 'vb1m8j51zbw4nntv4rjzx8r4m9o6zwatmeiwerxncyqrryse';

    // Contact data
    $contactData = [
        [
            'c1' => 'medical-insurance',
            'c2' => !empty($data['aff_sub']) ? $data['aff_sub'] : 'N/A',
            'c3' => !empty($data['aff_sub2']) ? $data['aff_sub2'] :  '',
            'c4' => 'quote-rocket',
            'c5' => !empty($data['employmentStatus']) ? $data['employmentStatus'] : '',
            'c6' => !empty($data['income']) ? $data['income'] : False,
            'c7' => !empty($data['shippingCity']) ? $data['shippingCity'] : 'N/A',
            'c8' => !empty($data['married']) ? $data['married'] : False,
            'c9' => !empty($data['gender']) ? $data['gender'] : 'N/A',
            'c10' =>  !empty($data['homeowner']) ? $data['homeowner'] : False,
            'cell' =>  '27' . ltrim($data['phone'], '0'),
            'firstname' => $data['given-name'],
            'lastname' => $data['family-name'],
            'idnumber' => !empty($data['idnumber']) ? $data['idnumber'] : '',
            'email' => !empty($data['email']) ? $data['email'] : '',
            'tags' => [$tag1, $tag2]
        ]

    ];
    $dripcellPayload = [
        'welcome_send_campaign_id' => '67bf0e1f1e936c68f8b1f313',
        "contacts" => $contactData,

    ];
    // echo '<pre>';
    // print_r($dripcellPayload);
    //die();
    $ch = curl_init($api_url);
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,  json_encode($dripcellPayload));
    // Execute cURL request
    $dripcellResponse = curl_exec($ch);
    // Close cURL session
    curl_close($ch);
    $dripcellResponseData = json_decode($dripcellResponse, true);
    //lead import api
    $leadUpdateData = array(
        'token' => 'm-f7fd694964k8b165ffeb3afc65eb27a4',
        'phone1' => $data['phone'],
        'dripcell_payload' => json_encode($dripcellPayload),
        'dripcell_response' => $dripcellResponse,
        'dripcellContactUpload' => $dripcellResponseData['data']['validContact']
    );
    $leadUpdateCurl = curl_init();
    curl_setopt_array($leadUpdateCurl, array(
        CURLOPT_URL => 'https://quoterocket.co.za/leads-import/main/updateData',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($leadUpdateData),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'Access-Control-Allow-Origin: *',
        ),
    ));
    $LeadUpdateResponse = curl_exec($leadUpdateCurl);

    curl_close($leadUpdateCurl);
}
function updateDripcellContact($cellNumber, $tag2)
{
    $api_url = 'https://api.dripcel.com/contacts/update';
    $api_key = 'vb1m8j51zbw4nntv4rjzx8r4m9o6zwatmeiwerxncyqrryse';
    // Contact update data
    $dripcellPayload = [

        "find" => [
            "cell" => ['27' . ltrim($cellNumber, '0')]
        ],
        "update" => [
            '$addToSet' => [
                'tag_ids' => [
                    '$each' => [$tag2],
                ]
            ]
        ]

    ];
    // echo json_encode($dripcellPayload);
    // die();
    // Output the array to check
    $ch = curl_init($api_url);
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dripcellPayload));
    // Execute cURL request
    $dripcellUpdateResponse = curl_exec($ch);
    // Close cURL session
    curl_close($ch);

    //echo $dripcellUpdateResponse;
}
