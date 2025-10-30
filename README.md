# SwaedUAE - Volunteer Management Platform

[![Laravel](https://img.shields.io/badge/Laravel-12.36-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-14-336791?logo=postgresql)](https://postgresql.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**SwaedUAE** is a comprehensive volunteer management platform built for the UAE, empowering organizations to manage volunteer events, track participation, issue certificates, and engage volunteers through gamification.

## 🌟 Features

### Core Functionality
- **Multi-Role System**: Admin, Organization, and Volunteer user types
- **Event Management**: Complete lifecycle from creation to completion
- **Smart Applications**: Automated application processing and approval
- **QR Code Attendance**: Check-in/out with location validation
- **Digital Certificates**: Automated PDF certificate generation
- **Gamification**: Badges, points, leaderboards, and achievements
- **Analytics Dashboard**: Real-time insights and reporting
- **Email Notifications**: Zoho Mail integration for system emails
- **API Platform**: RESTful API with rate limiting and authentication

### User Experience
- **Multilingual**: English and Arabic support
- **Responsive Design**: Mobile-first Tailwind CSS
- **PWA Support**: Progressive Web App capabilities
- **WhatsApp Integration**: Direct volunteer support
- **Static Pages**: FAQ, Privacy Policy, Terms of Service

## 🚀 Technology Stack

- **Framework**: Laravel 12.36
- **PHP**: 8.3.25
- **Database**: PostgreSQL 14.19
- **Frontend**: Blade + Alpine.js + Tailwind CSS
- **Build Tool**: Vite 7+
- **Authentication**: Laravel Sanctum + Spatie Permissions
- **Email**: Zoho Mail SMTP
- **PDF Generation**: DomPDF
- **QR Codes**: SimpleSoftwareIO QR Code

## 📋 Requirements

- PHP >= 8.2
- PostgreSQL >= 14.0
- Composer >= 2.8
- Node.js >= 18.x
- Nginx (recommended)

## 🛠️ Installation

```bash
# Clone the repository
git clone https://github.com/alsadi22/swaeduae_laravel.git
cd swaeduae_laravel

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=swaeduae_laravel
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations and seeders
php artisan migrate --seed

# Build frontend assets
npm run build

# Start the development server
php artisan serve
```

## 📊 Database Structure

- **31 Tables** managing users, events, applications, certificates, badges, and more
- **15 Eloquent Models** with relationships
- **6 User Roles**: Super Admin, Admin, Organization Manager, Organization Staff, Volunteer, Moderator

## 🔐 Default Credentials

**Admin**
- Email: admin@swaeduae.ae
- Password: See DEVELOPMENT-CREDENTIALS.md

**Organization**
- Email: org@swaeduae.ae
- Password: See DEVELOPMENT-CREDENTIALS.md

**Volunteer**
- Email: volunteer@swaeduae.ae
- Password: See DEVELOPMENT-CREDENTIALS.md

## 📖 Documentation

Comprehensive documentation is available in `.trae/rules/`:

- [Project Overview](/.trae/rules/SWAED-PROJECT-OVERVIEW.md)
- [Implementation Status](/.trae/rules/SWAED-IMPLEMENTATION-STATUS.md)
- [Features Summary](/.trae/rules/SWAED-FEATURES-SUMMARY.md)
- [Development Roadmap](/.trae/rules/SWAED-DEVELOPMENT-ROADMAP.md)
- [Missing Features](/.trae/rules/SWAED-MISSING-FEATURES.md)

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## 🔧 Configuration

### Email Setup (Zoho Mail)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.zoho.com
MAIL_PORT=587
MAIL_USERNAME=admin@swaeduae.ae
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=admin@swaeduae.ae
MAIL_FROM_NAME="SwaedUAE"
```

### Cache & Queue
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🌐 Production Deployment

1. Set environment to production in `.env`
2. Disable debug mode: `APP_DEBUG=false`
3. Configure proper database credentials
4. Set up SSL certificate
5. Configure Nginx/Apache
6. Run optimization commands
7. Set up queue workers
8. Configure automated backups

## 📈 Current Status

- ✅ **256 Routes** registered and functional
- ✅ **60 Controllers** implementing business logic
- ✅ **15 Models** with complete relationships
- ✅ **Email Verification** operational with Zoho Mail
- ✅ **Authentication System** fully functional
- ✅ **Role-Based Access Control** implemented
- ✅ **API Endpoints** with rate limiting
- ✅ **Production Environment** live at https://swaeduae.ae

## 🤝 Contributing

This is a private project for SwaedUAE. For internal contributions, please follow the development standards outlined in the project documentation.

## 📝 License

This project is proprietary software owned by SwaedUAE.

## 📧 Support

For support and inquiries:
- Email: admin@swaeduae.ae
- WhatsApp: +971 50 123 4567
- Website: https://swaeduae.ae

---

**Built with ❤️ for the UAE volunteer community**
