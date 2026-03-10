<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => 'Finance API',
        'version' => '1.0',
        'status' => 'running'
    ]);
});
