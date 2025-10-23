<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image_path',
        'filename',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    protected $appends = ['image_url'];

    /**
     * Get the full public URL for the image.
     * Works for both storage disk paths and absolute URLs.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        // If already an absolute URL, return as-is
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        // Otherwise, resolve from public disk
        return Storage::disk('public')->url($this->image_path);
    }

    /** @return HasMany<Table> */
    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    /** @return HasMany<MenuCategory> */
    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class);
    }

    /** @return HasMany<Menu> */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    /** @return HasMany<Order> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @return HasMany<Payment> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** @return HasMany<User> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
