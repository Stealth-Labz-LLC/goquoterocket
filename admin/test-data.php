<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataTable with 10k Records</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
</head>

<body>
    <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Position</th>
                <th>Office</th>
                <th>Age</th>
                <th>Start Date</th>
                <th>Salary</th>
            </tr>
        </thead>
    </table>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                processing: true,
                serverSide: true,
                ajax: "ajax-table-data.php", // Path to your PHP file
                pageLength: 50, // Set default number of rows
                columns: [{
                        data: "id"
                    },
                    {
                        data: "created_at"
                    },
                    {
                        data: "aff_sub"
                    },
                    {
                        data: "aff_sub2"
                    },
                    {
                        data: "aff_sub3"
                    },
                    {
                        data: "funnel_id"
                    },
                    {
                        data: "phone1"
                    }
                ]
            });
        });
    </script>
</body>

</html>