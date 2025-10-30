<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Page;

class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // About Us Page
        Page::updateOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'About Us',
                'content' => '<p>Welcome to SwaedUAE, the premier volunteer management platform in the United Arab Emirates. Our mission is to connect passionate volunteers with meaningful opportunities that make a positive impact in our communities.</p>
                
                <h2>Our Vision</h2>
                <p>We envision a UAE where every individual has the opportunity to contribute to society through meaningful volunteer work, creating stronger, more connected communities across all emirates.</p>
                
                <h2>Our Mission</h2>
                <p>Our mission is to empower volunteers and organizations by providing a comprehensive platform that facilitates meaningful connections, tracks impact, and celebrates contributions to society.</p>
                
                <h2>Core Values</h2>
                <ul>
                    <li><strong>Community:</strong> Building stronger connections between people and organizations</li>
                    <li><strong>Integrity:</strong> Maintaining transparency and honesty in all our operations</li>
                    <li><strong>Inclusivity:</strong> Welcoming volunteers from all backgrounds and abilities</li>
                    <li><strong>Impact:</strong> Measuring and celebrating the positive change we create</li>
                    <li><strong>Innovation:</strong> Continuously improving our platform and services</li>
                </ul>',
                'meta_title' => 'About SwaedUAE - Volunteer Management Platform UAE',
                'meta_description' => 'Learn about SwaedUAE, the leading volunteer management platform in the UAE that connects volunteers with meaningful opportunities across all emirates.',
                'meta_keywords' => 'volunteer, uae, community service, volunteer management, swaeduae',
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        // Contact Us Page
        Page::updateOrCreate(
            ['slug' => 'contact-us'],
            [
                'title' => 'Contact Us',
                'content' => '<h2>Get In Touch</h2>
                <p>We\'d love to hear from you! Whether you have questions about volunteering, need help with your account, or want to partner with us as an organization, our team is here to assist you.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="text-center">
                        <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-envelope text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Email Us</h3>
                        <p class="text-gray-600">support@swaeduae.ae</p>
                        <p class="text-gray-600">partnerships@swaeduae.ae</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-phone text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Call Us</h3>
                        <p class="text-gray-600">+971 4 123 4567</p>
                        <p class="text-gray-600">Mon-Fri, 9AM-5PM</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-map-marker-alt text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Visit Us</h3>
                        <p class="text-gray-600">Dubai, UAE</p>
                        <p class="text-gray-600">Office 123, Business Bay</p>
                    </div>
                </div>
                
                <h2 class="mt-12">Frequently Asked Questions</h2>
                <p>Before contacting us, you might find answers to your questions in our <a href="#" class="text-red-600 hover:underline">FAQ section</a>.</p>',
                'meta_title' => 'Contact SwaedUAE - Volunteer Management Platform UAE',
                'meta_description' => 'Contact SwaedUAE for support with volunteering, organization partnerships, or general inquiries about our volunteer management platform in the UAE.',
                'meta_keywords' => 'contact, volunteer, uae, support, swaeduae',
                'is_published' => true,
                'sort_order' => 2,
            ]
        );

        // Privacy Policy Page
        Page::updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Privacy Policy',
                'content' => '<h2>Introduction</h2>
                <p>SwaedUAE ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website and use our services.</p>
                
                <h2>Information We Collect</h2>
                <p>We collect information that you provide directly to us, including:</p>
                <ul>
                    <li>Personal identification information (name, email address, phone number)</li>
                    <li>Demographic information (age, gender, nationality)</li>
                    <li>Professional information (skills, interests, availability)</li>
                    <li>Emergency contact information</li>
                    <li>Payment information (if applicable)</li>
                </ul>
                
                <h2>How We Use Your Information</h2>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Provide, maintain, and improve our services</li>
                    <li>Process volunteer applications and event registrations</li>
                    <li>Communicate with you about events and opportunities</li>
                    <li>Send you administrative information</li>
                    <li>Protect the security and integrity of our platform</li>
                </ul>
                
                <h2>Information Sharing</h2>
                <p>We may share your information with:</p>
                <ul>
                    <li>Organizations you volunteer with</li>
                    <li>Service providers who assist us in operating our platform</li>
                    <li>Legal authorities when required by law</li>
                </ul>
                
                <h2>Your Rights</h2>
                <p>You have the right to:</p>
                <ul>
                    <li>Access and update your personal information</li>
                    <li>Delete your account and personal data</li>
                    <li>Object to the processing of your personal data</li>
                    <li>Request data portability</li>
                </ul>
                
                <h2>Contact Us</h2>
                <p>If you have any questions about this Privacy Policy, please contact us at privacy@swaeduae.ae.</p>',
                'meta_title' => 'Privacy Policy - SwaedUAE Volunteer Platform',
                'meta_description' => 'Read our privacy policy to understand how SwaedUAE collects, uses, and protects your personal information when using our volunteer management platform.',
                'meta_keywords' => 'privacy policy, data protection, volunteer, uae, swaeduae',
                'is_published' => true,
                'sort_order' => 3,
            ]
        );

        // Terms of Service Page
        Page::updateOrCreate(
            ['slug' => 'terms-of-service'],
            [
                'title' => 'Terms of Service',
                'content' => '<h2>Acceptance of Terms</h2>
                <p>By accessing or using the SwaedUAE platform, you agree to be bound by these Terms of Service and all applicable laws and regulations.</p>
                
                <h2>Use of Service</h2>
                <p>You agree to use our services only for lawful purposes and in accordance with these Terms of Service. You must not:</p>
                <ul>
                    <li>Violate any applicable laws or regulations</li>
                    <li>Infringe upon the rights of others</li>
                    <li>Transmit any harmful or malicious content</li>
                    <li>Attempt to gain unauthorized access to our systems</li>
                </ul>
                
                <h2>User Accounts</h2>
                <p>When you create an account, you are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.</p>
                
                <h2>Volunteer Responsibilities</h2>
                <p>As a volunteer, you agree to:</p>
                <ul>
                    <li>Provide accurate and complete information</li>
                    <li>Follow the instructions of event organizers</li>
                    <li>Respect the rights and dignity of all participants</li>
                    <li>Comply with all applicable laws and regulations</li>
                </ul>
                
                <h2>Organization Responsibilities</h2>
                <p>Organizations using our platform agree to:</p>
                <ul>
                    <li>Provide accurate information about volunteer opportunities</li>
                    <li>Treat all volunteers with respect and fairness</li>
                    <li>Ensure the safety and well-being of volunteers</li>
                    <li>Comply with all applicable laws and regulations</li>
                </ul>
                
                <h2>Limitation of Liability</h2>
                <p>SwaedUAE shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of our services.</p>
                
                <h2>Changes to Terms</h2>
                <p>We reserve the right to modify these terms at any time. We will notify you of any changes by posting the new Terms of Service on this page.</p>',
                'meta_title' => 'Terms of Service - SwaedUAE Volunteer Platform',
                'meta_description' => 'Read our terms of service to understand your rights and responsibilities when using the SwaedUAE volunteer management platform in the UAE.',
                'meta_keywords' => 'terms of service, volunteer, uae, swaeduae, agreement',
                'is_published' => true,
                'sort_order' => 4,
            ]
        );
    }
}