<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Swagger UI Documentation
Route::get('/api/documentation', function () {
    $spec = json_decode(file_get_contents(storage_path('api-docs/api-docs.json')), true);
    
    return view('swagger-ui', ['spec' => $spec]);
})->name('api.documentation');

// API Spec JSON
Route::get('/api/docs.json', function () {
    return response()->file(storage_path('api-docs/api-docs.json'), [
        'Content-Type' => 'application/json'
    ]);
})->name('api.docs.json');
