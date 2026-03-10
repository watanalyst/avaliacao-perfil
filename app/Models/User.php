<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // 👈 IMPORTANTE
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable; // 👈 PRECISA ESTAR AQUI

    protected $table = 'LOGIXPRD.RH_USERS';

    protected $fillable = [
        'numcad',
        'name',
        'email',
        'password',
        'role',
        'ativo',
        'foto',
    ];

    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute(): ?string
    {
        $foto = $this->FOTO ?? $this->foto ?? null;
        if (!$foto) return null;

        // Já é base64 data URL
        if (str_starts_with($foto, 'data:image')) {
            return $foto;
        }

        return 'data:image/jpeg;base64,' . $foto;
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
