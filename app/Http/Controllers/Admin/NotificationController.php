<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display the notification settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::count();
        $organizations = Organization::count();
        
        return view('admin.notifications.index', compact('users', 'organizations'));
    }

    /**
     * Send a broadcast notification to all users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function broadcast(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient_type' => 'required|in:all,users,organizations',
        ]);

        // In a real implementation, you would send the notification here
        // For now, we'll just return a success message
        
        return redirect()->back()->with('success', 'Broadcast notification sent successfully!');
    }

    /**
     * Display the notification templates page.
     *
     * @return \Illuminate\Http\Response
     */
    public function templates()
    {
        // In a real implementation, you would fetch templates from the database
        $templates = [
            [
                'id' => 1,
                'name' => 'Application Approved',
                'type' => 'email',
                'subject' => 'Your application has been approved',
            ],
            [
                'id' => 2,
                'name' => 'Event Reminder',
                'type' => 'email',
                'subject' => 'Reminder: Upcoming event',
            ],
            [
                'id' => 3,
                'name' => 'Certificate Issued',
                'type' => 'email',
                'subject' => 'Congratulations! Certificate issued',
            ],
        ];
        
        return view('admin.notifications.templates', compact('templates'));
    }

    /**
     * Display the notification logs page.
     *
     * @return \Illuminate\Http\Response
     */
    public function logs()
    {
        // In a real implementation, you would fetch logs from the database
        $logs = [
            [
                'id' => 1,
                'type' => 'email',
                'recipient' => 'user@example.com',
                'subject' => 'Application Approved',
                'status' => 'sent',
                'created_at' => '2023-01-01 10:00:00',
            ],
            [
                'id' => 2,
                'type' => 'database',
                'recipient' => 'user@example.com',
                'subject' => 'Event Reminder',
                'status' => 'failed',
                'created_at' => '2023-01-01 09:00:00',
            ],
        ];
        
        return view('admin.notifications.logs', compact('logs'));
    }
    
    /**
     * Display the leaderboard page.
     *
     * @return \Illuminate\Http\Response
     */
    public function leaderboard()
    {
        return view('admin.leaderboard.index');
    }
}