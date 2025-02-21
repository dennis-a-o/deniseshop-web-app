@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Profile</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
			    <li class="breadcrumb-item active" aria-current="page">profile</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="profile" style="min-height: 1200px;">
	<div class="row">
		<div class="col-lg-3">
			<div class="card sticky-top border-0 rounded-4 p-4" id="profile-nav">
				<ul class="nav nav-hover-hightlight flex-column">
					<li class="nav-item rounded-3">
						<a class="nav-link active" aria-current="page" href="#profile">
						<i class="bi-image me-2"></i>
						<span>Profile</span>
						</a>
					</li>
					<li class="nav-item rounded-3 mt-2">
						<a class="nav-link active" aria-current="page" href="#basic-info">
						<i class="bi-person-badge me-2"></i>
						<span>Basic Info</span>
						</a>
					</li>
					<li class="nav-item rounded-3 mt-2">
						<a class="nav-link active" aria-current="page" href="#password">
						<i class="bi-shield-lock me-2"></i>
						<span>Change Password</span>
						</a>
					</li>
					<li class="nav-item rounded-3 mt-2">
						<a class="nav-link active" aria-current="page" href="#sessions">
						<i class="bi-watch me-2"></i>
						<span>Sessions</span>
						</a>
					</li>
					
				</ul>
			</div>
		</div>
		<div class="col-lg-9 mt-lg-0 mt-4">
			<form action="{{ url('/admin/profile/edit') }}" method="post" enctype="multipart/form-data">
				@csrf
				<div class="card border-0 rounded-4 p-4" id="profile">
					<div class="row">
						<div class="col-12">
							<div class="d-flex align-items-center">
								<label id="image-input-label">
									<img class="rounded-3 shadow-sm pointer"  src="{{ url('/assets/img/users').'/'.$user->image }}" width="70" height="70" >
									<input type="file" name="image" id="image-input" style="display: none;">
								</label>
								<div class="ms-4">
									<h5>{{ $user->first_name.' '.$user->last_name }}</h5>
									<span class="text-capitalize">{{ $user->role }}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4" id="basic-info">
					<h5>Basic info</h5>
					<div class="row">
						<div class="col-6">
							<label class="form-label mt-4">First name</label>
							<input class="form-control" type="text" name="first_name" value="{{ $user->first_name }}">
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Last name</label>
							<input class="form-control" type="text" name="last_name" value="{{ $user->last_name }}">
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Username</label>
							<input class="form-control" type="text" name="username" value="{{ $user->username }}">
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Email</label>
							<input class="form-control" type="text" name="email" value="{{ $user->email }}">
						</div>
						<div class="col-12">
							<button class="btn btn-primary btn-sm mt-4 float-end">Update</button>
						</div>
					</div>
				</div>
			</form>
			<form  action="{{ url('/admin/profile/password/edit') }}" method="post">
				@csrf
				<div class="card border-0 rounded-4 p-4 mt-4" id="password">
					<h5>Change password</h5>
					<div class="row">
						<div class="col-12">
							<label class="form-label mt-4">Current password</label>
							<div class="input-group">
								<input type="password" class="form-control border-end-0" name="current_password" required>
								<span class="input-group-text bg-white border-start-0"><i id="toggle-password" class="bi-eye-slash pointer"></i> </span>
							</div>
						</div>
						<div class="col-12">
							<label class="form-label mt-4">New password</label>
							<div class="input-group">
								<input type="password" class="form-control border-end-0" name="new_password" required>
								<span class="input-group-text bg-white border-start-0"><i id="toggle-password" class="bi-eye-slash pointer"></i> </span>
							</div>
						</div>
						<div class="col-12">
							<button class="btn btn-gradient-primary btn-sm mt-4 float-end">Update password</button>
						</div>
					</div>
				</div>
			</form>
			<form method="post" id="session-form">
				@csrf
				<div class="card border-0 rounded-4 p-4 mt-4" id="sessions">
					<h5>Sessions</h5>
					<div class="row">
						<div class="col-12">
							<p>Did you lose your phone or leave your account logged in at a public computer? You can log out everywhere else, and stay logged in here.</p>
							<div class="input-group">
								<input type="password" class="form-control border-end-0" name="password" placeholder="Current password" required>
								<span class="input-group-text bg-white border-start-0"><i id="toggle-password" class="bi-eye-slash pointer"></i> </span>
								<button class="btn btn-outline-primary" type="submit">Log out everywhere else</button>
							</div>
							
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		
		 document.getElementById("session-form").addEventListener("submit", function(e){
		 	e.preventDefault();
		 	clearSession(this);
		 });

		 async function clearSession(form){
			const formData = new FormData(form);

			try{
				const response = await fetch("{{url('/admin/profile/session/clear')}}",{
					method: "POST",
					mode: "cors",
					cache: "no-cache",
					credentials: "same-origin",
					body: formData,
				});

				if (response.ok) {
					const result = await response.json();
					if (result.error) {
						Toast("error", result.message);
					}else{
						Toast("success", result.message);
					}
				}else{
					console.error(await response.text());
				}
			}catch(error){
				console.error(error);
				Toast("error", "Something went wrong, try again later.");
			}
		}
	});
</script>
@endsection