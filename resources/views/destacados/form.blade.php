@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class="bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <h1>Banner publicitario</h1>
                </div>
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
                            <li class="breadcrumb-item active" aria-current="page"><a href="{{url('banners')}}"></a>Formulario</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container pull-up">
        <div class="row">
            <div class="col-lg-12 m-b-30">
                <div class="card">
                    <div class="card-header">
                        <h2 class="">Complete el formulario</h2>
                    </div>
                    <div class="card-body">
                       <form id="form-data" action="{{url('banners/'.($item ? 'update' : 'save'))}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
                            <div class="row">
	                            <div class="form-group col-md-12 text-center">
	                                <label class="avatar-input" style="max-width: 300px;">
	                                	{{ $item && $item->tipo ? $item->tipo->medidas : '' }}
	                                    {{-- <span class="avatar avatar-xxl"> --}}
	                                        <img src="{{asset($item && $item->foto ? $item->foto : 'img/no-image.png')}}" alt="..." class="avatar-img avatar-profile-img">
	                                        <span class="avatar-input-icon"><i class="mdi mdi-upload mdi-24px"></i></span>
	                                    {{-- </span> --}}
	                                    <input type="file" name="foto" class="avatar-file-picker not-empty file image" data-target="avatar-profile-img" data-msg="Foto de banner">
	                                </label>
	                            </div>
	                            <div class="form-group col-md-12 d-none">
	                                <label>ID</label>
	                                <input type="text" class="form-control" name="id" value="{{$item ? $item->id : ''}}">
	                            </div>
	                            <div class="form-group col-md-6">
	                                <label>Tipo de banner</label>
	                                <input type="text" class="form-control" disabled value="{{$item && $item->tipo ? $item->tipo->nombre : ''}}">
	                            </div>
	                            <div class="form-group col-md-6">
	                                <label>País</label>
	                                <input type="text" class="form-control" disabled value="{{$item && $item->pais ? $item->pais->nombre : ''}}">
	                            </div>
	                            {{-- <div class="form-group {{$item && $item->foto ? 'col-md-10' : 'col-md-12' }}">
                                    <label class="label-control" for="foto">Foto</label>
                                    <input type="file" class="form-control file image not-empty" name="foto" data-msg="Foto">
                                </div> --}}
								<div class="form-group col-md-12">
                                    <label>Link del banner</label>
                                    <input type="text" class="form-control not-empty link" name="link" value="{{$item ? $item->link : ''}}" placeholder="Https://..." data-msg="Link">
                                </div>
                            </div>
                            
                            <div class="form-group m-t-15">
                                <a href="{{url('banners')}}"><button type="button" class="btn btn-primary">Regresar</button></a>
                                <button type="submit" class="btn btn-success save">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection