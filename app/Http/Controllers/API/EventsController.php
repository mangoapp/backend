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
}
