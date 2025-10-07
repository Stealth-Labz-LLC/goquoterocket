<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET POST");
// header("Access-Control-Allow-Headers: X-Requested-With");
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();
$requestData = !empty($_REQUEST) ? $_REQUEST : '';
//echo '<pre>'; print_r($requestData); die();
$queryParams = !empty($_SERVER['QUERY_STRING']) ? "&" . $_SERVER['QUERY_STRING'] : '';
//echo $base_url; die();
$triggerTrackApi = 0;
$triggerWarrantyApi = 0;
// Get the current date and time
$currentDateTime = new DateTime();
$updateTime = $currentDateTime->format('d/m/Y H:i:s');
$preferredtimeofcall = $currentDateTime->format('d/m/Y H:i:s');

// new code
if (!empty($requestData['insuranceApi']) && $requestData['insuranceApi'] === 'Yes') {
    $_SESSION['prevFormData'] = $requestData;
    $_SESSION['delaySetForTrack'] = 1;
}
$data =  !empty($_SESSION['prevFormData']) ? $_SESSION['prevFormData'] : $requestData;
if (!empty($requestData['trackApi']) && $requestData['trackApi'] === 'Yes') {
    $triggerTrackApi = 1;
    $_SESSION['delaySetForWarrant'] = 1;
    if (isset($_SESSION['delaySetForTrack']) && $_SESSION['delaySetForTrack'] == 1) {
        $trackPreferredtimeofcall = $currentDateTime->modify('+45 minutes')->format('d/m/Y H:i:s');
    } else {
        $trackPreferredtimeofcall = $currentDateTime->format('d/m/Y H:i:s');
    }
}
if (!empty($requestData['warrantApi']) && $requestData['warrantApi'] === 'Yes') {
    $triggerWarrantyApi = 1;
    if (isset($_SESSION['delaySetForWarrant']) && $_SESSION['delaySetForWarrant'] == 1) {
        $warrantyPreferredtimeofcall = $currentDateTime->modify('+90 minutes')->format('d/m/Y H:i:s');
    } else {
        $warrantyPreferredtimeofcall = $currentDateTime->modify('+45 minutes')->format('d/m/Y H:i:s');
    }
}
//echo '<pre>'; print_r($data); die();
$affilates_params = array(

    'aff_id' => !empty($data['aff_id']) ? $data['aff_id'] : 'N/A',
    'offer_id' => !empty($data['offer_id']) ? $data['offer_id'] : 'N/A',
    'aff_sub' => !empty($data['aff_sub']) ? $data['aff_sub'] : 'N/A',
    'aff_sub2' => !empty($data['aff_sub2']) ? $data['aff_sub2'] : 'N/A',
    'aff_sub3' => !empty($data['aff_sub3']) ? $data['aff_sub3'] : 'N/A'

);
$data['otherMakeYear'] = !empty($data['otherYears']) ? $data['otherYears'] : '0000';

$base_url = $_SERVER['HTTP_REFERER'] . dirname($_SERVER['REQUEST_URI']) . '/?aff_id=' . $affilates_params['aff_id'] . '&offer_id=' . $affilates_params['offer_id'] . '&aff_sub=' . $affilates_params['aff_sub'] . '&aff_sub2=' . $affilates_params['aff_sub2'] . '&aff_sub3=' . $affilates_params['aff_sub3'];
//echo $base_url;


