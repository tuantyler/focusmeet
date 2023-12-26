<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meet;
use App\Models\LogsMeetings;

class MeetController extends Controller
{
    public function updateName(Request $req) {
        $item = Meet::where("meeting_id", $req->meeting_id)->first();
        $this->authorize('userOwnedMeeting', $item);
        $item->update(['meeting_name' => $req->meeting_name]);
        
        return response()->json(['message' => 'Meeting name updated successfully']);
    }

    public function listParticipants($meeting_id) {
        $logsMeetings = LogsMeetings::where("meeting_id", $meeting_id)->with("user")->get();
        $result = $logsMeetings->map(function ($logsMeeting) {
            return [
                'user' => $logsMeeting->user,
                'created_at' => $logsMeeting->created_at,
            ];
        });

        return response()->json($result);
    }
}
