document.addEventListener('DOMContentLoaded', function(){

	/*Toggle show/hide passord input*/
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
});