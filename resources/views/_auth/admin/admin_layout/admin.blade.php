@php
	$admin = true;
@endphp
@extends('_layouts.app')
@section('content')
	<!-- Start: Dashboard -->
	<div class="row">
        {{--  left-side  --}}
		<div class="col-lg-4 mb-4 col-sm-12" style="box-sizing:border-box;">
            <div class="accordion mb-4" id="accordion">
                <div class="card rounded-0">
                    <div class="card-header rounded-0" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                      <h5 class="mb-0 f-rw-bold">
                          Permissions And Roles
                      </h5>
                    </div>
                    <div id="collapseOne" class="collapse
                    @if(Route::is('prole.*') || Route::is('admin.dashboard')) show @endif
                    rounded-0" aria-labelledby="headingOne" data-parent="#accordion">
                          <div class="card-body">
                              <p class="mb-1"><a href="{{route('prole.index')}}" class="bbp-breadcrumb-home forum-nav"><strong><i class="fas fa-list mr-2"></i>List All Roles</strong></a></p>
                              <p class="mb-1"><a href="{{route('prole.create')}}" class="bbp-breadcrumb-home forum-nav"><strong><i class="fas fa-plus mr-2"></i>Add New Role</strong></a></p>
                              <p class="mb-1"><a href="#" class="bbp-breadcrumb-home forum-nav"><strong><i class="fas fa-eye mr-2"></i>View User Permissions</strong></a></p>
                          </div>
                    </div>
                </div>
                <div class="card rounded-0">
                    <div class="card-header rounded-0" id="heading2" data-toggle="collapse" data-target="#collapse2" aria-expanded="false" aria-controls="collapseOne">
                      <h5 class="mb-0">
                          Users
                      </h5>
                    </div>

                    <div id="collapse2" class="collapse
					@if(Route::is('admin.user.*')) show @endif
					rounded-0" aria-labelledby="heading2" data-parent="#accordion">
                          <div class="card-body">
                              <p class="mb-1"><a href="{{route('admin.user.index')}}" class="bbp-breadcrumb-home forum-nav"><strong><i class="fas fa-list mr-2"></i>List All Users</strong></a></p>
                              <p class="mb-1"><a href="#" class="bbp-breadcrumb-home forum-nav"><strong><i class="fas fa-plus mr-2"></i>Create New User</strong></a></p>
                              <p class="mb-1"><a href="#" class="bbp-breadcrumb-home forum-nav"><strong><i class="fas fa-trash mr-2"></i>Delete User</strong></a></p>
                              <p class="mb-1"><a href="#" class="bbp-breadcrumb-home forum-nav"><strong><i class="fas fa-edit mr-2"></i>Edit User</strong></a></p>
                          </div>
                    </div>
                </div>
                <div class="card rounded-0">
                    <div class="card-header rounded-0" id="heading3" data-toggle="collapse" data-target="#collapse3" aria-expanded="false" aria-controls="collapseOne">
                      <h5 class="mb-0">
                          Departments and Specializations
                      </h5>
                    </div>

                    <div id="collapse3" class="collapse rounded-0" aria-labelledby="heading3" data-parent="#accordion">
                          <div class="card-body">
                              <p class="mb-1"><a href="#" class="bbp-breadcrumb-home forum-nav"><strong><i class="fas fa-list mr-2"></i>List All Departments</strong></a></p>
                              <p class="mb-1"><a href="#" class="bbp-breadcrumb-home forum-nav"><strong><i class="fas fa-list mr-2"></i>List All Specializations</strong></a></p>
                              <p class="mb-1"><a href="#" class="bbp-breadcrumb-home forum-nav"><strong><i class="fas fa-plus mr-2"></i>Add Department</strong></a></p>
                              <p class="mb-1"><a href="#" class="bbp-breadcrumb-home forum-nav"><strong><i class="fas fa-plus mr-2"></i>Add Specialization</strong></a></p>
                          </div>
                    </div>
                </div>
            </div>
		</div>
		<div class="col-lg-8 col-sm-12">
            @yield('admin_content')
			{{-- <div class="arrow"></div> --}}
		</div>
	</div> <!-- End: Dashboard -->
@endsection