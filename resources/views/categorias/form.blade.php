@extends('layouts.main')

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
                    <h1>{{isset($item) ? 'Categoría - Editar' : 'Nueva Categoria'}}</h1>
                </div>

            </div>
        </div>
    </div>

    <div class="container pull-up pb-5 mb-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                
                 

                    <div class="col-12 pt-4">
                    <form method="POST" action="{{isset($item) ? '/categorias/'.$item->id.'/update' : '/categorias/add'}}" enctype="multipart/form-data">
                        <div class="row">
                        @csrf
                        {{-- Campo: Imagen de la Categoría --}}
<div class="form-group col-12">
    <label for="thumbnailFile" class="custom-file-label">Imagen de la Categoría</label>
    <input 
        type="file" 
        id="thumbnailFile" 
        name="thumbnailFile" 
        class="custom-file-input" 
        accept=".png,.jpg,.svg">
    <small class="form-text text-muted">Imagen cuadrada (recomendado: 512px x 512px).</small>
    @error('thumbnailFile')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
{{-- Campo: Nombre de la Categoría --}}
<div class="form-group col-md-6">
    <label for="nombre">Nombre de la Categoría</label>
    <input 
        type="text" 
        id="nombre" 
        name="nombre" 
        class="form-control" 
        maxlength="120" 
        placeholder="Nombre de la categoría" 
        value="{{ isset($item) ? $item->nombre : '' }}">
    @error('nombre')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

{{-- Campo: Color de Fondo --}}
<div class="form-group col-md-6">
    <label for="background">Color de Fondo</label>
    <input 
        type="text" 
        id="background" 
        name="background" 
        class="form-control with-colorpicker" 
        placeholder="Color del fondo a mostrar en la app." 
        value="{{ isset($item) ? $item->background : '' }}">
    <small class="form-text text-muted">Color característico de la categoría.</small>
    @error('background')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>



<div class="form-group">

  
        <button type="submit" class="btn btn-danger float-right mx-1">{{isset($item) ? 'Guardar' : 'Crear'}}</button>
   

    <a href="{{ ('categorias') }}" class="btn btn-secondary float-right mx-1">Cancelar</a>
</div>
                        </div>
                        
                    </form>

                
                    
                        
                
                    </div>

                </div>
            </div>
        </div>
    </div>
<script>
    
</script>

@endsection