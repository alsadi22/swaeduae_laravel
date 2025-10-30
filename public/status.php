<?php
// Simple status page without Laravel dependencies
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwaedUAE - System Status</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
        }
        h1 { 
            color: #2c3e50; 
            text-align: center; 
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        .status { 
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); 
            padding: 20px; 
            border-radius: 10px; 
            margin: 20px 0; 
            border-left: 5px solid #e74c3c;
        }
        .info { 
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); 
            padding: 20px; 
            border-radius: 10px; 
            margin: 20px 0; 
            border-left: 5px solid #3498db;
        }
        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin: 30px 0; 
        }
        .card { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 10px; 
            text-align: center; 
            border: 2px solid #e9ecef;
        }
        .emoji { font-size: 2em; margin-bottom: 10px; }
        ul { line-height: 1.8; }
        .footer { text-align: center; margin-top: 40px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🇦🇪 SwaedUAE Platform</h1>
        
        <div class="status">
            <h3>⚠️ System Status: Under Maintenance</h3>
            <p><strong>Issue:</strong> Laravel 12 EventServiceProvider compatibility issue preventing full application bootstrap.</p>
            <p><strong>Impact:</strong> Main application routes are currently unavailable.</p>
            <p><strong>Status:</strong> Development team is actively working on a resolution.</p>
        </div>

        <div class="info">
            <h3>📋 Technical Details</h3>
            <p><strong>Server:</strong> Ubuntu 22.04 LTS</p>
            <p><strong>Web Server:</strong> Nginx + PHP-FPM</p>
            <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
            <p><strong>Framework:</strong> Laravel 12.x</p>
            <p><strong>Timestamp:</strong> <?php echo date('Y-m-d H:i:s T'); ?></p>
        </div>

        <h3>🎯 Platform Features (When Operational)</h3>
        <div class="grid">
            <div class="card">
                <div class="emoji">🎯</div>
                <h4>Event Management</h4>
                <p>Create and manage volunteer opportunities</p>
            </div>
            <div class="card">
                <div class="emoji">🏢</div>
                <h4>Organizations</h4>
                <p>Register and verify organizations</p>
            </div>
            <div class="card">
                <div class="emoji">📱</div>
                <h4>QR Attendance</h4>
                <p>Track attendance with QR codes</p>
            </div>
            <div class="card">
                <div class="emoji">🏆</div>
                <h4>Certificates</h4>
                <p>Digital certificates and badges</p>
            </div>
            <div class="card">
                <div class="emoji">📊</div>
                <h4>Analytics</h4>
                <p>Comprehensive reporting</p>
            </div>
            <div class="card">
                <div class="emoji">🔐</div>
                <h4>Security</h4>
                <p>Multi-role authentication</p>
            </div>
        </div>

        <div class="footer">
            <p>SwaedUAE - UAE Volunteer Management Platform</p>
            <p>Empowering communities through organized volunteering</p>
        </div>
    </div>
</body>
</html>