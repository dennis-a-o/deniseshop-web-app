@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Edit user</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/users') }}">Users</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Edit user</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="">
	<form action="{{ url('/admin/user/edit').'/'.$user->id }}" method="post" enctype="multipart/form-data">
		@csrf
		<div class="row">
			<div class="col-lg-9">
				<div class="card border-0 rounded-4 p-4">
					<div class="row">
						<div class="col-6">
							<label class="form-label mt-4">First name<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="text" name="first_name" value="{{ $user->first_name }}" required>
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Last name<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="text" name="last_name" value="{{ $user->last_name }}" required>
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Email<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="text" name="email" value="{{ $user->email }}" required>
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Role<span class="text-danger ms-1">*</span></label>
							<select class="form-select form-control" name="role">
								<option value="User">User</option>
								<option value="Manager" @if($user->role == "Manager") selected @endif>Manager</option>
								<option value="Admin" @if($user->role == "Admin") selected @endif>Admin</option>
							</select>
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Verification<span class="text-danger ms-1">*</span></label>
							<select class="form-select form-control" name="verification">
								<option value="verify">Verified</option>
								<option value="unverify" @if($user->email_verified_at == null) selected @endif>Unverified</option>
							</select>
						</div>
						<div class="col-12">
							<div class="form-check mt-4">
								<input id="changePasword" class="form-check-input" type="checkbox" role="switch"  name="none" data-bs-toggle="collapse" href="#collapsePassword" aria-expanded="false" aria-controls="collapseExample">
								<label class="form-check-label ms-1" for="changePasword">Change password?</label>
							</div>
						</div>
						<div class=" col-12 collapse" id="collapsePassword">
							<label class="form-label mt-4">Password<span class="text-danger ms-1">*</span></label>
							<div class="input-group">
								<input type="password" class="form-control border-end-0" name="password">
								<span class="input-group-text bg-white border-start-0"><i id="toggle-password" class="bi-eye-slash pointer"></i> </span>
							</div>
						</div>
					</div>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Address</h5>
					<div class="row">
						<div class="col-12">
							<button id="add-new-address" type="button" class="btn btn-gradient-primary btn-sm mt-4"><i class="bi-plus text-white"></i> New address</button>
						</div>
						<div class="col-12">
							@if(count($addresses))
							<table id="address-table" class="table mt-4">
								<thead>
									<tr>
										<th>Address</th>
										<th>Zip code</th>
										<th>Country</th>
										<th>State</th>
										<th>City</th>
										<th>Type</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@foreach($addresses as $address)
									<tr>
										<td>{{ $address->address }}</td>
										<td>{{ $address->zip_code }}</td>
										<td>{{ $address->country }}</td>
										<td>{{ $address->state}}</td>
										<td>{{ $address->city }}</td>
										<td><span class="badge badge-info">{{ $address->type }}</span></td>
										<td>
											<a id="edit-address" href="Javascript:">
												<span id="address-data" style="display: none;">
													{{ $address->id }}=
													{{ $address->user_id }}=
													{{ $address->name }}=
													{{ $address->phone }}=
													{{ $address->zip_code }}=
													{{ $address->email }}=
													{{ $address->address }}=
													{{ $address->country }}=
													{{ $address->state}}=
													{{ $address->city }}=
													{{ $address->type }}
												</span>
				                                <span class="me-2">
				                                    <i class="bi-pencil"></i>
				                                </span>
				                            </a>
				                            <a  href="Javascript:" id="delete-address" data-id="{{ $address->id }}" data-userid="{{ $address->user_id }}">
				                                <span class="p-2">
				                                    <i class="bi-trash"></i>
				                                </span>
				                            </a>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
							@endif
						</div>
					</div>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Payments</h5>
					<div class="row">
						<div class="col-12">
							@if(count($payments))
							<table id="address-table" class="table mt-4">
								<thead>
									<tr>
										<th>Order</th>
										<th>Transaction id</th>
										<th>Amount</th>
										<th>Payment method</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@foreach($payments as $payment)
									<tr>
										<td>
											<a href="{{ url('/admin/order/edit').'/'.$payment->order_id }}">
												{{ $payment->code }}
											</a>
										</td>
										<td>{{ $payment->transaction_id }}</td>
										<td>{{ $payment->currency.$payment->amount }}</td>
										<td>{{ $payment->name }}</td>
										<td>
											@if($payment->status == "completed")
												<span class="badge badge-success">{{ $payment->status }}</span>
											@else
											   <span class="badge badge-info">{{ $payment->status }}</span>
											@endif
										</td>
										<td>
											<a  href="{{ url('/admin/payment/edit').'/'.$payment->id }}" target="_blank">        
				                                <i class="bi-eye"></i> 
				                            </a>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
							@endif
						</div>
					</div>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Reviews</h5>
					<div class="row">
						<div class="col-12">
							<div class="table-responsive">
								<table class="table" id="reviewTable">
									<thead>
										<tr>
											<th>Product</th>
											<th>Rating</th>
											<th>comment</th>
											<th>Status</th>
											<th>Created at</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
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
								<option value="activated">Activated</option>
								<option value="locked" @if($user->status == "locked") selected @endif>Locked</option>
							</select>
						</div>
						<div class="col-12">
							<button class="btn btn-primary btn-sm mt-4"><i class="bi-ok text-white"></i> Update</button>
						</div>
					</div>
				</div>

				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Image</h5>
					<label class="pointer image-input-label" for="image-input">
						<img height="100" class="w-100 shadow-sm rounded-3 mt-4" src="{{ url('/assets/img/users').'/'.$user->image }}" id="image-preview">
						<input type="file" name="image" class="d-none" id="image-input">
					</label>
				</div>
			</div>
		</div>
	</form>
