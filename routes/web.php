<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/whatsapp-chat', function () {
    return view('whatsapp_chat');
})->name('whatsapp.form');

Route::post('/whatsapp/get-chat', [WhatsAppController::class, 'getChat'])->name('whatsapp.getChat');
Route::post('/whatsapp/fetch', [WhatsAppController::class, 'fetch'])->name('whatsapp.fetch');

