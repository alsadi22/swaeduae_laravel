# SWAED Mobile API Documentation

## Authentication

### Login
```
POST /api/auth/login
```
**Parameters:**
- email (string, required)
- password (string, required)

### Register
```
POST /api/auth/register
```
**Parameters:**
- name (string, required)
- email (string, required)
- password (string, required)
- password_confirmation (string, required)

### Logout
```
POST /api/auth/logout
```

## Mobile Dashboard

### Get Dashboard Data
```
GET /api/mobile/dashboard
```
Returns comprehensive dashboard data including user statistics, upcoming events, recent certificates, and badges.

**Response:**
```json
{
  "user": {
    "name": "John Doe",
    "email": "john@example.com",
    "avatar": "https://example.com/avatar.jpg"
  },
  "stats": {
    "total_volunteer_hours": 25.5,
    "total_events_attended": 3,
    "total_certificates": 2,
    "total_badges": 5,
    "points": 150
  },
  "upcoming_events": [
    {
      "id": 1,
      "title": "Beach Cleanup",
      "organization": "UAE Environmental Group",
      "start_date": "2023-06-15 09:00:00",
      "location": "Dubai, UAE"
    }
  ],
  "recent_certificates": [
    {
      "id": 1,
      "title": "Beach Cleanup Certificate",
      "event_title": "Beach Cleanup",
      "issued_date": "2023-05-20"
    }
  ],
  "recent_badges": [
    {
      "id": 1,
      "name": "First Timer",
      "description": "Completed your first volunteer event"
    }
  ]
}
```

## Events

### Get Events
```
GET /api/mobile/events
```
**Parameters:**
- per_page (integer, optional, default: 10)
- search (string, optional)
- category (string, optional)
- city (string, optional)
- emirate (string, optional)
- date (date, optional)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Beach Cleanup",
      "description": "Help clean up our local beaches",
      "start_date": "2023-06-15 09:00:00",
      "end_date": "2023-06-15 12:00:00",
      "location": "Jumeirah Beach, Dubai",
      "city": "Dubai",
      "emirate": "Dubai",
      "organization": {
        "name": "UAE Environmental Group"
      },
      "is_applied": true,
      "application_status": "approved"
    }
  ],
  "links": {},
  "meta": {}
}
```

## Applications

### Get Applications
```
GET /api/mobile/applications
```
**Parameters:**
- per_page (integer, optional, default: 10)
- status (string, optional) - pending, approved, rejected

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "event_id": 1,
      "status": "approved",
      "applied_at": "2023-05-01 10:00:00",
      "event": {
        "title": "Beach Cleanup",
        "start_date": "2023-06-15 09:00:00",
        "organization": {
          "name": "UAE Environmental Group"
        }
      }
    }
  ],
  "links": {},
  "meta": {}
}
```

## Attendance

### Get Attendance Records
```
GET /api/mobile/attendance
```
**Parameters:**
- per_page (integer, optional, default: 10)
- event_id (integer, optional)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "event_id": 1,
      "checked_in_at": "2023-06-15 09:15:00",
      "checked_out_at": "2023-06-15 11:45:00",
      "status": "checked_out",
      "actual_hours": 2.5,
      "event": {
        "title": "Beach Cleanup"
      }
    }
  ],
  "links": {},
  "meta": {}
}
```

### Check-in to Event
```
POST /api/mobile/checkin
```
**Parameters:**
- event_id (integer, required)
- latitude (float, optional)
- longitude (float, optional)

**Response:**
```json
{
  "message": "Successfully checked in",
  "attendance": {
    "id": 2,
    "event_id": 1,
    "checked_in_at": "2023-06-15 09:15:00",
    "status": "checked_in"
  }
}
```

### Check-out from Event
```
POST /api/mobile/checkout
```
**Parameters:**
- attendance_id (integer, required)
- latitude (float, optional)
- longitude (float, optional)

**Response:**
```json
{
  "message": "Successfully checked out",
  "attendance": {
    "id": 2,
    "event_id": 1,
    "checked_in_at": "2023-06-15 09:15:00",
    "checked_out_at": "2023-06-15 11:45:00",
    "status": "checked_out",
    "actual_hours": 2.5
  }
}
```

## Certificates

### Get Certificates
```
GET /api/mobile/certificates
```
**Parameters:**
- per_page (integer, optional, default: 10)
- type (string, optional)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "certificate_number": "CERT-2023-ABC123",
      "title": "Beach Cleanup Certificate",
      "description": "Certificate for participation in Beach Cleanup event",
      "hours_completed": 2.5,
      "issued_date": "2023-06-16",
      "event": {
        "title": "Beach Cleanup"
      },
      "organization": {
        "name": "UAE Environmental Group"
      }
    }
  ],
  "links": {},
  "meta": {}
}
```

## Badges

### Get Badges
```
GET /api/mobile/badges
```
**Parameters:**
- per_page (integer, optional, default: 10)
- type (string, optional)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "First Timer",
      "slug": "first-timer",
      "description": "Completed your first volunteer event",
      "icon": "first-timer-icon.png",
      "color": "#FF0000",
      "points": 50,
      "earned_at": "2023-05-05 10:00:00"
    }
  ],
  "links": {},
  "meta": {}
}
```

## Notifications

### Get Notifications
```
GET /api/mobile/notifications
```
**Parameters:**
- per_page (integer, optional, default: 10)
- unread_only (boolean, optional, default: false)

**Response:**
```json
{
  "data": [
    {
      "id": "1234567890",
      "type": "App\\Notifications\\ApplicationStatusUpdated",
      "notifiable_type": "App\\Models\\User",
      "notifiable_id": 1,
      "data": {
        "application_id": 1,
        "event_title": "Beach Cleanup",
        "status": "approved",
        "type": "application_status_updated"
      },
      "read_at": null,
      "created_at": "2023-05-01 10:30:00",
      "updated_at": "2023-05-01 10:30:00"
    }
  ],
  "links": {},
  "meta": {}
}
```

### Mark Notification as Read
```
POST /api/mobile/notifications/{id}/read
```

**Response:**
```json
{
  "message": "Notification marked as read"
}
```

## Error Responses

All API endpoints follow standard HTTP status codes:

- **200**: Success
- **201**: Created
- **400**: Bad Request
- **401**: Unauthorized
- **403**: Forbidden
- **404**: Not Found
- **422**: Unprocessable Entity
- **500**: Internal Server Error

**Error Response Format:**
```json
{
  "message": "Error description",
  "errors": {
    "field_name": [
      "Error message for field"
    ]
  }
}
```