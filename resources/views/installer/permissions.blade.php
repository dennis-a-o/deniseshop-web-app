@extends('layouts.installer')
@section('content')
<section class="installer-container">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-10">
            <div class="rounded-3 shadow-lg bg-white">
                <div class="header">
                    <h5 class="text-white">Permissions</h5>
                </div>
                <div class="steps">
                    <ul>
                        <li >
                            <a href="{{ url('/install') }}">
                                <i class="bi-house"></i>
                            </a>
                        </li>
                        <li class="divider active"></li>
                        <li> 
                            <a href="{{ url('/install/requirements') }}">
                                <i class="bi-list"></i>
                            </a>   
                        </li>
                        <li class="divider active"></li>
                        <li> 
                            <a href="{{ url('/install/permissions') }}">    
                                <i class="bi-shield-lock"></i> 
                            </a>   
                        </li>
                        <li class="divider"></li>
                        <li>                            
                            <i class="bi-gear"></i>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <i class="bi-stack"></i>
                        </li>
                    </ul>
                </div>
                
                <div class="content">
                    <ul class="list-group">
                        @foreach($permissions['permissions'] as $permission)
                        <li class="list-group-item p-0 d-flex justify-content-between align-items-center">
                            <span class="py-2 px-3">{{ $permission['folder'] }}</span>
                            <div class="bg-light py-2 px-3  d-flex align-items-center">
                                @if($permission['isSet'])
                                <span class="bi-check-circle text-success opacity-50 fs-5 me-2"></span>
                                @else
                                <span class="bi-x-circle text-danger opacity-50 fs-5 me-2"></span>
                                @endif
                                <span>{{ $permission['permission'] }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
            
                </div>
            
                <div class="button text-center">
                    <a class="btn btn-sm btn-primary  px-4" href="{{ url('/install/environment') }}">
                        Configure permissions
                        <i class="bi-chevron-right text-white"></i>
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</section>
@endsection
