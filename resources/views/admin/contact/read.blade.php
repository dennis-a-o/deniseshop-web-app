@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Read contact</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/contacts') }}">Contacts</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Read contacts</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="">
	<div class="row">
		<div class="col-lg-9">
			<div class="card border-0 rounded-4 p-4">
				<div class="row">
					<h5 class="mb-4">Contact information</h5>
					<div class="col-12">
						<p>Time: <i>{{ date("F j, Y H:m:s",strtotime($contact->created_at)) }}</i></p>
						<p>Name: <i>{{ $contact->name }}</i> </p>
						<p>Email: <i><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></i></p>
						<p>Phone: <i><a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a></i></p>
						<p>Subject: <i>{{ $contact->subject }}</i> </p>
						<p>Message:</p>
						<div class="rounded-3 bg-light p-4">
							<p>{!! $contact->message !!}</p>
						</div>
					</div>
				</div>
			</div>
			<div class="card border-0 rounded-4 p-4 mt-4">
				<div class="row">
					<h5 class="">Replies</h5>
					<div class="col-12">
						@if(count($replies))
							@foreach($replies as $reply)
							<div class="mt-4">
								<p>Time: <i>{{ date("F j, Y H:m:s",strtotime($reply->created_at)) }}</i></p>
								<p>Message:</p>
								<div class="rounded-3 bg-light p-4">
									<p class="m-0">{!! $reply->message !!}</p>
								</div>
							</div>
							@endforeach
						@else
						<p>No replies yet.</p>
						@endif
						<button id="reply-btn" class="btn btn-outline-primary btn-sm mt-4">Reply</button>
					</div>
					<form class="d-none" id="reply-form" method="post">
						@csrf
						<div class="col-12 mt-4">
							<input type="hidden" name="id" value="{{ $contact->id }}">
							<textarea class="form-control" rows="4" name="message" id="message"></textarea>
						</div>
						<div class="col-12">
							<button class="btn btn-success btn-sm mt-4">Send</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-lg-3">
			<form action="{{ url('/admin/contact/update').'/'.$contact->id }}" method="post">
				@csrf
				<input type="hidden" name="id" value="{{ $contact->id }}">
				<div class="card border-0 rounded-4 p-4 mt-lg-0 mt-4">
					<h5>Publish</h5>
					<div class="row">
						<div class="col-12">
							<label class="form-label mt-4">Status</label>
							<select class="form-select form-control" name="status">
								<option value="read">Read</option>
								<option value="unread" @if($contact->status == "unread") selected @endif>Unread</option>
							</select>
						</div>
						<div class="col-12">
							<button class="btn btn-primary btn-sm mt-4"><i class="bi-ok text-white"></i> Update</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>
<script type="text/javascript" src="{{ asset('/assets/vendor/tinymce/tinymce.min.js') }}"></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		/*Init tinymce*/
		tinymce.init({
	            selector:'#message',
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
	    });

	    var replyForm = document.getElementById("reply-form"); 

	    replyForm.addEventListener("submit", function(e){
	    	e.preventDefault();
	    	sendReply(this);
	    });

	    document.getElementById("reply-btn").addEventListener("click", function(){
	    	replyForm.classList.toggle("d-none");//show hide using boostrap class
	    });

	    async function sendReply(form){
	    	const formData = new FormData(form);
	    	try{
				const response = await fetch("{{url('/admin/contact/reply')}}",{
					method: "POST",
					mode: "cors",
					cache: "no-cache",
					credentials: "same-origin",
					body: formData,
				});

				if (response.ok) {
					const result = await response.json();
					if (result.error) {
						Toast("error", result.message);
					}else{
						const replyList = form.previousElementSibling;
						var item = document.createElement("div");
						item.innerHTML = result.data;
						replyList.insertBefore(item, replyList.childNodes[0]);

						Toast("success", result.message);
						form.reset();
					}
				}else{
					console.error(await response.text());
				}
			}catch(error){
				console.error(error);
				Toast("error", "Something went wrong, try again later.");
			}
	    }
	});   
</script>
@endsection