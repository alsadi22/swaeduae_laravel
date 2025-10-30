# Development Credentials

**⚠️ IMPORTANT: These are DEFAULT DEVELOPMENT credentials. CHANGE IMMEDIATELY in production!**

## Test User Accounts

### Admin User
- **Email:** admin@swaeduae.ae
- **Password:** Admin@2025!Swaed
- **Role:** Admin/Super Admin

### Volunteer User
- **Email:** volunteer@swaeduae.ae  
- **Password:** Volunteer@2025!Swaed
- **Role:** Volunteer

### Organization Manager
- **Email:** org@swaeduae.ae
- **Password:** Org@2025!Swaed
- **Role:** Organization Manager

---

## Production Deployment Checklist

Before deploying to production:

1. ✅ Change all default passwords above
2. ✅ Update `.env` with production database credentials
3. ✅ Set `APP_ENV=production` in `.env`
4. ✅ Set `APP_DEBUG=false` in `.env`
5. ✅ Generate new `APP_KEY` using `php artisan key:generate`
6. ✅ Configure mail settings in `.env`
7. ✅ Set up proper backup strategy
8. ✅ Configure SSL/TLS certificates
9. ✅ Set up monitoring and logging
10. ✅ Review and update CORS settings

---

**Last Updated:** October 30, 2025

