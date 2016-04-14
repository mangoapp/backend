<?php

namespace App\Http\Controllers\API;

use App\Models\Assignment;
use App\Models\AssignmentFileUpload;
use App\Models\FileUpload;
use App\Models\Section;
use App\Models\User;
use Auth;
use DateTime;
use File;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Response;
use Storage;
use Validator;

class FileController extends Controller
{
    //Apply middleware
    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth");
    }

    /**
     * Uploads a file to the server
     * @param Request $request
     * @return string
     */
    public function submitFile(Request $request) {
        $assignment = Assignment::findOrFail($request->assignment_id);
        $section = $assignment->section;
        if(GeneralController::hasPermissions($section, 1) == false) {
            return "invalid permissions"; //User is not in section
        }

        //Check assignment allows files
        if(!$assignment->filesubmission) {
            return "file submissions not allowed";
        }

        //Check Assignment Deadline
        if($assignment->deadline != null) {
            if(new DateTime() > $assignment->deadline) {
                return "deadline_passed";
            }
        }


        $fileToUpload = $request->file('file');
        //Check file type
        $fileType = File::extension($fileToUpload->getClientOriginalName());
        if($fileType != 'pdf') {
            return "invalid_filetype";
        }

        if($fileToUpload == null)
            return "invalid_file";
        $ret = $this->attachAssignmentFile($fileToUpload,$assignment,Auth::user());
        return $ret ? "success" : "file_upload_failed";
    }

    /**
     * Serves the given file
     * @param Request $request
     * @return \Illuminate\Http\Response|string
     */
    public function downloadFile(Request $request) {
        $fileUpload = AssignmentFileUpload::where('id',$request->file_id)->first();

        //Ensure that file exists
        if($fileUpload == null)
            return "invalid_file";

        //Check for TA permissions
        $section = $fileUpload->assignment->section;
        if(GeneralController::hasPermissions($section, 2) == false) {
            //User is not a TA. Is this the user who uploaded the file?
            if(Auth::user() != $fileUpload->user) {
                //User doesn't own this file AND they are not a TA
                return "invalid permissions";
            }
        }
        $fileName = storage_path()."/app/uploads/".$fileUpload->document->hash;
        if(File::exists($fileName)) {
            $file = File::get($fileName);
            $response = Response::make($file,200);
            $response->header("Content-Type", "application/pdf");
            return $response;
        } else
            return "invalid File";
    }

    /**
     * Returns the file submission ID's for an assignment
     * @param Request $request
     * @return \Illuminate\Http\Response|string
     */
    public function getFiles(Request $request) {
        $assignment = Assignment::findOrFail($request->assignment_id);
        $section = $assignment->section;
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions"; //User is not in section
        }
        $submittedFiles = $assignment->files()->get();
        return $submittedFiles;
    }

    /**
     * Uploads a file for the given assignment
     * @param $file
     * @param Assignment $assignment
     * @param User $user
     * @return bool
     */
    public static function attachAssignmentFile($file, Assignment $assignment, User $user) {
        //Check that  file exists
        if($file == null || $assignment == null || $user == null)
            return false;

        //Check that user is really in section
        if(GeneralController::userHasPermissions($user,$assignment->section,1) == false)
            return false;

        //Check that uploads are allowed for this file
        if($assignment->filesubmission == false)
            return false;

        //Check for a previous upload
        $existingFile = $assignment->files()->where('user_id',$user->id)->first();
        if($existingFile == null) {
            //Upload new file
            Log::debug("Uploaded file for assignment ".$assignment->id." and user ".$user->id);
            $fileId = FileController::uploadFile($file);
            $fileUpload = new AssignmentFileUpload;
            $fileUpload->file_id = $fileId;
            $fileUpload->assignment_id = $assignment->id;
            $fileUpload->user_id = $user->id;
            $fileUpload->save();
        } else {
            //Delete old file
            Log::debug("Overwriting previously submitted file for assignment ".$assignment->id." and user ".$user->id);
            $oldFile = $existingFile->document;
            FileController::updateFile($oldFile,$file);
        }
        return true;
    }

    /**
     * Uploads a file for the given assignment.
     * Returns the id of the uploaded file.
     * @param $file
     * @return bool
     * @internal param Assignment $assignment
     * @internal param User $user
     */
    private static function uploadFile($file) {
        //Upload file
        $contents = file_get_contents($file);
        if($contents == NULL)
            return false;

        //Generate a unique hash
        $hashedName = md5($file->getClientOriginalName().time());
        $filePath = "/uploads/".$hashedName;

        //Store file on the server
        Storage::put($filePath, $contents);

        //Store file entry in database
        $fileUpload = new FileUpload;
        $fileUpload->hash = $hashedName;
        $fileUpload->save();
        return $fileUpload->id;
    }

    /**
     * Udates an existing file.
     * @param FileUpload $fileEntry
     * @param $file
     * @return bool
     * @internal param Assignment $assignment
     * @internal param User $user
     */
    private static function updateFile(FileUpload $fileEntry, $file) {
        //Upload actual file
        $contents = file_get_contents($file);
        if($contents == NULL)
            return false;

        //Get the old hash
        $hashedName = $fileEntry->hash;
        $filePath = "/uploads/".$hashedName;

        //Store file on the server
        Storage::put($filePath, $contents);

        return $fileEntry->id;
    }

    /**
     * Returns the contents of the file requested. If the file does not exist, return false.
     * @param Assignment $assignment
     * @param User $user
     * @return bool
     */
    public static function fetchAssignmentFile(Assignment $assignment, User $user) {
        $pathToCheck = FileController::getAssignmentPath($assignment,$user).'../';
        if(!Storage::has($pathToCheck))
            return false;
        return Storage::get($pathToCheck);
    }



}
