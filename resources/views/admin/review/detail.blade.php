@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Review detail</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/reviews') }}">Reviews</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Review</li>
			 </ol>
		</div>
	</div>
</section>

<section class="">
	<div class="row">
		<div class="col-lg-8">
			<div class="card border-0 rounded-4 p-4">
				<div class="d-flex justify-content-between">
					<div class="d-flex">
						<img class="rounded-3 shadow-sm me-4" src="{{ url('/assets/img/users').'/'.$user->image }}" width="45" height="45">
						<div>
							<a href="{{ url('/admin/user/edit').'/'.$user->id }}"><h6 class="d-inline">{{ $user->first_name.' '.$user->last_name }}</h6></a>
							<span class="mx-2">|</span>
							<a href="mailto:{{ $user->email }}"><span>{{ $user->email }}</span></a>
							<div>
								@for($i = 1; $i <= 5; $i++)
									@if($i <= $review->star)
									<i class="bi-star-fill text-warning"></i>
									@else
									<i class="bi-star"></i>
									@endif
								@endfor
							</div>
						</div>
					</div>
					<div>
						@if($review->status == "approved")
						<span class="badge badge-success">Approved</span>
						@elseif($review->status == "pending")
						<span class="badge badge-warning">Pending</span>
						@else
						<span class="badge badge-danger">Rejected</span>
						@endif
					</div>
				</div>
				<div class="mt-4">
					<p>{{ $review->comment }}</p>
					
				</div>
				@php $images = Json_decode($review->images) @endphp
				@if($images != null)
				<div class="border-top">
					@foreach($images as $image)
					<img class="rounded-3 shadow-sm me-4 mt-4" src="{{ url('/assets/img/reviews').'/'.$image }}" width="90" height="90">
					@endforeach
				</div>
				@endif
			</div>
		</div>
		<div class="col-lg-4">
			<div class="card border-0 rounded-4 p-4 mt-4 mt-lg-0">
				<div class="card-header border-0 bg-transparent p-0">
					<h5>Product</h5>
				</div>
				<div class="card-body p-0 mt-4">
					<div class="d-flex">
						<img class="rounded-3 shadow-sm me-4" src="{{ url('/assets/img/products').'/'.$product->image }}" width="70" height="70">
						<div>
							<a href="{{ url('/admin/product/edit').'/'.$product->id }}"><h6>{{ $product->name }}</h6></a>
							<div>
								@for($i = 1; $i <= 5; $i++)
									@if($i <= $product->product_rating)
									<i class="bi-star-fill text-warning"></i>
									@else
									<i class="bi-star"></i>
									@endif
								@endfor
								<span>({{$product->review_count}})</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card border-0 rounded-4 p-4 mt-4">
				<h5 class="mb-4">Status</h5>
				<div class="row">
					<div class="col-12">
						<select class="form-select form-control" name="status">
							<option value="approved" @if($review->status == "approved") selected @endif>Approved</option>
							<option value="pending" @if($review->status == "pending") selected @endif>Pending</option>
							<option value="rejected" @if($review->status == "rejected") selected @endif>Rejected</option>
						</select>
						<input type="hidden" name="id" value="{{ $review->id }}">
					</div>
					<div class="col-12">
						<button class="btn btn-sm btn-primary mt-4 me-4" id="update-status">Update</button>
						<button class="btn btn-sm btn-outline-danger mt-4" id="delete-review">Delete</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {

		document.getElementById("update-status").addEventListener("click", function(){
			const id = document.querySelector('input[name="id"]').value;
			const status = document.querySelector('select[name="status"]').value;

			reviewStatus(id, status);
		});

		document.getElementById("delete-review").addEventListener("click", function(){
			const id = document.querySelector('input[name="id"]').value;
			confirmDelete("Do you really want to delete this review?",
				function(){
					deleteReview(id);
				},
				function(){}
			);
		});

	});

	async function reviewStatus(id, status){

		const formData = new FormData();
		formData.append("id[]", id);
		formData.append("status", status);
		formData.append("_token",  document.querySelector('meta[name="csrf-token"]').content);

		try{
			const response = await fetch("{{url('/admin/review/status/edit')}}",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				location.reload();
				Toast("success", result.message)
			}else{
				console.error(await response.text());
			}
		}catch(error){
			console.error(error);
			Toast("error", "Something went wrong, try again later.");
		}
	}

	async function deleteReview(id){

		const formData = new FormData();
		formData.append("id[]", id);
		formData.append("_token",  document.querySelector('meta[name="csrf-token"]').content);

		try{
			const response = await fetch("{{url('/admin/review/delete')}}",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				window.location.href = window.location.origin+"/admin/reviews";
				Toast("success", result.message)
			}else{
				console.error(await response.text());
			}
		}catch(error){
			console.error(error);
			Toast("error", "Something went wrong, try again later.");
		}
	}
</script>
@endsection