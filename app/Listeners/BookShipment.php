<?php

namespace App\Listeners;

use App\Events\OrderConfirmed;
use App\Models\Order;
use App\Models\OrderProcessLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookShipment implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderConfirmed $event): void
    {
        $orderId = $event->order->id;
        $attempt = null;

        try {
            DB::transaction(function () use ($orderId, &$attempt) {
                $order = Order::where('id', $orderId)->lockForUpdate()->first();

                if (!$order) {
                    Log::warning('Order not found');
                    return;
                }

                if (!$order->canBeProcessed()) {
                    Log::info("Order {$order->order_number} cannot be processed. Current state: {$order->state}");
                    return;
                }

                if ($order->hasStepSucceeded('shipping')) {
                    Log::info("Shipment already booked for order {$order->order_number}");
                    return;
                }

                $attempt = $order->getLatestAttemptForStep('shipping') + 1;

                if (rand(1, 100) <= 8) {
                    throw new \RuntimeException('Shipment booking failed: Carrier service unavailable');
                }

                usleep(200000);

                OrderProcessLog::logSuccess(
                    $order->id,
                    'shipping',
                    $attempt,
                    [
                        'tracking_number' => 'TRACK-' . strtoupper(uniqid()),
                        'carrier' => 'UPS',
                        'booked_at' => now()->toIso8601String(),
                    ]
                );

                Log::info("Shipment booked for order {$order->order_number} (attempt {$attempt})");

                $this->checkOrderCompletion($order);
            });
        } catch (\Throwable $e) {
            $this->recordFailure($orderId, $attempt, 'shipping', $e);
            throw $e;
        }
    }

    protected function checkOrderCompletion(Order $order): void
    {
        $requiredSteps = ['inventory', 'payment', 'shipping'];
        $completedSteps = OrderProcessLog::where('order_id', $order->id)
            ->where('status', 'success')
            ->whereIn('step', $requiredSteps)
            ->pluck('step')
            ->unique()
            ->toArray();

        if (count($completedSteps) === count($requiredSteps)) {
            $order->markCompleted();
            Log::info("Order {$order->order_number} completed successfully");
        }
    }

    public function failed(OrderConfirmed $event, \Throwable $exception): void
    {
        $order = $event->order;
        Log::error("BookShipment job failed for order {$order->order_number}: {$exception->getMessage()}");
    }

    protected function recordFailure(int $orderId, ?int $attempt, string $step, \Throwable $exception): void
    {
        DB::transaction(function () use ($orderId, $attempt, $step, $exception) {
            $order = Order::where('id', $orderId)->lockForUpdate()->first();
            if (!$order) {
                return;
            }

            $attemptNumber = $attempt ?? ($order->getLatestAttemptForStep($step) + 1);

            OrderProcessLog::logFailure(
                $order->id,
                $step,
                $exception->getMessage(),
                $attemptNumber,
                ['failed_at' => now()->toIso8601String()]
            );

            $order->markPartiallyFailed($step, $exception->getMessage());

            Log::error("Shipment booking failed for order {$order->order_number}: {$exception->getMessage()}");
        });
    }
}
