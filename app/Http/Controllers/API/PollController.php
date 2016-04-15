<?php

namespace App\Http\Controllers\API;

use App\Models\Poll;
use App\Models\Section;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class PollController extends Controller
{
    public function __construct() {
        //Require JWT token for all functions
        $this->middleware("jwt.auth"); //not sure if other params needed
    }

    public function createPoll(Request $request) {
        $validator = Validator::make($request->all(), [
            'answer' => 'required|integer' ,
            'section_id' => 'required|exists:sections,id',
            'description' => 'required'
        ]);
        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Check user auth level
        $section = Section::where('id',$request->section_id)->first();
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }
        //Create new poll
        $poll = new Poll;
        $poll->answer = $request->answer;
        $poll->status = 0;
        $poll->description = $request->description;
        $poll->section_id = $section->id;
        $poll->save();
        return "success";
    }

    public function updatePoll(Request $request) {
        $validator = Validator::make($request->all(), [
            'poll_id' => 'required|exists:polls,id',
            'answer' => 'required|integer' ,
            'description' => 'required',
            'status' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Find poll
        $poll = Poll::find($request->poll_id);
        if($poll == null)
            return "invalid_poll";

        //Check user auth level
        $section = $poll->section;
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }

        $poll->answer = $request->answer;
        $poll->status = 0;
        $poll->description = $request->description;
        $poll->section_id = $section->id;
        $poll->status = $request->status;
        $poll->save();

        return "success";
    }

    public function deletePoll(Request $request) {
        $validator = Validator::make($request->all(), [
            'poll_id' => 'required|exists:polls,id',
        ]);
        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $poll = Poll::find($request->poll_id);
        if($poll == null)
            return "invalid_poll";

        //Check user auth level
        $section = $poll->section;
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }

        //Delete
        $poll->delete();
        return "success";
    }

}
