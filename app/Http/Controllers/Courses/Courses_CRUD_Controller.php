<?php
namespace App\Http\Controllers\Courses;

use App\Course;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Department;
use App\Specialization;
use App\Discussion;
use App\ActionLog;

class Courses_CRUD_Controller extends Controller{

    private $controllerName = "Course";
    public function __construct()
    {
        $this->middleware(['auth', 'revalidate']);
    }


    /**
    | list the courses of the user:-
    |-------------------------------
     */

    public function listUserCourses(){
        if(canRead($this->controllerName)){
            // $user = User::find(Auth::User()->id);

            if(Auth::user()->isInstructor()){
                // Get the courses of the instructor and the instructor name of each course
                $courses = DB::table('courses')
                    ->leftjoin('users', 'users.id', '=', 'courses.instructor_id')
                    ->select('courses.*', 'users.fname', 'users.lname')
                    ->where('courses.instructor_id', '=', Auth::User()->id)
                    ->get();
            }elseif(Auth::user()->isStudent()){
                // Get the courses of the user and the instructor name of each course
                $courses = DB::table('courses')
                    ->leftjoin('course_user', 'course_user.course_id', '=', 'courses.id')
                    ->leftjoin('users as u1', 'u1.id', '=', 'courses.instructor_id')
                    ->leftjoin('users as u2', 'u2.id', '=','course_user.user_id')
                    ->select('courses.*', 'u1.fname', 'u1.lname')
                    ->where('course_user.user_id', '=', Auth::User()->id)
                    ->where('course_user.is_passed', '=', 0)
                    ->where('courses.is_active', '=', 1)
                    ->get();
            }
            return view('Courses.userCourses', compact('courses'));
        }else{
            return redirect()->route('user.dashboard')->with('error', 'Unauthorized Access');
        }

    }

    /**
    | Get new course form:-
    |----------------------
     */

    public function getNewCourseForm(){

        if(canCreate($this->controllerName)){
            $departments = Department::all();
            $specializations = Specialization::all();
            return view('courses.newCourseForm')->with('departments', $departments)->with('specializations', $specializations);
        }else{
            return redirect()->route('user.dashboard')->with('error', 'Unauthorized Access');
        }

    }

    /**
        | Create New Course
        |------------------
    */

    public function addNewCourse(Request $request){
        if(canCreate($this->controllerName)){
            if(\Illuminate\Support\Facades\Request::ajax()){

                // Variables to hold the valid selected values in the form

                $departmentValues = Department::where('id' ,'>' ,0)->pluck('id')->toArray();
                $courseSpecializationValues = Specialization::where('id' ,'>' ,0)->pluck('id')->toArray();
                $languageValues = ['Arabic', 'English'];
                $commitmentValues = [1, 2, 3, 4];

                // Validate the form fields

                $validator = Validator::make($request->all(), [
                    'title'           => 'required|string|max:255|unique:courses',
                    'code'            => 'required|string|max:255|unique:courses',
                    'course_department'  => ['required', Rule::in($departmentValues)],
                    'start_date'      => 'required|date|after:today',
                    'end_date'        => 'required|date|after:start_date',
                    'course_language' => ['required', Rule::in($languageValues)],
                    'course_specialization'  => ['required', Rule::in($courseSpecializationValues)],
                    'description'     => 'required|max:255',
                    'how_to_pass'     => 'required|max:255',
                    'commitment'      => ['required', Rule::in($commitmentValues)]
                ]);
                if (!($validator->passes())) {
                    return response($validator->errors(), 401);
                }
                //////
                //extra check to see if someone tampered with the values in select drop down and inserted invalid value
                if(Department::find($request->course_department)
                ->specializations
                ->where('id', $request->course_specialization)->count() == 0){
                    return "Unauthorized";
                }
                //////
                $course = Course::create([
                    'title'            => $request->title,
                    'code'             => $request->code,
                    'course_department'=> $request->course_department,
                    'start_date'       => $request->start_date,
                    'end_date'         => $request->end_date,
                    'course_language'  => $request->course_language,
                    'course_specialization' => $request->course_specialization,
                    'description'      => $request->description,
                    'how_to_pass'      => $request->how_to_pass,
                    'commitment'       => $request->commitment,
                    'instructor_id'      => Auth::User()->id,
                ]);
                //create Discussion for that course
                Discussion::create([
                    'course_id' => $course->id
                ]);
                ActionLog::create([
                    'subject' => 'user',
                    'subject_id' => Auth::user()->id,
                    'action' => 'create',
                    'type' => 'course',
                    'type_id' => $course->id,
                    'object' => 'department',
                    'object_id' => $course->course_department
                ]);
                ///
                if($course){
                    return response()->json([
                        'success' => 'course created successfully!',
                    ]);
                }
            }
        }else{
            return redirect()->route('user.dashboard')->with('error', 'Unauthorized Access');
        }

    }

