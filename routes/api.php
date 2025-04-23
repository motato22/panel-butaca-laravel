<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Api\Mobile\Auth\AuthenticationController;
use App\Http\Controllers\Api\Mobile\Categories\CategoriesController;
use App\Http\Controllers\Api\Mobile\Generos\GenerosController;
use App\Http\Controllers\Api\Mobile\Banners\BannersController;
use App\Http\Controllers\Api\Mobile\Cupones\CuponesController;
use App\Http\Controllers\Api\Mobile\Evento\EventoController;
use App\Http\Controllers\Api\Mobile\Recinto\RecintoController;
use App\Http\Controllers\Api\Mobile\Info\InfoController;
use App\Http\Controllers\Api\Mobile\Notificaciones\NotificacionesController;
use App\Http\Controllers\Api\Mobile\Intereses\InteresesController;
use App\Http\Controllers\Api\Mobile\Twilio\TwilioController;
use App\Http\Controllers\Api\Mobile\Email\EmailController;

/**
 * API Routes
 */

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post('v1/usuario/signoff', [AuthenticationController::class, 'logout']);
});

// Acceso
Route::post('login_check',            [AuthenticationController::class, 'authenticate']);
Route::post('auth/user/create',       [AuthenticationController::class, 'register']);
Route::get('v1/usuario/perfil/{id}',  [AuthenticationController::class, 'perfil']);
Route::put('auth/user/update/{id}',   [AuthenticationController::class, 'editPerfil']);
Route::post('token/refresh',          [AuthenticationController::class, 'refresh']);
Route::post('password/reset',         [AuthenticationController::class, 'resetPassword']);
Route::post('auth/email/exist',       [AuthenticationController::class, 'existsEmail']);
Route::post('auth/username/exist',    [AuthenticationController::class, 'existsUsername']);
Route::post('auth/user/udeg/check',   [AuthenticationController::class, 'checkUdegUser']);
Route::post('auth/user/udeg/checkExternal', [AuthenticationController::class, 'checkUdegUserExternal']);
Route::post('auth/user/udeg/checkStatus',   [AuthenticationController::class, 'checkStatusUdegUser']);
Route::post('fcm/token/{token}',      [AuthenticationController::class, 'registrarFCMToken']);
Route::post('auth/user/update/foto/{id}', [AuthenticationController::class, 'updateFotoPerfil']);
Route::post('v1/usuario/signoff',     [AuthenticationController::class, 'logout']);

// Categorías
Route::get('categorias',              [CategoriesController::class, 'index']);
Route::put('categorias/{id}',         [CategoriesController::class, 'update']);
Route::post('categorias/crear',       [CategoriesController::class, 'store']);

// Envío de código por SMS
Route::post('send/sms',               [TwilioController::class, 'sendSms']);
// Test SMS
Route::post('check/usersms',          [TwilioController::class, 'checUserSms']);
Route::put('validate/code/{id}',      [TwilioController::class, 'validateCode']);
Route::put('resend/code/{id}',        [TwilioController::class, 'resendCode']);

// Envío de código por email
Route::put('validate/email/code/{id}', [EmailController::class, 'validateCode']);
Route::put('resend/email/code/{id}',   [EmailController::class, 'resendCode']);

// V1 - Categoría y géneros
Route::get('v1/categoria',            [CategoriesController::class, 'index']);
Route::get('v1/generos',              [GenerosController::class, 'index']);

// Banners
Route::get('v1/banner',               [BannersController::class, 'index']);
Route::get('banner',                  [BannersController::class, 'getImage']);

// Cupones
Route::get('v1/cupon',                [CuponesController::class, 'index']);

// Eventos
Route::get('v1/evento/para_ti/{id}',  [EventoController::class, 'paraTi']);
Route::get('v1/evento/{id}',          [EventoController::class, 'show']);
Route::get('v1/evento/liked/{id}',    [EventoController::class, 'likedById']);
Route::post('v1/evento/{evento_id}/like/toggle', [EventoController::class, 'toggleLike']);
Route::post('v1/evento/delete/{evento_id}',      [EventoController::class, 'unlikeToggle']);

Route::post('evento/filtrar',         [EventoController::class, 'filtro']);
Route::post('evento/promociones',     [EventoController::class, 'promociones']);
Route::post('evento/futuros',         [EventoController::class, 'futuros']);

// Recintos
Route::get('v1/recinto',              [RecintoController::class, 'index']);
Route::get('v1/recinto/{id}',         [RecintoController::class, 'show']);

// Info
Route::get('v1/info/{slug}',          [InfoController::class, 'show']);
Route::get('v1/info/sitios/intereses',[InfoController::class, 'sitiosIntereses']);

// Notificaciones
Route::get('v1/usuario/notificacion', [NotificacionesController::class, 'index']);

// Intereses y segmento
Route::post('v1/usuario/intereses',    [InteresesController::class, 'getIntereses']);
Route::post('v1/usuario/intereses2',   [InteresesController::class, 'setInteresesUser']);
Route::get('v1/usuario/intereses2/{user_id}', [InteresesController::class, 'getInteresesUser']);
Route::post('v1/usuario/segmento',     [InteresesController::class, 'setSegmentoUser']);
