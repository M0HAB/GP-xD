@extends('_Auth.admin.admin_layout.admin')
@section('title', $specialization->name.' - Specializations')


@section('admin_content')
<div class="card">
  	<div class="card-body">
		@include('_partials.errors')
        <h3 class="f-rw"><a href="{{ route('specialization.show',$specialization->id)}}"><strong>{{$specialization->name}}</strong></a> specialization Departments</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">No. of Courses in this department</th>
                </tr>
            </thead>
            <tbody>
                @foreach($specialization->departments as $department)
                <tr>
                    <td>{{$department->id}}</td>
                    <td><a href="{{route('department.show', $department->id)}}">{{ucfirst($department->name)}}</a></td>
                    <td>{{count($department->courses->where('course_department', $department->id))}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
	</div>
</div>
@endsection
