<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BbsController;

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

Route::get('/', [BbsController::class, 'index']);
Route::post('/', [BbsController::class, 'index']);

Route::post('/post_confirm', [BbsController::class, 'post_confirm']);
Route::post('/post_complete', [BbsController::class, 'post_complete']);

Route::post('/editing', [BbsController::class, 'editing']);
Route::post('/edit_complete', [BbsController::class, 'edit_complete']);
Route::get('/editing', [BbsController::class, 'index']);

Route::post('/delete_confirm', [BbsController::class, 'delete_confirm']);
Route::post('/delete_complete', [BbsController::class, 'delete_complete']);