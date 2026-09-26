<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Platform extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'display_name',
        'provider',
        'active',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'config' => 'array',
        ];
    }

    public function connections(): HasMany
    {
        return $this->hasMany(Connection::class);
    }

    public function adAccounts(): HasMany
    {
        return $this->hasMany(AdAccount::class);
    }
}
