<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/queue-test', function () {
    return config('cache.default');
    dispatch(new \App\Jobs\TestRedisJob());
    return 'Job отправлена';
});
