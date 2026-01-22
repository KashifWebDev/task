<?php

namespace App\Models;

use App\Events\OrderConfirmed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'state',
        'total_amount',
        'metadata',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function processLogs(): HasMany
    {
        return $this->hasMany(OrderProcessLog::class);
    }

    public static function createNew(float $totalAmount, array $metadata = []): self
    {
        return self::create([
            'order_number' => self::generateOrderNumber(),
            'state' => 'pending',
            'total_amount' => $totalAmount,
            'metadata' => $metadata,
        ]);
    }

    protected static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(Str::random(8));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function confirm(): void
    {
        if ($this->state !== 'pending') {
            throw new \DomainException(
                "Cannot confirm order. Current state is '{$this->state}'. Only 'pending' orders can be confirmed."
            );
        }

        $this->state = 'confirmed';
        $this->save();

        event(new OrderConfirmed($this));
    }

    public function markPartiallyFailed(string $failedStep, string $errorMessage): void
    {
        if ($this->state === 'cancelled') {
            return;
        }

        $metadata = $this->metadata ?? [];
        $metadata['failed_steps'][] = [
            'step' => $failedStep,
            'error' => $errorMessage,
            'timestamp' => now()->toIso8601String(),
        ];

        $this->state = 'partially_failed';
        $this->metadata = $metadata;
        $this->save();
    }

    public function markCompleted(): void
    {
        if ($this->state === 'cancelled') {
            throw new \DomainException(
                "Cannot complete order. Order is cancelled."
            );
        }

        $this->state = 'completed';
        $this->save();
    }

    public function cancel(string $reason = null): void
    {
        if ($this->state === 'completed') {
            throw new \DomainException(
                "Cannot cancel order. Order is already completed."
            );
        }

        $metadata = $this->metadata ?? [];
        $metadata['cancellation_reason'] = $reason;
        $metadata['cancelled_at'] = now()->toIso8601String();

        $this->state = 'cancelled';
        $this->metadata = $metadata;
        $this->save();
    }

    public function canBeProcessed(): bool
    {
        return in_array($this->state, ['confirmed', 'partially_failed'], true);
    }

    public function hasStepSucceeded(string $step): bool
    {
        return $this->processLogs()
            ->where('step', $step)
            ->where('status', 'success')
            ->exists();
    }

    public function getLatestAttemptForStep(string $step): int
    {
        $latestLog = $this->processLogs()
            ->where('step', $step)
            ->orderBy('attempt', 'desc')
            ->first();

        return $latestLog ? $latestLog->attempt : 0;
    }
}
