@php
    $url = null;
    $path = $image_path ?? null;
    if (is_array($path)) {
        // Take first non-empty when state is an array (temporary upload state)
        $path = collect($path)->first(fn ($v) => !empty($v));
    }
    if (is_string($path) && $path !== '') {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $url = $path;
        } else {
            // Expecting a public disk path like restaurants/xyz.jpg
            $url = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
            // Fallback if disk URL not configured
            if (empty($url)) {
                $url = asset('storage/' . ltrim($path, '/'));
            }
        }
    }
@endphp
@if($url)
    <div class="mt-2">
        <div class="text-sm text-gray-600 dark:text-gray-300 mb-1">Current image</div>
        <img src="{{ $url }}" alt="Image" class="h-24 w-auto rounded border" />
    </div>
@endif