</section>
<script type="text/javascript" src="{{asset('/assets/vendor/js/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event){

		initReviewTable();

		document.getElementById("add-new-address").addEventListener("click", function(){
			addressFormModal();
		});

		document.querySelectorAll("#edit-address").forEach(function(el){
			el.addEventListener("click", function(){
				var data = el.querySelector("#address-data").innerHTML; 
				addressFormModal("Edit address", data);
			});
		});
		
		document.querySelectorAll("#delete-address").forEach(function(el){
			el.addEventListener("click", function(){
				var _this = this;
				confirmDelete(
					"Do you really want to delete this address?",
					function(){
						deleteAddress(_this);
					},
					function(){/*cancel callback no action*/},
					"Confirm delete"
				);
			});
		});

		function initReviewTable(){
			let data_table = new DataTable('#reviewTable', {
				responsive: true,
	            autoWidth: false,
	            searching: false,
	            processing: true,
	            stateSave: true,
	            serverSide: true,
	            serverMethod: 'get',
	            ajax: {
	                url: "/admin/user/review/{{ $user->id }}",
	            },
	             columns: [
		            {data: 'product'},
		            {data: 'star'},
		            {data: 'comment'},
		            {data: 'status'},
		            {data: 'created_at'},
	            ],
				language: {
	                oPaginate: {
	                    sNext: '›',
	                    sPrevious:'‹',
	                    sFirst: '‹‹',
	                    sLast: '››'
	                }
	            },
	            columnDefs: [ {
	                    'targets': [0,1,2,3,4],
	                    'orderable': false, 
	                }],
	            order:[[4, 'desc']],
			});
		} 

		function addressFormModal(title = "Add address", data = ""){
			var dialog = document.createElement("div");
			dialog.classList.add("modal", "fade");
			dialog.setAttribute("data-bs-backdrop","static");
			dialog.setAttribute("tabindex","-1");
			dialog.setAttribute("aria-labelledby","exampleModalLabel");
			dialog.setAttribute("aria-hidden","true");
			dialog.innerHTML = `<div class="modal-dialog z-3">
			    	<div class="modal-content">
			      		<div class="modal-header bg-info border-0">
			        		<h1 class="modal-title fs-6 text-white" id="exampleModalLabel">Add address</h1>
			        		<button type="button" class="close border-0 bg-transparent" data-bs-dismiss="modal" aria-label="Close">
			        		<i class="bi-x-lg text-white"></i></button>
			      		</div>
			      		<form id="add-address-form" method="post">
			      			<input type="hidden" class="form-control" name="id" value="">
			      			<input type="hidden" class="form-control" name="user_id" value="{{ $user->id}}">
			      			@csrf
				     		<div class="modal-body">
			     				<div class="row">
				     				<div class="col-6">
				     					<label class="col-form-label">Name</label>
            							<input type="text" class="form-control" name="name" value="" required>
				     				</div>
				     				<div class="col-6">
				     					<label class="col-form-label">Phone</label>
            							<input type="tel" class="form-control" name="phone" value="" required>
				     				</div>
				     				<div class="col-6">
				     					<label class="col-form-label mt-4">Zip code</label>
            							<input type="text" class="form-control" name="zip_code" value="" required>
				     				</div>
				     				<div class="col-6">
				     					<label class="col-form-label mt-4">Email</label>
            							<input type="email" class="form-control" name="email" value="" required>
				     				</div>
				     				<div class="col-12">
				     					<label class="col-form-label mt-4">Address</label>
            							<input type="text" class="form-control" name="address" value="" required>
				     				</div>
				     				<div class="col-12">
				     					<label class="col-form-label mt-4">Country</label>
            							<select class="form-select form-control" id="country" name="country" required>
            								@foreach(Util::$countries as $country)
            								<option value="{{ $country }}">{{ $country }}</option>
            								@endforeach
            							</select>
				     				</div>
				     				<div class="col-6">
				     					<label class="col-form-label mt-4">State</label>
            							<input type="text" class="form-control" name="state" value="" required>
				     				</div>
				     				<div class="col-6">
				     					<label class="col-form-label mt-4">City</label>
            							<input type="text" class="form-control" name="city" value="" required>
				     				</div>
				     				<div class="col-12">
				     					<label class="col-form-label mt-4">Type</label>
            							<select class="form-select form-control" id="type" name="type" required>
            								<option value="billing">Billing</option>
            								<option value="Shipping">Shipping</option>
            							</select>
				     				</div>
			     				</div>
				     		</div>
				      		<div class="modal-footer border-0">
				        		<button type="button" id="cancel" class="btn btn-sm btn-warning" data-bs-dismiss="modal">Cancel</button>
				        		<button type="submit" class="btn btn-sm btn-info text-white" >Add</button>
				      		</div>
			      		</form>
			    	</div>
			  	</div>`;

			document.body.appendChild(dialog);

			var isUpdate = false;

			if (data != "") {//for update
				isUpdate = true;
				var data = data.split("=");
				dialog.querySelector('input[name="id"]').value = data[0].trim();
				dialog.querySelector('input[name="user_id"]').value = data[1].trim();
				dialog.querySelector('input[name="name"]').value = data[2].trim();
				dialog.querySelector('input[name="phone"]').value = data[3].trim();
				dialog.querySelector('input[name="zip_code"]').value = data[4].trim();
				dialog.querySelector('input[name="email"]').value = data[5].trim();
				dialog.querySelector('input[name="address"]').value = data[6].trim();
				dialog.querySelector('#country').value = data[7].trim();
				dialog.querySelector('input[name="state"]').value = data[8].trim();
				dialog.querySelector('input[name="city"]').value = data[9].trim();
				dialog.querySelector('#type').value = data[10].trim();
			}

		 	var myModal = new bootstrap.Modal(dialog);
		 	myModal.show();

		 	dialog.querySelector(".close").addEventListener("click", function(){
		 		myModal.hide();
				dialog.remove();
			});

			dialog.querySelector("#cancel").addEventListener("click", function(){
				myModal.hide();
				dialog.remove();
			});

			dialog.querySelector("#add-address-form").addEventListener("submit", function(e){
				e.preventDefault();
				if (isUpdate) {
					//is update
					saveAddress(this, myModal, dialog, true);
				}else{
					saveAddress(this, myModal, dialog);
				}
				
			});
		}
	
		async function saveAddress(form, modal, dialog, isUpdate = false){
			const formData = new FormData(form);
			var url = "";

			if (isUpdate) {
				url = "{{url('/admin/user/address/update')}}";
			}else{
				url = "{{url('/admin/user/address/create')}}";
			}

			try{
				const response = await fetch(url,{
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
						if(document.getElementById("address-table") != null){
							if (!isUpdate) {
								var table = document.getElementById("address-table").querySelector("tbody");
								var row = document.createElement("tr");
								row.innerHTML = result.data;

								table.insertBefore(row, table.firstElementChild);
								
								form.reset();

								Toast("success", result.message);

								modal.hide();
								dialog.remove();
							}else{
								document.location.reload();
							}
						}else{
							document.location.reload();
						}
					}
				}else{
					console.error(await response.text());
				}
			}catch(error){
				console.error(error);
				Toast("error", "Something went wrong, try again later.");
			}
		}

		async function deleteAddress(el){
			const formData = new FormData();
			formData.append("id", el.dataset.id);
			formData.append("_token",  document.querySelector('meta[name="csrf-token"]').content);

			try{
				const response = await fetch("{{ url('/admin/user/address/delete') }}",{
					method: "POST",
					mode: "cors",
					cache: "no-cache",
					credentials: "same-origin",
					body: formData,
				});

				if (response.ok) {
					const result = await response.json();
					el.closest("tr").remove();
					Toast("success", result.message);
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