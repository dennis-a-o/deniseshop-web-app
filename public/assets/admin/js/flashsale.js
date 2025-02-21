document.addEventListener("DOMContentLoaded", function(event) {
		//window global variables
		var page = 1;
		var perPage = 10;
		var totalProduct = 0;
		var searchValue = "";
		var selectedProducts = [];
		var searchProductList = document.getElementById("product-search-list");
		var productList =  document.getElementById("product-list");
		
		//add event listener to search product input
		document.querySelector('input[name="search-products"]').addEventListener("focus", function(e){
			searchProducts();

			this.addEventListener("input", function(){
				searchValue = this.value;
				searchProducts();
			});
		});

		//add event listener to 'add-selected' button
		document.getElementById("add-selected").addEventListener("click", function(){
			addSelectedProduct();
		});

		//add event listener to 'cancel-search' button
		document.getElementById("cancel-search").addEventListener("click", function(){
			searchProductList.style.display = "none";
			searchProductList.querySelector("ul").innerHTML = "";
		});

		//add event listener to 'previous-search-page' button
		document.getElementById("previous-search-page").addEventListener("click", function(){
			if(totalProduct > perPage){
				if (page > 1) {
					--page;
					searchProducts();
				}
			}
		});

		//add event listener to 'next-search-page' button
		document.getElementById("next-search-page").addEventListener("click", function(){
			if(totalProduct > perPage){
				if (page < (totalProduct/perPage)) {
					++page;
					searchProducts();
				}
			}
		});

		// add event listener to 'remove-selected' button
		document.querySelectorAll("#remove-selected").forEach(function(el){
			el.addEventListener("click", function(){
				this.closest("#product-item").remove();
			});
		});

		/** 
		 * add product to selectedProduct[] to prevent dubplicates in edit page 
		 */
		document.querySelectorAll("#product-item").forEach(function(el){
			selectedProducts.push(el.dataset.id);
		});

		async function searchProducts(){
	
			var offset = (page - 1) * perPage+1;
			searchProductList.style.display = "block";
			searchProductList.querySelector("ul").innerHTML = '<li class="list-group-item text-center">'+
											'<div class="spinner-border text-primary" role="status">'+
													'<span class="visually-hidden">Loading...</span>'+
											'</div>'+
										'</li>';

	   		const queryParams = {
		   		offset: offset,
		   		per_page: perPage,
		   		search_value: searchValue,
		   	}

		   	const queryString = new URLSearchParams(queryParams).toString();

			let url = "/admin/flash-sale/search/product"+"?"+queryString;
			try{
				const response = await fetch(url,{
					method: "GET",
					mode: "cors",
					cache: "no-cache",
					credentials: "same-origin",
				});

				if (response.ok) {
					const result = await response.json();

					searchProductList.querySelector("ul").innerHTML = result.data;
					totalProduct = result.total;

					searchResultAddEvent();
				}else{
					console.error(await response.text());
				}

			}catch(error){
				searchProductList.querySelector("ul").innerHTML = "";
				console.error(error);
				Toast("error", "Something went wrong, try again later.");
			}
		}

		function searchResultAddEvent(){
			searchProductList.querySelectorAll("li").forEach(function(el){
				el.addEventListener("click", function(){
					//for background highlight color
					this.classList.toggle("badge-primary");
					//for slecting perpose
					this.classList.toggle("selected");
				});
			});
		}

		/**
		 * add selected product to document form
		 */
		function addSelectedProduct(){

			searchProductList.querySelectorAll("li").forEach(function(el){
				if (el.classList.contains("selected")) {
					if (!selectedProducts.includes(el.dataset.id)) {
						var node = document.createElement("li");
						node.classList.add("list-group-item","py-4", "border-top");
						node.setAttribute("id","product-item");
						node.innerHTML = '<div class="row">'+
													'<div class="col-12">'+
														'<div class="d-flex align-items-center justify-content-between">'+
															'<div>'+
																'<img class="rounded-2 me-2" src="'+el.dataset.image+'" width="40" height="40">'+
																'<a href="'+el.dataset.url+'">'+el.dataset.title+'</a>'+
															'</div>'+
															'<a id="remove-selected" href="javascript:"><i class="bi-x-lg"></i> </a>'+
														'</div>'+
													'</div>'+
													'<div class="col-6">'+
														'<label class="form-label mt-4">Price</label>'+
														'<input class="form-control" type="number" name="product['+el.dataset.id+'][price]" value="'+el.dataset.price+'">'+
													'</div>'+
													'<div class="col-6">'+
														'<label class="form-label mt-4">Quantity</label>'+
														'<input class="form-control" type="number" name="product['+el.dataset.id+'][quantity]" value="'+el.dataset.quantity+'">'+
													'</div>'+
												'</div>';
						productList.appendChild(node)
						//add event listener
						node.querySelector("#remove-selected").addEventListener("click", function(){
							node.remove();
							selectedProducts.pop(el.dataset.id);
						});
						//push id to selected array
						selectedProducts.push(el.dataset.id);
					}
				}
			});

			//remove search window
			searchProductList.style.display = "none";
			searchProductList.querySelector("ul").innerHTML = "";
		}
	});
