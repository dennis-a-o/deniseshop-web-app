@extends('layouts.installer')
@section('content')
<section class="installer-container">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-10">
            <div class="rounded-3 shadow-lg bg-white">
                <div class="header">
                    <h5 class="text-white">Environment wizard settings (.env)</h5>
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
                <div class="content">
                    <div>
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
                    </div>
                     <nav>
                        <div class="nav nav-tabs d-flex justify-content-center" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-environment" type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                                <i class="bi-gear me-2"></i>
                                Environment
                            </button>
                            <button class="nav-link" id="nav-database-tab" data-bs-toggle="tab" data-bs-target="#nav-database" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                                <i class="bi-stack me-2"></i>
                                Database
                            </button>
                            <button class="nav-link" id="nav-application-tab" data-bs-toggle="tab" data-bs-target="#nav-application" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">
                                <i class="bi-gear-wide-connected me-2"></i>
                                Application
                            </button>
                        </div>
                    </nav>
                    <form action="{{ url('/install/environment/wizard') }}" method="post">
                        @csrf
                         <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-environment" role="tabpanel" aria-labelledby="nav-environment-tab" tabindex="0">
                                <div class="row">
                                    <div class="col-12">
                                        <label class="form-label mt-4">App name</label>
                                        <input class="form-control" type="text" name="app_name" value=" " required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mt-4">App environment</label>
                                        <select class="form-select form-control" name="app_environment"  required>
                                            <option value="local">Local</option>
                                            <option value="development">Development</option>
                                            <option value="production">Production</option>
                                            <option value="qa">Qa</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mt-4">App debug</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="app_debug" id="appDebug1" checked>
                                            <label class="form-check-label" for="appDebug1">True</label>
                                         </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="app_debug" id="appDebug2" >
                                            <label class="form-check-label" for="appDebug2">False</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mt-4">App log level</label>
                                        <select class="form-select form-control" name="app_log_level" required>
                                            <option value="debug">debug</option>
                                            <option value="info">info</option>
                                            <option value="notice">notice</option>
                                            <option value="warning">warning</option>
                                            <option value="error">error</option>
                                            <option value="critical">critical</option>
                                            <option value="alert">alert</option>
                                            <option value="emergency">emergency</option>
                                        </select>
                                    </div>
                                     <div class="col-12">
                                        <label class="form-label mt-4">App url</label>
                                        <input class="form-control" type="text" name="app_url" value="http://localhost" required>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button id="setup-database" type="button" class="btn btn-primary btn-sm px-4 mt-4">Setup Database</button>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-database" role="tabpanel" aria-labelledby="nav-database-tab" tabindex="0">
                                <div class="row">
                                    <div class="col-12">
                                        <label class="form-label mt-4">Database connection</label>
                                        <select class="form-select form-control" name="database_connection" required>
                                            <option value="mysql">mysql</option>
                                            <option value="sqlite">sqlite</option>
                                            <option value="pgsql">pgsql</option>
                                            <option value="sqlsrv">sqlsrv</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mt-4">Database host</label>
                                        <input class="form-control" type="text" name="database_hostname" value="127.0.0.1" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mt-4">Database port</label>
                                        <input class="form-control" type="number" name="database_port" value="3306" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mt-4">Database name</label>
                                        <input class="form-control" type="text" name="database_name">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mt-4">Database user name</label>
                                        <input class="form-control" type="text" name="database_username">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mt-4">Database password</label>
                                        <div class="input-group mb-3">
                                            <input class="form-control" type="password" name="database_password">
                                            <button class="password-toggle btn btn-sm border" type="button">
                                                 <i class="bi-eye-slash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button id="setup-application" type="button" class="btn btn-primary btn-sm px-4 mt-4">Setup Application</button>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-application" role="tabpanel" aria-labelledby="nav-application-tab" tabindex="0">
                                <div class="list-group mt-4">
                                    <a class="list-group-item list-group-item-action bg-light" data-bs-toggle="collapse" href="#BCSQInput" role="button" aria-expanded="true">
                                        <span>Broadcasting, Caching, Session, &amp; Queue</span>
                                    </a>
                                     <a id="BCSQInput" class="collapse list-group-item show">
                                        <div  class="row">
                                            <div class="col-12">
                                                <label class="form-label mt-4">Broadcasting Driver</label>
                                                <input class="form-control" type="text" name="broadcast_driver" value="log">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label mt-4">Caching Driver</label>
                                                <input class="form-control" type="text" name="cache_driver" value="file">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label mt-4">Session Driver</label>
                                                <input class="form-control" type="text" name="session_driver" value="file">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label mt-4">Queue Driver</label>
                                                <input class="form-control" type="text" name="queue_driver" value="sync">
                                            </div>
                                        </div>
                                    </a>
                                    <a class="list-group-item list-group-item-action bg-light" data-bs-toggle="collapse" href="#redisInput" role="button" aria-expanded="false">
                                        <span>Redis Driver</span>
                                    </a>
                                    <a id="redisInput" class="collapse list-group-item">
                                        <div  class="row">
                                            <div class="col-12">
                                                <label class="form-label mt-4">Redis Host</label>
                                                <input class="form-control" type="text" name="redis_hostname" value="127.0.0.1" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label mt-4">Redis Port</label>
                                                <input class="form-control" type="number" name="redis_port" value="6379" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label mt-4">Redis password</label>
                                                <div class="input-group mb-3">
                                                    <input class="form-control" type="password" name="redis_password" value="null" >
                                                    <button class="password-toggle btn btn-sm border" type="button">
                                                         <i class="bi-eye-slash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="list-group-item list-group-item-action bg-light" data-bs-toggle="collapse" href="#mailInput" role="button" aria-expanded="false">
                                        <span>Mail</span>
                                    </a>
                                    <a id="mailInput" class="collapse list-group-item ">
                                        <div  class="row">
                                            <div class="col-12">
                                                <label class="form-label mt-4">Mail Driver</label>
                                                <input class="form-control" type="text" name="mail_driver" value="smtp" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label mt-4">Mail Host</label>
                                                <input class="form-control" type="text" name="mail_host" value="smtp.mailtrap.io" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label mt-4">Mail Port</label>
                                                <input class="form-control" type="number" name="mail_port" value="2525" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label mt-4">Mail Username</label>
                                                <input class="form-control" type="text" name="mail_username" value="null">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label mt-4">Mail Password</label>
                                                <input class="form-control" type="text" name="mail_password" value="null">
                                            </div>
                                             <div class="col-12">
                                                <label class="form-label mt-4">Mail Encryption</label>
                                                <input class="form-control" type="text" name="mail_encryption" value="null">
                                            </div>
                                        </div>
                                    </a>
                                    <a  class="list-group-item list-group-item-action bg-light" data-bs-toggle="collapse" href="#pusherInput" role="button" aria-expanded="false">
                                        <span>Pusher</span>
                                    </a>
                                    <a id="pusherInput" class="collapse list-group-item ">
                                        <div  class="row">
                                            <div class="col-12">
                                                <label class="form-label mt-4">Pusher App Id </label>
                                                <input class="form-control" type="text" name="pusher_app_id">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label mt-4">Pusher App Key</label>
                                                <input class="form-control" type="text" name="pusher_app_key">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label mt-4">Pusher App Secret</label>
                                                <input class="form-control" type="text" name="pusher_app_secret">
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="text-center">
                                        <button  class="btn btn-primary btn-sm px-4 mt-4">
                                            Install
                                            <i class="bi-chevron-right text-white ms-2"></i>
                                        </button>
                                 </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- <div class="button text-center">
                    <a class="btn btn-primary btn-sm me-3 px-4" href="{{ url('/install/environment/wizard') }}">
                        <i class="bi-sliders text-white me-2"></i>
                        Form wizard
                    </a>
                    <a class="btn btn-primary btn-sm px-4" href="{{ url('/install/environment/editor') }}">
                        <i class="bi-code text-white me-2"></i>
                        Text editor
                    </a>
                </div> -->
                
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function(event) {
        var applicationTab = document.getElementById('nav-application-tab');
        var databaseTab = document.getElementById('nav-database-tab');

        document.getElementById('setup-database').addEventListener('click', function(){
            databaseTab.click();
        });

        document.getElementById('setup-application').addEventListener('click', function(){
            applicationTab.click();
        });

        document.querySelectorAll('#password-toggle').forEach(function(it){
            it.addEventListener('click', function(){
                var sibling = it.previousElementSibling;
                var child = it.firstElementChild;

                if (sibling.getAttribute('type') == 'password') {
                    sibling.setAttribute('type', 'text');
                    child.classList.remove('bi-eye-slash');
                    child.classList.add('bi-eye');

                }else{
                    sibling.setAttribute('type', 'password');
                    child.classList.remove('bi-eye');
                    child.classList.add('bi-eye-slash');
                }

            });
        });
    });
</script>
@endsection
