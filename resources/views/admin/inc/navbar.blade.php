<nav class="navbar navbar_main_top rounded-4 p-0 ">
	<div class="w-100 px-4">
		<div class="d-flex justify-content-between align-items-center">	
			<a class="sidenav_toggler d-lg-block d-none" href="Javascript:">
				<i class="bi-list"></i>
			</a>
			<ul class="nav m-0 p-0 ´justify-content-lg-end align-items-center">
				<li class="nav-item">
					<a class="nav-link py-3" aria-current="page" href="{{url('')}}" target="blank">
						<i class="bi-globe pe-1"></i>
						<span>Website</span>
					</a>
				</li>
				<li id="admin-notification" class="nav-item dropdown"></li>
				<li id="contact-notification" class="nav-item dropdown"></li>
				<li id="order-notification" class="nav-item dropdown"></li>
				<li class="nav-item dropdown">
					<a class="nav-link py-3 dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
					<span>Theme</span>
					</a>
					<ul class="dropdown-menu p-0 border-0 rounded-0 shadow">
						<li>
							<a id="theme" class="dropdown-item" data-value="light.css" href="javascript:">
								<i class="bi-brightness-high pe-2"></i>
								<span>Light</span>
							</a>
						</li>
						<li>
							<a id="theme" class="dropdown-item border-bottom" data-value="dark.css" href="Javascript:">
								<i class="bi-brightness-high-fill pe-2"></i>
								<span>Dark</span>
							</a>
						</li>
						<li>
							<a id="palette" class="dropdown-item" data-value="indigo.css" href="Javascript:">
								<i class="bi-palette-fill pe-2"></i>
								<span>Indigo</span>
							</a>
						</li>
						<li>
							<a id="palette" class="dropdown-item" data-value="blue.css" href="Javascript:">
								<i class="bi-palette-fill pe-2"></i>
								<span>Blue</span>
							</a>
						</li>
						<li>
							<a id="palette" class="dropdown-item" data-value="red.css" href="Javascript:">
								<i class="bi-palette-fill pe-2"></i>
								<span>Red</span>
							</a>
						</li>
						<li>
							<a id="palette" class="dropdown-item" data-value="green.css" href="Javascript:">
								<i class="bi-palette-fill pe-2"></i>
								<span>Green</span>
							</a>
						</li>
						<li>
							<a id="palette" class="dropdown-item" data-value="yellow.css" href="Javascript:">
								<i class="bi-palette-fill pe-2"></i>
								<span>Yellow</span>
							</a>
						</li>
						<li>
							<a id="palette" class="dropdown-item" data-value="purple.css" href="javascript:">
								<i class="bi-palette-fill pe-2"></i>
								<span>Purple</span>
							</a>
						</li>
						<li>
							<a id="palette" class="dropdown-item" data-value="pink.css" href="Javascript:">
								<i class="bi-palette-fill pe-2"></i>
								<span>Pink</span>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link py-3 dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
						<img class="rounded-circle" width="28" height="28" src="{{asset('/assets/img/users')}}/{{ Auth::user()->image }}">
						<span>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
					</a>
					<ul class="dropdown-menu  p-0 border-0 rounded-0 shadow">
						<li>
							<a class="dropdown-item" href="{{url('/admin/profile')}}">
								<i class="bi-person-gear pe-2"></i>
								<span>Profile</span>
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{url('/logout')}}">
								<i class="bi-shield-lock pe-2"></i>
								<span>Logout</span>
							</a>
						</li>
					</ul>
				</li>
			</ul>
			<a class="sidenav_toggler d-lg-none d-block" href="javascript:">
				<i class="bi-list"></i>
			</a>
		</div>
	</div>
</nav>