if (!empty($requestData) && $requestData['insuranceApi'] === 'Yes' && $requestData['trackApi'] === 'Yes' &&  $requestData['warrantApi'] === 'Yes') {
    $insuranceApiResult = insuranceApiCall($data, $base_url, $updateTime, $triggerTrackApi, $preferredtimeofcall);
    if ($insuranceApiResult) {
        importDataToDripcell($data, $tag1 = 'AR', $tag2 = 'Car');
    }
    $trackApiResult = trackApiCall($data, $base_url, $updateTime, $triggerTrackApi, $trackPreferredtimeofcall);
    if ($trackApiResult) {
        updateDripcellContact($data['phone'], $tag2 = '67a7a99c9aa21d7fb3b84ac9');
    }
    $warrantApiResult = warrantyApiCall($data, $base_url, $updateTime, $triggerWarrantyApi, $warrantyPreferredtimeofcall);
    if ($warrantApiResult) {
        updateDripcellContact($data['phone'], $tag2 = '67a7a9b39aa21d7fb3b84ad2');
    }
} else if (!empty($requestData) && $requestData['insuranceApi'] === 'Yes' && $requestData['trackApi'] === 'Yes') {
    $insuranceApiResult = insuranceApiCall($data, $base_url, $updateTime, $triggerTrackApi, $preferredtimeofcall);
    if ($insuranceApiResult) {
        importDataToDripcell($data, $tag1 = 'AR', $tag2 = 'Car');
    }
    $trackApiResult = trackApiCall($data, $base_url, $updateTime, $triggerTrackApi, $trackPreferredtimeofcall);
    if ($trackApiResult) {
        updateDripcellContact($data['phone'], $tag2 = '67a7a99c9aa21d7fb3b84ac9');
    }
} else if (!empty($requestData) && $requestData['insuranceApi'] === 'Yes' && $requestData['warrantApi'] === 'Yes') {
    $insuranceApiResult = insuranceApiCall($data, $base_url, $updateTime, $triggerTrackApi, $preferredtimeofcall);
    if ($insuranceApiResult) {
        importDataToDripcell($data, $tag1 = 'AR', $tag2 = 'Car');
    }
    $warrantApiResult = warrantyApiCall($data, $base_url, $updateTime, $triggerWarrantyApi, $warrantyPreferredtimeofcall);
    if ($warrantApiResult) {
        updateDripcellContact($data['phone'], $tag2 = '67a7a9b39aa21d7fb3b84ad2');
    }
} else if (!empty($requestData) && $requestData['trackApi'] === 'Yes') {
    trackApiCall($data, $base_url, $updateTime, $triggerTrackApi, $trackPreferredtimeofcall);
    if ($trackApiResult) {
        updateDripcellContact($data['phone'], $tag2 = '67a7a99c9aa21d7fb3b84ac9');
    }
} else if (!empty($requestData) && $requestData['warrantApi'] === 'Yes') {
    warrantyApiCall($data, $base_url, $updateTime, $triggerWarrantyApi, $warrantyPreferredtimeofcall);
    if ($warrantApiResult) {
        updateDripcellContact($data['phone'], $tag2 = '67a7a9b39aa21d7fb3b84ad2');
    }
} else {
    $insuranceApiResult = insuranceApiCall($data, $base_url, $updateTime, $triggerTrackApi, $preferredtimeofcall);
    if ($insuranceApiResult) {
        importDataToDripcell($data, $tag1 = 'AR', $tag2 = 'Car');
    }
}

