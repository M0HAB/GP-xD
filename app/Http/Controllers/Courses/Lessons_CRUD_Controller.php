<?php

namespace App\Http\Controllers\Courses;

use App\Course;
use App\Http\Controllers\Controller;
use App\Lesson;
use App\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;



class Lessons_CRUD_Controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'revalidate']);
    }

    public function displayLessonsOfModules(Course $course, Module $module){

        $assignments = $module->assignments()->get();

        if(Auth::User()->isStudent()){
            $quizzes = DB::table('quizzes')
                ->leftjoin('modules', 'modules.id', '=', 'quizzes.module_id')
                ->select('quizzes.*')
                ->where('quizzes.module_id', '=', $module->id)
                ->where('quizzes.is_active', '=', 1)
                ->get();
        }elseif(Auth::User()->isInstructor()){
            $quizzes = DB::table('quizzes')
                ->leftjoin('modules', 'modules.id', '=', 'quizzes.module_id')
                ->select('quizzes.*')
                ->where('quizzes.module_id', '=', $module->id)
                ->get();
        }


        $lessons = DB::table('lessons')
            ->leftjoin('modules', 'modules.id', '=', 'lessons.module_id')
            ->select('lessons.*')
            ->where('lessons.module_id', '=', $module->id)
            ->get();
        if(Auth::User()->checkIfUserEnrolled($course->id) or Auth::User()->checkIfUserTeachCourse($course->id)) {
            return view('Courses.LessonsOfModule', ['course' => $course, 'module' => $module, 'lessons' => $lessons, 'assignments' => $assignments, 'quizzes' => $quizzes]);
        }else{
            return redirect()->back()->with('error', 'Unauthorized access');
        }

    }

    public function loadLessons(Course $course,Module $module){


        $lessons = DB::table('lessons')
            ->leftjoin('modules', 'modules.id', '=', 'lessons.module_id')
            ->select('lessons.*')
            ->where('lessons.module_id', '=', $module->id)
            ->get();
        return response()->json([
            'data' => $lessons,
            'course' => $course,
            'module' => $module
        ]);
    }

    public function getNewVideoForm(Course $course,Module $module){

        if(Auth::User()->checkIfUserEnrolled($course->id) or Auth::User()->checkIfUserTeachCourse($course->id)) {
            return view('Courses.newVideoForm', compact('module'));
        }else{
            return redirect()->back();
        }

    }

    public function uploadVideo(Request $request, Course $course, Module $module){

        ini_set('memory_limit','256M');
        $privacyValues = ['unlisted', 'public'];
        ini_set('max_execution_time', 1500);


        $this->validate($request, [
            'title'       => 'required|string|max:255|unique:lessons',
            'description' => 'required|string|max:255',
            'recap'       => 'required|string|max:255',
            'privacy' => ['required', Rule::in($privacyValues)],
        ]);


        $file = $request->file('myVideo');
        $fileName = storage_path('app/public/videos/' . $file->getClientOriginalName());
        $destination = storage_path('app/public/videos');
        if($file->move($destination, $fileName)){
            $fullPathToVideo = $fileName;
            $video = \Dawson\Youtube\Facades\Youtube::upload($fullPathToVideo, [
                'title'       => $request->title,
                'description' => $request->description,
                'category_id' => 10
            ], $request->privacy);

            $video_id = $video->getVideoId();

            if($video){
                $lesson = Lesson::create([
                    'title'       => $request->title,
                    'description' => $request->description,
                    'recap'       => $request->recap,
                    'privacy'     => $request->privacy,
                    'url'         => 'https://www.youtube.com/watch?v=' . $video_id,
                    'module_id'   => $module->id
                ]);

                if($lesson){
                    unlink($fileName);
                    Session::flash('success', "Video uploaded successfully!");
                    return redirect()->back();
                }

            }
        }


    }
}
