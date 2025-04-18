<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function() {    
    Route::post('v1/usuario/signoff', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@logout');
   // Route::post('v1/usuario/signoff',   [AuthController::class, 'logout']);
  });
/**** Acceso */
Route::post('login_check', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@authenticate');
Route::post('auth/user/create', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@register');
Route::get('v1/usuario/perfil/{id}', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@perfil');
Route::put('auth/user/update/{id}', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@editPerfil');
Route::post('token/refresh', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@refresh');
Route::post('password/reset', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@resetPassword');
Route::post('auth/email/exist', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@existsEmail');
Route::post('auth/username/exist', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@existsUsername');
Route::post('/auth/user/udeg/check', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@checkUdegUser');
Route::post('/auth/user/udeg/checkExternal', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@checkUdegUserExternal');
Route::post('/auth/user/udeg/checkStatus', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@checkStatusUdegUser');
Route::post('fcm/token/{token}', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@registrarFCMToken');
Route::post('auth/user/update/foto/{id}', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@updateFotoPerfil');
Route::post('v1/usuario/signoff', 'App\Http\Controllers\Api\Mobile\Auth\AuthenticationController@logout');

Route::get('categorias', 'App\Http\Controllers\Api\Mobile\Categories\CategoriesController@index');
Route::put('categorias/{id}', 'App\Http\Controllers\Api\Mobile\Categories\CategoriesController@update');
Route::post('categorias/crear', 'App\Http\Controllers\Api\Mobile\Categories\CategoriesController@store');

/***Envio de Codigo por sms*** */
Route::post('send/sms', 'App\Http\Controllers\Api\Mobile\Twilio\TwilioController@sendSms');
/**TEST */
Route::post('check/usersms', 'App\Http\Controllers\Api\Mobile\Twilio\TwilioController@checUserSms');
Route::put('validate/code/{id}', 'App\Http\Controllers\Api\Mobile\Twilio\TwilioController@validateCode');
Route::put('resend/code/{id}', 'App\Http\Controllers\Api\Mobile\Twilio\TwilioController@resendCode');

/***Envio de Codigo por email*** */
// Route::post('send/email/sms', 'App\Http\Controllers\Api\Mobile\Email\EmailController@sendCode'); // No se usa
// Route::post('check/email/usersms', 'App\Http\Controllers\Api\Mobile\Email\EmailController@checUserCode'); // No se usa
Route::put('validate/email/code/{id}', 'App\Http\Controllers\Api\Mobile\Email\EmailController@validateCode');
Route::put('resend/email/code/{id}', 'App\Http\Controllers\Api\Mobile\Email\EmailController@resendCode');

/*****Categoria */
Route::get('v1/categoria', 'App\Http\Controllers\Api\Mobile\Categories\CategoriesController@index');

/*****Generos */
Route::get('v1/generos', 'App\Http\Controllers\Api\Mobile\Generos\GenerosController@index');

/*****Banners */
Route::get('v1/banner', 'App\Http\Controllers\Api\Mobile\Banners\BannersController@index');
Route::get('banner', 'App\Http\Controllers\Api\Mobile\Banners\BannersController@getImage');

/*****Cupones */
Route::get('v1/cupon', 'App\Http\Controllers\Api\Mobile\Cupones\CuponesController@index');

/*****Eventos */
Route::get('v1/evento/para_ti/{id}', 'App\Http\Controllers\Api\Mobile\Evento\EventoController@paraTi');
Route::get('v1/evento/para_ti/{id}', 'App\Http\Controllers\Api\Mobile\Evento\EventoController@paraTi');
Route::get('v1/evento/{id}', 'App\Http\Controllers\Api\Mobile\Evento\EventoController@show');
Route::get('v1/evento/liked/{id}', 'App\Http\Controllers\Api\Mobile\Evento\EventoController@likedById');
//Route::get('v1/evento/filtro', 'App\Http\Controllers\Api\Mobile\Evento\EventoController@filtro');
Route::post('v1/evento/{evento_id}/like/toggle', 'App\Http\Controllers\Api\Mobile\Evento\EventoController@toggleLike');
Route::post('v1/evento/delete/{evento_id}', 'App\Http\Controllers\Api\Mobile\Evento\EventoController@unlikeToggle');

Route::post('evento/filtrar', 'App\Http\Controllers\Api\Mobile\Evento\EventoController@filtro');
Route::post('evento/promociones', 'App\Http\Controllers\Api\Mobile\Evento\EventoController@promociones');
Route::post('evento/futuros', 'App\Http\Controllers\Api\Mobile\Evento\EventoController@futuros');

/*****Recinto */
Route::get('v1/recinto', 'App\Http\Controllers\Api\Mobile\Recinto\RecintoController@index');
Route::get('v1/recinto/{id}', 'App\Http\Controllers\Api\Mobile\Recinto\RecintoController@show');

/*****Info */
Route::get('v1/info/{slug}', 'App\Http\Controllers\Api\Mobile\Info\InfoController@show');
Route::get('v1/info/sitios/intereses', 'App\Http\Controllers\Api\Mobile\Info\InfoController@sitiosIntereses');

/*****Notificaciones */
Route::get('/v1/usuario/notificacion', 'App\Http\Controllers\Api\Mobile\Notificaciones\NotificacionesController@index');

/***Intereses* */
Route::post('v1/usuario/intereses', 'App\Http\Controllers\Api\Mobile\Intereses\InteresesController@getIntereses');
Route::post('v1/usuario/intereses2', 'App\Http\Controllers\Api\Mobile\Intereses\InteresesController@setInteresesUser');
Route::get('v1/usuario/intereses2/{user_id}', 'App\Http\Controllers\Api\Mobile\Intereses\InteresesController@getInteresesUser');

/***Segmento* */
Route::post('v1/usuario/segmento', 'App\Http\Controllers\Api\Mobile\Intereses\InteresesController@setSegmentoUser');