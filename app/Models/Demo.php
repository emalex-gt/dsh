<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Demo extends Model
{
    use HasFactory;

    protected $fillable = [
        'demo_category_id',
        'name',
        'link',
        'preview_image',
        'preview_image_desktop',
        'preview_image_mobile',
    ];

    protected $appends = [
        'preview_image_url',
        'preview_image_desktop_url',
        'preview_image_mobile_url',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DemoCategory::class, 'demo_category_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['is_recommended'])
            ->withTimestamps();
    }

    public function getPreviewImageUrlAttribute(): ?string
    {
        if (blank($this->preview_image)) {
            return null;
        }

        return Storage::disk('public')->url($this->preview_image);
    }

    public function getPreviewImageDesktopUrlAttribute(): ?string
    {
        if (filled($this->preview_image_desktop)) {
            return Storage::disk('public')->url($this->preview_image_desktop);
        }

        return $this->preview_image_url;
    }

    public function getPreviewImageMobileUrlAttribute(): ?string
    {
        if (blank($this->preview_image_mobile)) {
            return null;
        }

        return Storage::disk('public')->url($this->preview_image_mobile);
    }
}
