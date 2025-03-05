<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsersController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\RecintosController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\GenerosController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TipoZonaController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\SitioInteresController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CuponController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\PropertiesController;
use App\Http\Controllers\SupportFaqsController;
use App\Http\Controllers\NotificationsPushController;
use App\Http\Controllers\ZonaRecintoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return auth()->check() ? redirect('dashboard') : redirect('login');
});

// Login nativo
Route::get('login', function () {
    return view('login');
})->name('login');

// Reset account
Route::get('reset-preview', function () {
    return view('mails.reset_password');
});

// Login
Route::post('login', [LoginController::class, 'index'])->name('login');

// Logout url
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

// Recover
Route::get('recuperar-cuenta', [LoginController::class, 'resetView'])->name('resetView');
Route::post('recuperar-cuenta', [LoginController::class, 'resetPassword'])->name('resetPassword');


// You must be logged in to access
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [LoginController::class, 'loadDashboard'])->name('dashboard');
    });

    // Usuarios

    Route::prefix('users')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('users.index');
        Route::get('/create', [UsersController::class, 'create'])->name('users.create');
        Route::get('/{id}/edit', [UsersController::class, 'edit'])->name('users.edit');
        Route::post('/{id}/update', [UsersController::class, 'update'])->name('users.update');
        Route::post('add', [UsersController::class, 'add'])->name('users.add');
        Route::get('/{id}/toggle-activation', [UsersController::class, 'toggleActivation'])->name('users.toggleActivation');
        Route::resource('users', UsersController::class)->except(['destroy']);
        Route::post('/notification/send', [UsersController::class, 'sendNotification'])->name('users.sendNotification');
        Route::post('/notification/bulk-send', [UsersController::class, 'sendBulkNotification'])->name('users.sendBulkNotification');
        // Route::post('/notificaciones/enviar', [NotificacionesController::class, 'enviar'])->name('notificaciones.enviar');
        Route::delete('/{id}', [UsersController::class, 'delete'])->name('users.destroy');
    });

    // Categorias
    Route::prefix('categorias')->group(function () {
        Route::get('/', [CategoriasController::class, 'index'])->name('categorias.index'); // Listar categorías
        Route::get('/create', [CategoriasController::class, 'create'])->name('categorias.create'); // Formulario de creación
        Route::post('/add', [CategoriasController::class, 'add'])->name('categorias.add'); // Crear categoría
        Route::get('/{id}/edit', [CategoriasController::class, 'edit'])->name('categorias.edit'); // Formulario de edición
        Route::put('/{id}/update', [CategoriasController::class, 'update'])->name('categorias.update'); // Actualizar categoría
        Route::delete('/{id}/delete', [CategoriasController::class, 'delete'])->name('categorias.delete'); // Eliminar categoría
    });

    // Rutas para gestión de géneros dentro de categorías
    Route::prefix('generos')->group(function () {
        Route::post('/{id}/editar', [GenerosController::class, 'update'])->name('generos.update'); // Editar género
        Route::delete('/{id}/eliminar', [GenerosController::class, 'delete'])->name('generos.delete'); // Eliminar género
    });


    // Recintos

    Route::prefix('recintos')->group(function () {
        Route::get('/', [RecintosController::class, 'index'])->name('recintos.index');
        Route::get('/create', [RecintosController::class, 'create'])->name('recintos.create');
        Route::post('/store', [RecintosController::class, 'store'])->name('recintos.store');
        Route::get('/{recinto}/edit', [RecintosController::class, 'edit'])->name('recintos.edit');
        Route::put('/{recinto}', [RecintosController::class, 'update'])->name('recintos.update');
        Route::delete('/{recinto}', [RecintosController::class, 'destroy'])->name('recintos.delete');

        // Gestión de usuarios de un recinto
        Route::get('{recinto}/addUsers', [RecintosController::class, 'addUsers'])->name('recintos.addUsers');
        Route::post('{recinto}/addUsers', [RecintosController::class, 'storeUser'])->name('recintos.storeUser');
        Route::delete('{recinto}/removeUser/{user}', [RecintosController::class, 'removeUser'])->name('recintos.removeUser');

        // Gestión de imágenes del recinto
        Route::get('/{recinto}/galeria', [RecintosController::class, 'addImages'])->name('recintos.addImages');
        Route::post('/{recinto}/galeria/agregar', [RecintosController::class, 'storeImages'])->name('recintos.storeImages');
        Route::delete('recintos/{recinto}/galeria/{imagen}', [RecintosController::class, 'deleteImage'])
            ->where('imagen', '[0-9]+')
            ->name('recintos.deleteImage');
    });

    Route::prefix('eventos2')->group(function () {
        Route::get('/', [EventosController::class, 'index'])->name('eventos.index');
        Route::get('/nuevo', [EventosController::class, 'create'])->name('eventos.create');
        Route::post('/store', [EventosController::class, 'store'])->name('eventos.store');
        Route::get('/{evento}/edit', [EventosController::class, 'edit'])->name('eventos.edit');
        Route::put('/{evento}', [EventosController::class, 'update'])->name('eventos.update');
        Route::delete('/{evento}', [EventosController::class, 'destroy'])->name('eventos.destroy');

        // Gestión de géneros asociados a un evento
        Route::get('{evento}/addGeneros', [EventosController::class, 'addGeneros'])->name('eventos.addGeneros');
        Route::delete('{evento}/{genero}/delGeneros', [EventosController::class, 'delGeneros'])->name('eventos.delGeneros');

        // Gestión de imágenes del evento
        Route::get('{evento}/galeria', [EventosController::class, 'addImages'])->name('eventos.addImages');
        Route::post('{evento}/galeria/agregar', [EventosController::class, 'storeImages'])->name('eventos.storeImages');
        Route::delete('galeria/{imagen}', [EventosController::class, 'deleteImage'])->name('eventos.deleteImage');

        // Duplicar evento
        Route::post('{evento}/duplicar', [EventosController::class, 'duplicateEvent'])->name('eventos.duplicar');

        // Horarios
        Route::post('/horario/agregar', [EventosController::class, 'agregarHorario'])->name('eventos.horario.agregar');
        Route::delete('{evento}/horario/{index}/remover', [EventosController::class, 'removerHorarioPorIndice'])->name('eventos.horario.remover');

        // Activación recomendada
        Route::get('{evento}/recomendado/toggle', [EventosController::class, 'toggleActivacion'])->name('eventos.toggleActivacion');
    });

    // Devuelve los géneros de una categoría en JSON (para el select de géneros)
    Route::get('/categorias/{categoria}/generos', [CategoriasController::class, 'getGeneros'])
        ->name('categorias.getGeneros');

    // Asocia un género a un evento (pivote genero_evento)
    Route::post('/eventos/{evento}/generos', [EventosController::class, 'attachGenero'])
        ->name('eventos.attachGenero');

    // Elimina un género de un evento
    Route::delete('/eventos/{evento}/generos/{genero}', [EventosController::class, 'detachGenero'])
        ->name('eventos.detachGenero');

    //Zonas
    Route::resource('zonas', ZonaRecintoController::class);
    // Ruta manual para eliminar:
    Route::delete('/zonas/{zona}', [ZonaRecintoController::class, 'destroy'])
        ->name('zonas.destroy');

    // Tipo Zonas
    Route::prefix('/tipoZona')->name('tipo_zona.')->group(function () {
        Route::get('/', [TipoZonaController::class, 'index'])->name('index');
        Route::get('/new', [TipoZonaController::class, 'create'])->name('create');
        Route::post('/new', [TipoZonaController::class, 'store'])->name('store');
        Route::get('/{tipoZona}/edit', [TipoZonaController::class, 'edit'])->name('edit');
        Route::put('/{tipoZona}', [TipoZonaController::class, 'update'])->name('update');
        Route::delete('/{tipoZona}', [TipoZonaController::class, 'destroy'])->name('destroy');
    });

    //Info
    Route::prefix('/info')->name('info.')->group(function () {
        Route::get('/', [InfoController::class, 'index'])->name('index');
        Route::get('/{info}', [InfoController::class, 'show'])->name('show');
        Route::get('/{info}/edit', [InfoController::class, 'edit'])->name('edit');
        Route::put('/{info}', [InfoController::class, 'update'])->name('update');
    });

    // Sitios de Interes
    Route::prefix('/sitios')->name('sitios.')->group(function () {
        Route::get('/', [SitioInteresController::class, 'index'])->name('index');
        Route::get('/create', [SitioInteresController::class, 'create'])->name('create');
        Route::post('/', [SitioInteresController::class, 'store'])->name('store');
        Route::delete('/{sitio}', [SitioInteresController::class, 'destroy'])->name('destroy');
    });

    // Banners
    Route::prefix('/banners')->name('banners.')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('index');
        Route::get('/create', [BannerController::class, 'create'])->name('create');
        Route::post('/', [BannerController::class, 'store'])->name('store');
        Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');
        Route::put('/{banner}', [BannerController::class, 'update'])->name('update');
        Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('destroy');
    });

    // Promociones
    Route::prefix('cupon')->name('cupon.')->group(function () {
        Route::get('/', [CuponController::class, 'index'])->name('index');
        Route::get('/create', [CuponController::class, 'create'])->name('create');
        Route::post('/', [CuponController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CuponController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CuponController::class, 'update'])->name('update');
        Route::delete('/{id}', [CuponController::class, 'destroy'])->name('destroy');

        // Toggle de activación
        Route::get('/{id}/toggle-activacion', [CuponController::class, 'toggleActivacion'])
            ->name('toggleActivacion');
    });

    // Notificaciones
    Route::prefix('notificacion')->name('notificacion.')->group(function () {
        Route::get('/', [NotificacionesController::class, 'index'])->name('index');
        Route::get('/create', [NotificacionesController::class, 'create'])->name('create');
        Route::post('/', [NotificacionesController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [NotificacionesController::class, 'edit'])->name('edit');
        Route::put('/{id}', [NotificacionesController::class, 'update'])->name('update');
        Route::delete('/{id}', [NotificacionesController::class, 'destroy'])->name('destroy');

        // Si deseas un toggle de activación para notificaciones
        Route::get('/{id}/toggleActivacion', [NotificacionesController::class, 'toggleActivacion'])
            ->name('toggleActivacion');
    });
});
