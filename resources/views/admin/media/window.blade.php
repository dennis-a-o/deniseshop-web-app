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
	<section class="media overflow-x-hidden">
		<div class="row">
			<div class="col-12">
				<div class="card border-0">
					<div class="p-4">
						<div class="d-flex justify-content-between">
							<div class="">
								<label class="pointer" for="upload-media-input">
									<a id="upload-btn" class="btn btn-secondary btn-sm px-3 mt-2"><i class="bi-upload text-white"></i>  Upload</a>
									<input id="upload-media-input" type="file" name="upload-media-input" style="display: none;" multiple>
								</label>
								<button id="download-btn" class="btn btn-secondary btn-sm px-3 mt-2">
									<i class="bi-download text-white"></i>  Download
								</button>
								<div class="dropdown d-inline">
									<button class="btn btn-secondary btn-sm dropdown-toggle px-3 mt-2" data-bs-toggle="dropdown" aria-expanded="false">
										<i class="bi-folder text-white"></i>  Create folder
									</button>
									<ul class="dropdown-menu rounded-0 border-0 p-0 shadow">
										<li  data-value="all">
											<div class="input-group">
												<input class="form-control py-1" type="text" name="folder_name" id="folder_name_input" placeholder="Name">
												<button class="btn btn-secondary btn-sm px-2 " id="create-folder-btn">create</button>
											</div>
										</li>
									</ul>
								</div>
								<button id="refresh-btn" class="btn btn-secondary btn-sm px-3 mt-2">
									<i class="bi-sync text-white"></i>  Refresh
								</button>
								<div class="dropdown d-inline">
									<button class="btn btn-secondary btn-sm px-3 dropdown-toggle mt-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
										<i class="bi-filter text-white"></i>
										Filter
									</button>
									<ul class="dropdown-menu rounded-0 border-0 p-0 shadow">
										<li id="filter-media" data-value="">
											<a class="dropdown-item" href="javascript:"><span><i class="bi-sync"></i>All media</span></a>
										</li>
										<li id="filter-media" data-value="image">
											<a class="dropdown-item" href="Javascript:"><span><i class="bi-image me-2"></i>Images</span> </a>
										</li>
										<li id="filter-media" data-value="video">
											<a class="dropdown-item" href="Javascript:"><span><i class="bi-image-alt me-2"></i>Video</span> </a>
										</li>
										<li id="filter-media" data-value="documents">
											<a class="dropdown-item" href="javascript:"><span><i class="bi-folder me-2"></i>Documents</span> </a>
										</li>
									</ul>
								</div>
							</div>
							<div class="pt-2">
								<div class="input-group">
									<input id="search-media-input" type="text" class="form-control py-2" placeholder="Search...">
									<button id="search-media-btn" class="btn btn-sm px-2 border"><i class="bi-search"></i> </button>
								</div>
							</div>
						</div>
					</div>
					<div class="p-4">
						<div class="d-flex justify-content-between">
							<div class="">
								<ol id="media-breadcrumb" class="breadcrumb justify-content-center align-items-center m-0"></ol>
							</div>
							<div class="d-inline-flex">
								<div class="dropdown d-inline">
									<button class="btn btn-outline-secondary btn-sm border dropdown-toggle me-2 px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
										Sort
									</button>
									<ul class="dropdown-menu rounded-0 border-0 p-0 shadow">
										<li id="sort-media" data-value="name" data-order="asc">
											<a class="dropdown-item" href="#"><span>File name - ASC</span> </a></li>
										<li id="sort-media" data-value="name" data-order="desc">
											<a class="dropdown-item" href="#"><span>File name - DESC</span> </a>
										</li>
										<li id="sort-media" data-value="created_at" data-order="asc">
											<a class="dropdown-item" href="#"><span>Uploaded date - ASC</span> </a>
										</li>
										<li id="sort-media" data-value="created_at" data-order="desc">
											<a class="dropdown-item" href="#"><span>Uploaded date - DESC</span></a>
										</li>
										<li id="sort-media" data-value="size" data-order="asc">
											<a class="dropdown-item" href="#"><span>Size - ASC</span> </a>
										</li>
										<li id="sort-media" data-value="size" data-order="desc">
											<a class="dropdown-item" href="#"><span>Size - DESC</span> </a>
										</li>
									</ul>
								</div>
								<div class="dropdown d-inline">
									<button id="action-btn" class="btn btn-outline-secondary btn-sm border dropdown-toggle me-2 px-3 disabled" type="button" data-bs-toggle="dropdown" aria-expanded="false">
										Action
									</button>
									<ul class="dropdown-menu rounded-0 border-0 p-0 shadow">
										<li id="action-media" data-value="preview">
											<a class="dropdown-item" href="#"><span><i class="bi-eye me-2"></i>Preview</span></a>
										</li>
										<li id="action-media" data-value="crop">
											<a class="dropdown-item" href="#"><span><i class="bi-crop me-2"></i>Crop</span></a>
										</li>
										<li id="action-media" data-value="copy_link">
											<a class="dropdown-item" href="#"><span><i class="bi-link me-2"></i>Copy link</span></a>
										</li>
										<li id="action-media" data-value="make_copy">
											<a class="dropdown-item" href="#"><span><i class="bi-files me-2"></i>Make copy</span></a>
										</li>
										<li id="action-media" data-value="download">
											<a class="dropdown-item" href="#"><span><i class="bi-download me-2"></i>Download</span></a>
										</li>
										<li id="action-media" data-value="delete">
											<a class="dropdown-item" href="#"><span><i class="bi-trash me-2"></i>Delete permanent</span></a>
										</li>
										
									</ul>
								</div>
								<div class="input-group">
									<button id="btn-grid" data-value="grid" class="btn btn-outline-secondary btn-sm border px-3" type="button">
										<i class="bi-grid fs-12"></i>
									</button>
									<button id="btn-grid" data-value="list" class="btn btn-outline-secondary btn-sm border px-3" type="button">
										<i class="bi-list fs-12"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
					<div class="media-section border-top">
						<div class="row">
							<div class="col-lg-9 col-lg-8 border-end">
								<div class="media-grid">
									<div class="media-list-container">
										<div class="media-item list-item">
											<div class="media-thumbnail">
												<span class="bi-image me-2"></span>
												<span>Hello there.jpg</span>
											</div>
											<div class="media-description">
												<span class="me-4">233 kB</span>
												<span class="me-4">Hello there</span>
											</div>
										</div>
										<div class="media-item list-item">
											<div class="media-thumbnail">
												<span class="bi-file-text me-2"></span>
												<span>Hello there.jpg</span>
											</div>
											<div class="media-description">
												<span class="me-4">233 kB</span>
												<span class="me-4">Hello there</span>
											</div>
										</div>
										<div class="media-item list-item">
											<div class="media-thumbnail">
												<span class="bi-file-text me-2"></span>
												<span>Hello there.jpg</span>
											</div>
											<div class="media-description">
												<span class="me-4">233 kB</span>
												<span class="me-4">Hello there</span>
											</div>
										</div>
										<div class="media-item list-item">
											<div class="media-thumbnail">
												<span class="bi-file-text me-2"></span>
												<span>Hello there.jpg</span>
											</div>
											<div class="media-description">
												<span class="me-4">233 kB</span>
												<span class="me-4">Hello there</span>
											</div>
										</div>
										
									</div>
									<div class="media-pagination pt-4" style="display: none;">
										<nav aria-label="Page navigation">
											<ul class="pagination justify-content-center">
												<li class="page-item">
													<a class="page-link media-page shadow-none" data-value="previous" href="Javascript:">
														<i class="bi-chevron-left"></i>
													</a>
												</li>
												<li class="page-item">
													<a class="page-link media-page shadow-none" data-value="next" href="Javascript:">
														<i class="bi-chevron-right"></i>
													</a>
												</li>
											</ul>
										</nav>
									</div>
								</div>
							</div>
							<div class="col-lg-3 col-md-4">
								<div class="media-detail">
									<div class="media-thumbnail"></div>
									<div class="media-description p-2 border-top"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="position-fixed bottom-0 start-0 end-0 bg-light">
		<div class="px-4 py-2 text-end">
			<button id="insert-media-link" class="btn btn-danger btn-sm">Insert</button>
		</div>
	</section>
	<footer>
		<button class="scroll_to_top rounded-3">
			<i class="bi-chevron-up"></i>
		</button>
	</footer>
	<script type="text/javascript" src="{{ asset('/assets/vendor/js/cropper.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/assets/admin/js/media.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/vendor/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>