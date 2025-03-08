<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" name="viewport">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('') }}">
    <meta name="user-id" content="{{ auth()->user() }}">
    <title>@yield('title', isset($title) ? $title .' | '.env('APP_NAME') : env('APP_NAME'))</title>
    <script src="{{ asset('vendor/jquery/jquery.min.js')}}"></script>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/pace/pace.css') }}">
    <script src="{{ asset('vendor/pace/pace.min.js') }}"></script>
    <!--vendors-->
    <link rel="stylesheet" type="text/css" href="https://rawgit.com/noppa/text-security/master/dist/text-security.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/jquery-scrollbar/jquery.scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/jquery-ui/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/timepicker/bootstrap-timepicker.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Hind+Vadodara:400,500,600" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fonts/jost/jost.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css?v=1.1') }}">
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.css') }}">

    <!--Material Icons-->
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/materialdesignicons/materialdesignicons.min.css') }}">
    <!--Bootstrap + atmos Admin CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/atmos.css') }}">



    <!-- Additional library for page -->
    <link rel="stylesheet" href="{{ asset('vendor/DataTables/datatables.min.css')}}">
    <link rel="stylesheet" href="{{ asset('vendor/DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">

    {{-- Summernote --}}
    <link rel="stylesheet" href="{{ asset('vendor/summernote/summernote-bs4.css') }}" />

    {{-- Gallery plugin --}}
    <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">

    @stack('styles')
</head>

