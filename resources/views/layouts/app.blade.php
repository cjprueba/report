<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Retail Reportes') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.css" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/plantilla.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm" style="margin-bottom:0px;" >
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Retail Reportes') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                             <!-- Nav Item - Alerts -->
                            <li class="nav-item dropdown no-arrow mx-1">
                              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" hidden>
                                <font-awesome-icon icon="bell" />
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                              </a>
                              <!-- Dropdown - Alerts -->
                              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown" >
                                <h6 class="dropdown-header">
                                  Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                  <div class="mr-3">
                                    <div class="icon-circle bg-primary">
                                      <i class="fas fa-file-alt text-white"></i>
                                    </div>
                                  </div>
                                  <div>
                                    <div class="small text-gray-500">December 12, 2019</div>
                                    <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                  </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                  <div class="mr-3">
                                    <div class="icon-circle bg-success">
                                      <i class="fas fa-donate text-white"></i>
                                    </div>
                                  </div>
                                  <div>
                                    <div class="small text-gray-500">December 7, 2019</div>
                                    $290.29 has been deposited into your account!
                                  </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                  <div class="mr-3">
                                    <div class="icon-circle bg-warning">
                                      <i class="fas fa-exclamation-triangle text-white"></i>
                                    </div>
                                  </div>
                                  <div>
                                    <div class="small text-gray-500">December 2, 2019</div>
                                    Spending Alert: We've noticed unusually high spending for your account.
                                  </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                              </div>
                            </li>
                             <!-- END Nav Item - Alerts -->

                            <!-- Nav Item - Messages -->
                            <li class="nav-item dropdown no-arrow mx-1" >
                              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" hidden>
                                <font-awesome-icon icon="envelope" />
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">7</span>
                              </a>
                              <!-- Dropdown - Messages -->
                              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                  Message Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                  <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="" alt="">
                                    <div class="status-indicator bg-success"></div>
                                  </div>
                                  <div class="font-weight-bold">
                                    <div class="text-truncate">Hi there! I am wondering if you can help me with a problem I've been having.</div>
                                    <div class="small text-gray-500">Emily Fowler · 58m</div>
                                  </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                  <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="" alt="">
                                    <div class="status-indicator"></div>
                                  </div>
                                  <div>
                                    <div class="text-truncate">I have the photos that you ordered last month, how would you like them sent to you?</div>
                                    <div class="small text-gray-500">Jae Chun · 1d</div>
                                  </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                  <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="" alt="">
                                    <div class="status-indicator bg-warning"></div>
                                  </div>
                                  <div>
                                    <div class="text-truncate">Last month's report looks great, I am very happy with the progress so far, keep up the good work!</div>
                                    <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                  </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                  <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60" alt="">
                                    <div class="status-indicator bg-success"></div>
                                  </div>
                                  <div>
                                    <div class="text-truncate">Am I a good boy? The reason I ask is because someone told me that people say this to all dogs, even if they aren't good...</div>
                                    <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                  </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                              </div>
                            </li> 
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="userDropdown" v-pre >
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#" disabled>
                                      <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                      Perfil
                                    </a>
                                    <a class="dropdown-item" href="#" disabled>
                                      <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                      Configuración
                                    </a>
                                    <a class="dropdown-item" href="#" disabled>
                                      <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                      Activity Log
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                         <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>                
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
