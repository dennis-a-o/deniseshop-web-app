<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ $seo_title ?? config('app.name') }}</title>
	<link rel="canonical" href="{{$seo_url ?? url()->full()}}">
	<meta name="description" content="{{ $seo_description ?? ''}}">
	<meta name="keywords" content="{{$seo_keywords ?? ''}}">
	<meta property="og:site_name" content="{{ $seo_sitename ?? config('app.name') }}">
	<meta property="og:image" content="{{ $seo_image ?? url('/assets/img/page/default.jpg')}}">
	<meta property="og:description" content="{{ $seo_description ?? '' }}">
	<meta property="og:url" content="{{ $seo_url ?? url()->full()}}">
	<meta property="og:title" content="{{ $seo_title ?? '' }}">
	<meta property="og:type" content="{{$seo_type ?? ''}}">
	<meta name="twitter:title" content="{{$seo_title ?? ''}}">
	<meta name="twitter:description" content="{{$seo_description ?? ''}}">

	<link rel="shortcut icon" href="{{ asset('assets/img/general/favicon.png') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/auth/css/style.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/css/bootstrap-icons.min.css') }}">
</head>
<body>
	<main class="py_150">
		@yield('content')
	</main>
	<footer class="footer">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="text-center pb-4">
						<span>&copy; {{date("Y")}} {{config('app.name')}}</span>
						<span class="px-2">|</span>
						<a href="{{url('/terms-and-conditions')}}"><span>Terms of use</span></a>
						<span class="px-2">|</span>
						<a href="{{url('/privacy-policy')}}"><span>Privacy policy</span></a>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<script type="text/javascript" src="{{ asset('assets/auth/js/main.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/vendor/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>