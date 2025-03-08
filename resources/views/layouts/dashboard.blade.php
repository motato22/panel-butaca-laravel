@extends('layouts.main')

@section('title', 'Panel')

@section('content')
<div class="container">
    <div class="row">

        @php
        $hour = now('America/Mexico_City')->format('H');

        if ($hour >= 6 && $hour < 12) {
            $greeting='Buenos Días' ;
            } elseif ($hour>= 12 && $hour < 19) {
                $greeting='Buenas Tardes' ;
                } else {
                $greeting='Buenas Noches' ;
                }
                @endphp

                <div class="col-lg-4 m-b-30">
                <div class="card m-b-30 full-height">
                    <img src="{{ asset('atmos/img/patterns/header.png') }}" class="rounded-top" width="100%" alt="header">
                    <div class="card-body bg-gray-900 rounded-bottom">
                        <div class="pull-up-sm">
                            <div class="avatar avatar-lg">
                                <div class="avatar-title rounded-circle mdi mdi-domain bg-primary"></div>
                            </div>
                        </div>
                        <h1 class="text-white pt-4 fw-300">
                            {{ $greeting }}, {{ auth()->user()->nombre }}
                        </h1>
                        <p class="opacity-75 text-white"></p>
                        <div>
                            <a href="javascript:void(0)" class="btn btn-success">View Reports</a>
                        </div>
                    </div>
                </div>
    </div>

</div>
</div>
@endsection