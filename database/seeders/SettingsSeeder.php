<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General settings
        Setting::set('site_name', 'SwaedUAE', 'string', 'general', 'Website name');
        Setting::set('site_description', 'Volunteer Management Platform for UAE', 'string', 'general', 'Website description');
        Setting::set('site_keywords', 'volunteer, uae, community, service', 'string', 'general', 'SEO keywords');
        Setting::set('site_logo', '/images/logo.png', 'string', 'general', 'Website logo path');
        Setting::set('site_favicon', '/images/favicon.ico', 'string', 'general', 'Website favicon path');
        Setting::set('maintenance_mode', '0', 'boolean', 'general', 'Maintenance mode status');
        
        // Email settings
        Setting::set('mail_driver', 'smtp', 'string', 'email', 'Mail driver');
        Setting::set('mail_host', 'smtp.zoho.com', 'string', 'email', 'Mail host');
        Setting::set('mail_port', '587', 'integer', 'email', 'Mail port');
        Setting::set('mail_username', 'admin@swaeduae.ae', 'string', 'email', 'Mail username');
        Setting::set('mail_password', '', 'string', 'email', 'Mail password (encrypted)');
        Setting::set('mail_encryption', 'tls', 'string', 'email', 'Mail encryption');
        Setting::set('mail_from_address', 'admin@swaeduae.ae', 'string', 'email', 'Mail from address');
        Setting::set('mail_from_name', 'SwaedUAE', 'string', 'email', 'Mail from name');
        
        // API settings
        Setting::set('api_base_url', 'https://api.swaeduae.ae', 'string', 'api', 'API base URL');
        Setting::set('api_key', '', 'string', 'api', 'API key');
        Setting::set('api_secret', '', 'string', 'api', 'API secret');
        Setting::set('google_maps_api_key', '', 'string', 'api', 'Google Maps API key');
        Setting::set('firebase_api_key', '', 'string', 'api', 'Firebase API key');
        
        // Social media settings
        Setting::set('facebook_url', 'https://facebook.com/swaeduae', 'string', 'social', 'Facebook URL');
        Setting::set('twitter_url', 'https://twitter.com/swaeduae', 'string', 'social', 'Twitter URL');
        Setting::set('instagram_url', 'https://instagram.com/swaeduae', 'string', 'social', 'Instagram URL');
        Setting::set('linkedin_url', 'https://linkedin.com/company/swaeduae', 'string', 'social', 'LinkedIn URL');
        
        // Appearance settings
        Setting::set('primary_color', '#dc2626', 'string', 'appearance', 'Primary color');
        Setting::set('secondary_color', '#2563eb', 'string', 'appearance', 'Secondary color');
        Setting::set('site_language', 'en', 'string', 'appearance', 'Default language');
    }
}