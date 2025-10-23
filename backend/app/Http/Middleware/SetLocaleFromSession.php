<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App as AppFacade;

class SetLocaleFromSession
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale', 'hy'));
        if (in_array($locale, ['hy', 'en', 'ru'], true)) {
            AppFacade::setLocale($locale);
        }
        return $next($request);
    }
}
