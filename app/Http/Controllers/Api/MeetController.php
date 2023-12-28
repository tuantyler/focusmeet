<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meet;
use App\Models\LogsMeetings;
use App\Models\RepeatPattern;
use App\Http\Requests\FocusedMeetCreationRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Exception;

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

    public function createFocusedMeet(FocusedMeetCreationRequest $request) {
        try {
            $meeting_id = Str::orderedUuid();
            $createMeetData = $request->validated();
            $createMeetData["meeting_id"] = (string)$meeting_id;
            $createMeetData["user_id"] = auth()->id();
    
            Meet::create($createMeetData);
    
            $repeatPatternInsertion = [
                'meeting_id' => $createMeetData['meeting_id']
            ];
    
            $repeatPatternsIndex = array_flip(config('constants.REPEAT_PATTERNS'));
            switch($createMeetData["repeat_pattern"]){
                case $repeatPatternsIndex["Does not repeat"]:
                    $repeatPatternInsertion['repeat_type'] = 'none';
                    break;
                case $repeatPatternsIndex["Daily"]:
                    $repeatPatternInsertion['repeat_type'] = 'daily';
                    break; 
                case $repeatPatternsIndex["Weekly On Wednesday"]:
                    $repeatPatternInsertion['repeat_type'] = 'weekly';
                    $repeatPatternInsertion['day_of_week'] = 'Wednesday';
                    break;
                 case $repeatPatternsIndex["Every weekday (Monday to Friday)"]:
                    $repeatPatternInsertion['repeat_type'] = 'weekdays';
                    break;
            }
    
            RepeatPattern::create($repeatPatternInsertion);
            return response()->json([
                'message' => 'Create meeting successfully',
                'meeting_id' => $createMeetData['meeting_id']
            ], Response::HTTP_CREATED);
        }
        catch(Exception $e){
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
