<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'connection_id',
        'platform_id',
        'platform',
        'event_type',
        'provider_event_id',
        'request_id',
        'signature_valid',
        'status',
        'payload',
        'headers',
        'processed_at',
        'error_message',
        'retry_count',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'headers' => 'array',
            'signature_valid' => 'boolean',
            'processed_at' => 'datetime',
            'retry_count' => 'integer',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function connection()
    {
        return $this->belongsTo(Connection::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function failures(): HasMany
    {
        return $this->hasMany(WebhookFailure::class);
    }
}
