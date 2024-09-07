<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vulnerabilities</title>
    <link rel="stylesheet" href="{{ asset('css/mail.css') }}">
</head>
<body>
<h3>Vulnerability Details</h3>
<table>
    <thead>
        <tr>
            @foreach($events[0] as $key => $event)
                @if($key !== 'dependencyLink' && $key !== 'cveLink')
                    <th>{{ $key }}</th>
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($events as $event)
            <tr>
                @foreach($event as $key => $val)
                    @if($key === 'dependency')
                        <td><a href="{{ $event['dependencyLink'] }}">{{ $val }}</a></td>
                    @elseif($key === 'cve')
                        <td><a href="{{ $event['cveLink'] }}">{{ $val }}</a></td>
                    @else
                        <td>{{ $val }}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
