@extends('layouts.main')

@section('title', 'BUTACA UDG | ' . $info->info)

@section('body')
<main class="admin-main bg-pattern">
    <div class="container p-t-20 p-b-20">
        <div class="row m-h-100">
            <div class="col-12 m-auto">
                <div class="card shadow-lg p-t-50 p-b-50 p-l-50 p-r-50">
                    <div class="card-body text-center">
                        <h1 class="display-3 fw-600 font-secondary">{{ $info->info }}</h1>
                        <h5 class="text-center pb-5">BUTACA UDG</h5>
                        <div class="text-justify p-b-30">
                            {!! $info->texto !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
