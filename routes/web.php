<?php

use App\Http\Controllers\NotificationsController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::controller(NotificationsController::class)->group(function () {
    Route::post('/notification/send', 'notificationSend')->name('notification.send');
});

if (app()->environment('local')) {
    Route::get('/debug/mail/previews', function () {
        $path = config('mail.debug.preview_path');
        $files = File::files($path);
        
        return view('debug.mail.index', compact('files'));
    });
    
    Route::get('/debug/mail/preview/{filename}', function ($filename) {
        $path = config('mail.debug.preview_path');
        return File::get("{$path}/{$filename}");
    });
}
