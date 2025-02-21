<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	<title>{{ $title ?? config('app.name')}}</title>

	<link rel="shortcut icon" href="{{ asset('assets/img/general/favicon.png') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/css/bootstrap-icons.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendor/css/jquery.dataTables.min.css') }}">
	<link rel="stylesheet" type="text/css" href="http://deniseshop.local/assets/vendor/css/cropper.min.css">

	<link id="theme-link" rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/theme') }}/{{ session('theme') ?? 'light.css' }}">
	<link  id="palette-link" rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/palette') }}/{{ session('palette') ?? 'indigo.css' }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/style.css') }}">

	<script type="text/javascript" src="{{ asset('assets/vendor/js/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/admin/js/main.js') }}"></script>
</head>
<body class="body">
	@include('admin.inc.sidebar')
	<main class="main_content position-relative p-4 rounded-4">
		@include('admin.inc.navbar')
		@yield('content')
	</main>
	<footer>
		<button class="scroll_to_top rounded-3">
			<i class="bi-chevron-up"></i>
		</button>
	</footer>

	
	<script type="text/javascript" src="{{ asset('assets/vendor/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>