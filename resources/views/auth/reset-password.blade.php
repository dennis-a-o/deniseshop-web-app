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
						<h4 class="fw-bolder text-center mt-4">Reset password</h4>
					</div>
					<div class="card-body px-4">
						 @include('includes.form-error')
						 @include('includes.form-success')
						<form action="{{url('/reset-password')}}" method="post">
							@csrf
							<input type="hidden" name="token" value="{{$token ?? ''}}">
							<input type="hidden" name="email" value="{{$email ?? ''}}">
							<div class="mb-4">
								<div class="input-group">
									<input type="password" class="form-control border-end-0" id="password" name="password" placeholder="New password" required>
									<span class="input-group-text bg-white border-start-0"><i id="toggle-password" class="bi-eye-slash pointer"></i> </span>
								</div>
							</div>
							<div class="mb-4">
								<div class="input-group">
									<input type="password" class="form-control border-end-0" id="password" name="password_confirmation" placeholder="Confirm new password" required>
									<span class="input-group-text bg-white border-start-0"><i id="toggle-password" class="bi-eye-slash pointer"></i> </span>
								</div>
							</div>
							<div class="mt-4">
								<button class="btn btn-primary">Reset password</button>
							</div>
						</form>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</section>
@endsection