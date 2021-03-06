<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use App\Reply;
use Auth;
use App\ActionLog;

class CommentController extends Controller
{
    private $controllerName = "Discussion";

    public function __construct()
    {
        $this->middleware(['auth', 'revalidate', 'checkUserEnrollmentInCourse', 'checkCourseActivation']);
    }
    public function store(Request $request)
    {
        if(canCreate($this->controllerName)){
            if($request->ajax()){
              $comment = new Comment;
              $comment->body = $request->body;
              $comment->reply_id = $request->id;
              $comment->user_id = Auth::user()->id;
              $comment->save();
              ActionLog::create([
                  'subject' => 'user',
                  'subject_id' => Auth::user()->id,
                  'action' => 'create',
                  'type' => 'comment',
                  'type_id' => $comment->id,
                  'object' => 'reply',
                  'object_id' => $comment->reply_id
              ]);
              $reply = Reply::find($request->id);
              $comments = $reply->comments()->latest()->get();
              return response()->json([
                'comments_body' => view('_auth.posts.load_comments')->with('comments', $comments)->render(),
                'votes' => count($reply->votes),
                'comments' => count($comments),
                'voters' => $reply->voters()
              ]);
            }
        }else{
            return redirect()->route('error.api', 'Unauthorized Operation!');
        }

    }
    public function edit(Request $request)
    {
        if(canUpdate($this->controllerName)){
            if($request->ajax()){
              $comment = Comment::find($request->id);
              $comment->body = $request->body;
              $comment->save();
              ActionLog::create([
                  'subject' => 'user',
                  'subject_id' => Auth::user()->id,
                  'action' => 'update',
                  'type' => 'comment',
                  'type_id' => $comment->id,
                  'object' => 'reply',
                  'object_id' => $comment->reply_id
              ]);
              return response()->json([
                'body' => $comment->body,
              ]);
            }
        }else{
            return redirect()->route('error.api', 'Unauthorized Operation!');
        }

    }
    public function delete(Request $request,$id)
    {
        if(canDelete($this->controllerName)){
            if($request->ajax()){
              $comment = Comment::find($id);
              if ($comment->user_id == Auth::user()->id){
                if($comment->delete()){
                    ActionLog::create([
                        'subject' => 'user',
                        'subject_id' => Auth::user()->id,
                        'action' => 'delete',
                        'type' => 'comment',
                        'type_id' => $comment->id,
                        'object' => 'reply',
                        'object_id' => $comment->reply_id
                    ]);
                  return 1;
                }
              }
              return 0;
            }
        }else{
            return redirect()->route('error.api', 'Unauthorized Operation!');
        }


    }
}
