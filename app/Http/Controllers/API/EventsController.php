<?php

namespace App\Http\Controllers\API;


use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Section;
use Auth;
use Illuminate\Http\Request;
use Log;
use Validator;
use Carbon\Carbon;

class EventsController extends Controller
{
    //Apply middleware
    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth");
    }

    /**
     * Returns a list of existing events by given section.
     * @param Request $request
     * @return string
     */
    public function getEventsBySection(Request $request) {
    	$section = Section::where('id',$request->section_id)->first();
        if($section == null || GeneralController::hasPermissions($section,1) == false) {
            return "invalid_permissions";
        }
        return $section->events()->get();
    }

    public function getEventsByUser(Request $request) {
    	// Get all sections
    	$sections = Auth::user()->sections;
    	$allevents = array();

    	// Iterate through all sections
    	foreach($sections as $section) {
    		// Get all events in this section
    		$events = Event::where('section_id', '=', $section->id)->get();
    		// Iterate through all events
    		foreach($events as $event) {
    			// Add event to collection array
    			$allevents[] = $event->toArray();
    		}
    	}
    	return $allevents;
    }

    public function createEvent(Request $request) {
    	$validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'begin' => 'required|integer',
            'end' => 'required|integer',
            'section_id' => 'required|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        if($request->end < $request->begin) {
        	return "invalid time";
        }
		$section = Section::where('id',$request->section_id)->first();
        if(GeneralController::hasPermissions($section,2) == false) {
            return "invalid_permissions";
        }

        $event = new Event;
        $event->title = $request->title;
        $event->description = $request->description;
        $event->begin = Carbon::createFromTimestamp($request->begin, 'America/New_York')->toDateTimeString();  
        $event->end = Carbon::createFromTimestamp($request->end, 'America/New_York')->toDateTimeString();  
        $event->section_id = $request->section_id;
        $event->user_id = Auth::user()->id;
        $event->save();
        return "success";
    }
    public function editEvent(Request $request) {
    	$validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'begin' => 'required|integer',
            'end' => 'required|integer',
            'section_id' => 'required|exists:sections,id',
            'event_id' => 'required|exists:events,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        if($request->end < $request->begin) {
        	return "invalid time";
        }
        
		$event = Event::where('id', '=', $request->event_id)->where('section_id', '=', $request->section_id);
        if($event->count()) {
        	$event = $event->first();
        }
        else {
        	return "invalid permissions to modify event";
        }
        $section = Section::where('id',$request->section_id)->first();
        if(GeneralController::hasPermissions($section,2) == false) {
            return "invalid_permissions";
        }
        $event->title = $request->title;
        $event->description = $request->description;
        $event->begin = Carbon::createFromTimestamp($request->begin, 'America/New_York')->toDateTimeString();
        $event->end = Carbon::createFromTimestamp($request->end, 'America/New_York')->toDateTimeString();
        $event->user_id = Auth::user()->id;
        $event->save();
        return "success";
    }

    public function deleteEvent(Request $request) {
		$validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'event_id' => 'required|exists:events,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $event = Event::where('id', '=', $request->event_id)->where('section_id', '=', $request->section_id);
        if($event->count()) {
        	$event = $event->first();
        }
        else {
        	return "invalid permissions to modify event";
        }
        $section = Section::where('id',$request->section_id)->first();
        if(GeneralController::hasPermissions($section,2) == false) {
            return "invalid_permissions";
        }

        $event = Event::where('id', '=', $request->event_id)->first();
        $event->delete();
        return "success";
    }
}
