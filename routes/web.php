<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ArticleController::class, 'index']);

Route::post('/post_confirm', [ArticleController::class, 'post_confirm']);
Route::get('/post_confirm', [ArticleController::class, 'index']);
Route::post('/post_complete', [ArticleController::class, 'post_complete']);

Route::post('/editing/{article}', [ArticleController::class, 'editing']);
Route::get('/editing/{article}', [ArticleController::class, 'index']);
Route::post('/edit_complete/{article}', [ArticleController::class, 'edit_complete']);

Route::post('/delete_confirm/{article}', [ArticleController::class, 'delete_confirm']);
Route::get('/delete_confirm/{article}', [ArticleController::class, 'index']);
Route::post('/delete_complete/{article}', [ArticleController::class, 'delete_complete']);