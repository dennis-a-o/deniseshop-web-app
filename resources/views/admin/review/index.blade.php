@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Reviews</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/products') }}">Products</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Reviews</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="">
	<div class="fluid-container p-0">
		<div class="row">
			<div class="col-12">
				<div class="card border-0 rounded-4">
					<div class="card-header border-0 bg-transparent p-4">
						<div class="d-flex">
							<div class="d-flex pe-2">
								<select class="form-select" id="bulk-action-input">
									<option>Bulk actions</option>
									<option value="approved">Approve</option>
									<option value="rejected">Reject</option>
									<option value="pending">Pending</option>
									<option value="delete">Delete</option>
								</select>
								<button class="btn btn-primary btn-sm ms-2" id="bulk-action-btn">Apply</button>
							</div>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-flush" id="myTable">
								<thead>
									<tr>
										<th>
											<div class="form-check">
												<input class="form-check-input" type="checkbox" id="check-all" name="" value="1">
											</div>
										</th>
										<th>Product</th>
										<th>Customer</th>
										<th>Rating</th>
										<th>Comment</th>
										<th>Status</th>
										<th>Created</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript" src="{{asset('/assets/vendor/js/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		initTable();
	});

	function initTable(){
		let data_table = new DataTable('#myTable', {
			responsive: true,
            autoWidth: false,
            processing: true,
            stateSave: true,
            serverSide: true,
            serverMethod: 'get',
            ajax: {
                url: "/admin/review-list",
            },
             columns: [
				{data:'id'},
	            {data: 'product'},
	            {data: 'customer'},
	            {data: 'star'},
	            {data: 'comment'},
	            {data: 'status'},
	            {data: 'created_at'},
	            {data: 'action'},  
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
                    'targets': [0,1,2,4,7], 
                    'orderable': false, 
                }],
            order:[[0, 'desc']],
		});

		/* Selecting all rows */
		document.getElementById("check-all").addEventListener("click", function(){
			var _this = this; 
			document.querySelectorAll('#check-item').forEach(function(el){
				if (_this.checked) {
					el.checked = true
				}else{
					el.checked = false
				}
			});
		});

		/* Deletee button listiner */
		data_table.on("click", "#delete-review", function(){
			var _this = this;
			confirmDelete("Do you really want to delete this review?",
				function(){
					deleteReview(data_table, _this.dataset.id);
				},
				function(){}
			);
		});

		/* Perform actions on table */
		document.getElementById("bulk-action-btn").addEventListener("click", function(){
			const bulkAction = document.getElementById("bulk-action-input");
			switch(bulkAction.value){
			case 'approved':
				reviewStatus(data_table, "approved");
				break;
			case 'rejected':
				reviewStatus(data_table, "rejected");
				break;
			case 'pending':
				reviewStatus(data_table, "pending");
				break;
			case 'delete':
				confirmDelete("Are you sure you want to delete selected reviews?<br> This cannot be undone.",
					function(){
						deleteReview(data_table, 0, true);
					},
					function(){}
				);
				break;
			}
		});
	}

	async function reviewStatus(table, status){
		var idArr = [];

		document.querySelectorAll("#check-item").forEach(function(el){
			if (el.checked) {
				idArr.push(el.value);
			}
		});
	
		if (idArr.length == 0) return;

		const formData = new FormData();
		for(var i in idArr){
			formData.append("id[]", idArr[i]);
		}
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
				table.ajax.reload(null, false);
				Toast("success", result.message)
			}else{
				console.error(await response.text());
			}
		}catch(error){
			console.error(error);
			Toast("error", "Something went wrong, try again later.");
		}
	}

	async function deleteReview(table, id = 0, isBulk = false){
		var idArr = [];

		if (isBulk) {
			document.querySelectorAll("#check-item").forEach(function(el){
				if (el.checked) {
					idArr.push(el.value);
				}
			});
		}else{
			idArr.push(id);
		}

		if (idArr.length == 0) return;

		const formData = new FormData();
		for(var i in idArr){
			formData.append("id[]", idArr[i]);
		}
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
				table.ajax.reload(null, false);
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