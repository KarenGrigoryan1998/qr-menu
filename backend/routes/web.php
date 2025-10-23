<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// Locale switcher for Filament/Admin
Route::get('/admin/locale/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, ['hy', 'en', 'ru'], true), 404);
    $request->session()->put('locale', $locale);
    return redirect()->back();
})->name('admin.locale');
