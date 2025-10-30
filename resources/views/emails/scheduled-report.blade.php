<!DOCTYPE html>
<html>
<head>
    <title>Scheduled Report: {{ $scheduledReport->name }}</title>
</head>
<body>
    <h2>Scheduled Report: {{ $scheduledReport->name }}</h2>
    
    <p>Hello,</p>
    
    <p>Your scheduled report "{{ $scheduledReport->name }}" has been generated and is attached to this email.</p>
    
    <h3>Report Details:</h3>
    <ul>
        <li><strong>Type:</strong> {{ ucfirst($scheduledReport->type) }}</li>
        <li><strong>Format:</strong> {{ strtoupper($scheduledReport->format) }}</li>
        <li><strong>Frequency:</strong> {{ ucfirst($scheduledReport->frequency) }}</li>
        <li><strong>Generated on:</strong> {{ now()->format('F j, Y g:i A') }}</li>
    </ul>
    
    <p>Please find the report attached to this email.</p>
    
    <p>Best regards,<br>
    SwaedUAE Volunteer Management Platform</p>
</body>
</html>