<aside class="sidenav position-fixed rounded-4" id="sidenav-main">
	<div class="sidenav_header p-4">
		<a class="navbar-brand m-0" href="{{ url('/admin/dashboard') }}">
			<img class="logo" src="{{ asset('/assets/img/general/logo_dark.png') }}">
			<img class="favicon" src="{{ asset('/assets/img/general/favicon.png') }}">
		</a>
	</div>
	<hr class="mt-0">
	<div class="w-auto h-auto" id="sidenav-collapse-main">
		<ul class="nav nav_menu flex-column">
			<li class="nav-item">
				<a class="nav-link" href="{{url('/admin/dashboard')}}">
					<i class="bi-speedometer"></i>
					<span class="ms-2">Dashboard</span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{url('/admin/pages')}}">
					<i class="bi-book"></i>
					<span class="ms-2">Pages</span>
				</a>
				<a class="nav-link" href="{{ url('/admin/page/create') }}" style="display: none;" ></a>
				<a class="nav-link" href="{{ url('/admin/page/edit') }}" style="display: none;" ></a>
			</li>
			<li class="nav-item">
				<a class="nav-link chevron_up_down" href="{{ url('/admin/faqs') }}" data-bs-toggle="collapse" data-bs-target="#faqs">
					<i class="bi-question-circle"></i>
					<span class="ms-2">Faqs</span>
					<span class="bi-chevron-down float-end"></span>
				</a>
				<ul class="nav nav_submenu collapse" id="faqs">
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/faqs') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">All faqs</span>
						</a>
						<a class="nav-link" href="{{ url('/admin/faq/create') }}" style="display: none;" ></a>
						<a class="nav-link" href="{{ url('/admin/faq/edit') }}" style="display: none;" ></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/faq-categories') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Faq categories</span>
						</a>
					</li>
				</ul>
			</li>
			<li class="nav-item">
				<a class="nav-link chevron_up_down" href="{{ url('/admin/products') }}" data-bs-toggle="collapse" data-bs-target="#ecommerce">
					<i class="bi-shop"></i>
					<span class="ms-2">Ecommerce</span>
					<span id="order-count-badge" class="badge badge-info "></span>
					<span class="bi-chevron-down float-end"></span>
				</a>
				<ul class="nav nav_submenu collapse" id="ecommerce">
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/reports') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Reports</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/products') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Products</span>
						</a>
						<a class="nav-link" href="{{ url('/admin/product/create') }}" style="display: none;" ></a>
						<a class="nav-link" href="{{ url('/admin/product/edit') }}" style="display: none;" ></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/product-categories') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Categories</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/product-tags') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Tags</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/brands') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Brands</span>
						</a>
						<a class="nav-link" href="{{ url('/admin/brand/create') }}" style="display: none;" ></a>
						<a class="nav-link" href="{{ url('/admin/brand/edit') }}" style="display: none;" ></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/flash-sales') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Flash sales</span>
						</a>
						<a class="nav-link" href="{{ url('/admin/flash-sale/create') }}" style="display: none;" ></a>
						<a class="nav-link" href="{{ url('/admin/flash-sale/edit') }}" style="display: none;" ></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/customers') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Customers</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/reviews') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Reviews</span>
						</a>
						<a class="nav-link" href="{{ url('/admin/review/detail') }}" style="display: none;" ></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/orders') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Orders</span>
							<span id="order-count-badge" class="badge badge-info "></span>
						</a>
						<a class="nav-link" href="{{ url('/admin/order/edit') }}" style="display: none;" ></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/order-returns') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Order returns</span>
						</a>
						<a class="nav-link"  href="{{ url('/admin/order-return/edit') }}" style="display: none;"></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/coupons') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Coupons</span>
						</a>
						<a class="nav-link"  href="{{ url('/admin/coupon/create') }}" style="display: none;"></a>
						<a class="nav-link"  href="{{ url('/admin/coupon/edit') }}" style="display: none;"></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/shipments') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Shipments</span>
						</a>
						<a class="nav-link"  href="{{ url('/admin/shipment/edit') }}" style="display: none;"></a>
					</li>
				</ul>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{url('/admin/sliders')}}">
					<i class="bi-caret-right-square"></i>
					<span class="ms-2">Sliders</span>
				</a>
				<a class="nav-link" href="{{ url('/admin/slider/create') }}" style="display: none;" ></a>
				<a class="nav-link" href="{{ url('/admin/slider/edit') }}" style="display: none;" ></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ url('/admin/contacts') }}">
					<i class="bi-envelope"></i>
					<span class="ms-2">Contact</span>
					<span id="contact-count-badge" class="badge badge-info "></span>
				</a>
				<a class="nav-link" href="{{ url('/admin/contact/read') }}" style="display: none;" ></a>
			</li>
			<li class="nav-item">
				<a class="nav-link chevron_up_down" href="{{ url('/admin/users') }}" data-bs-toggle="collapse" data-bs-target="#users">
					<i class="bi-person"></i>
					<span class="ms-2">Users</span>
					<span class="bi-chevron-down float-end"></span>
				</a>
				<ul class="nav nav_submenu collapse" id="users">
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/users') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">All users</span>
						</a>
						<a class="nav-link" href="{{ url('/admin/user/create') }}" style="display: none;" ></a>
						<a class="nav-link" href="{{ url('/admin/user/edit') }}" style="display: none;" ></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/subscribers') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Subscribers</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/profile') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Profile</span>
						</a>
					</li>
				</ul>
			</li>
			<li class="nav-item">
				<a class="nav-link chevron_up_down" href="{{ url('/admin/payment') }}" data-bs-toggle="collapse" data-bs-target="#payment">
					<i class="bi-credit-card"></i>
					<span class="ms-2">Payments</span>
					<span class="bi-chevron-down float-end"></span>
				</a>
				<ul class="nav nav_submenu collapse" id="payment">
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/payment/transactions') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Transactions</span>
						</a>
						<a class="nav-link" href="{{ url('/admin/payment/transaction/edit') }}" style="display: none;" ></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/payment/methods') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Payment method</span>
						</a>
					</li>
				</ul>
			</li>
			<li class="nav-item">
				<a class="nav-link chevron_up_down" href="{{ url('/admin/location') }}" data-bs-toggle="collapse" data-bs-target="#location">
					<i class="bi-geo-alt"></i>
					<span class="ms-2">Locations</span>
					<span class="bi-chevron-down float-end"></span>
				</a>
				<ul class="nav nav_submenu collapse" id="location">
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/countries') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Countries</span>
						</a>
						<a class="nav-link" href="{{ url('/admin/country/create') }}" style="display: none;" ></a>
						<a class="nav-link" href="{{ url('/admin/countrytry/edit') }}" style="display: none;" ></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/states') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">States</span>
						</a>
						<a class="nav-link" href="{{ url('/admin/state/create') }}" style="display: none;" ></a>
						<a class="nav-link" href="{{ url('/admin/state/edit') }}" style="display: none;" ></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/cities') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Cities</span>
						</a>
						<a class="nav-link" href="{{ url('/admin/city/create') }}" style="display: none;" ></a>
						<a class="nav-link" href="{{ url('/admin/city/edit') }}" style="display: none;" ></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/pickup-locations') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Pickup locations</span>
						</a>
						<a class="nav-link" href="{{ url('/admin/pickup-location/create') }}" style="display: none;" ></a>
						<a class="nav-link" href="{{ url('/admin/pickup-location/edit') }}" style="display: none;" ></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/shipping-zones') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Shipping zones</span>
						</a>
						<a class="nav-link" href="{{ url('/admin/shipping-zone/create') }}" style="display: none;" ></a>
						<a class="nav-link" href="{{ url('/admin/shipping-zone/edit') }}" style="display: none;" ></a>
					</li>
				</ul>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ url('/admin/media') }}">
					<i class="bi-image"></i>
					<span class="ms-2">Media</span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link chevron_up_down" href="{{ url('/admin/setting') }}" data-bs-toggle="collapse" data-bs-target="#setting">
					<i class="bi-gear"></i>
					<span class="ms-2">Settings</span>
					<span class="bi-chevron-down float-end"></span>
				</a>
				<ul class="nav nav_submenu collapse" id="setting">
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/setting/general') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">General</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/setting/ecommerce') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Ecommerce</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('/admin/setting/email') }}">
							<i class="bi-circle"></i>
							<span class="ms-2">Email</span>
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</aside>