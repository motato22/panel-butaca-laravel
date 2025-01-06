@extends('layouts.main')

@extends('categorias.index')

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-tag-plus" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>{{ 'Nueva Categoria' }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf     

                <div class="d-flex justify-content-center align-items-center mb-5">
                    <div class="form-group custom-file col-md-6">
                        <input type="file" 
                               id="thumbnailFile" 
                               name="thumbnailFile" 
                               class="custom-file-input" 
                               accept=".png,.jpg,.svg" 
                               lang="es"
                               onchange="previewImage(event)">
                        <label for="thumbnailFile" class="custom-file-label text-center">Imagen de la Categoría</label>
                        <small class="form-text text-muted text-center">Imagen cuadrada (recomendado: 512px x 512px).</small>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nombre">Nombre de la Categoría</label>
                        <input type="text" 
                               id="nombre" 
                               name="nombre" 
                               class="form-control" 
                               maxlength="120" 
                               placeholder="Nombre de la categoría"
                               value="{{ old('nombre') }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="background">Color de fondo</label>
                        <input type="text" 
                               id="background" 
                               name="background" 
                               class="form-control with-colorpicker" 
                               placeholder="Color del fondo a mostrar en la app."
                               value="{{ old('background') }}">
                        <small class="form-text text-muted">Color característico de la categoría.</small>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-danger float-right mx-1">Crear</button>
                    <a href="{{url('categorias')}}" class="btn btn-secondary float-right mx-1">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
