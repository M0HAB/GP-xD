@extends('_Auth.admin.admin_layout.admin')
@section('title', $user->fname.' '.$user->lname)

@section('admin_content')

<div class="card">
  <div class="card-body">
      <div class="row f-rw">
          {{--  User-Info  --}}
          <div class="col-lg-12 col-sm-12 mb-4">
              <h3 class="f-rw">
                  {{$user->fname.' '.$user->lname}}
                  <a  title="Edit" class="btn btn-link text-primary p-0" href="{{route('admin.user.edit', ['id'=>$user->id])}}"><i class="fas fa-edit fa-lg"></i></a>
                  @if(!$user->trashed())
                      <button class="btn btn-link text-primary p-0" type="submit" data-toggle="modal" data-target="#confirm" data-id="{{$user->id}}" data-type="user" data-keep="3" title="Delete">
                              <i class="fas fa-trash fa-lg"></i>
                      </button>
                  @else
                      <button class="btn btn-link text-primary p-0" type="submit" data-toggle="modal" data-target="#confirm" data-id="{{$user->id}}" data-type="user" data-keep="2" title="UnDelete">
                              <i class="fas fa-undo fa-lg"></i>
                      </button>
                  @endif
              </h3>

              <ul class="nav nav-tabs">
                  <li class="nav-item">
                      <a class="nav-link active" data-toggle="tab" href="#info">Info.</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="tab" href="#roll-courses">Courses</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="tab" href="#roll-posts">Discussions</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="tab" href="#roll-permissions">Permissions</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="tab" href="#roll-logs">Log</a>
                  </li>

              </ul>
              <div id="myTabContent" class="tab-content">
                  <div class="tab-pane show active" id="info">
                      <table class="table">
                          <tbody>
                              <tr>
                                  <th scope="row">Name</th>
                                  <td>{{$user->fname.' '.$user->lname}}</td>
                              </tr>
                              <tr>
                                  <th scope="row">Email</th>
                                  <td>{{$user->email}}</td>
                              </tr>
                              <tr>
                                  <th scope="row">Department</th>
                                  <td>{{$user->department->name}}</td>
                              </tr>
                              <tr>
                                  <th scope="row">Gender</th>
                                  <td>{{($user->gender == 1)?'Male':'Female'}}</td>
                              </tr>
                              <tr>
                                  <th scope="row">Role</th>
                                  <td>{{$user->role->name}}</td>
                              </tr>
                              <tr>
                                  <th scope="row">Location</th>
                                  <td>{{$user->location}}</td>
                              </tr>
                              @if($user->isStudent())
                              <tr>
                                  <th scope="row">Level</th>
                                  <td>{{$user->level}}</td>
                              </tr>
                              <tr>
                                  <th scope="row">GPA</th>
                                  <td>{{$user->gpa}}</td>
                              </tr>
                              @endif

                          </tbody>
                      </table>
                  </div>
                  <div class="tab-pane" id="roll-courses" style="overflow-y:auto;max-height:400px">
                      <table class="table">
                          <thead>
                              <tr>
                                  <th>Course Name</th>
                                  <th>Course Code</th>
                                  <th>Credit Hour</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach($user->courses as $course)
                              <tr>
                                  <td>
                                      <p class="font-weight-bold text-success">{{ucfirst($course->title)}} @if(!$course->is_active) <span class="badge badge-danger">Not-Active</span> @endif</p>
                                      <small>
                                          <p>Insrtuctor: <span class="font-weight-bold">{{$course->instructor->fname.' '.$course->instructor->lname}}</span></p>
                                      </small>
                                  </td>
                                  <td><span>{{$course->code}}</span></td>
                                  <td><span>{{$course->commitment}}</span></td>
                              </tr>
                              @endforeach
                          </tbody>
                      </table>
                  </div>
                  <div class="tab-pane" id="roll-posts" style="overflow-y:auto;max-height:400px">
                      <table class="table">
                          <thead>
                              <tr>
                                  <th>Discussion_Course</th>
                                  <th>Id</th>
                                  <th>Type</th>
                                  <th>body</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach($user->posts()->withTrashed()->get() as $post)
                                  <tr>
                                      <td>
                                          <p class="font-weight-bold">{{$post->discussion->course->title}}</p>
                                      </td>

                                      <td><p><span class="font-weight-bold">{{$post->id}}</span></p></td>
                                      <td>Post @if($post->trashed()) <span class="badge badge-danger">Deleted</span> @endif</td>
                                      <td><span>{{$post->body}}</span></td>
                                  </tr>
                              @endforeach
                              @foreach($user->replies()->withTrashed()->get() as $reply)
                                  <tr>
                                      <td>
                                          <p class="font-weight-bold">{{$reply->post()->withTrashed()->first()->discussion->course->title}}</p>
                                      </td>

                                      <td><p><span class="font-weight-bold">{{$reply->id}}</span></p></td>
                                      <td>Reply @if($reply->trashed()) <span class="badge badge-danger">Deleted</span> @endif</td>
                                      <td><span>{{$reply->body}}</span></td>
                                  </tr>
                              @endforeach
                              @foreach($user->comments as $comment)
                                    @php
                                    $record = $comment->reply()->withTrashed()->first();
                                    $record = $record->post()->withTrashed()->first();
                                    $title = $record->discussion->course->title;
                                    @endphp
                                  <tr>
                                      <td>
                                          <p class="font-weight-bold">{{$title}}</p>
                                      </td>

                                      <td><p><span class="font-weight-bold">{{$comment->id}}</span></p></td>
                                      <td>Comment</td>
                                      <td><span>{{$comment->body}}</span></td>
                                  </tr>
                              @endforeach
                          </tbody>
                      </table>
                  </div>
                  <div class="tab-pane" id="roll-permissions">
                      @if($user->permission === null)
                           <p class="text-center mt-4 text-muted">Default Permissions for <strong><a href="{{route('prole.show', $user->role->id)}}">{{$user->role->name}}</a> | <a href="{{route('prole.user.view', ['id'=>$user->id])}}">Edit here <i class="fas fa-edit"></i></a></strong></p>
                      @else
                       <table class="table">
                           <thead>
                               <tr>
                                   <th>Index Name</th>
                                   <th>Create</th>
                                   <th>Read</th>
                                   <th>Update</th>
                                   <th>Delete</th>
                               </tr>
                           </thead>
                           <tbody>
                               @foreach ($pindexes as $pindex)
                               <tr>
                                   <td>
                                       {{$pindex->name}}
                                   </td>
                                   <td>
       								@if(isset($envelope['create'.$pindex->index]))
       								<p class="f-rw text-success font-weight-bold"><i class="fas fa-check"></i></p>
       								@else
       								<p class="f-rw text-danger font-weight-bold"><i class="fas fa-times"></i></p>
       								@endif
                                   </td>
       							<td>
       								@if(isset($envelope['read'.$pindex->index]))
       								<p class="f-rw text-success font-weight-bold"><i class="fas fa-check"></i></p>
       								@else
       								<p class="f-rw text-danger font-weight-bold"><i class="fas fa-times"></i></p>
       								@endif
                                   </td>
                                   <td>
       								@if(isset($envelope['update'.$pindex->index]))
       								<p class="f-rw text-success font-weight-bold"><i class="fas fa-check"></i></p>
       								@else
       								<p class="f-rw text-danger font-weight-bold"><i class="fas fa-times"></i></p>
       								@endif
                                   </td>
                                   <td>
       								@if(isset($envelope['delete'.$pindex->index]))
       								<p class="f-rw text-success font-weight-bold"><i class="fas fa-check"></i></p>
       								@else
       								<p class="f-rw text-danger font-weight-bold"><i class="fas fa-times"></i></p>
       								@endif
                                   </td>
                               </tr>
                               @endforeach
                           </tbody>
                       </table>


                       <a href="{{route('prole.user.view', ['id'=>$user->id])}}" style="text-decoration:none"><button type="button" class="btn btn-success btn-lg btn-block">Edit here <i class="fas fa-edit"></i></button></a>
                       @endif
                  </div>
                  <div class="tab-pane" id="roll-logs" style="overflow-y:auto;max-height:400px">
                      <table class="table">
                          <thead>
                              <tr>
                                  <th>Subject</th>
                                  <th>Action</th>
                                  <th>Type</th>
                                  <th>Object</th>
                                  <th>Time</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach($user->actionlog->sortByDesc('id') as $log)
                                  <tr>
                                      <td>{{$log->subject}}</td>
                                      <td>{{$log->action}}</td>
                                      @if($log->type_id === 0)
                                        <td>{{$log->type}}</td>
                                      @else
                                        <td><a href="{{route('admin.user.action', ['type' => $log->type, 'id' => $log->type_id])}}">{{$log->type}}</a></td>
                                      @endif
                                      <td>
                                          @if ($log->object)
                                            <a href="{{route('admin.user.action', ['type' => $log->object, 'id' => $log->object_id])}}">{{$log->object}}</a>
                                          @else
                                          Null
                                          @endif
                                      </td>
                                      <td>{{$log->created_at->diffforhumans()}}</td>

                                  </tr>
                              @endforeach
                              @foreach($user->adminUserLog()->get()->sortByDesc('id') as $log)
                                  <tr>
                                      <td>{{$log->subject}}</td>
                                      <td>{{$log->action}}</td>
                                      <td>{{$log->type}}</td>
                                      <td>
                                          @if ($log->object)
                                            <a href="{{route('admin.user.action', ['type' => $log->object, 'id' => $log->object_id])}}">{{$log->object}}</a>
                                          @else
                                          Null
                                          @endif
                                      </td>
                                      <td>{{$log->created_at->diffforhumans()}}</td>

                                  </tr>
                              @endforeach

                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div> <!-- End: Profile -->
  </div>
</div>
@include('_partials.modal_confirm')
@endsection
@section('scripts')
<script type="text/javascript">
    var api_token    = "{{ Auth::user()->api_token}}";
</script>
<script src="{{asset('js/axios.min.js')}}"></script>
<script src="{{asset('js/modal_confirm.js')}}" charset="utf-8"></script>
@endsection
