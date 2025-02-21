@extends('layouts.installer')
@section('content')
<section class="installer-container">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-10">
            <div class="rounded-3 shadow-lg bg-white">
                <div class="header">
                    <h5 class="text-white">Installation finished</h5>
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
                        <li class="divider active"></li>
                        <li>
                            <a href="void:">
                                <i class="bi-stack"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="content">
                    <div class="alert alert-success" role="alert">
                        Application has been successfully installed.
                    </div>
                    @if(session('message')['dbOutputLog'])
                        <p><strong><small>Migration &amp; Seed Console Output:</small></strong></p>
                        <pre><code>{{ session('message')['dbOutputLog'] }}</code></pre>
                    @endif
                    <p><strong><small>Application Console Output</small></strong></p>
                    <pre><code>{{ $finalMessages }}</code></pre>

                    <p><strong><small>Installation Log Entry:</small></strong></p>
                    <pre><code>{{ $finalStatusMessage }}</code></pre>

                    <p><strong><small>Final .env File:</small></strong></p>
                    <pre><code>{{ $finalEnvFile }}</code></pre>
                </div>
                <div class="button text-center">
                    <a class="btn btn-primary btn-sm px-4" href="{{ url('') }}">
                        <i class="bi-sliders text-white me-2"></i>
                        Exit
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</section>
@endsection