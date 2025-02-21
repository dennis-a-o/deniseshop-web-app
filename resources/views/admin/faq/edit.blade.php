@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Edit faq</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/faqs') }}">Faqs</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Edit faq</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section>
	<div class="container-fluid p-0">
		<form method="post" action="">
			@csrf
			<div class="row">
				<div class="col-lg-9">
					<div class="card border-0 rounded-4 p-4">
						<div class="row">
							<div class="col-12">
								<label class="form-label">Question<span class="text-danger"> *</span></label>
								<input type="text" class="form-control" name="question" value="{{ $faq->question }}" required>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Category<span class="text-danger"> *</span></label>
								<select class="form-select form-control" name="category_id" required>
									<option>Select category</option>
									@foreach($categories as $category)
									<option value="{{ $category->id }}" @if($category->id == $faq->category_id) selected @endif>{{ $category->name }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Answer<span class="text-danger"> *</span></label>
								<textarea class="form-control" name="answer" rows="4" id="answer">{{ $faq->answer }}</textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 mt-4 mt-lg-0">
					<div class="card border-0 rounded-4 p-4">
						<h5>Publish</h5>
						<div class="row">
							<div class="col-12">
								<label class="form-label mt-4">Status<span class="text-danger"> *</span></label>
								<select class="form-select form-control" name="status">
									<option value="published">Published</option>
									<option value="pending" @if($faq->status == "pending") selected  @endif>Pending</option>
									<option value="draft" @if($faq->status == "draft") selected  @endif>Draft</option>
								</select>
							</div>
							<div class="col-12">
								<button class="btn btn-primary btn-sm mt-4">Save</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</section>
<script type="text/javascript" src="{{ asset('/assets/vendor/tinymce/tinymce.min.js') }}"></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		/* Init tinymce */
		tinymce.init({
            selector:'#answer',
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