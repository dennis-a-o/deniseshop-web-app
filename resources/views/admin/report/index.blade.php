@extends('layouts.admin')
@section('content')
<section class="py-4">
    <div class="row align-items-center justify-content-center">
        <div class="col-6">
            <h4 class="fw-bolder m-0">Reports</h4>
        </div>
        <div class="col-6">
            <ol class="breadcrumb justify-content-end align-items-center m-0">
                <li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a  href="{{ url('/admin/products') }}">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reports</li>
             </ol>
        </div>
    </div>
</section>
<section class="reports">
    <div class="row">
        <div class="col-12">
            <div class="">
                <div class="dropdown">
                    <button id="range-dropdown-btn" class="btn btn-outline-info btn-sm dropdown-toggle float-end" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                     This month
                    </button>
                    <div id="range-dropdown-menu" class="dropdown-menu border-0 rounded-0 shadow">
                        <div class="p-4">
                            <div class="row" style="min-width: 400px;">
                                <div class="col-lg-5">
                                   <ul class="list-group">
                                        <a href="javascript:" id="rangeDate" data-value="Today" class="list-group-item list-group-item-action" aria-current="true">
                                            Today
                                        </a>
                                        <a href="javascript:" id="rangeDate" data-value="ThisWeek" class="list-group-item list-group-item-action" aria-current="true">
                                            This week
                                        </a>
                                        <a href="javascript:" id="rangeDate" data-value="Last7Days" class="list-group-item list-group-item-action" aria-current="true">
                                            Last 7 days
                                        </a>
                                        <a href="javascript:" id="rangeDate" data-value="Last30Days" class="list-group-item list-group-item-action" aria-current="true">
                                            Last 30 days
                                        </a>
                                        <a href="javascript:" id="rangeDate" data-value="ThisMonth" class="list-group-item list-group-item-action" aria-current="true">
                                            This month
                                        </a>
                                        <a href="javascript:" id="rangeDate" data-value="ThisYear" class="list-group-item list-group-item-action" aria-current="true">
                                            This year
                                        </a>
                                    </ul>
                                </div>
                                <div class="col-lg-7 mt-4 mt-lg-0">
                                    <span class="fw-bold ">Custom</span>
                                    <div>
                                        <label class="form-label mt-2">From</label>
                                        <input class="form-control py-2" type="date" name="from">
                                    </div>
                                    <div>
                                        <label class="form-label mt-2">To</label>
                                        <input class="form-control py-2" type="date" name="to">
                                    </div>
                                    <div class="text-end">
                                        <button id="rangeDate" data-value="Custom" class="btn btn-primary btn-sm mt-3">Apply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-lg-3">
                    <div class="card border-0 rounded-4 mt-4 p-3">
                        <div class="d-flex">
                            <span class=" bi-coin fs-2 me-4"></span>
                            <div class="">
                                <p class="m-0">Revenue</p>
                                <span id="total-revenue"  class="fs-5">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card border-0 rounded-4 mt-4 p-3">
                        <div class="d-flex">
                            <span class=" bi-bag fs-2 me-4"></span>
                            <div class="">
                                <p class="m-0">Products</p>
                                <span id="total-product" class="fs-5">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card border-0 rounded-4 mt-4 p-3">
                        <div class="d-flex">
                            <span class=" bi-cart fs-2 me-4"></span>
                            <div class="">
                                <p class="m-0">Orders</p>
                                <span id="total-order" class="fs-5">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card border-0 rounded-4 mt-4 p-3">
                        <div class="d-flex">
                            <span class=" bi-person fs-2 me-4"></span>
                            <div class="">
                                <p class="m-0">Customers</p>
                                <span id="total-customer" class="fs-5">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-4">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card border-0 rounded-4 p-4">
                        <h5>Customers</h5>
                        <canvas id="customerChart" style="width:100%;"></canvas>
                    </div>
                </div>
                 <div class="col-lg-6 mt-4 mt-lg-0">
                    <div class="card border-0 rounded-4 p-4">
                        <h5>Orders</h5>
                        <canvas id="orderChart" style="width:100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-4">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 rounded-4 p-4">
                        <h5>Sales</h5>
                        <canvas id="saleChart" style="width:100%;"></canvas>
                    </div>
                </div>
                 <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card border-0 rounded-4 p-4">
                        <h5>Earnings</h5>
                        <canvas id="earningChart" style="width:100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-4">
            <div class="card border-0 rounded-4">
                <div class="p-4">
                   <h5>Top selling products</h5> 
                </div>
                <table id="top-selling-table" class="table">
                    <thead>
                        <th class="ps-4">Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Sold</th>
                    </thead>
                    <tbody> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript" src="{{ url('/assets/vendor/js/chart.umd.js') }}"></script>
<script type="text/javascript" src="{{ url('/assets/admin/js/report.js') }}"></script>
@endsection