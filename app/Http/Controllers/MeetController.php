<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Meet;
use App\Models\LogsMeetings;

class MeetController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $folderPath = public_path('recognition/data/data_faces_from_camera/User_' . Auth::id());
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, $mode = 0777, true, true);
            }
            return $next($request);
        });
    }

    public function index() {
        return view("admin.meet.index");
    }

    public function accessMeeting() {
        $userName = "Test";
        return view('admin.meetrooms.index', compact('userName'));
    }

    public function attendanceTaker() {
        return view('admin.attendance_taker');
    }

    public function createMeet() {
        $meeting_id = Str::orderedUuid();
        Meet::create([
            "meeting_id" => $meeting_id,
            "meeting_name" => $meeting_id,  
            "user_id" => auth()->id()
        ]);

        LogsMeetings::firstOrCreate([
            "user_id" => auth()->id(),
            "meeting_id" => $meeting_id,
            "log_description" => "Meet created",
        ]);
        
        return redirect()->to(route('meet') . "?room=" . $meeting_id);
    }

    public function listMeet() {
        $meetings = Meet::where("user_id", auth()->id())->withCount('meetings_log')->get();

        return view("admin.meet.list", compact("meetings"));
    }

    public function updateMeetingName(Request $request) {
        dd($request->all());
    }

    public function scheduleMeet() {
        return view("admin.meet.schedule");
    }
}
