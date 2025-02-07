@extends('layouts.main')

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-tag-multiple" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>Categorías
                    <a href="{{ url('categorias/create') }}" class="btn btn-rounded-circle py-1 btn-outline-primary ml-2">
                        <i class="mdi mdi-plus mdi-18px"></i>
                    </a>
                </h1>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pull-up pb-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive p-t-10">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Géneros</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categorias as $categoria)
                                <tr>
                                    <td>{{$categoria->nombre}}</td>
                                    <td style="max-width: 50%">

                                        @foreach($categoria->generos as $genero)
                                        <div class="btn ml-2 my-2 badge badge-soft-dark">
                                            {{$genero->nombre}}
                                            <a class="editar-genero text-primary ml-2">
                                                <i class="icon-placeholder mdi mdi-pencil-box-outline"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="delete-genero text-danger"></a>

                                            <i class="icon-placeholder mdi mdi-minus-box-outline"></i>
                                            </a>
                                        </div>
                                        @endforeach


                                        <div class="collapse">


                                            <div class="btn ml-2 my-2 badge badge-soft-dark">

                                                <a class="editar-genero text-primary ml-2"></a>

                                                <i class="icon-placeholder mdi mdi-pencil-box-outline"></i>
                                                </a>
                                                <a href="javascript:void(0)" class="delete-genero text-danger">
                                                    <i class="icon-placeholder mdi mdi-minus-box-outline"></i>
                                                </a>
                                            </div>


                                        </div>

                                        <a class="btn py-0 btn-sm btn-outline-primary ml-2 collapsed">
                                            ...
                                        </a>

                                    </td>
                                    <td>
                                        {{$categoria->updated_at->format('d/m/Y')}}
                                    </td>
                                    <td>
                                        <!-- Botón para agregar géneros (modal o acción) -->
                                        <a data-toggle="modal">
                                            <i class="mdi mdi-plus mdi-18px"></i>
                                        </a>

                                        <!-- Botón para editar la categoría -->
                                        <a href="/categorias/{{$categoria->id}}/edit" class="btn btn-sm m-b-15 ml-1 btn-primary py-0 px-1">
                                            <i class="mdi mdi-pencil mdi-18px"></i>
                                        </a>

                                        <!-- Botón para eliminar la categoría -->
                                        <form action="{{ route('categorias.delete', $categoria->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm ml-1 m-b-15 btn-danger py-0 px-1 action-delete">
                                                <i class="mdi mdi-tag-minus mdi-18px"></i>
                                            </button>
                                        </form>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection