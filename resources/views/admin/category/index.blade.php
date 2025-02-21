@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Product categories</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/products') }}">Products</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Product category</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="">
	<div class="fluid-container p-0">
		<div class="row">
			<div class="col-lg-4">
				<div class="card border-0 rounded-4 p-4">
					<h5>New Category</h5>
					<form id="categoryForm" action="{{ url('/admin/product-tag/create') }}" method="post" enctype="multipart/form-data">
						@csrf
						<div class="row">
							<div class="col-12">
								<label class="form-label mt-4">Name</label>
								<input class="form-control"  type="text" name="name" required>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Parent</label>
								<select class="form-select form-control" name="parent">
									<option style="padding-left: 20px ;" value="0">None</option>
									@if(count($categories))
									@foreach($categories as $category)
									<option  value="{{ $category->id }}">{{ $category->name }}</option>
										@foreach($category->categories as $sub_category)
										<option  value="{{ $sub_category->id }}">|__{{ $sub_category->name }}</option>
										@endforeach
									@endforeach
									@endif
								</select>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Description</label>
								<textarea class="form-control" rows="5" name="description"> </textarea>
							</div>
							<div class="col-6">
								<label class="form-label mt-4">Image</label><br>
								<label class="pointer image-input-label">
									<img class="rounded-4 shadow-sm" src="" width="75" height="75">
									<input class="form-control d-none" type="file" name="image" id="image-input">
								</label>
								
							</div>
							<div class="col-6">
								<label class="form-label mt-4">Icon</label><br>
								<label class="pointer image-input-label">
									<img class="rounded-4 shadow-sm" src="" width="75" height="75">
									<input class="form-control d-none" type="file" name="icon" id="image-input">
								</label>
							</div>
							<div class="col-12">
								<button class="btn btn-sm btn-primary mt-4">Save</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="col-lg-8">
				<div class="card border-0 rounded-4">
					<div class="card-header border-0 bg-transparent p-4">
						<div class="d-flex">
							<div class="d-flex pe-2">
								<select class="form-select" id="bulk-action-input">
									<option>Bulk actions</option>
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
										<th>Name</th>
										<th>Description</th>
										<th>Count</th>
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
                url: "/admin/product-category-list",
            },
             columns: [
				{data:'id'},
	            {data: 'name'},
	            {data: 'description'},
	            {data: 'product_count'},
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
                    'targets': [0,2,4],
                    'orderable': false, 
                }],
            order:[[0, 'desc']],
		});

		/*Selecting all rows */
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

		/* Deletee button listiner*/
		data_table.on("click", "#delete-category", function(){
			var _this = this;
			confirmDelete("Do you really want to delete this category?",
				function(){
					deleteCategory(data_table, _this.dataset.id);
				},
				function(){}
			);
		});

		/* Perform actions on table*/
		document.getElementById("bulk-action-btn").addEventListener("click", function(){
			const bulkAction = document.getElementById("bulk-action-input");
			switch(bulkAction.value){
			case 'delete':
				confirmDelete("Are you sure you want to delete selected categories?<br> This cannot be undone.",
					function(){
						deleteCategory(data_table, 0, true);
					},
					function(){}
				);
				break;
			}
		});

		/* Form submit*/
		document.getElementById("categoryForm").addEventListener("submit", function(e){
			e.preventDefault();
			createCategory(this);
		});
	}

	async function createCategory(form){
		const formData = new FormData(form);
		try{
			const response = await fetch("{{url('/admin/product-category/create')}}",{
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
					var table = document.querySelector("tbody");
					var row = document.createElement("tr");
					row.innerHTML = result.data;

					table.insertBefore(row, table.firstElementChild);
					
					form.reset();

					Toast("success", result.message)
				}
			}else{
				console.error(await response.text());
			}

		}catch(error){
			console.error(error);
			Toast("error", "Something went wrong, try again later.");
		}
	}

	async function deleteCategory(table, id = 0, isBulk = false){
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
			const response = await fetch("{{url('/admin/product-category/delete')}}",{
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