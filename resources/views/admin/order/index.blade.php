@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Orders</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/products') }}">Products</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Orders</li>
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
									<option value="pending">Change to pending</option>
									<option value="confirmed">Change to confirmed</option>
									<option value="processing">Change to processing</option>
									<option value="completed">Change to completed</option>
									<option value="cancelled">Change to cancelled</option>
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
										<th>Order</th>
										<th>Customer</th>
										<th>Amount</th>
										<th>Payment Status</th>
										<th>Payment Method</th>
										<th>Status</th>
										<th>Date</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<div class="form-check">
												<input class="form-check-input" type="checkbox" id="check-item" name="" value="1">
											</div>
										</td>
										<td>
											<a href=""><h6>#2022FF</h6></a>
										</td>
										<td>
											<div class="d-flex align-items-center">
												<img class="rounded-circle shadow-sm me-2" src="/assets/img/users/default.jpg" width="30" height="30">
												<span>Jonh Doe</span>
											</div>
										</td>
										<td>
											Ksh 254.00
										</td>
										<td>
											<span class="badge badge-success">Completed</span>
										</td>
										<td>Cash On Delivery (COD)</td>
										<td><span class="badge badge-info">Processing</span></td>
										<td>15/03/2024</td>
										<td>
											<a href="">
												<span class="">
													<i class="bi-eye"></i>
												</span>
											</a>
											<a href="" id="delete-review" data-id="0">
												<span class="ms-3">
													<i class="bi-trash"></i>
												</span>
											</a>
										</td>
									</tr>
								</tbody>
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
                url: "/admin/order-list",
            },
             columns: [
				{data:'id'},
			    {data: 'code'},
	            {data: 'customer_name'},
	            {data: 'amount'},
	            {data: 'payment_status'},
	            {data: 'payment_method'},
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
                    'targets': [0,7],
                    'orderable': false, 
                }],
            order:[[1, 'desc']],
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
		data_table.on("click", "#delete-order", function(){
			var _this = this;
			confirmDelete("Do you really want to delete this order?",
				function(){
					deleteOrder(data_table, _this.dataset.id);
				},
				function(){}
			);
		});

		/* Perform actions on table */
		document.getElementById("bulk-action-btn").addEventListener("click", function(){
			const bulkAction = document.getElementById("bulk-action-input");
			switch(bulkAction.value){
			case 'pending':
				orderStatus(data_table, "pending");
				break;
			case 'confirmed':
				orderStatus(data_table, "confirmed");
				break;
			case 'processing':
				orderStatus(data_table, "processing");
				break;
			case 'completed':
				orderStatus(data_table, "completed");
				break;
			case 'cancelled':
				orderStatus(data_table, "cancelled");
				break;
			case 'delete':
				confirmDelete("Are you sure you want to delete selected orders?<br> This cannot be undone.",
					function(){
						deleteOrder(data_table, 0, true);
					},
					function(){}
				);
				break;
			}
		});
	}

	async function orderStatus(table, status){
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
			const response = await fetch("{{url('/admin/order/status/edit')}}",{
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

	async function deleteOrder(table, id = 0, isBulk = false){
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
			const response = await fetch("{{url('/admin/order/delete')}}",{
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