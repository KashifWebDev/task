# Laravel Technical Exam

## Overview
This repository implements the three required parts of the exam as an API-only Laravel application:

1. CSV upload for schools/students with streaming and deduplication
2. Raw SQL queries over a seeded products table
3. Event-driven order fulfillment with queued listeners and persistent logs

No UI routes or views are exposed.

## Part I — CSV Upload (API Only)
**Endpoint:** `POST /api/uploads/students`

**File format:** CSV with headers `school_name`, `student_name`.

**Behavior:**
- Streams rows with `fgetcsv` to keep memory usage predictable for large files.
- Normalizes schools via a unique `schools.name` constraint.
- Prevents duplicate students per school via a composite unique index.
- Wraps the import in a single transaction for consistency.

**Response:**
```json
{ "schools_created": 5, "students_created": 150 }
```

## Part II — Raw SQL Queries
Queries are documented in `readme_p3.txt` and use raw SQL only.
Seed data via:
```bash
php artisan db:seed --class=ProductSeeder
```

## Part III — Event-Driven Order Fulfillment
**States:** `pending`, `confirmed`, `partially_failed`, `completed`, `cancelled`.

**Flow:**
- `Order::confirm()` transitions the state and dispatches `OrderConfirmed`.
- Three queued listeners (`ReserveInventory`, `CapturePayment`, `BookShipment`) run independently.
- Each listener is idempotent by checking for a successful step before side effects.
- Each listener locks the order row while the step runs to prevent concurrent double-processing.
- Execution details are stored in `order_process_logs` for observability and retries.

**Intentional omissions (by requirement):**
- No UI (API-only).
- No automatic compensation logic; partial failures are persisted for retry/manual review.
- No microservices or external orchestration.

## What Would Break First at 10× Scale
1. **Queue throughput:** one queue for all steps becomes a bottleneck. Split queues and scale workers independently.
2. **Order log growth:** `order_process_logs` will grow fast. Add retention/archiving and partitioning.
3. **Row lock contention:** high concurrency increases `SELECT … FOR UPDATE` wait time. Reduce lock scope or move to optimistic locking.
4. **External dependencies:** payment/shipping APIs will rate-limit. Add backoff and circuit breakers.
