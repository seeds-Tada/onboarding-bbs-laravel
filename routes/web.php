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

Route::post('/post_complete', [ArticleController::class, 'post_complete'])->middleware('auth');

Route::post('/reply_post', [ArticleController::class, 'reply_post'])->middleware('auth');

Route::post('/editing/{article}', [ArticleController::class, 'editing'])->middleware('auth');
Route::get('/editing/{article}', [ArticleController::class, 'editing'])->middleware('auth');
Route::post('/edit_complete/{article}', [ArticleController::class, 'edit_complete'])->middleware('auth');

Route::post('/delete_confirm/{article}', [ArticleController::class, 'delete_confirm'])->middleware('auth');
Route::post('/delete_complete/{article}', [ArticleController::class, 'delete_complete'])->middleware('auth');

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Route::get('/admin/login', [ArticleController::class, 'admin_login']);

// Route::get('/admin/index', [ArticleController::class, 'admin_index']);

// Route::post('/admin/post_complete', [ArticleController::class, 'admin_post_complete'])->middleware('auth');

// Route::post('/admin/reply_post', [ArticleController::class, 'admin_reply_post'])->middleware('auth');

// Route::post('/admin/editing/{article}', [ArticleController::class, 'admin_editing'])->middleware('auth');
// Route::get('/admin/editing/{article}', [ArticleController::class, 'admin_editing'])->middleware('auth');
// Route::post('/admin/edit_complete/{article}', [ArticleController::class, 'admin_edit_complete'])->middleware('auth');

// Route::post('/admin/delete_confirm/{article}', [ArticleController::class, 'admin_delete_confirm'])->middleware('auth');
// Route::post('/admin/delete_complete/{article}', [ArticleController::class, 'admin_delete_complete'])->middleware('auth');