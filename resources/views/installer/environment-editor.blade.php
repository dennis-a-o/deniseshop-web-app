@extends('layouts.installer')
@section('content')
<section class="installer-container">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-10">
            <div class="rounded-3 shadow-lg bg-white">
                <div class="header">
                    <h5 class="text-white">Environment editor settings (.env)</h5>
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
                        <li class="divider active"></li>
                        <li>                            
                            <a href="{{ url('/install/environment') }}">    
                                <i class="bi-gear"></i> 
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <i class="bi-stack"></i>
                        </li>
                    </ul>
                </div>
                
                <div class="button text-center">
                    <a class="btn btn-primary btn-sm me-3 px-4" href="{{ url('/install/environment/wizard') }}">
                        <i class="bi-sliders text-white me-2"></i>
                        Form wizard
                    </a>
                    <a class="btn btn-primary btn-sm px-4" href="{{ url('/install/environment/editor') }}">
                        <i class="bi-code text-white me-2"></i>
                        Text editor
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</section>
@endsection
