@extends('layouts.installer')
@section('content')
<section class="installer-container">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-10">
            <div class="rounded-3 shadow-lg bg-white">
                <div class="header">
                    <h5 class="text-white">{{ config('app.name') }} installer</h5>
                </div>
                <div class="steps">
                    <ul>
                        <li >
                            <a href="{{ url('/install') }}">
                                <i class="bi-house"></i>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>    
                            <i class="bi-list"></i>
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

                <div class="button text-center">
                    <a class="btn btn-sm btn-primary  px-4" href="{{ url('/install/requirements') }}">
                        Check Requirements
                        <i class="bi-chevron-right text-white"></i>
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</section>
@endsection
