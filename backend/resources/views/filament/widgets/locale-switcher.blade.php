<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between gap-4">
            <div class="text-sm font-medium">{{ __('Choose language') }}</div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.locale', 'hy') }}" class="px-3 py-1 border rounded text-sm hover:bg-gray-50">HY</a>
                <a href="{{ route('admin.locale', 'en') }}" class="px-3 py-1 border rounded text-sm hover:bg-gray-50">EN</a>
                <a href="{{ route('admin.locale', 'ru') }}" class="px-3 py-1 border rounded text-sm hover:bg-gray-50">RU</a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
