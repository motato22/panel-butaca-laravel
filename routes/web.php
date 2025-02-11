<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsersController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\RecintosController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\BlogsController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AwardsController;
use App\Http\Controllers\PrensaController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\PropertiesController;
use App\Http\Controllers\SupportFaqsController;
use App\Http\Controllers\NotificationsPushController;

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


    // Recintos

    Route::prefix('recintos')->group(function () {
        Route::get('/', [RecintosController::class, 'index'])->name('recintos.index');
        Route::get('/create', [RecintosController::class, 'create'])->name('recintos.create');
        Route::post('/store', [RecintosController::class, 'store'])->name('recintos.store');
        Route::get('/{id}/edit', [RecintosController::class, 'edit'])->name('recintos.edit');
        Route::put('/{id}/update', [RecintosController::class, 'update'])->name('recintos.update');
        Route::delete('/{id}/delete', [RecintosController::class, 'destroy'])->name('recintos.delete');
    });


    // Route::prefix('usuarios')->group(function () {
    //     Route::get('/nuevo-usuario', [UserController::class, 'create'])->name('nuevo_usuario');

    //     Route::get('/',[NewsController::class, 'index'])->name('Usuarios.index');
    //     Route::get('form/{id?}',[NewsController::class, 'form'])->name('news.form');
    //     Route::post('filter',[NewsController::class, 'filter'])->name('news.filter');
    //     Route::post('save',[NewsController::class, 'save'])->name('news.save');
    //     Route::post('update',[NewsController::class, 'update'])->name('news.update');
    //     Route::post('delete',[NewsController::class, 'delete'])->name('news.delete');
    // });


    // // Prensa
    // Route::prefix('prensa')->group(function () {
    //     Route::get('/',[NewsController::class, 'index'])->name('news.index');
    //     Route::get('form/{id?}',[NewsController::class, 'form'])->name('news.form');
    //     Route::post('filter',[NewsController::class, 'filter'])->name('news.filter');
    //     Route::post('save',[NewsController::class, 'save'])->name('news.save');
    //     Route::post('update',[NewsController::class, 'update'])->name('news.update');
    //     Route::post('delete',[NewsController::class, 'delete'])->name('news.delete');
    // });

    // Proyectos
    // Route::prefix('proyectos')->group(function () {
    //     Route::get('/',[ProjectsController::class, 'index'])->name('project.index');
    //     Route::get('form/{id?}',[ProjectsController::class, 'form'])->name('project.form');
    //     Route::get('get-gallery/{id}',[ProjectsController::class, 'getGallery'])->name('project.getGallery');
    //     Route::post('filter',[ProjectsController::class, 'filter'])->name('project.filter');
    //     Route::post('save',[ProjectsController::class, 'save'])->name('project.save');
    //     Route::post('update',[ProjectsController::class, 'update'])->name('project.update');
    //     Route::post('delete',[ProjectsController::class, 'delete'])->name('project.delete');
    //     Route::post('delete/photo',[ProjectsController::class, 'deletePhoto'])->name('project.deletePhoto');
    //     Route::post('upload-content',[ProjectsController::class, 'uploadContent'])->name('project.uploadContent');
    //     Route::post('delete-content',[ProjectsController::class, 'deleteContent'])->name('project.deleteContent');
    // });

    // Propiedades
    // Route::prefix('propiedades')->group(function () {
    //     Route::get('/',[PropertiesController::class, 'index'])->name('property.index');
    //     Route::get('form/{id?}',[PropertiesController::class, 'form'])->name('property.form');
    //     Route::get('crear-plan-de-pagos/{id}',[PaymentsController::class, 'formCreateInstallmentsPlan'])->name('property.formCreateInstallmentsPlan');
    //     Route::get('excel/export',[PropertiesController::class, 'export'])->name('property.export');
    //     Route::post('create-installment-plan',[PaymentsController::class, 'createInstallmentsPlan'])->name('property.createInstallmentsPlan');
    //     Route::post('filter',[PropertiesController::class, 'filter'])->name('property.filter');
    //     Route::post('state-account',[PropertiesController::class, 'stateAccount'])->name('property.stateAccount');
    //     Route::get('state-account/pdf/{id}',[PropertiesController::class, 'generateStateAccountPdf'])->name('property.generateStateAccountPdf');
    //     Route::get('state-account/preview/{id}',[PropertiesController::class, 'generateStateAccountPreview'])->name('property.generateStateAccountPreview');
    //     Route::post('save',[PropertiesController::class, 'save'])->name('property.save');
    //     Route::post('update',[PropertiesController::class, 'update'])->name('property.update');
    //     Route::post('delete',[PropertiesController::class, 'delete'])->name('property.delete');
    //     Route::post('delete/photo',[PropertiesController::class, 'deletePhoto'])->name('property.deletePhoto');
    // });

    // // Pagos
    // Route::prefix('pagos')->group(function () {
    //     Route::get('/',[PaymentsController::class, 'index'])->name('payment.index');
    //     Route::get('form/{id?}',[PaymentsController::class, 'form'])->name('payment.form');
    //     Route::get('show/{id?}',[PaymentsController::class, 'show'])->name('payment.show');
    //     Route::get('excel/export',[PaymentsController::class, 'export'])->name('payment.export');
    //     Route::post('change-pay-day',[PaymentsController::class, 'updatePaymentDay'])->name('property.updatePaymentDay');
    //     Route::post('filter',[PaymentsController::class, 'filter'])->name('payment.filter');
    //     Route::post('save',[PaymentsController::class, 'save'])->name('payment.save');
    //     Route::post('update',[PaymentsController::class, 'update'])->name('payment.update');
    //     Route::post('delete',[PaymentsController::class, 'delete'])->name('payment.delete');
    //     Route::post('change-status',[PaymentsController::class, 'changeStatus'])->name('payment.changeStatus');
    //     Route::post('delete/photo',[PaymentsController::class, 'deletePhoto'])->name('payment.deletePhoto');
    // });

    // Blogs
    // Route::prefix('blogs')->group(function () {
    //     Route::get('/',[BlogsController::class, 'index'])->name('blog.index');
    //     Route::get('form/{id?}',[BlogsController::class, 'form'])->name('blog.form');
    //     Route::get('get-gallery/{id}',[BlogsController::class, 'getGallery'])->name('blog.getGallery');
    //     Route::post('filter',[BlogsController::class, 'filter'])->name('blog.filter');
    //     Route::post('save',[BlogsController::class, 'save'])->name('blog.save');
    //     Route::post('update',[BlogsController::class, 'update'])->name('blog.update');
    //     Route::post('delete',[BlogsController::class, 'delete'])->name('blog.delete');
    //     Route::post('upload-content',[BlogsController::class, 'uploadContent'])->name('blog.uploadContent');
    //     Route::post('delete-content',[BlogsController::class, 'deleteContent'])->name('blog.deleteContent');
    // });

    // Premios
    // Route::prefix('premios')->group(function () {
    //     Route::get('/',[AwardsController::class, 'index'])->name('award.index');
    //     Route::get('form/{id?}',[AwardsController::class, 'form'])->name('award.form');
    //     Route::post('filter',[AwardsController::class, 'filter'])->name('award.filter');
    //     Route::post('save',[AwardsController::class, 'save'])->name('award.save');
    //     Route::post('update',[AwardsController::class, 'update'])->name('award.update');
    //     Route::post('delete',[AwardsController::class, 'delete'])->name('award.delete');
    //     Route::post('delete/photo',[AwardsController::class, 'deletePhoto'])->name('award.deletePhoto');
    // });

    // Mi perfil
    // Route::prefix('mi-perfil')->group(function () {
    //     Route::get('/',[MyProfileController::class, 'index'])->name('my_profile.index');
    //     Route::post('update',[MyProfileController::class, 'update'])->name('my_profile.update');
    // });

    // Notificaciones push
    // Route::prefix('notificaciones-push')->group(function () {
    //     Route::get('/',[NotificationsPushController::class, 'index'])->name('notification_push.index');
    //     Route::post('send',[NotificationsPushController::class, 'send'])->name('notification_push.send');
    //     Route::post('filter',[NotificationsPushController::class, 'filter'])->name('notification_push.filter');
    // });

    // Usuarios
    // Route::prefix('usuarios')->group(function () {
    //     // Clientes
    //     Route::prefix('clientes')->group(function () {
    //         Route::get('/',[CustomersController::class, 'index'])->name('customer.index');
    //         Route::get('form/{id?}',[CustomersController::class, 'form'])->name('customer.form');
    //         Route::get('excel/export',[CustomersController::class, 'export'])->name('customer.export');
    //         Route::post('filter',[CustomersController::class, 'filter'])->name('customer.filter');
    //         Route::post('save',[CustomersController::class, 'save'])->name('customer.save');
    //         Route::post('update',[CustomersController::class, 'update'])->name('customer.update');
    //         Route::post('delete',[CustomersController::class, 'delete'])->name('customer.delete');
    //         Route::post('change-status',[CustomersController::class, 'changeStatus'])->name('customer.changeStatus');
    //     });
    // });

    // Configuraciones
    // Route::prefix('configuracion')->group(function () {
    //     // Faqs
    //     Route::prefix('preguntas-frecuentes')->group(function () {
    //         Route::get('/',[FaqsController::class, 'index'])->name('faqs.index');
    //         Route::get('form/{id?}',[FaqsController::class, 'form'])->name('faqs.form');
    //         Route::post('save',[FaqsController::class, 'save'])->name('faqs.save');
    //         Route::post('update',[FaqsController::class, 'update'])->name('faqs.update');
    //         Route::post('delete',[FaqsController::class, 'delete'])->name('faqs.delete');
    //     });

    //     // Faqs
    //     Route::prefix('soporte')->group(function () {
    //         Route::get('/',[SupportFaqsController::class, 'index'])->name('faqs.index');
    //         Route::get('form/{id?}',[SupportFaqsController::class, 'form'])->name('faqs.form');
    //         Route::post('save',[SupportFaqsController::class, 'save'])->name('faqs.save');
    //         Route::post('update',[SupportFaqsController::class, 'update'])->name('faqs.update');
    //         Route::post('delete',[SupportFaqsController::class, 'delete'])->name('faqs.delete');
    //     });
    // });
    // });
});
