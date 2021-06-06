<?php

use App\Jobs\SendMail;
use Dacastro4\LaravelGmail\Facade\LaravelGmail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Webklex\IMAP\Facades\Client;

Route::get('/', function () {
    return redirect('admin');
});

Route::prefix('sigma')->name('sigma.')->group(function () {

    Auth::routes();

    Route::get('/logout', function () {
        Auth::logout();
        return redirect('sigma');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/', function () {
            return redirect('sigma');
        });

        /*
        * This is the main app route [sigma Messenger]
        */
        Route::get('', 'MessagesController@index')->name('sigma');
        /**
         *  Fetch info for specific id [user/group]
         */
        Route::post('/idInfo', 'MessagesController@idFetchData');

        /**
         * Send message route
         */
        Route::post('/sendMessage', 'MessagesController@send')->name('send.message');

        /**
         * Fetch messages
         */
        Route::post('/fetchMessages', 'MessagesController@fetch')->name('fetch.messages');

        /**
         * Download attachments route to create a downloadable links
         */
        Route::get('/download/{fileName}', 'MessagesController@download')->name(config('chatify.attachments.route'));

        /**
         * Authintication for pusher private channels
         */
        Route::post('/chat/auth', 'MessagesController@pusherAuth')->name('pusher.auth');

        /**
         * Make messages as seen
         */
        Route::post('/makeSeen', 'MessagesController@seen')->name('messages.seen');

        /**
         * Get contacts
         */
        Route::post('/getContacts', 'MessagesController@getContacts')->name('contacts.get');

        /**
         * Update contact item data
         */
        Route::post('/updateContacts', 'MessagesController@updateContactItem')->name('contacts.update');


        /**
         * Star in favorite list
         */
        Route::post('/star', 'MessagesController@favorite')->name('star');

        /**
         * get favorites list
         */
        Route::post('/favorites', 'MessagesController@getFavorites')->name('favorites');

        /**
         * Search in messenger
         */
        Route::post('/search', 'MessagesController@search')->name('search');

        /**
         * Get shared photos
         */
        Route::post('/shared', 'MessagesController@sharedPhotos')->name('shared');

        /**
         * Delete Conversation
         */
        Route::post('/deleteConversation', 'MessagesController@deleteConversation')->name('conversation.delete');

        /**
         * Delete Conversation
         */
        Route::post('/updateSettings', 'MessagesController@updateSettings')->name('avatar.update');

        /**
         * Set active status
         */
        Route::post('/setActiveStatus', 'MessagesController@setActiveStatus')->name('activeStatus.set');


        // Route::post('/callback/conversations/{cid}/create', 'fivenineCallbackController@chatCallback');

        /*
        * [Group] view by id
        */
        Route::get('/group/{id}', 'MessagesController@index')->name('group');

        /*
        * user view by id.
        * Note : If you added routes after the [User] which is the below one,
        * it will considered as user id.
        *
        * e.g. - The commented routes below :
        */
        // Route::get('/route', function(){ return 'Munaf'; }); // works as a route
        Route::get('/{id}', 'MessagesController@index')->name('user');
        // Route::get('/route', function(){ return 'Munaf'; }); // works as a user id
    });
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

Route::prefix('callback')->name('callback.')->group(function () {
    Route::post('/conversations/{cid}/create', 'FivenineCallbackController@chatSession');
    Route::post('/conversations/{cid}/message', 'FivenineCallbackController@chatCallback');
    Route::post('/conversations/{cid}/terminate', 'FivenineCallbackController@chatTerminate');
    Route::put('/conversations/{cid}/accept', 'FivenineCallbackController@chatAccept');
    Route::put('/conversations/{cid}/typing', 'FivenineCallbackController@chatTyping');
    Route::post('/updateContactList', 'FivenineCallbackController@updateContactItem');
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

Route::prefix('test')->name('test.')->group(function () {
    Route::get('', 'TestController@index')->name('client_index');
    Route::get('/client_mail', 'TestController@clientMail')->name('client_mail');
    Route::get('/client_gmail', 'TestController@gmailClient')->name('client_gmail');
    Route::get('/send_mail_html', 'TestController@sendMailHtml')->name('send_mail_html');
});

Route::get('auth/facebook', 'FacebookController@redirectToFacebook');
Route::get('auth/facebook/callback', 'FacebookController@handleFacebookCallback');
Route::get('attachment', 'MediaController@index');

Route::get('/oauth/gmail', function () {
    return LaravelGmail::redirect();
});

Route::get('/oauth/gmail/callback', function () {
    LaravelGmail::makeToken();
    return redirect()->to('/');
});

Route::get('/oauth/gmail/logout', function () {
    LaravelGmail::logout(); //It returns exception if fails
    return redirect()->to('/');
});
