<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AdminController;

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

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', [ArticleController::class, 'index']);

Route::middleware(['auth'])->group(function() {
	Route::post('/post_complete', [ArticleController::class, 'post_complete']);

	Route::post('/reply_post', [ArticleController::class, 'reply_post']);

	Route::post('/editing/{article}', [ArticleController::class, 'editing']);
	Route::get('/editing/{article}', [ArticleController::class, 'editing']);
	Route::post('/edit_complete/{article}', [ArticleController::class, 'edit_complete']);

	Route::post('/delete_complete/{article}', [ArticleController::class, 'delete_complete']);
});

Route::prefix('admin')->name('admin.')->group(function() {
	Route::get('', [AdminController::class, 'login_get']);
	Route::get('login', [AdminController::class, 'login_get']);
	Route::post('login', [AdminController::class, 'login_post']);

	Route::middleware(['auth.admin'])->group(function() {
		Route::post('logout', [AdminController::class, 'logout']);

		Route::get('index', [AdminController::class, 'index']);

		Route::post('post', [AdminController::class, 'post']);
		Route::post('reply_post', [AdminController::class, 'reply_post']);

		Route::post('editing/{article}', [AdminController::class, 'editing']);
		Route::get('editing/{article}', [AdminController::class, 'editing']);
		Route::post('edit_complete/{article}', [AdminController::class, 'edit_complete']);

		Route::post('delete_complete/{article}', [AdminController::class, 'delete_complete']);

		Route::get('users', [AdminController::class, 'users']);

		Route::post('users/admin_create', [AdminController::class, 'admin_create']);

		Route::post('users/admin_edit/{article}', [AdminController::class, 'admin_edit']);
		Route::get('users/admin_edit/{article}', [AdminController::class, 'admin_edit']);
		Route::post('users/admin_edit_complete/{article}', [AdminController::class, 'admin_edit_complete']);

		Route::post('users/admin_delete_confirm/{article}', [AdminController::class, 'admin_delete_confirm']);
		Route::post('users/admin_delete_complete/{article}', [AdminController::class, 'admin_delete_complete']);

		Route::post('users/user_create', [AdminController::class, 'user_create']);

		Route::post('users/user_edit/{article}', [AdminController::class, 'user_edit']);
		Route::get('users/user_edit/{article}', [AdminController::class, 'user_edit']);
		Route::post('users/user_edit_complete/{article}', [AdminController::class, 'user_edit_complete']);

		Route::post('users/user_delete_complete/{article}', [AdminController::class, 'user_delete_complete']);
	});
});