<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallerController;
use App\Models\Language;
use Illuminate\Support\Facades\App;
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


Route::get('/local/{ln}', function ($ln) {
    $language = Language::where('iso_code', $ln)->first();
    if (!$language){
        $language = Language::find(1);
        if ($language){
            $ln = $language->iso_code;
            session(['local' => $ln]);
            App::setLocale(session()->get('local'));
        }
    }

    session(['local' => $ln]);
    App::setLocale(session()->get('local'));
    return redirect()->back();
});

Route::get('notification-url/{uuid}', [InstallerController::class, 'notificationUrl'])->name('notification.url');

Auth::routes(['register' => false]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/logout', [LoginController::class, 'logout']);
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});



