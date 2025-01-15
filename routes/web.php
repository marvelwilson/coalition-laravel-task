<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'index']);
Route::post('/addProduct', [ProductController::class, 'addProduct']);
Route::get('/getProducts', [ProductController::class, 'getProducts']);
Route::post('/editProduct', [ProductController::class, 'editProduct']);

