@if ($errors->any())
<div class="alert alert-danger alert-dismissible">
	<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  	<strong>Error!</strong> 
  	<ul class="p-0 m-0 text-left">
        @foreach ($errors->all() as $error)
            <li>{!! $error !!}</li>
        @endforeach
    </ul>
</div>
@endif