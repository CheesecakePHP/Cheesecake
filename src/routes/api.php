<?php

Router::get('ping', 'Test@ping');

Router::group([
    'prefix' => 'v1/',
    'middleware' => \App\Middleware\Auth::class
], function() {
    Router::get('user/{id}', 'User@get');
    Router::get('user/{id}/edit', 'User@edit');
    Router::get('user/{id}/edit/{identifier}', 'User@edit');
});