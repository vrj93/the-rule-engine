<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>File upload status</title>
    <style>
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 18px;
            text-align: left;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h3>Upload Status Summery</h3>
    <table>
        <tr>
            <th>Category</th>
            <th>Data</th>
        </tr>
        <tr>
            <td>Progress</td>
            <td>{{ $data['progress'].' %' }}</td>
        </tr>
        @isset($data['vulnerabilities'])
            <tr>
                <td>Vulnerabilities</td>
                <td>{{ $data['vulnerabilities'] }}</td>
            </tr>
        @endisset
        <tr>
            <td colspan="2" style="text-align: center;"><a href="{{ $data['detailsUrl'] }}" target="_blank"><b>View Detailed Report</b></a></td>
        </tr>
    </table>
</body>
</html>
