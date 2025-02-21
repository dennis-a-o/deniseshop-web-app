@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Products</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Products</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="">
	<div class="container-fluid p-0">
		<div class="card border-0 rounded-4">
			<div class="card-header p-4 pb-0 border-0 bg-transparent">
				<div class="d-flex">
					<div class="d-flex pe-2">
						<select class="form-select" id="bulk-action-input">
						  <option>Bulk actions</option>
						  <option value="published">Publish</option>
						  <option value="draft">Draft</option>
						   <option value="pending">Pending</option>
						  <option value="delete">Delete</option>
						</select>
						<button class="btn btn-primary btn-sm ms-2" id="bulk-action-btn">Apply</button>
					</div>
					<div class="d-flex">
						<select class="form-select" id="filter-input">
						  <option value="">Filter by status</option>
						  <option value="stock_status#in_stock">In stock</option>
						  <option value="stock_status#out_stock">Out stock</option>
						  <option value="status#draft">Draft</option>
						  <option value="status#pending">Pending</option>
						  <option value="status#published">Published</option>
						  <option disabled><s>Filter by type</s></option>
						  <option value="type#physical">Physical</option>
						  <option value="type#digital">Digital</option>
						  @if($categories)
						   <option disabled><b>Filter by category</b></option>
						  @foreach($categories as $category)
						   <option value="categories.name#{{$category->name}}">{{$category->name}}</option>
						  @endforeach
						  @endif
						</select>
						<button class="btn btn-primary btn-sm ms-2" id="filter-btn">Filter</button>
					</div>
					<div class="ms-auto">
						<a class="btn btn-gradient-primary btn-sm" href="{{url('/admin/product/create')}}"><i class="bi-plus pe-2 text-white"></i>New product</a>
						<button class="btn btn-outline-primary btn-sm ms-2" id="export-btn">Export</button>
					</div>
				</div>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table id="myTable" class="table table-flush">
						<thead>
							<tr>
								<th>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" id="check-all" name="" value="1">
									</div>
								</th>
								<th>Product</th>
								<th>Category</th>
								<th>price</th>
								<th>Sku</th>
								<th>Stock</th>
								<th>Quantity</th>
								<th>Created_at</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript" src="{{asset('/assets/vendor/js/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/vendor/js/xlsx.mini.js') }}"></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		initTable();
	});

	function initTable(){
		var url = "/admin/product-list";
		var requestData = {"mine":0};

		let data_table = new DataTable('#myTable', {
			responsive: true,
            autoWidth: false,
            processing: true,
            stateSave: true,
            serverSide: true,
            serverMethod: 'get',
            ajax: {
                url: url,
                data: function(data){
                     return $.extend({},data,requestData);
                },
                error:function(err, status){
                    console.log(err);
                },
            },
             columns: [
				{data:'id'},
	            {data: 'name'},
	            {data: 'category'},
	            {data: 'price'},
	            {data: 'sku'},
	            {data: 'stock_status'},
	            {data: 'quantity'},
	            {data: 'created_at'},
	            {data: 'status'},
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
                    'targets': [0,9],
                    'orderable': false, 
                }],
            order:[[7, 'desc']],
		});

		/* Filter butotn action */
		document.getElementById("filter-btn").addEventListener("click", function(){
			const filter = document.getElementById("filter-input");
			const filterData = filter.value.split("#");
			requestData = {"filter": filterData[0],"filterValue": filterData[1]};
			data_table.ajax.reload();
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
		data_table.on("click", "#delete-product", function(){
			var _this = this;
			confirmDelete("Do you really want to delete this product?",
				function(){
					deleteProduct(data_table, _this.dataset.productid);
				},
				function(){}
			);
		});

		/* Perform actions on table */
		document.getElementById("bulk-action-btn").addEventListener("click", function(){
			const bulkAction = document.getElementById("bulk-action-input");
			switch(bulkAction.value){
			case 'published':
				updateStatus(data_table,"published");
				break;
			case 'pending':
				updateStatus(data_table,"pending");
				break;
			case 'draft':
				updateStatus(data_table,"draft");
				break;
			case 'delete':
				confirmDelete("Are you sure you want to delete this products?<br> This cannot be undone.",
					function(){
						deleteProduct(data_table, 0, true);
					},
					function(){}
				);
				break;
			}
		});

		/* Export table to excel */
		document.getElementById("export-btn").addEventListener("click", function(){
	        let table = document.getElementById('myTable');
	        var excelFile = XLSX.utils.table_to_book(table, {sheet: "sheet1"});
	        XLSX.write(excelFile, {bookType: "xlsx", bookSST: true, type: 'base64'});
	        XLSX.writeFile(excelFile, 'Contacts'+'.'+"xlsx");
		});
	}

	async function deleteProduct(table, id = 0, isBulk = false){
		var idArr = [];
		if (isBulk) {
			var rows = document.querySelectorAll("#check-item");
			rows.forEach(function(el){
				if (el.checked) {
					idArr.push(el.value);
				}
			});
		}else{
			idArr.push(id)
		}

		if (idArr.length <= 0) { return; }

		const formData = new FormData();

		for(var i in idArr){
			formData.append("id[]", idArr[i]);
		}
		formData.append("_token", "{{ csrf_token() }}");

		try{
			const response = await fetch("{{url('/admin/product/delete')}}",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				Toast("success", result.message)
				table.ajax.reload(null, false);
			}else{
				console.error(await response.text());
			}
		}catch(error){
			Toast("error", "Something went wrong, try again later.");
		}
	}

	async function updateStatus(table, status){
		var idArr = [];
		var rows = document.querySelectorAll("#check-item");

		rows.forEach(function(el){
			if (el.checked) {
				idArr.push(el.value);
			}
		});

		if (idArr.length <= 0) { return; }

		const formData = new FormData();

		for(var i in idArr){
			formData.append("id[]", idArr[i]);
		}
		formData.append("status", status);
		formData.append("_token", "{{ csrf_token() }}");

		try{
			const response = await fetch("{{url('/admin/product/status')}}",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				Toast("success", result.message);
				table.ajax.reload(null, false);
			}else{
				Toast("error", "Something went wrong, try again later.");
			}
		}catch(error){
			Toast("error", "Something went wrong, try again later.");
		}
	}

</script>
@endsection