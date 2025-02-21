@extends('layouts.installer')
@section('content')
<section class="installer-container">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-10">
            <div class="rounded-3 shadow-lg bg-white">
                <div class="header">
                    <h5 class="text-white">Server requirements</h5>
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
                        <li class="divider"></li>
                        <li>     
                            <i class="bi-shield-lock"></i>    
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
                    @foreach($requirements['requirements'] as $type => $requirement)
                     <ul class="list-group @if($type == 'apache') mt-4 @endif">
                         <li class="list-group-item bg-light d-flex justify-content-between align-items-center">
                            <span class="me-4 text-capitalize">{{ $type }}
                                @if($type == 'php')
                                <small>
                                (Required  v{{ $phpVersion['minimum'] }}
                                  found  <b>v{{ $phpVersion['current'] }}</b>)
                                </small>
                                @endif
                            </span>
                            @if($type == 'php')
                                @if($phpVersion['supported'])
                                <span class="bi-check-circle text-success opacity-50 fs-5"></span>
                                @else
                                <span class="bi-x-circle text-danger opacity-50 fs-5"></span>
                                @endif
                            @endif
                        </li>
                        @foreach($requirements['requirements'][$type] as $extention => $enabled)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="me-4">{{ $extention }}</span>
                                @if($enabled)
                                <span class="bi-check-circle text-success opacity-50 fs-5"></span>
                                @else
                                <span class="bi-x-circle text-danger opacity-50 fs-5"></span>
                                @endif
                            </li>
                            
                        @endforeach
                     </ul>
                    @endforeach
                </div>
                @if(!isset($requirements['errors']) && $phpVersion['supported'])
                <div class="button text-center">
                    <a class="btn btn-sm btn-primary  px-4" href="{{ url('/install/permissions') }}">
                        Check permissions
                        <i class="bi-chevron-right text-white"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
