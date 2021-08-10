<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('admin');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Auth::routes();

    Route::get('/logout', function () {
        Auth::logout();
        return redirect('admin');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/', function () {
            return redirect('admin');
        });

        Route::get('', 'HomeController@index')->name('admin');

        Route::resource('usuarios', 'Admin\UserController', ['as' => 'users'])->names([
            'index'   => 'users.index',
            'create'  => 'users.create',
            'store'   => 'users.store',
            'edit'    => 'users.edit',
            'update'  => 'users.update',
            'destroy' => 'users.destroy',
        ]);

        Route::resource('faturamento', 'Admin\BillingController', ['as' => 'billings'])->names([
            'index'   => 'billings.index',
            'create'  => 'billings.create',
            'store'   => 'billings.store',
            'edit'    => 'billings.edit',
            'update'  => 'billings.update',
            'destroy' => 'billings.destroy',
        ]);

        Route::resource('dados-faturamento', 'Admin\CompanyController', ['as' => 'company'])->names([
            'index'   => 'company.index',
            'create'  => 'company.create',
            'store'   => 'company.store',
            'edit'    => 'company.edit',
            'update'  => 'company.update',
            'destroy' => 'company.destroy',
        ]);

        Route::resource('faturas', 'Admin\InvoicesController', ['as' => 'invoices'])->names([
            'index'    => 'invoices.index',
            'create'   => 'invoices.create',
            'store'    => 'invoices.store',
            'edit'     => 'invoices.edit',
            'update'   => 'invoices.update',
            'destroy'  => 'invoices.destroy',
        ]);

        Route::get('fatura/gerar', 'Admin\InvoicesController@generate')->name('invoice.generate');

        Route::get('total', 'Admin\GeneralTableController@index')->name('generaltable');

        Route::post('addresses/showByPostcode', 'Admin\AddressController@showByPostcode')->name('address.showByPostcode');

        Route::get('unauthorized', 'Admin\ErrorController@error403')->name('errors.403');
    });
});

Route::prefix('twitter')->name('twitter.')->group(function () {
    Route::post('/callback', 'TwitterCallbackController@twitterCallback');
    Route::get('/callback', 'TwitterCallbackController@twitterPing');
    Route::post('/conversations/{cid}/create', 'TwitterCallbackController@twitterSession');
    Route::post('/conversations/{cid}/message', 'TwitterCallbackController@twitterMessageCallback');
    Route::post('/conversations/{cid}/terminate', 'TwitterCallbackController@twitterTerminate');
    Route::put('/conversations/{cid}/accept', 'TwitterCallbackController@twitterAccept');
    Route::put('/conversations/{cid}/typing', 'TwitterCallbackController@twitterTyping');
});

Route::prefix('facebook')->name('facebook.')->group(function () {
    Route::post('/callback', 'FacebookCallbackController@facebookCallback');
    Route::get('/callback', 'FacebookCallbackController@facebookPing');
    Route::post('/conversations/{cid}/create', 'FacebookCallbackController@facebookSession');
    Route::post('/conversations/{cid}/message', 'FacebookCallbackController@facebookMessageCallback');
    Route::post('/conversations/{cid}/terminate', 'FacebookCallbackController@facebookTerminate');
    Route::put('/conversations/{cid}/accept', 'FacebookCallbackController@facebookAccept');
    Route::put('/conversations/{cid}/typing', 'FacebookCallbackController@facebookTyping');
});

Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
    Route::post('/callback', 'WhatsappCallbackController@whatsappCallback');
    Route::post('/status', 'WhatsappCallbackController@whatsappStatus');
    Route::post('/conversations/{cid}/create', 'WhatsappCallbackController@whatsappSession');
    Route::post('/conversations/{cid}/message', 'WhatsappCallbackController@whatsappMessageCallback');
    Route::post('/conversations/{cid}/terminate', 'WhatsappCallbackController@whatsappTerminate');
    Route::put('/conversations/{cid}/accept', 'WhatsappCallbackController@whatsappAccept');
    Route::put('/conversations/{cid}/typing', 'WhatsappCallbackController@whatsappTyping');
});

Route::prefix('reclame_aqui')->name('reclame_aqui.')->group(function () {
    Route::get('/moderation', 'ReclameAquiModerationController@index');
    Route::get('/evaluation', 'ReclameAquiEvaluationController@index');
    Route::post('/conversations/{cid}/create', 'ReclameAquiCallbackController@ReclameAquiSession');
    Route::post('/conversations/{cid}/message', 'ReclameAquiCallbackController@ReclameAquiMessageCallback');
    Route::post('/conversations/{cid}/terminate', 'ReclameAquiCallbackController@ReclameAquiTerminate');
    Route::post('/moderation', 'ReclameAquiModerationController@SendModeration')->name('moderation');
    Route::post('/evaluation', 'ReclameAquiEvaluationController@SendEvaluation')->name('evaluation');
    Route::put('/conversations/{cid}/accept', 'ReclameAquiCallbackController@ReclameAquiAccept');
    Route::put('/conversations/{cid}/typing', 'ReclameAquiCallbackController@ReclameAquiTyping');
});

Route::prefix('chat')->name('chat.')->group(function () {
    Route::post('/message', 'ChatCallbackController@chatCallback');
    Route::post('/conversations/{cid}/create', 'ChatCallbackController@chatSession');
    Route::post('/conversations/{cid}/message', 'ChatCallbackController@chatMessageCallback');
    Route::post('/conversations/{cid}/terminate', 'ChatCallbackController@chatTerminate');
    Route::put('/conversations/{cid}/accept', 'ChatCallbackController@chatAccept');
    Route::put('/conversations/{cid}/typing', 'ChatCallbackController@chatTyping');
});

Route::prefix('test')->name('test.')->group(function () {
    Route::get('image/{fileName}', 'TestController@index')->name('client_index');
    Route::get('/client_mail', 'TestController@clientMail')->name('client_mail');
    Route::get('/client_gmail', 'TestController@gmailClient')->name('client_gmail');
    Route::get('/send_mail_html', 'TestController@sendMailHtml')->name('send_mail_html');
});

Route::prefix('anexos')->name('anexos.')->group(function () {
    Route::get('/', 'MediasController@index');
    Route::get('/images', 'MediasController@show');
    Route::get('images/{fileName}', 'ImagesController@getImages');
    Route::post('images/upload', 'ImagesController@uploadImage')->name('upload');
});

Route::get('auth/facebook', 'FacebookController@redirectToFacebook');
Route::get('auth/facebook/callback', 'FacebookController@handleFacebookCallback');
