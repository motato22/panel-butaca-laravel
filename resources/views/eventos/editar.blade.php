@extends('layouts.main')
@section('content')
<div class="container">
    <h1>Editar Evento</h1>
    <form action="{{ route('eventos.update', $evento->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" value="{{ $evento->nombre }}" required>
        </div>
        <div class="mb-3">
            <label for="foto" class="form-label">Foto Principal</label>
            <input type="file" class="form-control" name="foto">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>

<script>
    // let horarios = @json($evento->horario);

    // Renderizar horarios al cargar la página
    document.addEventListener("DOMContentLoaded", function() {
        let contenedor = document.getElementById("contenedor_horarios");
        contenedor.innerHTML = "";

        horarios.forEach(h => {
            let div = document.createElement("div");
            div.innerHTML = `
                <div class="form-group">
                    <label>Día</label>
                    <select name="horario[dias][]" class="form-control">
                        <option value="1" ${h.dias.includes("1") ? "selected" : ""}>Lunes</option>
                        <option value="2" ${h.dias.includes("2") ? "selected" : ""}>Martes</option>
                        <option value="3" ${h.dias.includes("3") ? "selected" : ""}>Miércoles</option>
                        <option value="4" ${h.dias.includes("4") ? "selected" : ""}>Jueves</option>
                        <option value="5" ${h.dias.includes("5") ? "selected" : ""}>Viernes</option>
                        <option value="6" ${h.dias.includes("6") ? "selected" : ""}>Sábado</option>
                        <option value="7" ${h.dias.includes("7") ? "selected" : ""}>Domingo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Horarios</label>
                    <input type="text" name="horario[horarios][]" class="form-control" value="${h.horarios.join(', ')}">
                </div>
            `;
            contenedor.appendChild(div);
        });
    });
</script>

@endsection