@php
    /** @var \App\Models\Attendance $attendance */
    /** @var string $type */
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance {{ $type }} recorded</title>
</head>
<body>
    <p>Hello {{ $attendance->employee->names }},</p>

    <p>Your attendance <strong>{{ $type }}</strong> has been recorded.</p>

    <ul>
        <li><strong>Check in:</strong> {{ $attendance->check_in_at }}</li>
        <li><strong>Check out:</strong> {{ $attendance->check_out_at ?? 'N/A' }}</li>
    </ul>

    <p>Have a great day!</p>
</body>
</html>

