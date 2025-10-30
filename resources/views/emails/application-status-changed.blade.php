<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status Update - SwaedUAE</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e74c3c;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            margin: 10px 0;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .event-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .event-title {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }
        .detail-value {
            color: #495057;
        }
        .cta-button {
            display: inline-block;
            background-color: #e74c3c;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .message-content {
            margin: 20px 0;
            line-height: 1.8;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .container {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                margin-bottom: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">SwaedUAE</div>
            <p style="margin: 0; color: #6c757d;">Volunteer Management Platform</p>
        </div>

        <h1 style="color: #333; margin-bottom: 20px;">
            @if($application->status === 'approved')
                🎉 Congratulations! Your Application Has Been Approved
            @elseif($application->status === 'rejected')
                Application Status Update
            @else
                Your Application Status Has Changed
            @endif
        </h1>

        <div class="message-content">
            <p>Dear {{ $volunteer->name }},</p>

            @if($application->status === 'approved')
                <p>We're excited to inform you that your volunteer application has been <strong>approved</strong>! 
                The organization has reviewed your application and would love to have you join their volunteer team.</p>
                
                <p>You're now officially registered as a volunteer for this event. Please make sure to:</p>
                <ul>
                    <li>Mark your calendar for the event date and time</li>
                    <li>Arrive at the specified location on time</li>
                    <li>Bring any required items mentioned in the event details</li>
                    <li>Check your email for any additional instructions from the organization</li>
                </ul>
            @elseif($application->status === 'rejected')
                <p>Thank you for your interest in volunteering with us. After careful consideration, 
                the organization has decided not to move forward with your application for this particular event.</p>
                
                <p>Please don't be discouraged! There are many other volunteer opportunities available on our platform. 
                We encourage you to explore other events that match your interests and skills.</p>
            @else
                <p>Your volunteer application status has been updated from <strong>{{ ucfirst($previousStatus) }}</strong> 
                to <strong>{{ ucfirst($application->status) }}</strong>.</p>
            @endif
        </div>

        <div class="event-details">
            <div class="event-title">{{ $event->title }}</div>
            
            <div class="detail-row">
                <span class="detail-label">Organization:</span>
                <span class="detail-value">{{ $organization->name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Event Date:</span>
                <span class="detail-value">
                    {{ $event->start_date->format('l, F j, Y') }}
                    @if($event->start_time)
                        at {{ $event->start_time->format('g:i A') }}
                    @endif
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Location:</span>
                <span class="detail-value">{{ $event->location }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Application Status:</span>
                <span class="detail-value">
                    <span class="status-badge status-{{ $application->status }}">
                        {{ ucfirst($application->status) }}
                    </span>
                </span>
            </div>
            
            @if($application->status === 'approved' && $event->start_date->isFuture())
                <div class="detail-row">
                    <span class="detail-label">Next Steps:</span>
                    <span class="detail-value">Check your dashboard for event details and updates</span>
                </div>
            @endif
        </div>

        <div style="text-align: center;">
            @if($application->status === 'approved')
                <a href="{{ route('volunteer.events.show', $event) }}" class="cta-button">
                    View Event Details
                </a>
            @else
                <a href="{{ route('volunteer.events.index') }}" class="cta-button">
                    Explore Other Events
                </a>
            @endif
        </div>

        @if($application->status === 'approved')
            <div style="background-color: #d1ecf1; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #bee5eb;">
                <h4 style="margin: 0 0 10px 0; color: #0c5460;">Important Reminders:</h4>
                <ul style="margin: 0; padding-left: 20px; color: #0c5460;">
                    <li>Please arrive 15 minutes before the event start time</li>
                    <li>Bring a valid ID for check-in purposes</li>
                    <li>Dress appropriately for the volunteer activity</li>
                    <li>Contact the organization if you need to cancel or have questions</li>
                </ul>
            </div>
        @endif

        <div class="footer">
            <p>Thank you for being part of the SwaedUAE volunteer community!</p>
            <p>
                <a href="{{ route('volunteer.dashboard') }}" style="color: #e74c3c;">Visit Your Dashboard</a> | 
                <a href="{{ route('volunteer.applications.index') }}" style="color: #e74c3c;">View All Applications</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #adb5bd;">
                This email was sent to {{ $volunteer->email }}. 
                If you have any questions, please contact us through our platform.
            </p>
        </div>
    </div>
</body>
</html>