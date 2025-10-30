<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attendances = Attendance::with(['user', 'event'])->paginate(15);
        
        return view('admin.attendance.index', compact('attendances'));
    }

    /**
     * Display the specified attendance record.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        $attendance->load(['user', 'event', 'application', 'verifier']);
        
        return view('admin.attendance.show', compact('attendance'));
    }

    /**
     * Verify an attendance record.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function verify(Attendance $attendance)
    {
        $attendance->update([
            'verified_by_organizer' => true,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Attendance verified successfully!');
    }

    /**
     * Display attendance records for a specific event.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function eventAttendance(Event $event)
    {
        $attendances = $event->attendances()->with(['user'])->paginate(15);
        
        return view('admin.attendance.event', compact('attendances', 'event'));
    }
}