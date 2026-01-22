<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProcessLog extends Model
{
    protected $fillable = [
        'order_id',
        'step',
        'status',
        'attempt',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function logSuccess(int $orderId, string $step, int $attempt = 1, array $metadata = []): self
    {
        return self::create([
            'order_id' => $orderId,
            'step' => $step,
            'status' => 'success',
            'attempt' => $attempt,
            'metadata' => $metadata,
        ]);
    }

    public static function logFailure(int $orderId, string $step, string $errorMessage, int $attempt = 1, array $metadata = []): self
    {
        return self::create([
            'order_id' => $orderId,
            'step' => $step,
            'status' => 'failed',
            'attempt' => $attempt,
            'error_message' => $errorMessage,
            'metadata' => $metadata,
        ]);
    }
}
