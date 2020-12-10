<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Auth::routes();

Route::get('/', function () {
    return redirect('chatify');
});

Route::prefix('chatify')->name('chatify.')->group(function () {

    Route::get('/logout', function () {
        Auth::logout();
        return redirect('chatify');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/', function () {
            return redirect('chatify');
        });

        /*
        * This is the main app route [Chatify Messenger]
        */
        Route::get('', 'MessagesController@index')->name('chatify');
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

Route::prefix('callback')->name('callback.')->group(function () {
    Route::post('/conversations/{cid}/create', 'FivenineCallbackController@chatSession');
    Route::post('/conversations/{cid}/message', 'FivenineCallbackController@chatCallback');
    Route::post('/conversations/{cid}/terminate', 'FivenineCallbackController@chatTerminate');
    Route::post('/conversations/{cid}/typing', 'FivenineCallbackController@chatTyping');
});

Route::prefix('twitter')->name('twitter.')->group(function () {
    Route::post('/callback', 'TwitterCallbackController@twitterCallback');
    Route::get('/callback', 'TwitterCallbackController@twitterPing');
    Route::post('/conversations/{cid}/create', 'TwitterCallbackController@twitterSession');
    Route::post('/conversations/{cid}/message', 'TwitterCallbackController@twitterMessageCallback');
    Route::post('/conversations/{cid}/terminate', 'TwitterCallbackController@twitterTerminate');
    Route::post('/conversations/{cid}/typing', 'TwitterCallbackController@twitterTyping');
});
