# SWAED API Documentation

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

## Users

### Get All Users
```
GET /api/users
```
**Parameters:**
- per_page (integer, optional, default: 15)
- search (string, optional)
- role (string, optional)

### Get User
```
GET /api/users/{id}
```

### Update User
```
PUT /api/users/{id}
```
**Parameters:**
- name (string, optional)
- email (string, optional)
- phone (string, optional)
- date_of_birth (date, optional)
- gender (string, optional)
- nationality (string, optional)
- emirates_id (string, optional)
- address (string, optional)
- city (string, optional)
- emirate (string, optional)
- postal_code (string, optional)
- emergency_contact_name (string, optional)
- emergency_contact_phone (string, optional)
- emergency_contact_relationship (string, optional)
- skills (array, optional)
- interests (array, optional)
- bio (string, optional)
- languages (array, optional)
- education_level (string, optional)
- occupation (string, optional)
- has_transportation (boolean, optional)
- availability (array, optional)

### Delete User
```
DELETE /api/users/{id}
```

### Get User Applications
```
GET /api/users/{id}/applications
```

### Get User Attendance
```
GET /api/users/{id}/attendance
```

### Get User Certificates
```
GET /api/users/{id}/certificates
```

### Get User Badges
```
GET /api/users/{id}/badges
```

### Get User Statistics
```
GET /api/users/{id}/statistics
```

### Update Notification Preferences
```
PUT /api/users/{id}/notification-preferences
```
**Parameters:**
- notification_preferences (array, required)

### Update Privacy Settings
```
PUT /api/users/{id}/privacy-settings
```
**Parameters:**
- privacy_settings (array, required)

## Applications

### Get All Applications
```
GET /api/applications
```
**Parameters:**
- per_page (integer, optional, default: 15)
- status (string, optional)
- event_id (integer, optional)
- user_id (integer, optional)

### Create Application
```
POST /api/applications
```
**Parameters:**
- event_id (integer, required)
- motivation (string, required)
- skills (array, optional)
- availability (array, optional)
- experience (string, optional)
- custom_responses (array, optional)

### Get Application
```
GET /api/applications/{id}
```

### Update Application
```
PUT /api/applications/{id}
```
**Parameters:**
- motivation (string, optional)
- skills (array, optional)
- availability (array, optional)
- experience (string, optional)
- custom_responses (array, optional)

### Delete Application
```
DELETE /api/applications/{id}
```

### Approve Application
```
POST /api/applications/{id}/approve
```

### Reject Application
```
POST /api/applications/{id}/reject
```
**Parameters:**
- rejection_reason (string, required)

### Get My Applications
```
GET /api/my-applications
```
**Parameters:**
- per_page (integer, optional, default: 15)
- status (string, optional)

### Get Organization Applications
```
GET /api/organization-applications
```
**Parameters:**
- per_page (integer, optional, default: 15)
- status (string, optional)
- event_id (integer, optional)

## Events

### Get All Events
```
GET /api/events
```
**Parameters:**
- per_page (integer, optional, default: 15)
- search (string, optional)
- category (string, optional)
- city (string, optional)
- emirate (string, optional)
- is_featured (boolean, optional)

### Get Event
```
GET /api/events/{id}
```

## Organizations

### Get All Organizations
```
GET /api/organizations
```
**Parameters:**
- per_page (integer, optional, default: 15)
- search (string, optional)
- city (string, optional)
- emirate (string, optional)
- is_verified (boolean, optional)

### Get Organization
```
GET /api/organizations/{id}
```

## Attendance

### Scan QR Code
```
POST /api/attendance/scan
```
**Parameters:**
- qr_code (string, required)
- latitude (float, optional)
- longitude (float, optional)
- device_info (string, optional)

### Check In
```
POST /api/attendance/checkin
```
**Parameters:**
- event_id (integer, required)
- latitude (float, optional)
- longitude (float, optional)
- device_info (string, optional)

### Check Out
```
POST /api/attendance/checkout
```
**Parameters:**
- attendance_id (integer, required)
- latitude (float, optional)
- longitude (float, optional)
- device_info (string, optional)

### Get Attendance History
```
GET /api/attendance/history
```
**Parameters:**
- per_page (integer, optional, default: 15)

### Get Attendance Record
```
GET /api/attendance/{id}
```

### Validate Location
```
POST /api/attendance/validate-location
```
**Parameters:**
- event_id (integer, required)
- latitude (float, required)
- longitude (float, required)

## Certificates

### Get All Certificates
```
GET /api/certificates
```
**Parameters:**
- per_page (integer, optional, default: 15)
- user_id (integer, optional)
- event_id (integer, optional)
- organization_id (integer, optional)
- is_public (boolean, optional)

### Create Certificate
```
POST /api/certificates
```
**Parameters:**
- user_id (integer, required)
- event_id (integer, required)
- organization_id (integer, required)
- type (string, required)
- title (string, required)
- description (string, optional)
- hours_completed (float, required)
- event_date (date, required)
- template (string, optional)
- custom_fields (array, optional)
- is_public (boolean, optional)

### Get Certificate
```
GET /api/certificates/{id}
```

### Update Certificate
```
PUT /api/certificates/{id}
```
**Parameters:**
- title (string, optional)
- description (string, optional)
- hours_completed (float, optional)
- template (string, optional)
- custom_fields (array, optional)
- is_public (boolean, optional)
- is_verified (boolean, optional)

### Delete Certificate
```
DELETE /api/certificates/{id}
```

### Get My Certificates
```
GET /api/my-certificates
```
**Parameters:**
- per_page (integer, optional, default: 15)
- type (string, optional)

### Verify Certificate
```
POST /api/certificates/verify
```
**Parameters:**
- verification_code (string, required)

### Get User Public Certificates
```
GET /api/users/{id}/public-certificates
```

## Badges

### Get All Badges
```
GET /api/badges
```
**Parameters:**
- per_page (integer, optional, default: 15)
- is_active (boolean, optional)
- type (string, optional)

### Create Badge
```
POST /api/badges
```
**Parameters:**
- name (string, required)
- slug (string, required)
- description (string, optional)
- icon (string, optional)
- color (string, optional)
- type (string, optional)
- criteria (array, optional)
- points (integer, required)
- is_active (boolean, optional)
- sort_order (integer, optional)

### Get Badge
```
GET /api/badges/{id}
```

### Update Badge
```
PUT /api/badges/{id}
```
**Parameters:**
- name (string, optional)
- slug (string, optional)
- description (string, optional)
- icon (string, optional)
- color (string, optional)
- type (string, optional)
- criteria (array, optional)
- points (integer, optional)
- is_active (boolean, optional)
- sort_order (integer, optional)

### Delete Badge
```
DELETE /api/badges/{id}
```

### Get Badge Users
```
GET /api/badges/{id}/users
```
**Parameters:**
- per_page (integer, optional, default: 15)

### Award Badge to User
```
POST /api/badges/{id}/award
```
**Parameters:**
- user_id (integer, required)
- metadata (array, optional)

### Get My Badges
```
GET /api/my-badges
```
**Parameters:**
- per_page (integer, optional, default: 15)
- type (string, optional)

### Get Available Badges
```
GET /api/available-badges
```
**Parameters:**
- per_page (integer, optional, default: 15)
- type (string, optional)