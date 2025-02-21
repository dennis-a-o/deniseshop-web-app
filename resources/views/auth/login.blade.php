@extends('layouts.auth')

@section('content')
<section class="">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 text-center pb-5">
				<a href="{{url('')}}">
					<img src="{{asset('assets/img/general/logo_dark.png')}}" height="30"  alt="Logo">
				</a>
			</div>
		</div>
		<div class="row justify-content-center">
			<div class="col-lg-4 col-md-7">
				<div class="card border-0 shadow rounded-4">
					<div class="card-header bg-transparent border-0">
						<h4 class="fw-bolder text-center mt-4">Sign in</h4>
					</div>
					<div class="card-body px-4">
						 @include('includes.form-error')
						 @include('includes.form-success')
						<form action="{{url('/login')}}" method="post">
							@csrf
							<div class="mb-4">
								<input class="form-control" type="text" name="username_email" placeholder="Username or Email" required>
							</div>
							<div class="mb-4">
								<div class="input-group">
									<input type="password" class="form-control border-end-0" id="password" name="password" placeholder="Password" required>
									<span class="input-group-text bg-white border-start-0"><i id="toggle-password" class="bi-eye-slash pointer"></i> </span>
								</div>
							</div>
							<div class="form-check form-switch">
							  	<input class="form-check-input" type="checkbox" id="switch1" name="remember_me" value="1">
							 	<label class="form-check-label ms-2" for="switch1">Remember me</label>
							</div>
							<div class="mt-4">
								<button class="btn btn-primary">Sign in</button>
							</div>
							<div class="mt-4">
								<p>Don't have an account? <a href="{{url('/register')}}">Sign up</a> </p>
								<p>Forgot password? <a href="{{url('/forgot-password')}}">Reset</a> </p>
							</div>
						</form>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</section>
@endsection