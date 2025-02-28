@extends('layouts.main')

@section('title', 'Información')

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-information" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>Información</h1>
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @foreach($infos as $info)
                <div class="col-12 pt-5 pb-5">
                    <h2>{{ $info->info }}</h2>
                    {!! $info->texto !!}
                    <br>
                    <a href="{{ route('info.edit', $info->id) }}" class="btn btn-warning">Editar</a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
