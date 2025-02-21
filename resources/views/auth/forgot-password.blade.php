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
						<h4 class="fw-bolder text-center mt-4">Forgot password</h4>
					</div>
					<div class="card-body px-4">
						 @include('includes.form-error')
						 @include('includes.form-success')
						 @if(session('success'))
						 <p>A password reset email has been sent to the email address on file for your account, but may take several minutes to show up in your inbox. Please wait at least 10 minutes before attempting another reset.</p>
						 @else
						<form action="{{url('/forgot-password')}}" method="post">
							@csrf
							<div class="mb-4">
								<input class="form-control" type="text" name="email" placeholder="Email" required>
							</div>
							
							<div class="mt-4">
								<button class="btn btn-primary">Request reset</button>
							</div>
						</form>
						@endif
					</div>
					
				</div>
			</div>
		</div>
	</div>
</section>
@endsection