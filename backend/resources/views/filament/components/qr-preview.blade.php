<div class="space-y-3">
    @if(!empty($qr_thumb_url))
        <div class="flex items-center gap-4">
            <a href="{{ $qr_download_url ?? '#' }}" target="_blank" @if($qr_download_url) download @endif>
                <img src="{{ $qr_thumb_url }}" alt="QR" class="h-28 w-28 rounded border" />
            </a>
            <div class="text-sm text-gray-600 dark:text-gray-300">
                @if(!empty($qr_table_url))
                    <div class="truncate"><span class="font-medium">URL:</span> <a class="text-primary-600 hover:underline" href="{{ $qr_table_url }}" target="_blank">{{ $qr_table_url }}</a></div>
                @endif
                @if(!empty($qr_download_url))
                    <div class="mt-2">
                        <a class="inline-flex items-center gap-1 text-primary-600 hover:underline" href="{{ $qr_download_url }}" target="_blank" download>
                            <x-heroicon-m-arrow-down-tray class="h-4 w-4" />
                            {{ __('filament.actions.download_qr') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="text-sm text-gray-500">{{ __('filament.fields.qr_url') }}</div>
    @endif
</div>
