<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // Get all settings grouped by category
        $generalSettings = Setting::group('general');
        $emailSettings = Setting::group('email');
        $apiSettings = Setting::group('api');
        $socialSettings = Setting::group('social');
        $appearanceSettings = Setting::group('appearance');
        
        return view('admin.settings.index', compact(
            'generalSettings',
            'emailSettings',
            'apiSettings',
            'socialSettings',
            'appearanceSettings'
        ));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        // Validate the request
        $request->validate([
            // General settings
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'site_keywords' => 'nullable|string|max:500',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'site_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
            'maintenance_mode' => 'nullable|boolean',
            
            // Email settings
            'mail_driver' => 'nullable|string|max:50',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|max:50',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
            
            // API settings
            'api_base_url' => 'nullable|url|max:255',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'google_maps_api_key' => 'nullable|string|max:255',
            'firebase_api_key' => 'nullable|string|max:255',
            
            // Social media settings
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            
            // Appearance settings
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'site_language' => 'nullable|string|max:10',
        ]);

        try {
            // Handle file uploads
            if ($request->hasFile('site_logo')) {
                $logoPath = $request->file('site_logo')->store('logos', 'public');
                Setting::set('site_logo', '/storage/' . $logoPath, 'string', 'general');
            }
            
            if ($request->hasFile('site_favicon')) {
                $faviconPath = $request->file('site_favicon')->store('favicons', 'public');
                Setting::set('site_favicon', '/storage/' . $faviconPath, 'string', 'general');
            }
            
            // Update general settings
            if ($request->has('site_name')) {
                Setting::set('site_name', $request->site_name, 'string', 'general');
            }
            
            if ($request->has('site_description')) {
                Setting::set('site_description', $request->site_description, 'string', 'general');
            }
            
            if ($request->has('site_keywords')) {
                Setting::set('site_keywords', $request->site_keywords, 'string', 'general');
            }
            
            if ($request->has('maintenance_mode')) {
                Setting::set('maintenance_mode', $request->maintenance_mode, 'boolean', 'general');
            }
            
            // Update email settings
            if ($request->has('mail_driver')) {
                Setting::set('mail_driver', $request->mail_driver, 'string', 'email');
            }
            
            if ($request->has('mail_host')) {
                Setting::set('mail_host', $request->mail_host, 'string', 'email');
            }
            
            if ($request->has('mail_port')) {
                Setting::set('mail_port', $request->mail_port, 'integer', 'email');
            }
            
            if ($request->has('mail_username')) {
                Setting::set('mail_username', $request->mail_username, 'string', 'email');
            }
            
            if ($request->has('mail_password') && !empty($request->mail_password)) {
                Setting::set('mail_password', $request->mail_password, 'string', 'email');
            }
            
            if ($request->has('mail_encryption')) {
                Setting::set('mail_encryption', $request->mail_encryption, 'string', 'email');
            }
            
            if ($request->has('mail_from_address')) {
                Setting::set('mail_from_address', $request->mail_from_address, 'string', 'email');
            }
            
            if ($request->has('mail_from_name')) {
                Setting::set('mail_from_name', $request->mail_from_name, 'string', 'email');
            }
            
            // Update API settings
            if ($request->has('api_base_url')) {
                Setting::set('api_base_url', $request->api_base_url, 'string', 'api');
            }
            
            if ($request->has('api_key') && !empty($request->api_key)) {
                Setting::set('api_key', $request->api_key, 'string', 'api');
            }
            
            if ($request->has('api_secret') && !empty($request->api_secret)) {
                Setting::set('api_secret', $request->api_secret, 'string', 'api');
            }
            
            if ($request->has('google_maps_api_key') && !empty($request->google_maps_api_key)) {
                Setting::set('google_maps_api_key', $request->google_maps_api_key, 'string', 'api');
            }
            
            if ($request->has('firebase_api_key') && !empty($request->firebase_api_key)) {
                Setting::set('firebase_api_key', $request->firebase_api_key, 'string', 'api');
            }
            
            // Update social media settings
            if ($request->has('facebook_url')) {
                Setting::set('facebook_url', $request->facebook_url, 'string', 'social');
            }
            
            if ($request->has('twitter_url')) {
                Setting::set('twitter_url', $request->twitter_url, 'string', 'social');
            }
            
            if ($request->has('instagram_url')) {
                Setting::set('instagram_url', $request->instagram_url, 'string', 'social');
            }
            
            if ($request->has('linkedin_url')) {
                Setting::set('linkedin_url', $request->linkedin_url, 'string', 'social');
            }
            
            // Update appearance settings
            if ($request->has('primary_color')) {
                Setting::set('primary_color', $request->primary_color, 'string', 'appearance');
            }
            
            if ($request->has('secondary_color')) {
                Setting::set('secondary_color', $request->secondary_color, 'string', 'appearance');
            }
            
            if ($request->has('site_language')) {
                Setting::set('site_language', $request->site_language, 'string', 'appearance');
            }
            
            return redirect()->back()->with('success', 'Settings updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }
}