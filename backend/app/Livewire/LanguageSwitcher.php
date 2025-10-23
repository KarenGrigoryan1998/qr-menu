<?php

namespace App\Livewire;

use Livewire\Component;

class LanguageSwitcher extends Component
{
    public function switchLanguage($locale)
    {
        session()->put('locale', $locale);
        app()->setLocale($locale);
        
        // Force full page reload to refresh all translations
        return redirect(request()->header('Referer') ?? '/admin');
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
