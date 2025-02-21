@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Dashboard</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
			    <li class="breadcrumb-item active" aria-current="page"></li>
			 </ol>
		</div>
	</div>
</section>
<section class="dashboard_area">
	<div class="row">
		<div class="col-lg-3 col-md-6 col-12 mt-4 mt-lg-0">
			<a href="{{ url('/admin/products') }}">
				<div class="card dashboard_item border-0 rounded-4">
					<div class="card-body p-3">
						<div class="row">
							<div class="col-8">
								<div class="info">
									<span class="fw-bolder opacity-50 text-uppercase">Products</span>
									<h5 class="m-0 mt-2 fw-bold">{{ $productCount }}</h5>
								</div>
							</div>
							<div class="col-4">
								<div class="icon rounded-circle">
									<i class="bi-archive text-white"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-lg-3 col-md-6 col-12 mt-4 mt-lg-0">
			<a href="{{ url('/admin/orders') }}">
				<div class="card dashboard_item border-0 rounded-4">
					<div class="card-body p-3">
						<div class="row">
							<div class="col-8">
								<div class="info">
									<span class="fw-bolder opacity-50 text-uppercase">Orders</span>
									<h5 class="m-0 mt-2 fw-bold">{{ $orderCount }}</h5>
								</div>
							</div>
							<div class="col-4">
								<div class="icon rounded-circle">
									<i class="bi-bag-check text-white"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-lg-3 col-md-6 col-12 mt-4 mt-lg-0">
			<a href="{{ url('/admin/customers') }}">
				<div class="card dashboard_item border-0 rounded-4">
					<div class="card-body p-3">
						<div class="row">
							<div class="col-8">
								<div class="info">
									<span class="fw-bold opacity-50 text-uppercase">Customers</span>
									<h5 class="m-0 mt-2 fw-bolder">{{ $customerCount }}</h5>
								</div>
							</div>
							<div class="col-4">
								<div class="icon rounded-circle">
									<i class="bi-person text-white"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-lg-3 col-md-6 col-12 mt-4 mt-lg-0">
			<a href="{{ url('/admin/reviews') }}">
				<div class="card dashboard_item border-0 rounded-4">
					<div class="card-body p-3">
						<div class="row">
							<div class="col-8">
								<div class="info">
									<span class="fw-bold opacity-50 text-uppercase">Reviews</span>
									<h5 class="m-0 mt-2 fw-bolder">{{ $reviewCount }}</h5>
								</div>
							</div>
							<div class="col-4">
								<div class="icon rounded-circle">
									<i class="bi-chat-left-text text-white"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
</section>
<section class="">
	<div class="row">
		<div class="col-lg-4 mt-4">
			<div class="card border-0 rounded-4">
				<h5 class="p-4">Payments</h5>
                <canvas id="totalChart" style="width:100%;"></canvas>
			</div>
		</div>
		<div class="col-lg-8 mt-4">
            <div class="card border-0 rounded-4 p-4">
                <h5>Revenue</h5>
                <canvas id="revenueChart" style="width:100%;"></canvas>
            </div>
        </div>
	</div>
</section>
<script type="text/javascript" src="{{ url('/assets/vendor/js/chart.umd.js') }}"></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		
		getCurrentMonthData();

		async function getCurrentMonthData(){
			var date = new Date();

			dateFrom = date.getFullYear()+"-"+(date.getMonth()+1)+"-01";
            dateTo = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();

			const queryParams = {
	            from_date: dateFrom,
	            to_date: dateTo,
	        }

	        const queryString = new URLSearchParams(queryParams).toString();

	        const url = "/admin/dashboard/current-month-data"+"?"+queryString;

	        try{
	            const response = await fetch(url,{
	                method: "GET",
	                mode: "cors",
	                cache: "no-cache",
	                credentials: "same-origin",
	            });
	            if (response.ok) {
	                const result = await response.json();

	                revenueChart(result.data);
	                totalChart(result.data);
	            }else{
	                console.log(await response.text());
	            }
	        }catch(error){
	            console.log(error);
	        }
		}

		function revenueChart(data){
			const xValues = [];
	        const yValues = [];

	        if (data.revenueData.length) {
	            data.revenueData.forEach((it) => {
	                yValues.push(it.total);
	                xValues.push(it.weekday);
	            });
	        }

	        new Chart(document.getElementById('revenueChart'),{
	            type: "line",
	            data: { 
	                labels: xValues,
	                datasets: [{
	                    fill: true,
	                    showLine: true,
	                    label: data.currency,
	                    backgroundColor:"rgba(0,0,255,0.1)",
	                    borderColor: "rgba(0,0,255,0.1)",
	                    data: yValues
	                }]
	            },
	            options:{}
	        });
		}

		function totalChart(data){
			var xValues = [];
	        var yValues = [];

	        xValues = ["Completed", "Pending", "Refunded"];
	        yValues = [data.totalCompleted, data.totalPending, data.totalRefund];

	        new Chart(document.getElementById('totalChart'),{
	            type: "pie",
	            data: { 
	                labels: xValues,
	                datasets: [{
	                    backgroundColor:["rgba(0,153,1,0.5)","rgba(204,0,0,0.5)","rgba(0, 51, 102,0.9)"],
	                    data: yValues
	                }]
	            },
	            options:{}
	        });
		}
	});
</script>
@endsection