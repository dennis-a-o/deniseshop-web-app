@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">General setting</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
			    <li class="breadcrumb-item active" aria-current="page">General setting</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="profile" style="min-height: 1200px;">
	<div class="row">
		<div class="col-lg-3">
			<div class="card sticky-top border-0 rounded-4 p-4" id="profile-nav">
				<ul class="nav nav-hover-hightlight flex-column">
					<li class="nav-item rounded-3">
						<a class="nav-link active" aria-current="page" href="#logo">
						<i class="bi-image me-2"></i>
						<span>Logo</span>
						</a>
					</li>
					<li class="nav-item rounded-3 mt-2">
						<a class="nav-link active" aria-current="page" href="#contact-info">
						<i class="bi-person-badge me-2"></i>
						<span>Contact info</span>
						</a>
					</li>
					<li class="nav-item rounded-3 mt-2">
						<a class="nav-link active" aria-current="page" href="#gps-location">
						<i class="bi-shield-lock me-2"></i>
						<span>Location</span>
						</a>
					</li>
					<li class="nav-item rounded-3 mt-2">
						<a class="nav-link active" aria-current="page" href="#social-link">
						<i class="bi-watch me-2"></i>
						<span>Social link</span>
						</a>
					</li>
					
				</ul>
			</div>
		</div>
		<div class="col-lg-9 mt-lg-0 mt-4">
			<form action="{{ url('/admin/setting/general') }}" method="post" enctype="multipart/form-data">
				@csrf
				<div class="card border-0 rounded-4 p-4" id="logo">
					<h5>Logo</h5>
					<div class="row">
						<div class="col-4 mt-4">
							<div class="w-100 bg-light rounded-3 shadow-sm">
								<label class="w-100" id="image-input-label">
									<img class="pointer p-2"  src="{{ url('/assets/img/general') }}/{{ $setting['logo_dark'] ?? 'logo_dark.png' }}" height="50" >
									<input type="file" name="logo_dark" id="image-input" style="display: none;">
								</label>
							</div>
							<p class="text-center fw-bolder mt-2">Dark</p>
						</div>
						<div class="col-4 mt-4">
							<div class="w-100 bg-light rounded-3 shadow-sm">
								<label class="w-100" id="image-input-label">
									<img class="pointer p-2"  src="{{ url('/assets/img/general') }}/{{ $setting['logo_light'] ?? 'logo_light.png' }}" height="50" >
									<input type="file" name="logo_light" id="image-input" style="display: none;">
								</label>
							</div>
							<p class="text-center fw-bolder mt-2">Light</p>
						</div>
						<div class="col-4 mt-4">
							<div class="w-100 bg-light rounded-3 shadow-sm">
								<label class="w-100" id="image-input-label">
									<img class="pointe p-2"  src="{{ url('/assets/img/general') }}/{{ $setting['favicon'] ?? 'favicon.png' }}" height="50" >
									<input type="file" name="favicon" id="image-input" style="display: none;">
								</label>
							</div>
							<p class="text-center fw-bolder mt-2">Favicon</p>
						</div>
					</div>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4" id="contact-info">
					<h5>Contact info</h5>
					<div class="row">
						<div class="col-6">
							<label class="form-label mt-4">Email</label>
							<input class="form-control" type="text" name="contact_email" value="{{ $setting['contact_email'] ?? '' }}">
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Email text</label>
							<input class="form-control" type="text" name="contact_email_text" value="{{ $setting['contact_email_text'] ?? '' }}">
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Phone</label>
							<input class="form-control" type="text" name="contact_phone" value="{{ $setting['contact_phone'] ?? '' }}">
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Phone text</label>
							<input class="form-control" type="text" name="contact_phone_text" value="{{ $setting['contact_phone_text'] ?? '' }}">
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Address</label>
							<input class="form-control" type="text" name="contact_address" value="{{ $setting['contact_address'] ?? '' }}">
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Address text</label>
							<input class="form-control" type="text" name="contact_address_text" value="{{ $setting['contact_address_text'] ?? '' }}">
						</div>
					</div>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4" id="gps-location">
					<h5>Location</h5>
					<div class="row">
						<div class="col-12">
							<label class="form-label mt-4">Latitude</label>
							<input class="form-control" type="number" name="latitude" value="{{ $setting['latitude'] ?? '' }}">
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Longitude</label>
							<input class="form-control" type="number" name="longitude" value="{{ $setting['longitude'] ?? '' }}">
						</div>
					</div>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4" id="social-link">
					<h5>Social link</h5>
					<div class="d-flex justify-content-between mt-4">
						<select class="form-select form-control w-25 me-4" id="social-link-icon">
							<option value="bi-facebook">Facebook</option>
							<option value="bi-instagram">Instagram</option>
							<option value="bi-telegram">Telegram</option>
							<option value="bi-linkedin">Linkedin</option>
							<option value="bi-youtube">Youtube</option>
							<option value="bi-pinterest">Pinterest</option>
							<option value="bi-reddit">Reddit</option>
							<option value="bi-tiktok">Tiktok</option>
							<option value="bi-x">Twitter/X</option>
							<option value="bi-link">Other</option>
						</select>
						<input class="form-control me-4" type="text" id="social-link" placeholder="Link e.g https://www.facebook.com/johndoe">
						<button id="add-social-link" type="button" class="btn btn-success btn-sm">Add</button>
					</div>
					<table class="table mt-5" id="social-link-table">
                        <tbody>
                            @if(isset($setting['social_link']))
                            @foreach($setting['social_link'] as $key => $value)
                            <tr>
                                <td><i class="{{$key}}"></i></td>
                                <td><span>{{$value}}</span></td>
                                <td>
                                	<a id="social-link-remove"  href="javascript:">
                                		<span class="bi-x shadow-sm rounded-2 text-danger p-2"></span>
                               		</a>
                            	</td>
                                <input type="hidden" name="social_link[{{$key}}]" value="{{$value}}">
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4" id="location">
					<div class="row">
						<div class="col-12">
							<button class="btn btn-primary btn-sm">Save setting</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		
		document.querySelectorAll('#social-link-remove').forEach(function(el){
	        el.addEventListener('click',function(){
	            this.closest('tr').remove();
	        });
        });

		/* Handle adding of social link */
		document.getElementById("add-social-link").addEventListener("click", function(e){
			let social_icon = document.getElementById('social-link-icon');
            let social_link = document.getElementById('social-link');
            let social_table = document.getElementById('social-link-table');

            if (social_link.value === "" && social_link.value === "") { return;}
            
            let item = document.createElement('tr');
            item.innerHTML = `<td><i class="`+social_icon.value+`"></i></td>
            	<td><span>`+social_link.value+`</span></td>
            	<td><a id="social-link-remove"  href="javascript:">
                    <span class="bi-x-lg shadow-sm rounded-2 text-danger p-2 m-4"></span>
                </a></td>
            	<input type="hidden" name="social_link[`+social_icon.value+`]" value="`+social_link.value+`"><br>`;
            social_table.firstElementChild.appendChild(item);

            item.querySelector("#social-link-remove").addEventListener("click", function(e){
            	this.closest('tr').remove();
            });

            social_link.value = "";
		});

	});
</script>
@endsection