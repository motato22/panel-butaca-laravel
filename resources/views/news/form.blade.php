@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-40 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum">
                            {{-- <li class="breadcrumb-item"><a href="javascript:;">Prensa</a></li> --}}
                            <li class="breadcrumb-item "><a href="{{url('prensa')}}">Prensa </a></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 m-auto text-white p-t-40 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
                            <li class="breadcrumb-item active" aria-current="page">Formulario</li>
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
                        <h2 class="">Ingresa los datos de la noticia</h2>
                    </div>
                    <div class="card-body">
                        <form id="form-data" action="{{url('prensa/'.($item ? 'update' : 'save'))}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
                            <div class="text-center">
                                <label class="avatar-input">
                                    <span class="avatar avatar-xxl">
                                        <img src="{{asset($item ? $item->photo : 'img/no-image.png')}}" alt="..." class="avatar-img avatar-profile-img rounded-circle">
                                        <span class="avatar-input-icon rounded-circle"><i class="mdi mdi-upload mdi-24px"></i></span>
                                    </span>
                                    <input type="file" name="photo" class="avatar-file-picker file image {{$item ? '' : 'not-empty'}}" data-target="avatar-profile-img" data-msg="Foto">
                                </label>
                            </div>
                            <div class="form-row">
                                <div class="form-group d-none">
                                    <label>ID</label>
                                    <input type="text" class="form-control" name="id" value="{{$item ? $item->id : ''}}">
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Link*</label>
                                    <input type="text" class="form-control not-empty" name="link" value="{{$item ? $item->link : ''}}" placeholder="Enlace" data-msg="Enlace">
                                </div>
                            </div>
                            <div class="form-group m-t-15">
                                <a href="{{url('prensa')}}"><button type="button" class="btn btn-primary">Regresar</button></a>
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