    /**
    | Get The Update Form Of Existing course :-
    |------------------------------------------
     */
    public function getUpdateCourseForm(Course $course){
        if(canUpdate($this->controllerName)){
            $departments = Department::all();
            $specializations = Specialization::all();
            return view('courses.updateCourseForm', compact('course'))->with('departments' ,$departments)->with('specializations', $specializations);
        }else{
            return redirect()->route('user.dashboard')->with('error', 'Unauthorized Access');
        }

    }

    /**
    | Update A Specific Course :-
    |----------------------------
     */
    public function updateCourse(Request $request, Course $course){
        if(canUpdate($this->controllerName)){
            if(\Illuminate\Support\Facades\Request::ajax()){

                // Variables to hold the valid selected values in the form
                $departmentValues = Department::where('id' ,'>' ,0)->pluck('id')->toArray();
                $courseSpecializationValues = Specialization::where('id' ,'>' ,0)->pluck('id')->toArray();
                $languageValues = ['Arabic', 'English'];
                $commitmentValues = [1, 2, 3, 4];

                // Validate the form fields

                $validator = Validator::make($request->all(), [
                    'title'           => 'required|string|max:255|unique:courses,title,'.$course->id,
                    'code'            => 'required|string|max:255|unique:courses,code,'.$course->id,
                    'course_department'  => ['required', Rule::in($departmentValues)],
                    'start_date'      => 'required|date|after:today',
                    'end_date'        => 'required|date|after:start_date',
                    'course_language' => ['required', Rule::in($languageValues)],
                    'course_specialization'  => ['required', Rule::in($courseSpecializationValues)],
                    'description'     => 'required|max:255',
                    'how_to_pass'     => 'required|max:255',
                    'commitment'      => ['required', Rule::in($commitmentValues)]
                ]);
                if (!($validator->passes())) {
                    return response($validator->errors(), 401);
                }
                //////
                //extra check to see if someone tampered with the values in select drop down and inserted invalid value
                if(Department::find($request->course_department)
                ->specializations
                ->where('id', $request->course_specialization)->count() == 0){
                    return "Unauthorized";
                }
                //////
                $myCourse = Course::where('id', '=',$course->id)->update([
                        'title'            => $request->title,
                        'code'             => $request->code,
                        'course_department'=> $request->course_department,
                        'start_date'       => $request->start_date,
                        'end_date'         => $request->end_date,
                        'course_language'  => $request->course_language,
                        'course_specialization' => $request->course_specialization,
                        'description'      => $request->description,
                        'how_to_pass'      => $request->how_to_pass,
                        'commitment'       => $request->commitment,
                        'instructor_id'      => Auth::User()->id,
                ]);
                if($myCourse){
                    ActionLog::create([
                        'subject' => 'user',
                        'subject_id' => Auth::user()->id,
                        'action' => 'update',
                        'type' => 'course',
                        'type_id' => $course->id,
                        'object' => 'department',
                        'object_id' => $course->course_department
                    ]);
                    $course = Course::find($course->id);
                    return response()->json([
                        'success' => 'course updated successfully!',
                        'course' => $course
                    ]);
                }
            }
        }else{
            return redirect()->route('user.dashboard')->with('error', 'Unauthorized Access');
        }

    }

    /**
    | Update A Course Activation :-
    |----------------------------
     */
    public function updateCourseActivation(Request $request, Course $course){
        if(canDelete($this->controllerName)){
            if(\Illuminate\Support\Facades\Request::ajax()){

                $myCourse = Course::where('id', $course->id)->update([
                    'is_active' => $request->is_active,
                ]);
                $action = ($request->is_active)? 'activate' : 'deactivate';
                if($myCourse){
                    ActionLog::create([
                        'subject' => 'user',
                        'subject_id' => Auth::user()->id,
                        'action' => $action,
                        'type' => 'course',
                        'type_id' => $course->id,
                        'object' => 'department',
                        'object_id' => $course->course_department
                    ]);
                    $course = Course::find($course->id);
                    return response()->json([
                        'success' => $course->is_active?'course activated successfully!':'course deactivated successfully!',
                        'course' => $course

                    ]);
                }else{
                    return response()->json([
                        'message' => 'Something went wrong.Please,Try again!',
                    ]);
                }
            }
        }else{
            return response()->json([
                'message' => 'Unauthorized Operation!',
            ], 401);
        }

    }

}
