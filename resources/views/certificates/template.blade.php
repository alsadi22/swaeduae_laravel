<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Volunteer Service</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 40px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            position: relative;
            height: 100vh;
            box-sizing: border-box;
        }
        
        .certificate-container {
            background: white;
            border: 8px solid #1e40af;
            border-radius: 20px;
            padding: 60px;
            height: calc(100% - 120px);
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .certificate-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: #1e40af;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        .certificate-title {
            font-size: 48px;
            font-weight: bold;
            color: #1e40af;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        
        .certificate-subtitle {
            font-size: 24px;
            color: #666;
            margin-bottom: 40px;
        }
        
        .certificate-body {
            text-align: center;
            margin: 40px 0;
            line-height: 1.8;
        }
        
        .recipient-name {
            font-size: 36px;
            font-weight: bold;
            color: #1e40af;
            text-decoration: underline;
            margin: 20px 0;
            text-transform: uppercase;
        }
        
        .certificate-text {
            font-size: 18px;
            margin: 20px 0;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .event-details {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
        }
        
        .event-details h3 {
            color: #1e40af;
            margin-top: 0;
            font-size: 20px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 16px;
        }
        
        .detail-label {
            font-weight: bold;
            color: #374151;
        }
        
        .certificate-footer {
            position: absolute;
            bottom: 40px;
            left: 60px;
            right: 60px;
            display: flex;
            justify-content: space-between;
            align-items: end;
        }
        
        .signature-section {
            text-align: center;
            flex: 1;
        }
        
        .signature-line {
            border-top: 2px solid #333;
            width: 200px;
            margin: 40px auto 10px;
        }
        
        .signature-title {
            font-size: 14px;
            font-weight: bold;
            color: #666;
        }
        
        .certificate-number {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 12px;
            color: #666;
        }
        
        .verification-code {
            position: absolute;
            bottom: 20px;
            left: 20px;
            font-size: 12px;
            color: #666;
        }
        
        .decorative-border {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid #fbbf24;
            border-radius: 15px;
            pointer-events: none;
        }
        
        .uae-colors {
            position: absolute;
            top: 30px;
            left: 30px;
            width: 100px;
            height: 20px;
            background: linear-gradient(to right, #ff0000 25%, #00ff00 25% 50%, #ffffff 50% 75%, #000000 75%);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="decorative-border"></div>
        <div class="uae-colors"></div>
        
        <div class="certificate-number">
            Certificate No: {{ $certificate->certificate_number }}
        </div>
        
        <div class="certificate-header">
            <div class="logo">UAE</div>
            <h1 class="certificate-title">Certificate of Volunteer Service</h1>
            <p class="certificate-subtitle">United Arab Emirates Volunteer Platform</p>
        </div>
        
        <div class="certificate-body">
            <p class="certificate-text">This is to certify that</p>
            
            <div class="recipient-name">{{ $certificate->user->name }}</div>
            
            <p class="certificate-text">
                has successfully completed volunteer service and demonstrated exceptional commitment 
                to community development in the United Arab Emirates.
            </p>
            
            <div class="event-details">
                <h3>Volunteer Service Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Event:</span>
                    <span>{{ $certificate->event->title }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Organization:</span>
                    <span>{{ $certificate->organization->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date of Service:</span>
                    <span>{{ $certificate->event_date->format('F j, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Hours Completed:</span>
                    <span>{{ $certificate->hours_completed }} hours</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Certificate Type:</span>
                    <span>{{ ucfirst($certificate->type) }} Service</span>
                </div>
            </div>
            
            <p class="certificate-text">
                {{ $certificate->description }}
            </p>
        </div>
        
        <div class="certificate-footer">
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-title">Organization Representative</div>
                <div>{{ $certificate->organization->name }}</div>
            </div>
            
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-title">Platform Administrator</div>
                <div>SwaedUAE Platform</div>
            </div>
        </div>
        
        <div class="verification-code">
            Verification Code: {{ $certificate->verification_code }} | 
            Issued: {{ $certificate->issued_date->format('M j, Y') }} |
            Verify at: swaeduae.ae/verify/{{ $certificate->verification_code }}
        </div>
    </div>
</body>
</html>