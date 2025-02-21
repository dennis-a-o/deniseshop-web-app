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
						<h4 class="fw-bolder text-center mt-4">Verify email</h4>
					</div>
					<div class="card-body px-4">
						 @include('includes.form-error')
						 @include('includes.form-success')
						 <p>Before proceeding, please check your email for a verification link. If you did not receive the email,</p>
						<form action="{{url('/verify-email')}}" method="post">
							@csrf
							<div class="mt-4">
								<button class="btn btn-primary">Verify email</button>
							</div>
						</form>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</section>
@endsection