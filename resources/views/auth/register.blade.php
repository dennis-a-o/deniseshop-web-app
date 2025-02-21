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
						<h4 class="fw-bolder text-center mt-4">Sign Up</h4>
					</div>
					<div class="card-body px-4">
						 @include('includes.form-error')
						 @include('includes.form-success')
						<form action="{{url('/register')}}" method="post">
							@csrf
							<div class="mb-4">
								<input class="form-control" type="text" name="firstname" placeholder="First name" required>
							</div>
							<div class="mb-4">
								<input class="form-control" type="text" name="lastname" placeholder="Last name" required>
							</div>
							<div class="mb-4">
								<input class="form-control" type="email" name="email" placeholder="Email" required>
							</div>
							<div class="mb-4">
								<div class="input-group">
									<input type="password" class="form-control border-end-0" id="password" name="password" placeholder="Password" required>
									<span class="input-group-text bg-white border-start-0"><i id="toggle-password" class="bi-eye-slash pointer"></i> </span>
								</div>
							</div>
							<div class="mb-4">
								<div class="input-group">
									<input type="password" class="form-control border-end-0" id="password" name="password_confirmation" placeholder="Confirm password" required>
									<span class="input-group-text bg-white border-start-0"><i id="toggle-password" class="bi-eye-slash pointer"></i> </span>
								</div>
							</div>
							<div class="form-check">
							  	<input class="form-check-input" type="checkbox" id="switch1" name="agree" value="1">
							 	<label class="form-check-label ms-2" for="switch1">I agree to <a href="{{url('/terms-and-conditions')}}">Terms and conditons</a> </label>
							</div>
							<div class="mt-4">
								<button class="btn btn-primary">Sign up</button>
							</div>
							<div class="mt-4">
								<p>Don't have an account? <a href="{{url('/login')}}">Sign in</a> </p>
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