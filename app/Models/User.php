<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'is_admin',
        'project_name',
        'legal_name',
        'brand_name',
        'contact_name',
        'contact_role',
        'contact_phone',
        'country',
        'city',
        'direct_demo_name',
        'direct_demo_link',
        'direct_demo_desktop_image',
        'direct_demo_mobile_image',
        'hosting_link',
        'hosting_username',
        'hosting_password',
        'hosting_expires_at',
        'hosting_price',
        'domain_expires_at',
        'domain_price',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'direct_demo_desktop_image_url',
        'direct_demo_mobile_image_url',
        'development_expired_notifications',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'password' => 'hashed',
            'hosting_expires_at' => 'date',
            'domain_expires_at' => 'date',
        ];
    }

    public function briefs(): HasMany
    {
        return $this->hasMany(Brief::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function latestBrief(): HasOne
    {
        return $this->hasOne(Brief::class)->latestOfMany();
    }

    public function demos(): BelongsToMany
    {
        return $this->belongsToMany(Demo::class)
            ->withPivot(['is_recommended'])
            ->withTimestamps();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function developmentRequests(): HasMany
    {
        return $this->hasMany(DevelopmentRequest::class);
    }

    public function emailAccounts(): HasMany
    {
        return $this->hasMany(ClientEmailAccount::class);
    }

    public function getDirectDemoDesktopImageUrlAttribute(): ?string
    {
        if (blank($this->direct_demo_desktop_image)) {
            return null;
        }

        return Storage::disk('public')->url($this->direct_demo_desktop_image);
    }

    public function getDirectDemoMobileImageUrlAttribute(): ?string
    {
        if (blank($this->direct_demo_mobile_image)) {
            return null;
        }

        return Storage::disk('public')->url($this->direct_demo_mobile_image);
    }

    public function getDevelopmentExpiredNotificationsAttribute(): array
    {
        $notifications = [];
        $today = now()->startOfDay();

        if ($this->hosting_expires_at && $this->hosting_expires_at->copy()->startOfDay()->lt($today)) {
            $notifications[] = 'El hosting de tu proyecto esta vencido desde el '.$this->hosting_expires_at->format('d/m/Y').'.';
        }

        if ($this->domain_expires_at && $this->domain_expires_at->copy()->startOfDay()->lt($today)) {
            $notifications[] = 'El dominio de tu proyecto esta vencido desde el '.$this->domain_expires_at->format('d/m/Y').'.';
        }

        return $notifications;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin' && $this->is_admin;
    }
}
