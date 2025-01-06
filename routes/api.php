<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ApiController as ApiController;
use App\Http\Controllers\Api\CardsController as CardsController;
use App\Http\Controllers\Api\UsersController as UsersController;
use App\Http\Controllers\Api\AwardsController as AwardsController;
use App\Http\Controllers\Api\PaymentsController as PaymentsController;
use App\Http\Controllers\Api\SupportFaqsController as SupportFaqsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Api rest
Route::prefix('v1')->group(function () {
    // Webhooks
    Route::post('webhook',[ApiController::class, 'webhookMain'])->name('webhook.index');
    Route::post('webhook/spei-recurrente',[ApiController::class, 'webhookSpeiRecurrent'])->name('webhook.spei_recurrent');

    // Endpoints básicos
    Route::post('login-app',[UsersController::class, 'signInCustomer'])->name('signInCustomer');
    Route::post('sign-up',[UsersController::class, 'signUpCustomer'])->name('signUpCustomer');
    Route::post('reset-password',[UsersController::class, 'recoverPassword'])->name('recoverPassword');
    
    // Información legal de la app
    Route::get('faqs',[ApiController::class, 'getFaqs'])->name('getFaqs');
    Route::get('news',[ApiController::class, 'getNews'])->name('getNews');
    Route::get('legal-info',[ApiController::class, 'getLegalInfo'])->name('getLegalInfo');
    Route::get('awards',[AwardsController::class, 'index'])->name('getAwards');
    
    // Estado de cuenta
    Route::get('account-status/{id}',[UsersController::class, 'accountStatus'])->name('accountStatus');

    // Authentication required
    Route::group(['middleware' => ['auth:sanctum']], function() {
        Route::post('logout',[UsersController::class, 'logoutCustomer'])->name('logoutCustomer');
        Route::get('notifications',[UsersController::class, 'getNotifications'])->name('getNotifications');
        // Route::get('account-status/{id}',[UsersController::class, 'accountStatus'])->name('accountStatus');
        Route::post('notifications',[UsersController::class, 'updateNotification'])->name('updateNotification');
        Route::post('recover-password',[UsersController::class, 'recoverPassword'])->name('recoverPassword');
        Route::post('user',[UsersController::class, 'updateUser'])->name('updateUser');
        Route::post('my-profile',[UsersController::class, 'myProfile'])->name('myProfile');
        Route::post('account-status',[UsersController::class, 'accountStatus'])->name('accountStatus');
        
        // Support Faqs
        Route::get('support-faqs',[SupportFaqsController::class, 'index'])->name('supportFaqs.get');
        Route::post('support-faqs',[SupportFaqsController::class, 'save'])->name('supportFaqs.save');
        
        // Conekta methods 
        Route::get('cards',[CardsController::class, 'index'])->name('cards.get');
        Route::post('cards',[CardsController::class, 'save'])->name('cards.save');
        Route::post('make-payment',[PaymentsController::class, 'processOrder'])->name('payment.save');

        // Payment methods (only admin)
        Route::middleware('role:Administrador')->get('payments/filters',[PaymentsController::class, 'getFilters'])->name('payment.getFilters');
        Route::middleware('role:Administrador')->post('payments',[PaymentsController::class, 'getPayments'])->name('payment.get');
    });
});
