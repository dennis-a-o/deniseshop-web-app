document.addEventListener("DOMContentLoaded", function(event) {
	var isGrid = true;
	var page = 1;
	var folderId = 0;
	var offset = 0;
	var perPage = 10;
	var totalPage = 0;
	var searchValue = "";
	var sortColumn = "created_at";
	var sortOrder = "desc";
	var filter = "";
	var selectedItem = [];

	const inputUploadMedia = document.getElementById("upload-media-input");
	const uploadBtn = document.getElementById("upload-btn");
	const btnDownloadMedia = document.getElementById('download-btn');
	const btnCreateFolder = document.getElementById('create-folder-btn');
	const btnRefresh = document.getElementById('refresh-btn');

	const mediaFilter = document.querySelectorAll('#filter-media');
	const mediaSort = document.querySelectorAll('#sort-media');

	const inputSearchBtn = document.getElementById('search-media-btn');

	const mediaBreadcrumb = document.getElementById('media-breadcrumb');

	const actionBtn = document.getElementById('action-btn');
	const mediaAction = document.querySelectorAll('#action-media');
	
	const btnGrid = document.querySelectorAll('#btn-grid');

	const mediaPagination = document.querySelector('.media-pagination');
	const mediaPage = document.querySelectorAll('.media-page');

	const mediaListContainer = document.querySelector('.media-list-container');
	const mediaDetail = document.querySelector('.media-detail');

	const insertLinkBtn = document.getElementById('insert-media-link');

	inputUploadMedia.addEventListener('change', function(){
		if (this.files) {
			uploadMedia(this);
		}
	});

	btnDownloadMedia.addEventListener('click', function(){
		downloadMedia();
	});

	btnCreateFolder.addEventListener('click', function(){
		var folderName = document.getElementById('folder_name_input').value;
		if (folderName != "") {
			createFolder(folderName);
		}
	});

	btnRefresh.addEventListener('click', function(){
		fetchMedia();//reload
	});

	mediaFilter.forEach(function(el){
		el.addEventListener('click', function(){
			filter = this.dataset.value;
			fetchMedia();//reload
		});
	});

	mediaSort.forEach(function(el){
		el.addEventListener('click', function(){
			sortColumn = this.dataset.value;
			sortOrder = this.dataset.order;
			fetchMedia();//reload
		});
	});

	inputSearchBtn.addEventListener('click', function(){
		searchValue = document.getElementById('search-media-input').value;
		fetchMedia();//reload
	});

	if (insertLinkBtn != null) {
		insertLinkBtn.addEventListener('click', function(){
			if (selectedItem.length >= 1) {
				//Tinymce5 callback
				if(usingTinymce5()){
					parent.postMessage({
				      mceAction: 'insert',
				      content: selectedItem[0].url
				    });
					parent.postMessage({ mceAction: 'close' });
				}
			}
		});
	}

	mediaAction.forEach(function(el){
		el.addEventListener('click',function(){
			switch(this.dataset.value){
			case "preview":
				previewMedia();
				break;
			case "crop":
				cropMedia(this);
				break;
			case "copy_link":
				copyLink(this);
				break;
			case "make_copy":
				makeCopy();
				break;
			case "download":
				downloadMedia();
				break;
			case "delete":
				mediaDialog(
					"Do you really want to delete the selected items, this CANNOT be UNDONE, for folders all sub folders and files in it will be deleted permanently!",
					function(){
						trashMedia();
					},
					function(){},
					"default",
					"Corfirm delete"
				);
				break;
			default:
				break;
			}
		});
	});

	btnGrid.forEach(function(el){
		el.addEventListener('click', function(){
			el.classList.add("bg-light");
			if (el.dataset.value == "list") {
				isGrid = false;
				this.previousElementSibling.classList.remove('bg-light');
				fetchMedia();//reload
			}else{
				isGrid = true;
				this.nextElementSibling.classList.remove('bg-light')
				fetchMedia();//reload
			}
		});
	});

	mediaPage.forEach(function(el){
		el.addEventListener('click', function(){
			switch(this.dataset.value){
			case "next":
				pageinateMedia(true);
				break;
			case "previous":
				pageinateMedia(false);
				break;
			}
		});
	});

	const media_spinner = `<div class="media-loader">
								<div class="d-flex justify-content-center">
									<div class="spinner-border spinner-border-lg text-info" role="status">
									  <span class="visually-hidden">Loading...</span>
									</div>
								</div>
							</div>`;

	/**------------------------------
	 * MAIN ENTRY
	 *------------------------------*/
	fetchMedia();

	async function fetchMedia(){

		var cache_list = mediaListContainer.innerHTML;
		mediaListContainer.innerHTML = media_spinner;

		const queryParams = {
			folderId: folderId,
	   		offset: offset,
	   		perPage: perPage,
	   		searchValue: searchValue,
	   		sortColumn: sortColumn,
	   		sortOrder: sortOrder,
	   		filter: filter
	   	}

	   	const queryString = new URLSearchParams(queryParams).toString();

		const url = "/admin/media/list"+"?"+queryString;

		try{
			const response = await fetch(url,{
				method: "GET",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
			});

			if (response.ok) {
				const result = await response.json();
				if (!result.error) {
					if (isGrid) {
						setMediaGrid(result.data);
					}else{
						setMediaList(result.data);
					}
					
					totalPage = result.totalRecords;

					if (totalPage > perPage) {
						mediaPagination.style.display = "block"; 
					}else{
						mediaPagination.style.display = "none";
					}

				}else{
					//restore previous list if error occur
					mediaListContainer.innerHTML = cache_list;
					Toast("error", result.message);
				}
			}else{
				//restore previous list if error occur
				mediaListContainer.innerHTML = cache_list;
				console.log(await response.text());
			}

		}catch(error){
			console.log(error);
		}
	}

	/** 
	*  Fetch next or previous page 
	* @param isNext true means next page and false previous page
	* */
	function pageinateMedia(isNext = true){
		if(totalPage > perPage){
			if (isNext) {
				if (page < (totalPage/perPage)) {
					++page;
					offset = (page - 1) * perPage;
					fetchMedia();
				}
			}else{
				if (page > 1) {
					--page;
					offset = (page - 1) * perPage;
					fetchMedia();
				}
			}
		}
	}

	function setMediaGrid(data){

		mediaListContainer.innerHTML = "";

		if (data.folders.length > 0 || data.files.length > 0) {
			if (data.folders.length) {
				data.folders.forEach(function(folder){
					var item = document.createElement('div');
					item.classList.add('media-item','mgrid');
					item.setAttribute('data-id',folder.id);
					item.setAttribute('data-name',folder.name);
					item.setAttribute('data-created',folder.created_at);
					item.setAttribute('data-updated',folder.updated_at);
					item.setAttribute('data-type', 'folder');
					item.innerHTML = `<div class="media-thumbnail">
										<span class="bi-folder"></span>
									</div>
									<div class="media-description">
										<span>`+folder.name.substr(0, 15)+`</span>
									</div>`;

					mediaListContainer.appendChild(item);
				});
			}
			
			if (data.files.length) {
				data.files.forEach(function(file){
					var thumbnail = "";

					switch(file.type){
					case "image":
						thumbnail = '<img src="'+file.url+'">';
						break;
					case "video":
						thumbnail = '<span class="bi-camera-video"></span>';
						break;
					default:
						thumbnail = '<span class="bi-file-text"></span>';
						break;
					} 

					var item = document.createElement('div');
					item.classList.add('media-item','mgrid');
					item.setAttribute('data-id',file.id);
					item.setAttribute('data-name',file.name);
					item.setAttribute('data-type',file.type);
					item.setAttribute('data-size',file.size);
					item.setAttribute('data-mime_type',file.mime_type);
					item.setAttribute('data-url',file.url);
					item.setAttribute('data-created',file.created_at);
					item.setAttribute('data-updated',file.updated_at);
					item.innerHTML = `<div class="media-thumbnail">
										`+thumbnail+`
									</div>
									<div class="media-description">
										<span>`+file.name.substr(0, 10)+`</span>
									</div>`;

					mediaListContainer.appendChild(item);
				});
			}
		}else{
			var item = document.createElement('div');
			item.classList.add('no-item');
			item.innerHTML =`<span class="fs-4">No media or folder found</span>
							<p>Use the upload button or the create folder buttons provided above.</p>`;

			mediaListContainer.appendChild(item);
		}

		if (data.breadcrumbs) {
			setBreadcrumb(data);
		}

		addEventMediaList(data);
	}

	function setMediaList(data){

		mediaListContainer.innerHTML = "";

		if (data.folders.length > 0 || data.files.length > 0) {
			if (data.folders.length) {
				data.folders.forEach(function(folder){
					var item = document.createElement('div');
					item.classList.add('media-item','mlist');
					item.setAttribute('data-id',folder.id);
					item.setAttribute('data-name',folder.name);
					item.setAttribute('data-created',folder.created_at);
					item.setAttribute('data-updated',folder.updated_at);
					item.setAttribute('data-type', 'folder');
					item.innerHTML = `<div class="media-thumbnail">
										<span class="bi-folder me-2"></span>
										<span>`+folder.name.substr(0, 30)+`</span>
									</div>
									<div class="media-description">
										<span class="me-2">`+folder.created_at.substr(0,19)+`</span>
									</div>`;

					mediaListContainer.appendChild(item);
				});
			}
			
			if (data.files.length) {
				data.files.forEach(function(file){
					var thumbnail = "";

					switch(file.type){
					case "image":
						thumbnail = '<span class="bi-image me-2"></span>';
						break;
					case "video":
						thumbnail = '<span class="bi-camera-video me-2"></span>';
						break;
					default:
						thumbnail = '<span class="bi-file-text me-2"></span>';
						break;
					} 

					var item = document.createElement('div');
					item.classList.add('media-item','mlist');
					item.setAttribute('data-id',file.id);
					item.setAttribute('data-name',file.name);
					item.setAttribute('data-type',file.type);
					item.setAttribute('data-size',file.size);
					item.setAttribute('data-mime_type',file.mime_type);
					item.setAttribute('data-url',file.url);
					item.setAttribute('data-created',file.created_at);
					item.setAttribute('data-updated',file.updated_at);
					item.innerHTML = `<div class="media-thumbnail">
										`+thumbnail+`
										<span>`+file.name.substr(0, 30)+`</span>
									</div>
									<div class="media-description">
										<span class="me-2">`+file.size+`</span>
										<span class="me-2">`+file.created_at.substr(0, 19)+`</span>
									</div>`;

					mediaListContainer.appendChild(item);
				});
			}
		}else{
			var item = document.createElement('div');
			item.classList.add('no-item');
			item.innerHTML =`<span class="fs-4">No media or folder found</span>
							<p>Use the upload button or the create folder buttons provided above.</p>`;

			mediaListContainer.appendChild(item);
		}

		if (data.breadcrumbs) {
			setBreadcrumb(data);
		}

		addEventMediaList(data);
	}

	function setBreadcrumb(data){
		mediaBreadcrumb.innerHTML = "";

		data.breadcrumbs.forEach(function(breadcrumb){
			var item = document.createElement('li');
			item.classList.add('breadcrumb-item');
			item.setAttribute('data-id',breadcrumb.id);
			item.innerHTML = `<a class="javascript:"   href="javascript:">
								<span>
									`+breadcrumb.icon+`
									`+breadcrumb.name+`
								</span>
							</a>`;
			mediaBreadcrumb.appendChild(item);

			item.addEventListener('click', function(el){
				folderId = breadcrumb.id;
				fetchMedia();
			});
		});
	}

	function addEventMediaList(data){
		if (data.folders.length > 0 || data.files.length > 0) {

			// single click
			document.querySelectorAll(".media-item").forEach(function(it){
				it.addEventListener('click',function(e){
					e.stopPropagation();

					onSelectMediaItem(this);

					actionBtn.classList.remove('disabled');
				});
			});

			//double click
			document.querySelectorAll(".media-item").forEach(function(it){
				it.addEventListener("dblclick", function(e){
					switch(this.dataset.type){
					case "folder":
						folderId = this.dataset.id;
						fetchMedia();//reload
						break;
					case "image":
					case "video":
						previewMedia(this);
						break;
					default:
						downloadMedia(this);
						break;
					}
				});
			});

			// for unselecting all media
			mediaListContainer.addEventListener('click', function(){
				//reset items selected array
				selectedItem = [];
				//remove hightlight
				if (document.querySelectorAll('.media-item')) {
					document.querySelectorAll('.media-item').forEach(function(el) {
						el.classList.remove('selected');
					});
				}
				//disable action button
				actionBtn.classList.add('disabled');

				mediaDetail.querySelector('.media-thumbnail').innerHTML = "";
				mediaDetail.querySelector('.media-description').innerHTML = "";
			});

		}
	}

	function onSelectMediaItem(item){

		if(!selectedItem.some(it => { return it.id == item.dataset.id && it.type == item.dataset.type; })){

			selectedItem.push({id: item.dataset.id,type: item.dataset.type, url: item.dataset.url});
			item.classList.add('selected');

			var thumbnail = "";
			var textField = "";
			switch(item.dataset.type){
			case "image":
				thumbnail = '<img src="'+item.dataset.url+'">';
				break;
			case "video":
				thumbnail = '<span class="bi-camera-video"></span>';
				break;
			case "folder":
				thumbnail = '<span class="bi-folder"></span>';
				break;
			default:
				thumbnail = '<span class="bi-file-text"></span>';
				break;
			} 
			if (item.dataset.url != null) {
				textField = `<p class="m-0 fw-bolder mt-2">Url</p>
					<div class="input-group mb-2">
							<input type="text" id="copy-url-input" class="form-control py-1 border"  aria-label="Url" value="`+item.dataset.url+`" aria-describedby="urlf">
							<button class="btn btn-sm btn-light px-2" type="button" id="urlf" onclick="copyLink()">copy</button>
					</div>`;
			}

			mediaDetail.querySelector('.media-thumbnail').innerHTML = thumbnail;
			mediaDetail.querySelector('.media-description').innerHTML = 
			`<p class="m-0 fw-bolder">Name</p>
			<span>`+item.dataset.name.substr(0,15)+`</span>
			`+textField+`
			<p class="m-0 fw-bolder">Created</p>
			<span>`+item.dataset.created.substr(0,19)+`</span>
			<p class="m-0 fw-bolder">Updated</p>
			<span>`+item.dataset.updated.substr(0,19)+`</span>`;
			
		}else{

			item.classList.remove('selected');
			selectedItem.pop({id: item.dataset.id, type: item.dataset.type});
			mediaDetail.querySelector('.media-thumbnail').innerHTML = "";
			mediaDetail.querySelector('.media-description').innerHTML = "";
		}
	}

	function previewMedia(item = null){
		var imageUrl = [];

		if (item != null) {
			imageUrl.push({type: item.dataset.type, url: item.dataset.url});
		}else{
			selectedItem.forEach((it) =>{
				if (it.type == "image" || it.type == "video") {
					imageUrl.push({type: it.type, url: it.url});
				}
			});
		}

		if (imageUrl.length == 0) return;

		previewImageDialog(imageUrl);
	}

	function cropMedia(){
		if (!(selectedItem.length > 0)) return;
		//Get only one item of type image
		if (selectedItem[0].type != "image") return;
		const imageEdit = selectedItem[0];

		cropImageDialog(
			imageEdit.url,
			function(blob = null){
				if (blob != null) {
					updateCroppedMedia(imageEdit.id, blob);
				}
			},
			function(){}
		)
	}

	async function updateCroppedMedia(id, blob){
		const formData = new FormData();
		formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);
		formData.append("id", id);
		formData.append('image', blob);

		try{
			const response = await fetch("/admin/media/crop",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) { 
				const result = await response.json();
				fetchMedia();//reload
				Toast("success", result.message);
			}else{
				console.log(await response.text())
				Toast("error", "Internal server error, try again later");
			}
		}catch(error){
			console.log(error);
		}
	}

	window.copyLink = () => {
		var text = document.getElementById('copy-url-input');
		text.select();

		document.execCommand("copy");
	}

	async function makeCopy(){
		if (!(selectedItem.length > 0)) return;

		var cache_list = mediaListContainer.innerHTML;
		mediaListContainer.innerHTML = media_spinner;

		const formData = new FormData();
		formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);
		formData.append("folderId", folderId);
		selectedItem.forEach(function(sl){
			formData.append("media["+sl.id+"]", sl.type);
			console.log("type = "+sl.type)
		});
	
		try{
			const response = await fetch("/admin/media/make-copy",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) { 
				const result = await response.json();
				fetchMedia();//reload
				Toast("success", result.message);
			}else{
				mediaListContainer.innerHTML = cache_list;
				console.log(await response.text())
				Toast("error", "Internal server error, try again later");
			}
		}catch(error){
			console.log(error);
		}
		mediaListContainer.innerHTML = cache_list;
	}

	async function downloadMedia(){

		if (!(selectedItem.length > 0)) return;

		var cache_list = mediaListContainer.innerHTML;
		mediaListContainer.innerHTML = media_spinner;

		var query = "";
		query +="folderId="+folderId;
		selectedItem.forEach(function(it){
			query += "&media["+it.id+"]="+it.type;
		});

		const url = "/admin/media/download"+"?"+query;
		try{
			const response = await fetch(url,{
				method: "GET",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
			});

			if (response.ok) { 
				const header = await response.headers.get('Content-Disposition');
				const parts = header.split(';');
				const filename = parts[1].split('=')[1];
				console.log(filename);

				var blob =  await response.blob();
				var file_url = window.URL.createObjectURL(blob);

				var a = document.createElement('a');
				a.href = file_url;
				a.download = filename;
				document.body.appendChild(a);
				a.click();
				a.remove();
				//window.location.assign(file);
				Toast("success", "Media file  downloaded successfully.s");
			}else{
				mediaListContainer.innerHTML = cache_list;
				console.log(await response.text())
				Toast("error", "Internal server error, try again later");
			}
		}catch(error){
			console.log(error);
		}
		mediaListContainer.innerHTML = cache_list;
	}

	async function trashMedia(){
		
		if (!(selectedItem.length > 0)) return;

		var cache_list = mediaListContainer.innerHTML;
		mediaListContainer.innerHTML = media_spinner;

		var formData = new FormData();
		formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);
		selectedItem.forEach(function(sl){
			formData.append("media["+sl.id+"]", sl.type);
		});

		try{
			const response = await fetch("/admin/media/delete",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				fetchMedia();//reload
				Toast("success", result.message);
			}else{
				mediaListContainer.innerHTML = cache_list;
				console.log(await response.text())
				Toast("error", "Internal server error, try again later");
			}
		}catch(error){
			console.log(error);
		}
	}

	async function createFolder(folderName){

		const formData = new FormData();
		formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);
		formData.append("folderId", folderId);
		formData.append("folderName", folderName);

		try{
			const response = await fetch("/admin/media/create/folder",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				if (!result.error) {
					fetchMedia();//reload
					Toast("success", result.message);
				}else{
					Toast("error", result.message);
				}
			}else{
				console.log(await response.text())
				Toast("error", "Internal server error, try again later");
			}

			uploadBtn.innerHTML = `<i class="bi-upload text-white"></i>  Upload`;

		}catch(error){
			console.log(error);
		}
	}

	async function uploadMedia(input){

		uploadBtn.innerHTML = `<div class="spinner-grow spinner-grow-sm text-info" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>`;

		const formData = new FormData();
		formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);
		formData.append("folderId", folderId);
		for (var i = 0; i < input.files.length; i++) {
			formData.append("file[]", input.files[i]);
		}
		
		try{
			const response = await fetch("/admin/media/upload",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				if (!result.error) {
					fetchMedia();//reload
					Toast("success", result.message);
					input.files = null;
				}else{
					Toast("error", result.message);
				}
			}else{
				console.log(await response.text())
				Toast("error", "Internal server error, try again later");
			}

			uploadBtn.innerHTML = `<i class="bi-upload text-white"></i>  Upload`;
		}catch(error){
			console.log(error);
		}
	}

	/**
	 * Preview images
	 * @param Array Object item for images to be previewed
	 * */
	function previewImageDialog(item){
		var carouselItem = "";
		var items = 0;
		var active = "active";
		item.forEach((it) => {
			if(item > 0) active ="";
			if (it.type == "image") {
				carouselItem += `<div class="carousel-item `+active+`">
					  			<img src="`+it.url+`" style="max-height:600px; margin:auto; display:block;"  alt="...">
					  		</div>`;
			}else{
				carouselItem += `<div class="carousel-item `+active+`">
									<video style="max-height:600px; margin:0 auto; display:block;" controls>
									  <source src="`+it.url+`" type="video/mp4">
									  <source src="`+it.url+`" type="video/ogg">
									</video>
					  			</div>`;
			}
		});
		const dialog = document.createElement('div');
		dialog.classList.add("modal", "fade");
		dialog.setAttribute("id","errorModal");
		dialog.setAttribute("tabindex","-1");
		dialog.setAttribute("aria-labelledby","cropModal");
		dialog.setAttribute("aria-hidden","true");
		dialog.setAttribute("data-bs-backdrop","static");
		dialog.innerHTML = `<div class="modal-dialog modal-xl z-3">
		    <div class="modal-content">
		      	<div class="modal-header bg-secondary border-0">
		        	<h1 class="modal-title fs-6 text-white" id="exampleModalLabel">Preview</h1>
		        	<button type="button" id="close" class="border-0 bg-transparent" data-bs-dismiss="modal" aria-label="Close">
		        		<i class="bi-x-lg text-white"></i>
		        	</button>
		      	</div>
		      <div class="modal-body bg-light">
		      <div class="row">
		      	<div class="col-12">
		      		<div id="carouselMedia" class="carousel slide" style="overflow:hidden;">
					  	<div class="carousel-inner" style="min-height:400px">
					  		`+carouselItem+`
					  	</div>
					  	<button class="carousel-control-prev" type="button" data-bs-target="#carouselMedia" data-bs-slide="prev">
					  		<span class="carousel-control-prev-icon bg-info p-2" aria-hidden="true"></span>
					  		<span class="visually-hidden">Previous</span>
					  	</button>
					  	<button class="carousel-control-next" type="button" data-bs-target="#carouselMedia" data-bs-slide="next">
					  		<span class="carousel-control-next-icon bg-info p-2" aria-hidden="true"></span>
					  		<span class="visually-hidden">Next</span>
					  	</button>
					 </div>
		      	</div>
		      </div>
		    </div>
		 </div>`;
		document.body.appendChild(dialog);
		new bootstrap.Modal(dialog).show();

		dialog.querySelector('#close').onclick = () =>{
			dialog.remove();
		}
	}

	/**
	 * Display image crop dialog/modal
	 * @param String url of image
	 * @param Function crop for saving the cropped image
	 * @param Fuction cancel for cancelling cropping
	 * */
	function cropImageDialog(url, crop, cancel){
		const dialog = document.createElement('div');
		dialog.classList.add("modal", "fade");
		dialog.setAttribute("id","errorModal");
		dialog.setAttribute("tabindex","-1");
		dialog.setAttribute("aria-labelledby","cropModal");
		dialog.setAttribute("aria-hidden","true");
		dialog.setAttribute("data-bs-backdrop","static");
		dialog.innerHTML = `<div class="modal-dialog modal-xl z-3">
		    <div class="modal-content">
		      	<div class="modal-header bg-secondary border-0">
		        	<h1 class="modal-title fs-6 text-white" id="exampleModalLabel">Crop</h1>
		        	<button type="button" id="close" class="border-0 bg-transparent" data-bs-dismiss="modal" aria-label="Close">
		        		<i class="bi-x-lg text-white"></i>
		        	</button>
		      	</div>
		      <div class="modal-body">
		      <div class="row">
		      	<div class="col-lg-9">
		      		<div class="" style="min-height:300px; width:100%;">
		      		   <img id="image" src="" style="diplay:block;max-width:100%;">
		      		</div>
		      	</div>
		      	<div class="col-lg-3">
		      		<span><b>Original dimension:</b></span><br>
		      		<span id="original-dimension">200 x 300</span>
		      		<div class="row">
		      			<div class="col-6">
		      				<label class="form-label mt-4">height</label>
		      				<input class="form-control px-1" type="number" name="height" value="0">
		      			</div>
		      			<div class="col-6">
		      				<label class="form-label mt-4">width</label>
		      				<input class="form-control px-1" type="number" name="width" value="0">
		      			</div>
		      		</div>
		      	</div>
		      </div>
		      	<div class="modal-footer border-0 mt-4">
		        	<button type="button" id="cancel" class="btn btn-sm btn-outline-info" data-bs-dismiss="modal">Cancel</button>
		        	<button type="button" id="crop" class="btn btn-sm btn-info text-white" data-bs-dismiss="modal">Crop</button>
		     	 </div>
		    </div>
		 </div>`;
		 document.body.appendChild(dialog);
		 new bootstrap.Modal(dialog).show();

		var img = dialog.querySelector("#image");
		var originalDimension = dialog.querySelector('#original-dimension');
		var imageHeight = dialog.querySelector('input[name="height"]');
		var imageWidth = dialog.querySelector('input[name="width"]');
		var cropper;
	
		img.onload = () => {
		    cropper = new Cropper(image, {
		    	viewMode: 2,
		        dragMode: 'move',
		        autoCropArea: 1,
		        restore: true,
		        guides: true,
		        center: true,
		        highlight: true,
		        cropBoxMovable: true,
		        cropBoxResizable: true,
		        toggleDragModeOnDblclick: false,
		        minContainerHeight: 300,
		        minContainerWidth: 600,
		        crop: function(e){
		        	var data = e.detail;

		        	imageHeight.value = Math.round(data.height);
  					imageWidth.value = Math.round(data.width);
		        },
	      	});

	      	originalDimension.innerHTML = img.height+" x "+img.width;
		}
		
		img.src = url;

		dialog.querySelector('#close').onclick = () =>{
			dialog.remove();
		}

		dialog.querySelector('#cancel').onclick = () =>{
			dialog.remove();
		}

		dialog.querySelector('#crop').onclick = () =>{
			var canvas = cropper.getCroppedCanvas();
      		canvas.toBlob(function (blob) {
      			crop(blob);
      		});

      		dialog.remove();
		}
	}

	/**
	 * Display action dialog
	 * @param String msg message of the dialog
	 * @param Function proceed  action ok/accept callback
	 * @param Function cancel action cancel callback
	 * @param String type the type of dialog
	 * @param String header the title of dialog
	 **/
	function mediaDialog(msg, proceed, cancel, type = "secondary", header="Confirm"){
		var bg = "";

		switch(type){
		case "error":
			bg = "bg-danger";
			break;
		case "info":
			bg = "bg-info";
			break;
		case "warning":
			bg = "bg-warning";
			break;
		case "success":
			bg = "bg-success";
			break;
		default:
			bg = "bg-secondary";
			break;
		}

		var dialog = document.createElement("div");
		dialog.classList.add("modal", "fade");
		dialog.setAttribute("id","errorModal");
		dialog.setAttribute("tabindex","-1");
		dialog.setAttribute("aria-labelledby","exampleModalLabel");
		dialog.setAttribute("aria-hidden","true");
		dialog.innerHTML = `<div class="modal-dialog z-3">
		    <div class="modal-content">
		      	<div class="modal-header `+bg+` border-0">
		        	<h1 class="modal-title fs-6 text-white" id="exampleModalLabel">`+header+`</h1>
		        	<button type="button" class="close border-0 bg-transparent" data-bs-dismiss="modal" aria-label="Close">
		        	<i class="bi-x-lg text-white"></i></button>
		      	</div>
		      <div class="modal-body"><span>`+msg+`</span>
		      	<div class="modal-footer border-0">
		        	<button type="button" id="cancel" class="btn btn-sm btn-outline-info" data-bs-dismiss="modal">Cancel</button>
		        	<button type="button" id="ok" class="btn btn-sm btn-danger text-white" data-bs-dismiss="modal">Confirm</button>
		     	 </div>
		    </div>
		 </div>`;
		document.body.appendChild(dialog);
	 	var modal = new bootstrap.Modal(dialog).show();

		dialog.querySelector(".close").onclick = () => {
			dialog.remove();
		}

		dialog.querySelector("#cancel").onclick = () => {
			dialog.remove();
			cancel();
		}

		dialog.querySelector("#ok").onclick = () => {
			dialog.remove();
			proceed();
		} 
	}


	function getUrlParam(paramName) {
	  var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
	  var match = window.location.search.match(reParam);
	  return ( match && match.length > 1 ) ? match[1] : null;
	}

	/**
	 * WYSIWYG Editors Check
	 **/
	function usingTinymce5(){
		return !!getUrlParam('editor');
	}
});