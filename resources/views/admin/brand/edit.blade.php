@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Create brand</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/brands') }}">Brands</a></li>
				<li class="breadcrumb-item active" aria-current="page">Create brand</li>
			</ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section>
	<div class="fluid-container p-0">
		<form action="{{ url('/admin/brand/edit').'/'.$brand->id }}" method="post" enctype="multipart/form-data">
			@csrf
			<div class="row">
				<div class="col-lg-9">
					<div class="card border-0 rounded-4 p-4">
						<div class="row">
							<div class="col-12">
								<label class="form-label">Name</label>
								<input class="form-control"  type="text" name="name" value="{{ $brand->name }}" required>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Website(url)</label>
								<input class="form-control"  type="url" name="url" value="{{ $brand->url }}" required>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Description</label>
								<textarea class="form-control" rows="4" name="description" id="description">
								{{ $brand->description }}
								 </textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 mt-4 mt-lg-0">
					<div class="card border-0 rounded-4 p-4">
						<h5>Publish</h5>
						<div class="row">
							<div class="col-12">
								<label class="form-label mt-4">Status</label>
								<select class="form-select form-control" name="status">
									<option value="draft">Draft</option>
									<option value="published" @if($brand->status == "published") selected @endif>Published</option>
								</select>
							</div>
							<div class="col-12">
								<div class="form-check mt-4">
									<input class="form-check-input" type="checkbox" value="1" name="is_featured"  id="is_featured" @if($brand->is_featured) checked @endif>
									<label class="form-check-label" for="is_featured">
										Is featured?
									</label>
								</div>
							</div>
							<div class="col-12">
								<button class="btn btn-sm btn-primary mt-4">Save</button>
							</div>
						</div>
					</div>
					<div class="card border-0 rounded-4 p-4 mt-4">
						<h5 class="fw-bold">Logo</h5>
						<div class="row">
							<div class="col-12">
								<label class="pointer image-input-label">
									<img class="rounded-4 shadow-sm mt-4" src="{{ url('/assets/img/brands'.'/'.$brand->logo) }}" width="150" height="75">
									<input class="form-control d-none" type="file" name="logo" id="image-input">
								</label>
								
							</div>
						</div>
					</div>
					<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Categories</h5>
					<div>
						<ul class="nav flex-scolumn overflow-auto" style="height:300px; padding-left: 2px;">
							@foreach($categories as $category)
                            <li class="nav-item p-0 mt-2 border-0 w-100">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="category[]" id="check{{ $category->id }}" value="{{ $category->id }}" @if(in_array($category->id, $brandCategories)) checked @endif>
                                    <label class="form-check-label ms-2" for="check{{ $category->id }}">{{ $category->name }}</label>
                                </div>
                             </li>
		                        @foreach($category->categories as $sub_category)
		                        <li class="nav-item p-0 ps-4 mt-2 border-0 w-100">
		                            <div class="form-check">
		                                <input class="form-check-input" type="checkbox" name="category[]" id="check{{ $sub_category->id }}" value="{{ $sub_category->id }}" @if(in_array($sub_category->id, $brandCategories)) checked @endif>
		                                <label class="form-check-label ms-2" for="check{{ $sub_category->id }}">{{ $sub_category->name }}</label>
		                            </div>
		                         </li>
			                        @foreach($sub_category->categories as $child_category)
			                        <li class="nav-item p-0 ps-5 mt-2 border-0 w-100">
			                            <div class="form-check">
			                                <input class="form-check-input" type="checkbox" name="category[]" id="check{{ $child_category->id }}" value="{{ $child_category->id }}" @if(in_array($child_category->id, $brandCategories)) checked @endif>
			                                <label class="form-check-label ms-2" for="check{{ $child_category->id }}">{{ $child_category->name }}</label>
			                            </div>
			                         </li>
			                        @endforeach
		                        @endforeach
                            @endforeach
                        </ul>
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
	/*-------------------
	Init tinymce
	-------------------*/
	tinymce.init({
        selector:'#description',
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
        height:400,
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