<body class="sidebar-pinned">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-brand">
            <!-- begin sidebar branding-->
            {{-- <img class="admin-brand-logo" src="{{ asset('img/logo.png') }}" width="40" alt="atmos Logo"> --}}
            <i class="mdi mdi-alpha-b-box mdi-36px"></i>
            <a href="{{ url('dashboard') }}" class="menu-link">
                <span class="admin-brand-content">Butaca</span>
            </a>
            <!-- end sidebar branding-->
            <div class="ml-auto">
                <!-- sidebar pin-->
                <a href="#" class="admin-pin-sidebar btn-ghost btn btn-rounded-circle"></a>
                <!-- sidebar close for mobile device-->
                <a href="#" class="admin-close-sidebar"></a>
            </div>
        </div>
        <div class="admin-sidebar-wrapper js-scrollbar">
            <ul class="menu">
                @if( auth()->user()->role == 'ROLE_ADMIN' )
                <li class="menu-item {{ in_array($menu, ['Usuarios']) ? 'active opened' : ''}}">
                    <a href="{{url('users')}}" class="menu-link">
                        <span class="menu-label">
                            <span class="menu-name">Usuarios</span>
                        </span>
                        <span class="menu-icon"><i class="icon-placeholder mdi mdi-account-group"></i></span>
                    </a>
                </li>
                @endif

                @if( auth()->user()->role == 'ROLE_ADMIN' )
                <li class="menu-item {{ in_array($menu, ['Categorias']) ? 'active opened' : ''}}">
                    <a href="{{url('categorias')}}" class="menu-link">
                        <span class="menu-label">
                            <span class="menu-name">Categorias</span>
                        </span>
                        <span class="menu-icon">
                            <i class="icon-placeholder mdi mdi-tag-multiple"></i>
                        </span>
                    </a>
                </li>
                @endif

                @if( auth()->user()->role == 'ROLE_ADMIN' )
                <li class="menu-item {{ in_array($menu, ['Recintos']) ? 'active opened' : ''}}">
                    <a href="{{url('recintos')}}" class="menu-link">
                        <span class="menu-label">
                            <span class="menu-name">Recintos</span>
                        </span>
                        <span class="menu-icon">
                            <i class="icon-placeholder mdi mdi-theater"></i>
                        </span>
                    </a>
                </li>
                @endif

                @if( auth()->user()->role == 'ROLE_ADMIN' )
                <li class="menu-item {{ in_array($menu, ['Eventos']) ? 'active opened' : ''}}">
                    <a href="{{url('eventos2')}}" class="menu-link">
                        <span class="menu-label">
                            <span class="menu-name">Eventos</span>
                        </span>
                        <span class="menu-icon">
                            <i class="icon-placeholder mdi mdi-calendar-clock"></i>
                        </span>
                    </a>
                </li>
                @endif

                @if( auth()->user()->role == 'ROLE_ADMIN' )
                <li class="menu-item {{ in_array($menu, ['Zona_recintos']) ? 'active opened' : ''}}">
                    <a href="{{url('zonas')}}" class="menu-link">
                        <span class="menu-label">
                            <span class="menu-name">Zona Recintos</span>
                        </span>
                        <span class="menu-icon">
                            <i class="icon-placeholder mdi mdi-map"></i>
                        </span>
                    </a>
                </li>
                @endif

                @if( auth()->user()->role == 'ROLE_ADMIN' )
                <li class="menu-item {{ in_array($menu, ['Tipo_Zonas']) ? 'active opened' : ''}}">
                    <a href="{{url('tipoZona')}}" class="menu-link">
                        <span class="menu-label">
                            <span class="menu-name">Tipos de Zona</span>
                        </span>
                        <span class="menu-icon">
                            <i class="icon-placeholder mdi mdi-map-legend"></i>
                        </span>
                    </a>
                </li>
                @endif

                @if( auth()->user()->role == 'ROLE_ADMIN' )
                <li class="menu-item {{ in_array($menu, ['Informacion']) ? 'active opened' : ''}}">
                    <a href="{{url('info')}}" class="menu-link">
                        <span class="menu-label">
                            <span class="menu-name">Información</span>
                        </span>
                        <span class="menu-icon">
                            <i class="icon-placeholder mdi mdi-information"></i>
                        </span>
                    </a>
                </li>
                @endif

                @if( auth()->user()->role == 'ROLE_ADMIN' )
                <li class="menu-item {{ in_array($menu, ['SitiosInteres']) ? 'active opened' : ''}}">
                    <a href="{{ route('sitios.index') }}" class="menu-link">
                        <span class="menu-label">
                            <span class="menu-name">Sitios de Interés</span>
                        </span>
                        <span class="menu-icon">
                            <i class="icon-placeholder mdi mdi-link"></i>
                        </span>
                    </a>
                </li>
                @endif

                @if( auth()->user()->role == 'ROLE_ADMIN' )
                <li class="menu-item {{ in_array($menu, ['Banner']) ? 'active opened' : ''}}">
                    <a href="{{ route('banners.index') }}" class="menu-link">
                        <span class="menu-label">
                            <span class="menu-name">Banner</span>
                        </span>
                        <span class="menu-icon">
                            <i class="icon-placeholder mdi mdi-page-layout-header"></i>
                        </span>
                    </a>
                </li>
                @endif

                @if( auth()->user()->role == 'ROLE_ADMIN' )
                <li class="menu-item {{ in_array($menu, ['Promociones']) ? 'active opened' : ''}}">
                    <a href="{{ route('cupon.index') }}" class="menu-link">
                        <span class="menu-label">
                            <span class="menu-name">Promociones|Notificaciones</span>
                        </span>
                        <span class="menu-icon">
                            <i class="icon-placeholder mdi mdi-ticket-percent"></i>
                        </span>
                    </a>
                </li>
                @endif


            </ul>{{-- Ul menu container --}}
        </div>

    </aside>
    <main class="admin-main">
        <!--site header begins-->
        <header class="admin-header">
            <a href="#" class="sidebar-toggle" data-toggleclass="sidebar-open" data-target="body"></a>

            {{-- Menú izquierdo vacío (mr-auto) --}}
            <nav class="mr-auto my-auto"></nav>

            {{-- Menú derecho (ml-auto) --}}
            <nav class="ml-auto">
                <ul class="nav align-items-center">
                    {{-- Campana de notificaciones (idéntico al Panel.html original) --}}
                    <li class="nav-item">
                        <div class="dropdown">
                            <a href="#" class="nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-24px mdi-bell-outline"></i>
                                <!-- Si quieres el globito rojo de ejemplo:
                             Ajusta la clase .notification-counter con tu CSS para posicionar y colorear el círculo. -->
                                <span class="notification-counter">3</span>
                            </a>
                            <div class="dropdown-menu notification-container dropdown-menu-right">
                                <div class="d-flex p-all-15 bg-white justify-content-between border-bottom">
                                    <a href="#!" class="mdi mdi-18px mdi-settings text-muted"></a>
                                    <span class="h5 m-0">Notificaciones</span>
                                    <a href="#!" class="mdi mdi-18px mdi-notification-clear-all text-muted"></a>
                                </div>
                                <div class="notification-events bg-gray-300">
                                    <div class="text-overline m-b-5">Hoy</div>
                                    <p class="d-block m-b-10">
                                    <div class="card">
                                        <div class="card-body">
                                            <i class="mdi mdi-information-variant text-info"></i> No tienes notificaciones
                                        </div>
                                    </div>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </li>

                    {{-- Avatar con foto o inicial --}}
                    <li class="nav-item dropdown">
                        @php
                        $foto = auth()->user()->foto; // Campo con la ruta de la foto en la BD
                        $nombre = auth()->user()->nombre; // Campo con el nombre del usuario
                        $inicial = strtoupper(substr($nombre, 0, 1));
                        @endphp

                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="avatar avatar-sm avatar-online">
                                @if(!empty($foto) && file_exists(public_path($foto)))
                                {{-- Si existe la foto en /public/... --}}
                                <img src="{{ asset($foto) }}" alt="Foto de {{ $nombre }}" class="avatar-img rounded-circle">
                                @else
                                {{-- Si no hay foto, usamos la inicial con fondo azul marino (#000080) --}}
                                <span class="avatar-title rounded-circle text-white"
                                    style="background-color: #000080; width: 40px; height: 40px; font-size: 1rem; font-weight: 600;">
                                    {{ $inicial }}
                                </span>
                                @endif
                            </div>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right">
                            {{-- Ajusta según tus roles o links --}}
                            <a class="dropdown-item" href="{{ url('mi-perfil') }}">Cambiar contraseña</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item log-out" href="javascript:;">Cerrar sesión</a>
                        </div>
                    </li>
                </ul>
            </nav>
        </header>
        <!--site header ends-->
        @yield('content')
    </main>


    <script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{ asset('vendor/popper/popper.js')}}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js')}}"></script>
    <script src="{{ asset('vendor/jquery-scrollbar/jquery.scrollbar.min.js')}}"></script>
    <script src="{{ asset('vendor/listjs/listjs.min.js')}}"></script>
    <script src="{{ asset('vendor/moment/moment.min.js')}}"></script>
    <script src="{{ asset('vendor/daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{ asset('vendor/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
    <script src="{{ asset('js/atmos.min.js')}}"></script>
    <script src="{{ asset('vendor/DataTables/datatables.min.js')}}"></script>
    <script src="{{ asset('js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/systemFunctions.js')}}"></script>
    <script src="{{ asset('js/general-ajax.js')}}"></script>
    <script src="{{ asset('js/validfunctions.js')}}"></script>
    <script src="{{ asset('js/globalFunctions.js?v=1.2')}}"></script>
    <script src="{{ asset('vendor/blockui/jquery.blockUI.js')}}"></script>
    <script src="{{ asset('vendor/timepicker/bootstrap-timepicker.min.js')}}"></script>
    <script src="{{ asset('vendor/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('vendor/jquery.mask/jquery.mask.min.js') }}"></script>
    {{-- <script src="https://js.pusher.com/4.1/pusher.min.js"></script> --}}
    <script src="{{ asset('vendor/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
    <script src="{{ asset('js/bootstrap-notify-data.js')}}"></script>
    <script src="{{ asset('js/jszip.min.js')}}"></script>

    {{-- Summernote --}}
    <script src="{{ asset('/vendor/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('/js/summernote-data.js') }}"></script>

    {{-- Printable --}}
    <script src="{{ asset('js/invoice-print.js') }}"></script>

    {{-- Gallery plugin --}}
    <script src="{{ asset('js/jquery.magnific-popup.js') }}"></script>

    <!--page specific scripts for demo-->

    <!--Additional Page includes-->
    <script src="{{ asset('vendor/apexchart/apexcharts.min.js')}}"></script>
    <!--chart data for current dashboard-->
    <script src="{{ asset('/js/dashboard-02.js')}}"></script>

    <script type="text/javascript">
        var puntos_min = 33;
        id_photos = [];
        var baseUrl = "{{url('')}}";
        var current_user_id = $('meta[name=user-id]').attr('content');
    </script>
    @yield('scripts')
    @stack('scripts')
</body>

</html>