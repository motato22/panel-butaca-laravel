@extends('layouts.main')

@section('title', 'Editar recinto')

{{-- Inyectas los CSS de Froala al stack "styles" --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/froala-editor@4.0.14/css/froala_editor.pkgd.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/froala-editor@4.0.14/css/froala_style.min.css" />
@endpush

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
                <div class="row">
                   <div class="col-12 pt-4">
                        @if(session('error'))
                            <div class="alert alert-danger text-center">
                                <p><strong>No se pudo guardar la información</strong>: {{ session('error') }}</p>
                            </div>
                        @endif

                        <form action="{{ route('info.update', $info->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-12">
                                    <label for="info_texto">Texto</label>
                                    <textarea id="info_texto" name="texto" class="form-control" rows="10" required>
                                        {!! old('texto', $info->texto) !!}
                                    </textarea>
                                    @error('texto')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group p-t-25">
                                <button type="submit" class="btn btn-danger float-right mx-1">Guardar</button>
                                <a href="{{ route('info.index') }}" class="btn btn-secondary float-right mx-1">Cancelar</a>
                            </div>
                        </form>
                   </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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
                heightMin: 300
            });
        });
    </script>
@endpush
