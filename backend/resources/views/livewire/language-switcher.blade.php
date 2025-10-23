<div class="flex items-center gap-2 mr-4">
    <button 
        wire:click="switchLanguage('hy')"
        class="text-2xl hover:opacity-70 transition {{ app()->getLocale() === 'hy' ? 'opacity-100' : 'opacity-40' }}"
        title="Հայերեն"
    >
        🇦🇲
    </button>
    <button 
        wire:click="switchLanguage('en')"
        class="text-2xl hover:opacity-70 transition {{ app()->getLocale() === 'en' ? 'opacity-100' : 'opacity-40' }}"
        title="English"
    >
        🇬🇧
    </button>
    <button 
        wire:click="switchLanguage('ru')"
        class="text-2xl hover:opacity-70 transition {{ app()->getLocale() === 'ru' ? 'opacity-100' : 'opacity-40' }}"
        title="Русский"
    >
        🇷🇺
    </button>
</div>
