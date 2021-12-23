<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::resourceVerbs([
    'create' => 'cadastro',
    'edit' => 'editar'
]);

Route::get('/', [App\Http\Controllers\AuthController::class, 'index'])->name('loginForm');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');

// config
Route::name('chat.')->prefix('chat')->middleware(['auth'])->group(function () {

    Route::get('sair', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    Route::get('dashboard', [\App\Http\Controllers\ChatController::class, 'index'])->name('index');
    Route::get('chat/{user}', [\App\Http\Controllers\ChatController::class, 'chat'])->name('chat');
    Route::post('message/{user}', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('sendMessage');

});

// Route::get('/', function () {
//     return view('pages.users.users');
// });
