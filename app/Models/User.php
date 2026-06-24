<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'corporate_id',
        'name',
        'email',
        'role',
        'phone_number',
        'is_approved',
        'approved_at',
        'password',
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
            'approved_at' => 'datetime',
            'is_approved' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin->value;
    }

    public function isCorporateAdmin(): bool
    {
        return $this->role === UserRole::CorporateAdmin->value;
    }

    public function isCorporateViewer(): bool
    {
        return $this->role === UserRole::CorporateViewer->value;
    }

    public function canWriteCorporateData(): bool
    {
        return $this->isSuperAdmin() || $this->isCorporateAdmin();
    }
}
