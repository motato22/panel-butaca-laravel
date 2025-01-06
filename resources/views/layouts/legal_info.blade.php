<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" name="viewport">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<title> {{$title}} | Información legal</title>
<link rel="icon" type="image/x-icon" href="{{ asset('/img/logo.png') }}"/>
<link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png "sizes="16x16">
<link rel="stylesheet" href="{{ asset('vendor/pace/pace.css') }}">
<script src="{{ asset('vendor/pace/pace.min.js') }}"></script>
<!--vendors-->
<link rel="stylesheet" type="text/css" href="{{ asset('/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('/vendor/jquery-scrollbar/jquery.scrollbar.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/jquery-ui/jquery-ui.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/timepicker/bootstrap-timepicker.min.css') }}">
<link href="https://fonts.googleapis.com/css?family=Hind+Vadodara:400,500,600" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('fonts/jost/jost.css') }}">
<link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">

<!--Material Icons-->
<link rel="stylesheet" type="text/css" href="{{ asset('fonts/materialdesignicons/materialdesignicons.min.css') }}">
<!--Bootstrap + atmos Admin CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('css/atmos.min.css') }}">
<!-- Additional library for page -->

</head>
<body class="jumbo-page">

<main class="admin-main">
    <div class="container">
        <div class="row m-h-100 ">
            <div class="col-md-12 m-auto">
                <div class="card shadow-lg ">
                    <div class="card-body">
                    	<div class="col-lg-12 m-b-20 text-center">
                    		<img src="{{asset('img/logo_completo.png')}}" class="img-responsive" width="150px;">
                    	</div>
						{!! $info->descripcion !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>
