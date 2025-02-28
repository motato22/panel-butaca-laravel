@extends('layouts.main')

@section('title', 'Editar Información')

@section('styles')

    <link rel="stylesheet" href="/css/atmos.css" />
    <link rel="stylesheet" href="/css/custom.css" /> 

   
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/froala-editor@4.0.14/css/froala_editor.pkgd.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/froala-editor@4.0.14/css/froala_style.min.css" />
@endsection

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
                <h1>Editando "{{ $info->info }}"</h1>
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger text-center">
                    <p><strong>No se pudo guardar la información</strong>: {{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('info.update', $info->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-12 pt-4">
                        <div class="form-group">
                            <label for="info_texto">Texto</label>
                            <textarea id="info_texto" name="texto" required class="form-control" rows="10">
                                {!! old('texto', $info->texto) !!}
                            </textarea>
                            @error('texto')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group pt-25">
                    <button type="submit" class="btn btn-danger float-right mx-1">Guardar</button>
                    <a href="{{ route('info.index') }}" class="btn btn-secondary float-right mx-1">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    
    <script src="https://cdn.jsdelivr.net/npm/froala-editor@4.0.14/js/froala_editor.pkgd.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        new FroalaEditor('#info_texto', {
            language: 'es',
            
            toolbarButtons: [
                'bold', 'italic', 'underline', '|',
                'formatOL', 'formatUL', 'paragraphFormat', '|',
                'align', 'insertLink', '|',
                'undo', 'redo'
            ],
            toolbarButtonsXS: [
                'bold', 'italic', 'underline', '|',
                'formatOL', 'formatUL', 'paragraphFormat', '|',
                'align', 'insertLink', '|',
                'undo', 'redo'
            ],
            toolbarButtonsSM: [
                'bold', 'italic', 'underline', '|',
                'formatOL', 'formatUL', 'paragraphFormat', '|',
                'align', 'insertLink', '|',
                'undo', 'redo'
            ],
           
            quickInsertTags: [],
            
            heightMin: 300
        });
    });
    </script>
@endsection
