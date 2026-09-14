<?php

use App\Http\Controllers\OficioDocumentoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/oficios/documentos/{media}', OficioDocumentoController::class)
    ->middleware('signed')
    ->name('oficios.documentos.ver');
