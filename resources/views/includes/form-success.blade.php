@if (session('success'))
<div class="alert alert-success alert-dismissible">
	<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  	<strong>Success!</strong> 
  	<p class="p-0 m-0 text-left">
        {{ session('success') }}
    </p>
</div>
@endif

@if (session('unsuccess'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <strong>Error!</strong> 
    <p class="p-0 m-0 text-left">
        {{ session('unsuccess') }}
    </p>
</div>
@endif

@if (session('message'))
<div class="alert alert-info alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <strong>message!</strong> 
    <p class="p-0 m-0 text-left">
        {{ session('message') }}
    </p>
</div>
@endif

@if (session('warning'))
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <strong>Warning!</strong> 
    <p class="p-0 m-0 text-left">
        {{ session('warning') }}
    </p>
</div>
@endif