function insuranceApiCall($data, $base_url, $updateTime, $triggerTrackApi, $preferredtimeofcall)
{
    //Insurance api call
    $insurancePayload = array(
        'returnjson' => 'yes',
        'campid' => 'CAR-INSURANCE',
        'sid' => !empty($data['aff_id']) ? $data['aff_id'] : 'N/A',
        //'sid' =>'25293',
        'firstname' => $data['given-name'],
        'lastname' => $data['family-name'],
        'phone1' => $data['phone'],
        'optinurl' => $base_url,
        'optindate' => $updateTime,
        'channel' => 'JMAff',
        'product' => 'JMCar',
        'leadsource' => 'JMLSAUET',
        'affiliateshortcode' => "JMAFFSite25325",
        'currentinsurer' => "Auto and General",
        'vehiclemake' => !empty($data['make']) ? $data['make'] : $data['otherMake'],
        'vehiclemodel' => !empty($data['vehicle-model']) ? $data['vehicle-model'] : 'N/A',
        'vehicleyear' => !empty($data['year']) ? $data['year'] : $data['otherMakeYear'],
        'mileage' => !empty($data['mileage']) ? $data['mileage'] : 'null',
        'currentcarrier' => !empty($data['current_carrier']) ? $data['current_carrier'] : 'N/A',
        'vehicle_used_as' => !empty($data['vehicle_used_as']) ? $data['vehicle_used_as'] : 'N/A',
        'currentlyinsured' => !empty($data['currentlyinsured']) ? $data['currentlyinsured'] : 'N/A',
        'employmentstatus' => !empty($data['employment-status']) ? $data['employment-status'] : 'N/A',
        'income' => !empty($data['income']) ? $data['income'] : 'null',
        'acceptterms' => 'true',
        'aff_sub' => !empty($data['aff_sub']) ? $data['aff_sub'] : 'N/A',
        'aff_sub2' => !empty($data['aff_sub2']) ? $data['aff_sub2'] : 'N/A',
        'aff_sub3' => !empty($data['aff_sub3']) ? $data['aff_sub3'] : 'N/A',
        'offer_id' => !empty($data['offer_id']) ? $data['offer_id'] : 'N/A',
        'preferredtimeofcall' => $preferredtimeofcall,
    );
    //call track Api if $triggerTrackApi value set to 1
    //echo '<pre>'; print_r($insurancePayload); die();
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
        CURLOPT_POSTFIELDS => http_build_query($insurancePayload),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'Access-Control-Allow-Origin: *',
        ),
    ));
    $response = curl_exec($curl);
    //echo curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
    curl_close($curl);
    $insuranceResponseData = json_decode($response);

    if ($insuranceResponseData->code !== -2) {

        $leadPayload = array(
            'token' => 'm-f7fd694964k8b165ffeb3afc65eb27a4',
            'campid' => 'CAR-INSURANCE',
            'funnel_id' => !empty($data['funnelId']) ? $data['funnelId'] : 'N/A',
            'sid' => !empty($data['aff_id']) ? $data['aff_id'] : 'N/A',
            'insurance_payload' => json_encode($insurancePayload),
            'insurance_response' => $response,
            'city' => !empty($data['shippingCity']) ? $data['shippingCity'] : 'N/A',
            'firstname' => $data['given-name'],
            'lastname' => $data['family-name'],
            'phone1' => $data['phone'],
            'optinurl' => $base_url,
            'optindate' => $updateTime,
            'currentinsurer' => "Auto and General",
            'vehiclemake' => !empty($data['make']) ? $data['make'] : $data['otherMake'],
            'vehiclemodel' => !empty($data['vehicle-model']) ? $data['vehicle-model'] : 'N/A',
            'vehicleyear' => !empty($data['year']) ? $data['year'] : $data['otherMakeYear'],
            'mileage' => !empty($data['mileage']) ? $data['mileage'] : 'null',
            'currentcarrier' => !empty($data['current_carrier']) ? $data['current_carrier'] : 'N/A',
            'currentlyinsured' => !empty($data['currentlyinsured']) ? $data['currentlyinsured'] : 'N/A',
            'employmentstatus' => !empty($data['employment-status']) ? $data['employment-status'] : 'N/A',
            'income' => !empty($data['income']) ? $data['income'] : 'null',
            'acceptterms' => 'true',
            'aff_sub' => !empty($data['aff_sub']) ? $data['aff_sub'] : 'N/A',
            'aff_sub2' => !empty($data['aff_sub2']) ? $data['aff_sub2'] : 'N/A',
            'aff_sub3' => !empty($data['aff_sub3']) ? $data['aff_sub3'] : 'N/A',
            'offer_id' => !empty($data['offer_id']) ? $data['offer_id'] : 'N/A',
            'ins_preferredtimeofcall' => $preferredtimeofcall,
            'triggerTrackapi' => $triggerTrackApi
        );
        //echo '<pre>'; print_r($leadPayload); die();
        //lead import api
        $leadCurl = curl_init();
        curl_setopt_array($leadCurl, array(
            CURLOPT_URL => 'https://quoterocket.co.za/leads-import/addData',
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
}
function trackApiCall($data, $base_url, $updateTime, $triggerTrackApi, $trackPreferredtimeofcall)
{
    $trackPayload = array(
        'returnjson' => 'yes',
        'campid' => 'VEHICLE-TRACKING',
        //'sid' => !empty($data['aff_id']) ? $data['aff_id'] : 'N/A',
        'sid' => '25335',
        'city' => !empty($data['shippingCity']) ? $data['shippingCity'] : 'N/A',
        'firstname' => $data['given-name'],
        'lastname' => $data['family-name'],
        'phone1' => $data['phone'],
        'optinurl' => $base_url,
        'optindate' => $updateTime,
        'channel' => 'JMAff',
        'product' => 'JMTracker',
        'leadsource' => 'JMLSAUET',
        'affiliateshortcode' => "JMAFFSite25528",
        'currentinsurer' => "Auto and General",
        'vehiclemake' => !empty($data['make']) ? $data['make'] : $data['otherMake'],
        'vehiclemodel' => !empty($data['vehicle-model']) ? $data['vehicle-model'] : 'N/A',
        'vehicleyear' => !empty($data['year']) ? $data['year'] : $data['otherMakeYear'],
        'currentcarrier' => !empty($data['current_carrier']) ? $data['current_carrier'] : 'N/A',
        'currentlyinsured' => !empty($data['currentlyinsured']) ? $data['currentlyinsured'] : 'N/A',
        'employmentstatus' => !empty($data['employment-status']) ? $data['employment-status'] : 'N/A',
        'acceptterms' => 'true',
        'aff_sub' => !empty($data['aff_sub']) ? $data['aff_sub'] : 'N/A',
        'aff_sub2' => !empty($data['aff_sub2']) ? $data['aff_sub2'] : 'N/A',
        'aff_sub3' => !empty($data['aff_sub3']) ? $data['aff_sub3'] : 'N/A',
        'offer_id' => '1523',
        'preferredtimeofcall' => $trackPreferredtimeofcall,
    );
    $trackCurl = curl_init();
    curl_setopt_array($trackCurl, array(
        CURLOPT_URL => 'https://returnxdigital.leadbyte.co.uk/api/submit.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($trackPayload),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'Access-Control-Allow-Origin: *',
        ),
    ));
    $trackResponse = curl_exec($trackCurl);
    curl_close($trackCurl);
    //lead import api
    $leadUpdateData = array(
        'token' => 'm-f7fd694964k8b165ffeb3afc65eb27a4',
        'phone1' => $data['phone'],
        'track_payload' => json_encode($trackPayload),
        'track_response' => $trackResponse,
        'track_preferredtimeofcall' => $trackPreferredtimeofcall,
        'triggerTrackapi' => $triggerTrackApi
    );
    $leadUpdateCurl = curl_init();
    curl_setopt_array($leadUpdateCurl, array(
        CURLOPT_URL => 'https://quoterocket.co.za/leads-import/updateData',
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
    //echo curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
    //file_put_contents('./leadUpdateReslog_View' . time() . '.log', $trackResponse, FILE_APPEND);
    curl_close($leadUpdateCurl);
    //echo $trackResponse;
    return true;
}
function warrantyApiCall($data, $base_url, $updateTime, $triggerWarrantyApi, $warrantyPreferredtimeofcall)
{
    $warrantyPayload = array(
        'returnjson' => 'yes',
        'campid' => 'MOTOR-WARRANTY',
        //'sid' => !empty($data['aff_id']) ? $data['aff_id'] : 'N/A',
        'sid' => '25335',
        'city' => !empty($data['shippingCity']) ? $data['shippingCity'] : 'N/A',
        'firstname' => $data['given-name'],
        'lastname' => $data['family-name'],
        'phone1' => $data['phone'],
        'optinurl' => $base_url,
        'optindate' => $updateTime,
        'channel' => 'JMAff',
        'product' => 'JMMotorEx',
        'leadsource' => 'JMLSAUET',
        'affiliateshortcode' => "JMAFFSite25529",
        'currentinsurer' => "Auto and General",
        'vehiclemake' => !empty($data['make']) ? $data['make'] : $data['otherMake'],
        'vehiclemodel' => !empty($data['vehicle-model']) ? $data['vehicle-model'] : 'N/A',
        'vehicleyear' => !empty($data['year']) ? $data['year'] : $data['otherMakeYear'],
        'currentcarrier' => !empty($data['current_carrier']) ? $data['current_carrier'] : 'N/A',
        'currentlyinsured' => !empty($data['currentlyinsured']) ? $data['currentlyinsured'] : 'N/A',
        'employmentstatus' => !empty($data['employment-status']) ? $data['employment-status'] : 'N/A',
        'acceptterms' => 'true',
        'aff_sub' => !empty($data['aff_sub']) ? $data['aff_sub'] : 'N/A',
        'aff_sub2' => !empty($data['aff_sub2']) ? $data['aff_sub2'] : 'N/A',
        'aff_sub3' => !empty($data['aff_sub3']) ? $data['aff_sub3'] : 'N/A',
        'offer_id' => '2944',
        'preferredtimeofcall' => $warrantyPreferredtimeofcall,
    );
    $warrantyCurl = curl_init();
    curl_setopt_array($warrantyCurl, array(
        CURLOPT_URL => 'https://returnxdigital.leadbyte.co.uk/api/submit.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($warrantyPayload),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'Access-Control-Allow-Origin: *',
        ),
    ));
    $warrantyResponse = curl_exec($warrantyCurl);
    curl_close($warrantyCurl);
    //lead import api
    $leadUpdateData = array(
        'token' => 'm-f7fd694964k8b165ffeb3afc65eb27a4',
        'phone1' => $data['phone'],
        'warranty_payload' => json_encode($warrantyPayload),
        'warranty_response' => $warrantyResponse,
        'warranty_preferredtimeofcall' => $warrantyPreferredtimeofcall,
        'triggerWarrantyapi' => $triggerWarrantyApi
    );
    $leadUpdateCurl = curl_init();
    curl_setopt_array($leadUpdateCurl, array(
        CURLOPT_URL => 'https://quoterocket.co.za/leads-import/updateData',
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
    //echo curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
    //file_put_contents('./leadUpdateReslog_View' . time() . '.log', $warrantyResponse, FILE_APPEND);
    curl_close($leadUpdateCurl);
    return true;
}
function importDataToDripcell($data, $tag1, $tag2)
{
    $api_url = 'https://api.dripcel.com/contacts';
    $api_key = 'vb1m8j51zbw4nntv4rjzx8r4m9o6zwatmeiwerxncyqrryse';

    // Contact data
    $dripcellPayload = [
        [
            'c1' => 'car-insurance',
            'c2' => !empty($data['aff_sub']) ? $data['aff_sub'] : 'N/A',
            'c3' => !empty($data['aff_sub2']) ? $data['aff_sub2'] : 'N/A',
            'c4' => 'auto-rocket',
            'c5' => !empty($data['employment-status']) ? $data['employment-status'] : '',
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
    // echo '<pre>';
    // print_r($dripcellPayload);
    // die();
    $ch = curl_init($api_url);
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,  json_encode(['contacts' => $dripcellPayload]));
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
        CURLOPT_URL => 'https://quoterocket.co.za/leads-import/updateData',
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
