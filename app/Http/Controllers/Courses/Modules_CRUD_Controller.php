<?php
namespace App\Http\Controllers\Courses;

use App\Course;
use App\grade;
use App\Http\Controllers\Controller;
use App\Module;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\ActionLog;

class Modules_CRUD_Controller extends Controller{

    private $controllerName = "Course";

    public function __construct()
    {
        $this->middleware(['auth', 'revalidate']);
    }

    /**
    | list the modules of the course:-
    |-------------------------------
     */

    public function viewCourseModules(Course $course, User $user){
        if(canRead($this->controllerName)){
            $course = DB::table('courses')
                ->leftjoin('users', 'users.id', '=', 'courses.instructor_id')
                ->select('courses.*', 'users.fname', 'users.lname')
                ->where('courses.id', '=', $course->id)
                ->first();

            $modules = DB::table('modules')
                ->leftjoin('courses', 'courses.id', '=', 'modules.course_id')
                ->select('modules.*')
                ->where('modules.course_id', '=', $course->id)
                ->orderBy('module_order')
                ->get();
            $courseModal = Course::find($course->id);
            $instructor = $user->find($course->instructor_id);
            return view('courses.courseModules', compact('course', 'modules'))->with('courseModal', $courseModal)->with('instructor', $instructor);

        }else{
            return redirect()->route('user.dashboard')->with('error', 'Unauthorized Access');
        }


    }


    /**
    | Get new module form:-
    |----------------------
     */

    public function getNewModuleForm(Course $course){
        if(canCreate($this->controllerName)){
            return view('courses.newModuleForm')->with('course_id', $course->id);
        }else{
            return redirect()->route('user.dashboard')->with('error', 'Unauthorized Access');
        }
    }

    /**
    | Create New Module
    |------------------
     */

    public function addNewModule(Request $request, Course $course){
        if(canCreate($this->controllerName)){
            if(\Illuminate\Support\Facades\Request::ajax()){
                // Validate the form fields

                $validator = Validator::make($request->all(), [
                    'title'      => 'required|string|max:255|unique:modules',
                    'commitment' => 'required|min:1|integer',
                    'module_order' => 'required|min:1|integer',
                    'introduction' => 'required|max:400',

                ]);
                if (!($validator->passes())) {
                    return response($validator->errors(), 401);
                }

                $module = Module::create([
                    'title'        => $request->title,
                    'commitment'   => $request->commitment,
                    'module_order' => $request->module_order,
                    'introduction' => $request->introduction,
                    'course_id' => $course->id
                ]);

                if($module){
                    ActionLog::create([
                        'subject' => 'user',
                        'subject_id' => Auth::user()->id,
                        'action' => 'create',
                        'type' => 'module',
                        'type_id' => $module->id,
                        'object' => 'course',
                        'object_id' => $course->id
                    ]);
                    return response()->json([
                        'success' => 'module created successfully!',
                    ]);
                }
            }
        }else{
            return response()->json([
                'error' => 'Unauthorized Operation!',
            ], 401);
        }

    }

    /**
    | Get The Update Form Of Existing Module :-
    |------------------------------------------
     */
    public function getUpdateModuleForm(Course $course, Module $module){
        if(canUpdate($this->controllerName)){
            return view('courses.updateModuleForm', compact('course', 'module'));
        }else{
            return redirect()->route('user.dashboard')->with('error', 'Unauthorized Access');
        }

    }

    /**
    | Update A Specific Module :-
    |----------------------------
     */
    public function updateModule(Request $request, Course $course, Module $module){
        if(canUpdate($this->controllerName)){
            if(\Illuminate\Support\Facades\Request::ajax()){

                // Validate the form fields
                $validator = Validator::make($request->all(), [
                    'title'           => 'required|string|max:255|unique:modules,title,'.$module->id,
                    'commitment' => 'required|min:1|integer',
                    'module_order' => 'required|min:1|integer',
                    'introduction' => 'required|max:400'
                ]);
                if (!($validator->passes())) {
                    return response($validator->errors(), 401);
                }

                $myModule = $module->update([
                    'title'        => $request->title,
                    'commitment'   => $request->commitment,
                    'module_order' => $request->module_order,
                    'introduction' => $request->introduction,
                    'course_id'    => $course->id
                ]);

                if($myModule){
                    ActionLog::create([
                        'subject' => 'user',
                        'subject_id' => Auth::user()->id,
                        'action' => 'update',
                        'type' => 'module',
                        'type_id' => $module->id,
                        'object' => 'course',
                        'object_id' => $course->id
                    ]);
                    $module = Module::find($module->id);
                    return response()->json([
                        'success' => 'module updated successfully!',
                        'module' => $module
                    ]);
                }
            }
        }else{
            return response()->json([
                'error' => 'Unauthorized Operation!'
            ], 401);
        }

    }


}
