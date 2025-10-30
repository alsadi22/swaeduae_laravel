<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use Illuminate\Support\Str;

class StaticPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h2>Privacy Policy</h2>
                <p>Last updated: ' . date('F d, Y') . '</p>
                
                <h3>1. Information We Collect</h3>
                <p>We collect information that you provide directly to us when you register for an account, create or modify your profile, participate in volunteer activities, or communicate with us.</p>
                
                <h3>2. How We Use Your Information</h3>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Provide, maintain, and improve our services</li>
                    <li>Connect volunteers with organizations</li>
                    <li>Send you technical notices, updates, security alerts, and support messages</li>
                    <li>Respond to your comments, questions, and provide customer service</li>
                </ul>
                
                <h3>3. Information Sharing</h3>
                <p>We do not share your personal information with third parties except as described in this policy or with your consent.</p>
                
                <h3>4. Data Security</h3>
                <p>We implement appropriate technical and organizational measures to protect your personal data.</p>
                
                <h3>5. Contact Us</h3>
                <p>If you have any questions about this Privacy Policy, please contact us at privacy@swaeduae.ae</p>',
                'meta_title' => 'Privacy Policy - SwaedUAE',
                'meta_description' => 'Learn about how SwaedUAE collects, uses, and protects your personal information.',
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => '<h2>Terms of Service</h2>
                <p>Last updated: ' . date('F d, Y') . '</p>
                
                <h3>1. Acceptance of Terms</h3>
                <p>By accessing and using SwaedUAE, you accept and agree to be bound by the terms and provision of this agreement.</p>
                
                <h3>2. User Accounts</h3>
                <p>You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.</p>
                
                <h3>3. Volunteer Activities</h3>
                <p>Users agree to participate in volunteer activities in good faith and to fulfill commitments made to organizations.</p>
                
                <h3>4. Code of Conduct</h3>
                <p>All users must:</p>
                <ul>
                    <li>Treat others with respect and professionalism</li>
                    <li>Provide accurate and truthful information</li>
                    <li>Comply with all applicable laws and regulations</li>
                    <li>Respect the rights and property of organizations and other volunteers</li>
                </ul>
                
                <h3>5. Termination</h3>
                <p>We reserve the right to terminate or suspend access to our service immediately, without prior notice, for any reason.</p>
                
                <h3>6. Contact</h3>
                <p>For questions about these Terms, contact us at legal@swaeduae.ae</p>',
                'meta_title' => 'Terms of Service - SwaedUAE',
                'meta_description' => 'Read the terms and conditions for using SwaedUAE volunteer platform.',
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Cookie Policy',
                'slug' => 'cookie-policy',
                'content' => '<h2>Cookie Policy</h2>
                <p>Last updated: ' . date('F d, Y') . '</p>
                
                <h3>What Are Cookies?</h3>
                <p>Cookies are small pieces of text sent to your web browser by a website you visit. They help that website remember information about your visit.</p>
                
                <h3>How We Use Cookies</h3>
                <p>We use cookies to:</p>
                <ul>
                    <li>Keep you signed in</li>
                    <li>Understand how you use our website</li>
                    <li>Remember your preferences</li>
                    <li>Improve your experience</li>
                </ul>
                
                <h3>Types of Cookies We Use</h3>
                <ul>
                    <li><strong>Essential Cookies:</strong> Required for the website to function properly</li>
                    <li><strong>Functional Cookies:</strong> Remember your preferences and choices</li>
                    <li><strong>Analytics Cookies:</strong> Help us understand how visitors use our website</li>
                </ul>
                
                <h3>Managing Cookies</h3>
                <p>You can control and/or delete cookies as you wish. You can delete all cookies that are already on your computer and you can set most browsers to prevent them from being placed.</p>
                
                <h3>Contact Us</h3>
                <p>For questions about our use of cookies, contact privacy@swaeduae.ae</p>',
                'meta_title' => 'Cookie Policy - SwaedUAE',
                'meta_description' => 'Learn about how SwaedUAE uses cookies to improve your experience.',
                'is_published' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'FAQ - Frequently Asked Questions',
                'slug' => 'faq',
                'content' => '<h2>Frequently Asked Questions</h2>
                
                <h3>General Questions</h3>
                
                <h4>What is SwaedUAE?</h4>
                <p>SwaedUAE is a volunteer management platform that connects volunteers with organizations across the UAE, making it easy to find, join, and track volunteer opportunities.</p>
                
                <h4>Is SwaedUAE free to use?</h4>
                <p>Yes! SwaedUAE is completely free for both volunteers and organizations.</p>
                
                <h3>For Volunteers</h3>
                
                <h4>How do I sign up as a volunteer?</h4>
                <p>Click the "Volunteer Sign Up" button on the homepage and fill in your details. Once registered, you can browse and apply for volunteer opportunities immediately.</p>
                
                <h4>How do I find volunteer opportunities?</h4>
                <p>Browse available opportunities on the Events page or through your dashboard after logging in. You can filter by location, date, and interest area.</p>
                
                <h4>Can I earn certificates?</h4>
                <p>Yes! After completing volunteer activities, organizations can issue certificates that you can download and share.</p>
                
                <h3>For Organizations</h3>
                
                <h4>How can my organization join?</h4>
                <p>Click "Organization Sign Up" and submit your organization details. Our team will review and approve your registration.</p>
                
                <h4>How do I post volunteer opportunities?</h4>
                <p>Once your organization is approved, you can create and manage volunteer events through your organization dashboard.</p>
                
                <h4>How do I track volunteer attendance?</h4>
                <p>Our platform provides QR code-based check-in/check-out system and real-time attendance tracking for all your events.</p>
                
                <h3>Need More Help?</h3>
                <p>If you couldn\'t find the answer to your question, please contact us at support@swaeduae.ae</p>',
                'meta_title' => 'FAQ - Frequently Asked Questions | SwaedUAE',
                'meta_description' => 'Find answers to common questions about volunteering and using the SwaedUAE platform.',
                'is_published' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Volunteer Guide',
                'slug' => 'volunteer-guide',
                'content' => '<h2>Volunteer Guide</h2>
                <p>Welcome to the SwaedUAE Volunteer Guide! This guide will help you make the most of your volunteering experience.</p>
                
                <h3>Getting Started</h3>
                <ol>
                    <li><strong>Create Your Profile:</strong> Sign up and complete your volunteer profile with your skills, interests, and availability.</li>
                    <li><strong>Browse Opportunities:</strong> Explore available volunteer opportunities that match your interests.</li>
                    <li><strong>Apply:</strong> Submit applications for events you\'re interested in.</li>
                    <li><strong>Get Confirmed:</strong> Wait for organization approval.</li>
                    <li><strong>Participate:</strong> Attend the event and make a difference!</li>
                </ol>
                
                <h3>Best Practices</h3>
                <ul>
                    <li>Complete your profile thoroughly to help organizations find you</li>
                    <li>Only apply for events you can commit to attending</li>
                    <li>Arrive on time and be prepared</li>
                    <li>Follow organization guidelines and instructions</li>
                    <li>Communicate any issues or conflicts promptly</li>
                </ul>
                
                <h3>Earning Badges and Certificates</h3>
                <p>As you volunteer, you\'ll earn badges for milestones like hours completed, events attended, and special achievements. Organizations will also issue certificates for your contributions.</p>
                
                <h3>Tracking Your Impact</h3>
                <p>Use your dashboard to:</p>
                <ul>
                    <li>View your volunteer hours</li>
                    <li>See badges you\'ve earned</li>
                    <li>Download certificates</li>
                    <li>Track your applications</li>
                </ul>
                
                <h3>Need Help?</h3>
                <p>Contact us at volunteer@swaeduae.ae for assistance.</p>',
                'meta_title' => 'Volunteer Guide - How to Get Started | SwaedUAE',
                'meta_description' => 'Learn how to become an effective volunteer and make the most of the SwaedUAE platform.',
                'is_published' => true,
                'sort_order' => 5,
            ],
            [
                'title' => 'Organization Resources',
                'slug' => 'organization-resources',
                'content' => '<h2>Organization Resources</h2>
                <p>Everything you need to successfully manage volunteers through SwaedUAE.</p>
                
                <h3>Getting Started</h3>
                <ol>
                    <li><strong>Register:</strong> Submit your organization details for approval</li>
                    <li><strong>Complete Profile:</strong> Add your organization information, logo, and description</li>
                    <li><strong>Create Events:</strong> Post volunteer opportunities</li>
                    <li><strong>Manage Applications:</strong> Review and approve volunteer applications</li>
                    <li><strong>Track Attendance:</strong> Use our QR code system for check-in/check-out</li>
                </ol>
                
                <h3>Features Available</h3>
                <ul>
                    <li><strong>Event Management:</strong> Create and manage volunteer opportunities</li>
                    <li><strong>Attendance Tracking:</strong> Real-time attendance monitoring with QR codes</li>
                    <li><strong>Certificate Generation:</strong> Issue certificates to volunteers</li>
                    <li><strong>Communication Tools:</strong> Send announcements and messages to volunteers</li>
                    <li><strong>Reporting:</strong> Access analytics and volunteer statistics</li>
                </ul>
                
                <h3>Best Practices</h3>
                <ul>
                    <li>Provide clear event descriptions and requirements</li>
                    <li>Respond to volunteer applications promptly</li>
                    <li>Communicate schedule changes immediately</li>
                    <li>Recognize and appreciate your volunteers</li>
                    <li>Issue certificates for completed activities</li>
                </ul>
                
                <h3>Support</h3>
                <p>For organization support, email organizations@swaeduae.ae or call +971 4 123 4567</p>',
                'meta_title' => 'Organization Resources - Manage Volunteers | SwaedUAE',
                'meta_description' => 'Resources and guides for organizations to effectively manage volunteers on SwaedUAE.',
                'is_published' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }

        $this->command->info('Static pages created successfully!');
    }
}
