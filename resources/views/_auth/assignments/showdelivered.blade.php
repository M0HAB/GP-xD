@extends('_layouts.app')
@section('title', 'Assignments Delivered')
@section('content')
    {{-- Start Breadcrumbs--}}
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item text-success"><a href="/Courses/">Courses</a></li>
                <li class="breadcrumb-item text-success"><a href="/Courses/{{$course->id}}">{{ $course->title }}</a></li>
                <li class="breadcrumb-item text-success">Module</li>
                <li class="breadcrumb-item text-success"><a href="/Courses/{{$course->id. "/Modules/" .$module->id}}">{{ $module->title }}</a></li>
                <li class="breadcrumb-item text-success"><a href="/Courses/{{$course->id. "/Modules/" .$module->id. "/assignments"}}">Assignments</a></li>
                <li class="breadcrumb-item active"><a href="/Courses/{{$course->id. "/Modules/" .$module->id. "/assignmentDelivered"}}">Delivered</a></li>
            </ol>
        </div>
        <div class="col-lg-12">
            <h1 class="f-rw my-4">Assignments Delivered</h1>
        </div>
    </div>
    {{-- End Breadcrumbs--}}

    <!-- Start: Content -->

    <br>
    @if (count($assdelivered)>0)
        <div class="row justify-content-center">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Student Name</th>
                        <th>Student Comment</th>
                        <th>File</th>
                        <th>Submitted Date</th>
                        <th>Status</th>
                        <th>Grade</th>
                        <th>Dr Comment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assdelivered as $delivered)
                        <tr>

                            <td>
                                {{$delivered->title ? $delivered->title : "No title" }}
                            </td>
                            <td>
                                {{$delivered->fname ? $delivered->fname : "No name"}}
                            </td>
                            <td>
                                {{$delivered->answer ? $delivered->answer : 'No Answer'}}

                            </td>
                            <td>

                                @if(is_null($delivered->file))

                                    No File Attached

                                @else
                                    <a href="{{asset("uploads\assignments\delivered") }}\{{$delivered->file}}" download="{{$delivered->file}}">
                                        <button type="button" class="btn btn-primary btn-block">
                                            <i class="fas fa-cloud-download-alt "></i>
                                            Download
                                        </button>
                                    </a>

                                @endif
                            </td>

                            <td>
                                {{{date('d-m-Y', strtotime($delivered->created_at))}}}
                            </td>


                            <td>
                                @if($delivered->created_at >= $delivered->deadline)
                                    <p class="text-danger">LATE </p>

                                @else
                                    <p class="text-success">ON TIME</p>

                                @endif
                            </td>
                            <td>
                                <p> {{$delivered->grade ? $delivered->grade : "-"}} / {{$delivered->full_mark}} </p>
                            </td>
                            <td>
                                <p> {{$delivered->comment ? $delivered->comment : "No Comment "}} </p>
                            </td>
                            @if(canUpdate('Assignment'))
                                <td>
                                    <button  class="btn btn-group-sm btn-link"><a href="{{route('assignmentdelivered.edit', ['course'=>$course->id, 'module'=>$module->id ,'assignment'=>$delivered->ass_id,'stdudent'=>$delivered->user_id,'assdel'=>$delivered->id])}}"><i class="far fa-edit fa-lg text-primary"></i> </a> </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-left"><i class="fa fa-info-circle mr-2"></i>The module has no assignment delivered yet</p>

    @endif
@stop
