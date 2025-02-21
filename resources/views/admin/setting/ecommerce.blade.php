@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Ecommerce setting</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Ecommerce setting</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="">
	<form action="{{ url('/admin/setting/ecommerce') }}" method="post">
		<div class="row">
			<div class="col-lg-8">
				<div class="card border-0 rounded-4 p-4">
					<div class="row">
						@csrf
						<div class="col-12">
							<label class="form-label">Shop name <i class="text-danger">*</i></label>
							<input class="form-control" type="text" name="shop_name" value="{{ $setting['shop_name'] ?? '' }}">
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Company name</label>
							<input class="form-control" type="text" name="company_name" value="{{ $setting['company_name'] ?? '' }}">
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Currency <i class="text-danger">*</i></label>
							<select class="form-select form-control" name="currency_code">
								@foreach(Util::$currencies as $currency)
									@if($currency['code'] == ($setting['currency_code'] ?? ''))
									<option value="{{ $currency['code'] }}" selected>{{ $currency['name'] }}</option>
									@else
									<option value="{{ $currency['code'] }}" >{{ $currency['name'] }}</option>
									@endif
								@endforeach
							</select>
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Unit of weight <i class="text-danger">*</i></label>
							<select class="form-select form-control" name="weight_unit">
								<option value="g">Grams (g)</option>
								<option value="kg" @if(($setting["weight_unit"] ?? '') == "kg") selected @endif>Kilograms (kg)</option>
								<option value="lb" @if(($setting["weight_unit"] ?? '') == "lb") selected @endif>Pound (lb)</option>
								<option value="oz" @if(($setting["weight_unit"] ?? '') == "oz") selected @endif>Ounce (oz)</option>
							</select>
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Unit of length <i class="text-danger">*</i></label>
							<select class="form-select form-control" name="length_unit">
								<option value="cm" @if(($setting["length_unit"] ?? '') == "cm") selected @endif>Centimeter (cm)</option>
								<option value="m" @if(($setting["length_unit"] ?? '') == "m") selected @endif>Meter (m)</option>
								<option value="inch" @if(($setting["length_unit"] ?? '') == "inch") selected @endif>Inch</option>
							</select>
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Country <i class="text-danger">*</i></label>
							<select class="form-select form-control" name="country">
								@foreach(Util::$countries as $country)
									@if($country == ($setting['country'] ?? ''))
									<option value="{{ $country }}" selected>{{ $country }}</option>
									@else
									<option value="{{ $country }}" >{{ $country }}</option>
									@endif
								@endforeach
							</select>
						</div>
						<div class="col-12">
							<button class="btn btn-primary btn-sm mt-4">Save settings</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>
@endsection