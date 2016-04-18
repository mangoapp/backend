<?php

namespace App\Http\Controllers\API;

use App\Models\Poll;
use App\Models\PollResponse;
use App\Models\Section;
use Auth;
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

    /**
     * Submits an answer for a poll
     */
    public function submitResponse(Request $request) {
        $validator = Validator::make($request->all(), [
            'answer' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Find poll
        $poll = Poll::find($request->poll_id);
        if($poll == null)
            return "invalid_poll";

        //Check permissions
        $section = $poll->section;
        if(GeneralController::userHasPermissions(Auth::user(),$section,1) == false) {
            return "invalid_permissions";
        }

        //Check that poll is open
        if($poll->status != 1)
            return "poll_closed";

        //Check if response exists
        $response = PollResponse::where('user_id',Auth::user()->id)->where('poll_id',$poll->id)->first();
        if($response == null) {
            //Create new response
            $response = new PollResponse;
            $response->poll_id = $poll->id;
            $response->user_id = Auth::user()->id;
            $response->answer = $request->answer;
            $response->save();
            return "success";
        } else {
            //Update existing response
            $response->answer = $request->answer;
            $response->save();
            return "success";
        }

    }

    /**
     * Opens a poll for student responses
     * @param Request $request
     * @return array|string
     */
    public function openPoll(Request $request) {
        //Find poll
        $poll = Poll::find($request->poll_id);
        if($poll == null)
            return "invalid_poll";

        //Check permissions
        $section = $poll->section;
        if(GeneralController::userHasPermissions(Auth::user(),$section,2) == false) {
            return "invalid_permissions";
        }
        $poll->status = 1;
        $poll->save();
        return "success";

    }


    /**
     * Opens a poll for student responses
     * @param Request $request
     * @return array|string
     */
    public function closePoll(Request $request) {
        //Find poll
        $poll = Poll::find($request->poll_id);
        if($poll == null)
            return "invalid_poll";

        //Check permissions
        $section = $poll->section;
        if(GeneralController::userHasPermissions(Auth::user(),$section,2) == false) {
            return "invalid_permissions";
        }
        $poll->status = 2;
        $poll->save();
        return "success";
    }

    /**
     * Gets all polls, even inactive ones
     * @param Request $request
     * @return string
     */
    public function getAllPolls(Request $request) {
        //Check permissions
        $section = Section::find($request->section_id);
        if(GeneralController::hasPermissions($section,1) == false) {
            return "invalid_permissions";
        }

        return $this->attachPollStats($section->polls);
    }

    /**
     * Gets all polls, even inactive ones
     * @param Request $request
     * @return string
     */
    public function getActivePolls(Request $request) {
        //Check permissions
        $section = Section::find($request->section_id);
        if(GeneralController::hasPermissions($section,1) == false) {
            return "invalid_permissions";
        }

        $pollResponses = $section->polls()->where('status',1)->get();
        return $this->attachPollStats($pollResponses);
    }

    /**
     * Gets all responses to a poll
     * @param Request $request
     * @return string
     */
    public function getPollResponses(Request $request) {
        //Find poll
        $poll = Poll::find($request->poll_id);
        if($poll == null)
            return "invalid_poll";

        //Check permissions
        $section = $poll->section;
        if(GeneralController::userHasPermissions(Auth::user(),$section,2) == false) {
            return "invalid_permissions";
        }

        return $poll->responses;
    }

    public function attachPollStats($polls) {
        foreach($polls as $poll) {
            $poll->total_responses = sizeof($poll->responses());
            $poll->responses_A = sizeof($poll->responses()->where('answer', '1')->get());
            $poll->responses_B = sizeof($poll->responses()->where('answer', '2')->get());
            $poll->responses_C = sizeof($poll->responses()->where('answer', '3')->get());
            $poll->responses_D = sizeof($poll->responses()->where('answer', '4')->get());
            $poll->responses_E = sizeof($poll->responses()->where('answer', '5')->get());
            $poll->responses_E = sizeof($poll->responses()->where('answer', '5')->get());
            $responses = $poll->responses()->get();
            $userArray = array();
            foreach($responses as $response) {
                array_push($userArray,$response->user);
            }
            $poll->users = $userArray;
        }
        return $polls;
    }
}
