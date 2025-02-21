@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Faq categories</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/faqs') }}">Faqs</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Faq categories</li>
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
					<h5>New faq category</h5>
					<form id="faq-category-form" action="{{ url('/admin/faq-category/create') }}" method="post">
						@csrf
						<div class="row">
							<div class="col-12">
								<label class="form-label mt-4">Name</label>
								<input class="form-control"  type="text" name="name" required>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Description</label>
								<textarea class="form-control" rows="5" name="description"> </textarea>
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
								<tbody><tr></tr></tbody>
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
                url: "/admin/faq-category-list",
                data: function(data){
                     return $.extend({},data);
                },
                error:function(err, status){
                    console.log(err);
                }
            },
             columns: [
				{data:'id'},
	            {data: 'name'},
	            {data: 'description'},
	            {data: 'faq_count'},
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
		data_table.on("click", "#delete-faq-category", function(){
			var _this = this;
			confirmDelete("Do you really want to delete this faq category?",
				function(){
					deleteFaqCategory(data_table, _this.dataset.id);
				},
				function(){}
			);
		});

		/* Perform actions on table */
		document.getElementById("bulk-action-btn").addEventListener("click", function(){
			const bulkAction = document.getElementById("bulk-action-input");
			switch(bulkAction.value){
			case 'delete':
				confirmDelete("Are you sure you want to delete selected faq categories?<br> This cannot be undone.",
					function(){
						deleteFaqCategory(data_table, 0, true);
					},
					function(){}
				);
				break;
			}
		});

		/* Form submit */
		document.getElementById("faq-category-form").addEventListener("submit", function(e){
			e.preventDefault();

			createFaqCategory(this);
		});
	}

	async function createFaqCategory(form){
		const formData = new FormData(form);
		try{
			const response = await fetch("{{url('/admin/faq-category/create')}}",{
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

	async function deleteFaqCategory(table, id = 0, isBulk = false){
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
			const response = await fetch("{{url('/admin/faq-category/delete')}}",{
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