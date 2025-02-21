@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Edit page</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/pages') }}">Pages</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Edit page</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="">
	<form action="{{ url('/admin/page/edit').'/'.$page->id }}" method="post" enctype="multipart/form-data">
		@csrf
		<div class="row">
			<div class="col-lg-9">
				<div class="card border-0 rounded-4 p-4">
					<div class="row">
						<div class="col-12">
							<label class="form-label mt-4">Name<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="text" name="name" value="{{ $page->name }}">
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Description</label>
							<textarea class="form-control" rows="3" name="description" id="description">{{ $page->description }}</textarea>
						</div>

						<div class="col-12">
							<label class="form-label mt-4">Content<span class="text-danger ms-1">*</span></label>
							<textarea class="form-control" rows="4" name="content" id="content">{{ $page->content }}</textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="card border-0 rounded-4 p-4 mt-lg-0 mt-4">
					<h5>Publish</h5>
					<div class="row">
						<div class="col-12">
							<label class="form-label mt-4">Status</label>
							<select class="form-select form-control" name="status">
								<option value="published">Published</option>
								<option value="draft" @if($page->status == "draft") selected @endif>Draft</option>
								<option value="pending" @if($page->status == "pending") selected @endif>Pending</option>
							</select>
						</div>
						<div class="col-12">
							<button class="btn btn-primary btn-sm mt-4"><i class="bi-ok text-white"></i> Save</button>
						</div>
					</div>
				</div>

				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Image</h5>
					<label class="pointer image-input-label" for="image-input">
						<img height="100" class="w-100 shadow-sm rounded-3 mt-4" src="{{ url('/assets/img/pages').'/'.$page->image }}" id="image-preview">
						<input type="file" name="image" class="d-none" id="image-input">
					</label>
				</div>
			</div>
		</div>
	</form>
</section>
<script type="text/javascript" src="{{ asset('/assets/vendor/tinymce/tinymce.min.js') }}"></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		/* Init tinymce */
		tinymce.init({
            selector:'#content',
            relative_urls: false,
            content_style:'body{font-family:"Open Sans", sans-serif;}p,span{color:#67748E;},',
            plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar:'blocks | forecolor backcolor fonts bold italic underline link strikethrough | alignleft aligncenter alignright alignjustify | image |undo redo',
            toolbar_sticky: false,
            automatic_uploads: false,
            toolbar_mode: 'fixed',
            branding: false,
            promotion: false,
            height:300,
            file_picker_callback: function(callback, value, meta){
                var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

                var cmsURL = '/admin/media/window?editor=' + meta.fieldname;

                tinymce.activeEditor.windowManager.openUrl({
                    url: cmsURL,
                    title: 'Filemanager',
                    width: x * 0.8,
                    height: y * 0.8,
                    close_previous: "no",
                    onMessage: (api, message) => {
                        callback(message.content);
                    }
                });
            }
	    });
	});   
</script>
@endsection