document.addEventListener("DOMContentLoaded", function(event) {
	/*-------------------
	Init tinymce
	-------------------*/
	tinymce.init({
            selector:'#description',
            relative_urls: false,
            content_style:'body{font-family:"Open Sans", sans-serif;}p,span{color:#67748E;},',
            plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar:'blocks | forecolor backcolor fonts bold italic underline link strikethrough | alignleft aligncenter alignright alignjustify | image |undo redo',
            toolbar_sticky: false,
            automatic_uploads: false,
            toolbar_mode: 'fixed',
            branding: false,
            promotion: false,
            height:400,
            file_picker_callback: function(callback, value, meta){
                var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

                var cmsURL = '/admin/media/window?editor=' + meta.fieldname;

                tinymce.activeEditor.windowManager.openUrl({
                    url: cmsURL,
                    title: 'Filemanager',
                    width: x * 0.8,
                    height: y * 0.8,
                    close_previous: "no",
                    onMessage: (api, message) => {
                        callback(message.content);
                    }
                });
            }
    });

	/*----------------------------------
	 * Afílliate input fields show/hide
	 -----------------------------------*/
	document.getElementById("product-type").addEventListener("change", function(){
		var affliate_input = document.getElementById("affliate-input");
		switch(this.value){
		case "internal":
			affliate_input.style.display = "none";
			break;
		case "external":
			affliate_input.style.display = "block";
			break;
		}
	});

	/*----------------------------
	* Color input fields script
	-----------------------------*/
	document.querySelectorAll("#remove-color").forEach(function(el){
		el.addEventListener("click", function(){
			this.closest("tr").remove();
		});
	});	


	document.getElementById("add-color").addEventListener("click", function(e){
		e.preventDefault();
		var colorInput = document.getElementById("color-input");
		var colorTable = document.getElementById("color-table");
		var colorData = colorInput.value.split(","); 

		var row = document.createElement("tr");
		row.innerHTML = '<td><i class="bi-palette-fill" style="color:'+colorData[1]+'"></i></td>'+
						'<td><span>'+colorData[0]+'</span></td>'+
						'<td><span class="p-1 rounded-3 shadow-sm pointer" id="remove-color"><i class="bi-x-lg text-danger"></i></span></td>'+
						'<input type="hidden" name="color['+colorData[0]+']" value="'+colorData[1]+'">';
		colorTable.firstElementChild.appendChild(row);

		row.querySelector("#remove-color").addEventListener("click", function(){
			row.remove();
		});
	});

	/*----------------------------
	* Size input fields script
	------------------------------*/
	document.querySelectorAll("#remove-size").forEach(function(el){
		el.addEventListener("click", function(){
			this.closest("div").remove();
		});
	});	
	

	document.getElementById("add-size").addEventListener("click", function(e){
		e.preventDefault();
		var sizeInput = document.getElementById("size-input");
		var sizeList = document.getElementById("size-list");

		var item = document.createElement("div");
		item.classList.add("d-inline-block", "mb-4");
		item.innerHTML = '<span class="bg-light px-3 py-2 rounded-2 shadow-sm me-4 mt-2" id="remove-size"><i class="bi-x pointer me-2"></i>'+sizeInput.value+'</span>'+
						'<input type="hidden" name="size[]" value="'+sizeInput.value+'">';
		sizeList.appendChild(item);
		
		item.querySelector("#remove-size").addEventListener("click", function(){
			item.remove();
		});
	});

	/*----------------------------------
	* Gallery input select and preview 
	------------------------------------*/
	if (document.getElementById("gallery-select") != null) {
		document.getElementById("gallery-select").addEventListener("change", function(){
			var galleryList = document.getElementById("gallery-list");

			var item = document.createElement("div");
			item.classList.add("d-inline-block", "me-2", "position-relative");
			item.innerHTML = '<img class="rounded-3 shadow-sm mb-4" src="#" height="75" width="75">'+
							'<input type="file" name="gallery[]" class="d-none" id="gallery">'+
							'<a class="position-absolute start-0" id="remove-image" href="javascript:">'+
							'<i class="bi-x-lg p-1 bg-light rounded-3 shadow-sm"></i></a>';

			galleryList.appendChild(item);

			item.querySelector("img").src = URL.createObjectURL(this.files[0]);
			item.querySelector("input").files = this.files;
			item.querySelector("#remove-image").addEventListener("click", function(){ 
				item.remove(); 
			});
		});
	}

	/*-------------------
	* Gallery delete
	---------------------*/
	if (document.querySelectorAll("#gallery")) {
		document.querySelectorAll("#gallery").forEach(function(el){
			el.querySelector("a").addEventListener("click", function(){
				removeGallery(el);
			});
		});
	}

	/*------------------------
	* Removing preadded tags
	--------------------------*/
	if (document.querySelectorAll("#tag")) {
		document.querySelectorAll("#tag").forEach(function(el){
			el.querySelector("#remove-tag").addEventListener("click", function(){
				removeTag(el);
			});
		});
	}

	/*------------------------
	* Adding removing tags
	-----------------------*/
	if (document.getElementById("tag-select") != null) {
		document.getElementById("tag-select").addEventListener("keypress", function(e){
			if (e.key === "Enter") {
				e.preventDefault()
				var tagValue = this.value;
				var tagList = document.getElementById("tag-list");
				
				var tag = document.createElement("div");
				tag.classList.add("d-inline-block", "mb-3", "me-1");
				tag.innerHTML = '<span class="bg-light rounded-3 shadow-sm px-2 py-1">'+tagValue+
								'<a href="javascript:" id="remove-tag"><i class="bi-x ms-2"></i></a></span>'+
								'<input type="hidden" name="tag[]" value="0">';
				tagList.appendChild(tag);

				addTag(tagValue, tag);
				this.value = "";

				tag.querySelector("#remove-tag").addEventListener("click", function(){
					tag.remove();
				});
			}
		});
	}

	async function addTag(tag, element){
		const formData = new FormData();
		formData.append("tag", tag);
		formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

		try{
			const response = await fetch("/admin/product/tag/create",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				element.querySelector("input").value = result.id;
			}else{
				console.error(await response.text());
			}

		}catch(error){
			console.log(error);
		}
	}

	async function removeTag(element){
		const formData = new FormData();
		formData.append("tag_id", element.querySelector("input").value);
		formData.append("product_id", element.querySelector("input").dataset.productid);
		formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

		try{
			const response = await fetch("/admin/product/tag/delete",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				element.remove();
			}else{
				console.error(await response.text());
			}

		}catch(error){
			console.log(error);
		}
	}

	async function removeGallery(element){
		const formData = new FormData();
		formData.append("image_name", element.querySelector("input").dataset.galleryname);
		formData.append("product_id", element.querySelector("input").dataset.productid)
		formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

		try{
			const response = await fetch("/admin/product/gallery/delete",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				Toast("success", result.message)
				element.remove();
			}else{
				console.error(await response.text());
			}

		}catch(error){
			Toast("error", "Something went wrong!");
			//console.log(error);
		}
	}
});