@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Customers</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Customers</li>
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
						<div class="sd-flex">
							<div class="float-end">
								<button id="download-customer" class="btn btn-outline-secondary btn-sm"><i class="bi-download me-3"></i>Download</button>
							</div>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-flush" id="myTable">
								<thead>
									<tr>
										<th>Name</th>
										<th>Created at</th>
										<th>Email</th>
										<th>Orders</th>
										<th>Total spend</th>
										<th>Country</th>
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

		/* Export customers as excel*/
		document.getElementById("download-customer").addEventListener("click", function(){
	        let table = document.getElementById('myTable');
	        var excelFile = XLSX.utils.table_to_book(table, {sheet: "sheet1"});
	        XLSX.write(excelFile, {bookType: "xlsx", bookSST: true, type: 'base64'});
	        XLSX.writeFile(excelFile, 'Customers'+'.'+"xlsx");
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
                url: "/admin/customer-list",
            },
             columns: [
			    {data: 'id'},
	            {data: 'created_at'},
	            {data: 'email'},
	            {data: 'order_count'},
	            {data: 'total_spend'},
	            {data: 'country'},  
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
                    'targets': [],
                    'orderable': false, 
            }],
            order:[[1, 'desc']],
		});
	}
</script>
@endsection