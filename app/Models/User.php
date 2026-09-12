<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use SoftDeletes, HasFactory, Notifiable, HasRoles, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'ecole_setting_id', 'employee_id'])
            ->logOnlyDirty()
            ->useLogName('user')
            ->dontSubmitEmptyLogs();
    }

    /** Accès limité sans Autres Demandes */
    public const BASIC_ROLES = ['employee', 'femme-de-menage', 'chauffeur', 'gardien'];

    /** Accès limité + Autres Demandes */
    public const EXTENDED_ROLES = ['enseignant', 'enseignante', 'assistante-transport'];

    /** Tous les rôles à accès limité (basic + extended) */
    public const ALL_LIMITED_ROLES = [...self::BASIC_ROLES, ...self::EXTENDED_ROLES];

    /** Rôle à accès limité (espace perso, congés, documents) */
    public function isBasicRole(): bool
    {
        return $this->hasAnyRole(self::ALL_LIMITED_ROLES);
    }

    /** A accès aux Autres Demandes en plus */
    public function isExtendedRole(): bool
    {
        return $this->hasAnyRole(self::EXTENDED_ROLES);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['super-admin', 'directeur', 'secretaire', 'surveillante', ...self::ALL_LIMITED_ROLES]);
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'ecole_setting_id',
        'employee_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
