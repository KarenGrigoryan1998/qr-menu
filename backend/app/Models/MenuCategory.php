<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MenuCategory extends Model
{
    use HasFactory;

    protected $fillable = [ 'restaurant_id', 'name', 'name_hy', 'name_en', 'name_ru', 'image_url', 'image_path', 'filename' ];

    protected $appends = ['image_url'];

    /**
     * Get the full public URL for the image.
     */
    public function getImageUrlAttribute(): ?string
    {
        // Prefer direct image_url column if present
        if (!empty($this->attributes['image_url'])) {
            return $this->attributes['image_url'];
        }

        // Fallback to legacy image_path stored in public disk
        $path = $this->attributes['image_path'] ?? null;
        if (empty($path)) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return Storage::disk('public')->url($path);
    }

    /** @return HasMany<Menu> */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'category_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Restaurant, MenuCategory> */
    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
