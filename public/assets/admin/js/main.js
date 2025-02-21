
document.addEventListener("DOMContentLoaded", function(event) {
	/*-------------------------------
	Toggle chevron on main sidebar
	-------------------------------*/
	if (document.querySelectorAll(".chevron_up_down")) {
		var carets = document.querySelectorAll(".chevron_up_down");
		carets.forEach(function(caret){
			caret.addEventListener("click", function(){
				this.lastElementChild.classList.toggle('bi-chevron-down');
				this.lastElementChild.classList.toggle('bi-chevron-up');
			});
		});
	}
	
	/*------------------------------------------
	Set main sidebar active links and collapse menu
	----------------------------------------*/
	var links = document.getElementById("sidenav-main").querySelectorAll(".nav-link");
	if (links.length) {
		//select navlinks under sidebar only
		

		links.forEach(function(link){
			if (link.href === window.location.href) {
				link.classList.add('active');
				if (link.closest('.nav_submenu') != null) {
					link.closest('.nav_submenu').classList.add('show');
					if (link.closest('.nav_submenu').previousElementSibling != null) {
						link.closest('.nav_submenu').previousElementSibling.classList.add('active');
					}
				}
			}

			/*workround to enable collapse menu for urls under that category*/
			var navUrl =  link.href.split("/").slice(0, 5).join("/");
			var docUrl =  window.location.href;

			if (docUrl.startsWith(navUrl)) {
				if (link.closest('.nav_submenu') != null){
					link.closest('.nav_submenu').classList.add('show');
					if (link.closest('.nav_submenu').previousElementSibling != null) {
						link.closest('.nav_submenu').previousElementSibling.classList.add('active');
					}
				}else{
					link.closest('li').firstElementChild.classList.add('active');
				}
			}
		});
	}

	/*-----------------------------------------
	Collapse and show main sidebar
	------------------------------------------*/
	if (document.querySelectorAll(".sidenav_toggler").length) {
		var navTogglers = document.querySelectorAll(".sidenav_toggler"); 
		navTogglers.forEach(function(navToggler){
			navToggler.addEventListener("click", function(){
				document.body.classList.toggle("sidenav_collapse");
			});
		});
	}

	/*------------------------
	Scroll top button actions
	-------------------------*/
	if (document.querySelector('.scroll_to_top') != null) {
		var scrollTopButton = document.querySelector('.scroll_to_top');

		scrollTopButton.addEventListener("click", function(){
			document.body.scrollTop = 0;
			document.documentElement.scrollTop = 0;
		});
	
		window.onscroll = function(){
			if (window.pageYOffset > 400) {
				document.querySelector('.scroll_to_top').style.display = "block";
			} else {
				document.querySelector('.scroll_to_top').style.display = "none";
			}
		}
	}

	/**-------------------------------------------------------------
	 * Change theme color and mode
	 -------------------------------------------------------------**/
	document.querySelectorAll('#theme').forEach(function(it){
		it.addEventListener('click', function(){
			changeTheme(it);
		});
	});

	document.querySelectorAll('#palette').forEach(function(it){
		it.addEventListener('click', function(){
			changePalette(it);
		});
	});

	async function changeTheme(item){
		var link = document.getElementById('theme-link');
		link.href = location.origin+'/assets/admin/css/theme/'+item.dataset.value;

		var formData = new FormData;
		formData.append('theme', item.dataset.value);
		formData.append('_token',  document.querySelector('meta[name="csrf-token"]').content);

		try{
			const response = await fetch("/admin/setting/theme",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData
			});

			if (response.ok) {
				const result = await response.json();
			}else{
				console.error(await response.text());
			}
		}catch(error){
			console.log(error);
		}
	}

	async function changePalette(item){
		var link = document.getElementById('palette-link');
		link.href = location.origin+'/assets/admin/css/palette/'+item.dataset.value;

		var formData = new FormData;
		formData.append('palette', item.dataset.value);
		formData.append('_token',  document.querySelector('meta[name="csrf-token"]').content);

		try{
			const response = await fetch("/admin/setting/palette",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData
			});

			if (response.ok) {
				const result = await response.json();
			}else{
				console.error(await response.text());
			}
		}catch(error){
			console.log(error);
		}
	}

	/*--------------------------------
	Toggle show/hide passord input
	-----------------------------------*/
	if (document.querySelectorAll('#toggle-password').length) {
		document.querySelectorAll('#toggle-password').forEach(function(el){
			el.addEventListener('click', function(){
				var _input = el.parentElement.previousElementSibling;
				var _this = this;
				if (_input.type === 'password') {
					_input.type = "text";
					_this.classList.remove("bi-eye-slash")
					_this.classList.add("bi-eye")
				}else{
					_input.type = "password";
					_this.classList.remove("bi-eye")
					_this.classList.add("bi-eye-slash")
				}
			});
		});
	}

	/*--------------------------------
	Popup notifications errors/success
	---------------------------------*/
	window.Toast = function(type = "info", message = "Hello", position = "bottom"){
		var container = document.createElement("div");
		container.classList.add("toast_container");
		container.classList.add(position);
		container.innerHTML = 
					'<div class="toast border-0 badge-'+type.charAt(0).toLowerCase()+type.slice(1)+' shadow-none show" role="alert" aria-live="assertive" aria-atomic="true">'+
						'<div class="toast-header border-0 bg-transparent">'+
						    '<i class="bi-check-fill"></i>'+
						    '<span class="me-auto fw-bolder">'+type.charAt(0).toUpperCase()+type.slice(1)+'</span>'+
						    '<button type="button" class="close border-0 bg-transparent" data-bs-dismiss="toast" aria-label="Close">'+
						    	'<i class="bi-x-lg"></i>'+
						    '</button>'+
						'</div>'+
						'<div class="toast-body">'+message+'</div>'+
					'</div>';
		document.body.appendChild(container);
		setTimeout(function(){
			container.remove();
		}, 10000);			
	}

	/*---------------------------------
	 Custom confirm delete dialog modal
	-----------------------------------*/
	window.confirmDelete = function(msg, proceed, cancel,header="Confirm"){
		var dialog = document.createElement("div");
		dialog.classList.add("modal", "fade");
		dialog.setAttribute("id","errorModal");
		dialog.setAttribute("tabindex","-1");
		dialog.setAttribute("aria-labelledby","exampleModalLabel");
		dialog.setAttribute("aria-hidden","true");
		dialog.innerHTML = ' <div class="modal-dialog z-3">'+
		    '<div class="modal-content">'+
		      '<div class="modal-header bg-danger border-0">'+
		        '<h1 class="modal-title fs-6 text-white" id="exampleModalLabel">'+header+'</h1>'+
		        '<button type="button" class="close border-0 bg-transparent" data-bs-dismiss="modal" aria-label="Close">'+
		        '<i class="bi-x-lg text-white"></i></button>'+
		      '</div>'+
		      '<div class="modal-body">'+msg+
		      '<div class="modal-footer border-0">'+
		        '<button type="button" id="cancel" class="btn btn-sm btn-warning" data-bs-dismiss="modal">Cancel</button>'+
		        '<button type="button" id="ok" class="btn btn-sm btn-danger text-white" data-bs-dismiss="modal">Delete</button>'+
		      '</div>'+
		    '</div>'+
		  '</div>';
		document.body.appendChild(dialog);
	 	new bootstrap.Modal(dialog).show();

		dialog.querySelector(".close").addEventListener("click", function(){
			dialog.remove();
		});
		dialog.querySelector("#cancel").addEventListener("click", function(){
			dialog.remove();
			cancel();
		});
		dialog.querySelector("#ok").addEventListener("click", function(){
			dialog.remove();
			proceed();
		}); 
	}

	/*---------------------------------
	 Custom confirm action dialog modal
	-----------------------------------*/
	window.confirmAction = function(msg, proceed, cancel,header="Confirm"){
		var dialog = document.createElement("div");
		dialog.classList.add("modal", "fade");
		dialog.setAttribute("id","errorModal");
		dialog.setAttribute("tabindex","-1");
		dialog.setAttribute("aria-labelledby","exampleModalLabel");
		dialog.setAttribute("aria-hidden","true");
		dialog.innerHTML = ' <div class="modal-dialog z-3">'+
		    '<div class="modal-content">'+
		      '<div class="modal-header bg-info border-0">'+
		        '<h1 class="modal-title fs-6 text-white" id="exampleModalLabel">'+header+'</h1>'+
		        '<button type="button" class="close border-0 bg-transparent" data-bs-dismiss="modal" aria-label="Close">'+
		        '<i class="bi-x-lg text-white"></i></button>'+
		      '</div>'+
		      '<div class="modal-body">'+msg+
		      '<div class="modal-footer border-0">'+
		        '<button type="button" id="cancel" class="btn btn-sm btn-warning" data-bs-dismiss="modal">Cancel</button>'+
		        '<button type="button" id="ok" class="btn btn-sm btn-info text-white" data-bs-dismiss="modal">Confirm</button>'+
		      '</div>'+
		    '</div>'+
		  '</div>';
		document.body.appendChild(dialog);
	 	new bootstrap.Modal(dialog).show();

		dialog.querySelector(".close").addEventListener("click", function(){
			dialog.remove();
		});
		dialog.querySelector("#cancel").addEventListener("click", function(){
			dialog.remove();
			cancel();
		});
		dialog.querySelector("#ok").addEventListener("click", function(){
			dialog.remove();
			proceed();
		}); 
	}

	/*-------------------------
	For image input previews
	With <img> sibling for preview
	--------------------------*/
	if (document.querySelectorAll('#image-input').length) {
		document.querySelectorAll('#image-input').forEach(function(el){
			 el.addEventListener('change', function(){
	            let preview = el.previousElementSibling;
	            preview.src = URL.createObjectURL(el.files[0]);
	            preview.onload = function(){
	                URL.revokeObjectURL(preview.src);
	            } 
	        })
		});
	}

	/**---------------------------------------------------------------
	 * Top navbar notifications
	----------------------------------------------------------------**/
	getNavbarNotification();

	async function getNavbarNotification(){
		try{
			const response = await fetch("/admin/notification/navbar",{
				method: "GET",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
			});

			if (response.ok) {
				const result = await response.json();

				setAdminNotification(result);
				setContactNotification(result);
				setOrderNotification(result);
			}else{
				console.error(await response.text());
			}

		}catch(error){
			console.log(error);
		}
	}

	function setAdminNotification(data){
		if (data.notificationCount === 0) return;

		var adminNotification = document.getElementById('admin-notification');

		var messages = "";

		data.notifications.forEach(function(it){
			messages += `<li class="border-top">
							<div class="p-2 badge-warning">
								<div class="d-flex justify-content-between align-items-top">
									<span class="bi-bell fs-3 text-danger"></span>
									<div>
										<h6>`+it.title+`</h6>
										<span>`+it.created_at.substr(0,19)+`</span>
										<p class="mb-1">`+it.description.substr(0,20)+`</p>
										<a id="view" data-id="`+it.id+`" href="Javascript:" class="text-danger">view</a>
									</div>
									<a id="remove" data-id="`+it.id+`" href="javascript:"><i class="bi-x-lg text-danger"></i> </a>
								</div>
							</div>
						</li>`;
		});

		var content = `<a class="nav-link py-3 position-relative" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="true">
						<i class="bi-bell"></i>
						<span id="notification-badge" class="badge badge-danger position-absolute bottom-50 start-50">`+data.notificationCount+`</span>
					</a>
					<ul id="notification-list" class="dropdown-menu p-0 border-0 rounded-0 shadow" style="min-width: 300px;">
						<li class="dropdown-item bg-light border-bottom">
							<span class="fw-bolder">Notifications </span><br>
							<a id="mark-read" href="javascript:"><span class="text-info pe-4">Mark as read</span> </a>
							<a id="clear" href="javascript:"><span class="text-info">Clear</span> </a>
						</li>
						<div style="max-height:400px; overflow-y: scroll;">
						`+messages+`
						</div>
					</ul>`;

		adminNotification.innerHTML = content;

		adminNotification.querySelectorAll('#remove').forEach(function(it){
			it.addEventListener('click', function(){
				removeNotification([it.dataset.id]);
				it.closest('li').remove();
			});
		});

		adminNotification.querySelectorAll('#view').forEach(function(it){
			it.addEventListener('click', function(){
				viewNotification(it.dataset.id);
			});
		});

		adminNotification.querySelector('#clear').onclick = () => {
			var idArray = [];
			data.notifications.forEach(function(it){
				idArray.push(it.id);
			});
			removeNotification(idArray);
		}

		adminNotification.querySelector('#mark-read').onclick = () => {
			var idArray = [];
			data.notifications.forEach(function(it){
				idArray.push(it.id);
			});
			markRead(idArray);
		}
	}


	function setContactNotification(data){
		if (data.contactCount === 0) return;
	
		var contactNotification = document.getElementById('contact-notification');
		var sidebarContactBadge = document.getElementById('contact-count-badge');


		var messages = "";

		data.contacts.forEach(function(it){
			messages += `<li>
							<a class="dropdown-item py-3" href="/admin/contact/read/`+it.id+`">
								<div class="d-flex">
									<div class="avator bg-info text-center rounded-circle" style="width: 35px; height: 35px;">
										<h4 class="fw-bolder text-white text-center m-0">`+it.name.charAt(0)+`</h4>
									</div>
									<div class="ps-2">
										<span class="name pe-2 fw-bold">`+it.name+`</span>
										<span class="date">`+it.created_at.substr(0,19)+`</span><br>
										<span class="email">`+it.email+`</span>
									</div>
								</div>
							</a>
						</li>`;
		});

		var content = `<a class="nav-link py-3 position-relative" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="true">
						<i class="bi-envelope"></i>
						<span id="messages-badge" class="badge badge-danger position-absolute bottom-50 start-50">`+data.contactCount+`</span>
					</a>
					<ul class="dropdown-menu p-0 border-0 rounded-0 shadow">
						<li class="dropdown-item bg-light border-bottom">
							<span>You have `+data.contactCount+` new messages</span>
							<a href="/admin/contacts"><span class="text-info">View all</span></a>
						</li>
						`+messages+`
					</ul>`;
		contactNotification.innerHTML = content;
		sidebarContactBadge.innerHTML = data.contactCount;
	}

	function setOrderNotification(data){
		if (data.orderCount === 0) return;
	
		var orderNotification = document.getElementById('order-notification');
		var sidebarOrderBadge = document.querySelectorAll('#order-count-badge');


		var messages = "";

		data.orders.forEach(function(it){
			messages += `<li>
							<a class="dropdown-item py-3" href="/admin/order/edit/`+it.id+`">
								<div class="d-flex">
									<div class="me-2">
										<span class="bi-cart fs-3"></span>
									</div>
									<div class="ps-2">
									    <h6>`+it.first_name+` `+it.last_name+`</h6>
										<span class="name pe-2 fw-bold">`+it.code+`</span>
										<span class="date">`+it.created_at.substr(0,19)+`</span><br>
										<span class="email">`+it.email+`</span>
									</div>
								</div>
							</a>
						</li>`;
		});

		var content = `<a class="nav-link py-3 position-relative" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="true">
						<i class="bi-cart"></i>
						<span  class="badge badge-danger position-absolute bottom-50 start-50">`+data.orderCount+`</span>
					</a>
					<ul class="dropdown-menu p-0 border-0 rounded-0 shadow">
						<li class="dropdown-item bg-light border-bottom">
							<span>You have `+data.orderCount+` new Order(s)</span>
							<a href="/admin/orders"><span class="text-info">View all</span></a>
						</li>
						`+messages+`
					</ul>`;
		orderNotification.innerHTML = content;
		sidebarOrderBadge.forEach(function(it){
			it.innerHTML = data.orderCount;
		});
	}

	async function removeNotification(idArr){

		const formData = new FormData();
		formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

		idArr.forEach(function(id){
			formData.append('id[]', id);
		});

		try{
			const response = await fetch("/admin/notification/clear",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();

				Toast('success', result.message);

				if (idArr.length > 1) {
					document.location.reload();
				}
			}else{
				Toast('error', "Something wrong happened, refresh browser and try again.");
				console.error(await response.text());
			}
		}catch(error){
			console.log(error);
		}
	}

	async function markRead(idArr){
		const formData = new FormData();
		formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

		idArr.forEach(function(id){
			formData.append('id[]', id);
		});

		try{
			const response = await fetch("/admin/notification/read",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();

				Toast('success', result.message);

				document.location.reload();
			}else{
				Toast('error', "Something wrong happened, refresh browser and try again.");
				console.error(await response.text());
			}
		}catch(error){
			console.log(error);
		}
	}

	async function viewNotification(id){
		try{
			const response = await fetch("/admin/notification/view/"+id,{
				method: "GET",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
			});

			if (response.ok) {
				const result = await response.json();

				Toast('success', result.message);
				var url = location.origin;
				window.location.href =  url+result.url;
			}else{
				Toast('error', "Something wrong happened, refresh browser and try again.");
				console.error(await response.text());
			}
		}catch(error){
			console.log(error);
		}
	}
});

