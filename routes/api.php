<?php

use Illuminate\Support\Facades\Route;

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: content-type, cache-control, postman-token, Authorization, X-Requested-With');
    header('Access-Control-Allow-Methods: GET,HEAD,PUT,PATCH,POST,DELETE');
    header('X-Powered-By: Allison Oliveira');
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        header('Content-Type: application/json');
        header('HTTP/1.1 204 OK');
        exit();
    }
}

Route::group(['prefix' => 'v1'], function () {
    Route::get('', function () {
        return response()->json([
            'content' => date('c'),
            'author'  => 'Allison Oliveira',
            'Message' => 'Sigma',
        ]);
    });

    Route::any('ping', function () {
        return response()->json([
            'content' => date('c'),
            'author'  => 'Allison Oliveira',
            'Message' => 'Pong',
        ]);
    });
});
