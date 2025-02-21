<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ $seo_title ?? config('app.name') }} Installer</title>

	<link rel="shortcut icon" href="{{ asset('assets/img/general/favicon.png') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/css/bootstrap-icons.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/installer/css/style.css') }}">
</head>
<body>
	@yield('content')
	<script type="text/javascript" src="{{ asset('assets/vendor/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>