@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Contacts</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Contacts</li>
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
						<div class="d-flex justify-content-between">
							<div class="d-flex pe-2">
								<select class="form-select" id="bulk-action-input">
									<option>Bulk actions</option>
									<option value="unread">Unread</option>
									<option value="read">Read</option>
									<option value="delete">Delete</option>
								</select>
								<button class="btn btn-primary btn-sm ms-2" id="bulk-action-btn">Apply</button>
							</div>
							<div class="d-flex align-items-center">
								<button id="download-contact" class="btn btn-gradient-primary btn-sm"><i class="bi-download text-white me-2"></i>Download</button>
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
										<th>Email</th>
										<th>Phone</th>
										<th>Created at</th>
										<th>Status</th>
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
<script type="text/javascript" src="{{ asset('assets/vendor/js/xlsx.mini.js') }}"></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		initTable();

		/*Export customers as excel*/
		document.getElementById("download-contact").addEventListener("click", function(){
	        let table = document.getElementById('myTable');
	        var excelFile = XLSX.utils.table_to_book(table, {sheet: "sheet1"});
	        XLSX.write(excelFile, {bookType: "xlsx", bookSST: true, type: 'base64'});
	        XLSX.writeFile(excelFile, 'Contact'+'.'+"xlsx");
		});
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
                url: "/admin/contact-list",
            },
             columns: [
				{data:'id'},
			    {data: 'name'},
			    {data: 'email'},
			    {data: 'phone'},
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
                    'targets': [0,6],
                    'orderable': false, 
            }],
            order:[[4, 'desc']],
		});

		/*Selecting all rows and add click event listener*/
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

		/*Deletee button listener*/
		data_table.on("click", "#delete-contact", function(){
			var _this = this;
			confirmDelete("Do you really want to delete this contact?",
				function(){
					deleteContact(data_table, _this.dataset.id);
				},
				function(){},
				"Confirm delete"
			);
		});

		/*Perform actions on table*/
		document.getElementById("bulk-action-btn").addEventListener("click", function(){
			const bulkAction = document.getElementById("bulk-action-input");
			switch(bulkAction.value){
			case 'unread':
			case 'read':
				contactStatus(data_table, bulkAction.value);
				break;
			case 'delete':
				confirmDelete("Are you sure you want to delete selected contacts?<br> This cannot be undone.",
					function(){
						deleteContact(data_table, 0, true);
					},
					function(){},
					"Confirm delete"
				);
				break;
			}
		});
	}

	async function contactStatus(table, status){
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
			const response = await fetch("{{url('/admin/contact/status')}}",{
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

	async function deleteContact(table, id = 0, isBulk = false){
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
			const response = await fetch("{{url('/admin/contact/delete')}}",{
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