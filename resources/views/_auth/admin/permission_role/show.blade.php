@extends('_layouts.app')
@section('title', 'Roles-Permissions')


@section('content')
<!-- Start: Content -->
	<div class="content mt-5 mb-4">
		<div class="container">
        <h1>Permissions <a href="{{ route('prole.create')}}">
            <button class="btn btn-primary" href="{{ route('prole.create')}}" data-toggle="tooltip" data-placement="top" title="Create">
                <span class="fas fa-plus" ></span>
            </button>
        </a> </h1>

			<div class="row justify-content-center">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Permission</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($perRole as $x)
                        <tr id="prole_container_{{$x->id}}">
                            <td>
                                {{$x->id}}
                            </td>
                            <td>
                                {{$x->name}}
                            </td>
                            <td>
                                <a href={{ route('prole.show',$x->id)}}>View Permissions for this role</a>
                            </td>
                            <td>

                                <a href="{{ route('prole.edit',$x->id)}}">
                                    <button class="btn btn-success" href="{{ route('prole.edit',$x->id)}}">
                                        <span class="far fa-edit"></span>
                                    </button>
                                </a>
                            </td>
                            <td>
															<button class="btn btn-danger" type="submit" data-toggle="modal" data-target="#confirm" data-id="{{$x->id}}" data-type="prole">
																	<span class="far fa-trash-alt fa-lg"></span>
															</button>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>

			</div>
		</div>
	</div> <!-- End: Content -->
	@include('_partials.modal_confirm')

@endsection
@section('scripts')
<script src="{{asset('js/axios.min.js')}}"></script>
<script>
  var api_token = "{{ Auth::user()->api_token}}";
</script>
<script src="{{asset('js/modal_confirm.js')}}" charset="utf-8"></script>
@endsection