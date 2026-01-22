# Testing Guide

## Part I: CSV Upload

```bash
curl -X POST http://localhost:8000/api/uploads/students \
  -F "file=@sample.csv" \
  -H "Accept: application/json"

# sample.csv
school_name,student_name
Lincoln High School,John Doe
Lincoln High School,Jane Smith
Washington Elementary,Mike Johnson
Lincoln High School,Bob Williams
```

### Expected Response

```json
{
  "schools_created": 2,
  "students_created": 4
}
```

## Part II: Raw SQL Queries

```bash
php artisan db:seed --class=ProductSeeder
```

Queries are documented in `readme_p3.txt`. Run them directly or via Tinker:
```bash
php artisan tinker
```

Then run queries like:
```php
DB::select("SELECT name, quantity_per_unit FROM products ORDER BY name");
```

## Part III: Event-Driven Order Fulfillment

```bash
php artisan migrate
```

```bash
php artisan db:seed --class=OrderSeeder
```

```bash
php artisan queue:work
```

### Test Order Confirmation

```php
use App\Models\Order;

$order = Order::createNew(299.99);

$order->confirm();

$order->fresh()->state; // Should be 'confirmed' initially, then 'completed' when all listeners succeed

$order->processLogs;

$order->fresh()->state; // Should be 'completed' if all steps succeeded
```

### Test Failure Scenario

Listeners simulate failure rates (10% inventory, 5% payment, 8% shipping). When a step fails:

1. Order state becomes `partially_failed`
2. Failure is logged in `order_process_logs`
3. Order metadata contains failure details
4. Failed step can be retried (idempotent)

### Test Cancellation

```php
$order = Order::createNew(199.99);
$order->confirm();
$order->cancel('Customer requested cancellation');

$order->fresh()->state;
```

### Database Queries for Debugging

```sql
-- Check order states
SELECT state, COUNT(*) FROM orders GROUP BY state;

-- Check process logs
SELECT order_id, step, status, attempt, error_message, created_at 
FROM order_process_logs 
ORDER BY order_id, step, attempt;

-- Find stuck orders
SELECT o.id, o.order_number, o.state, COUNT(l.id) as log_count
FROM orders o
LEFT JOIN order_process_logs l ON o.id = l.order_id
WHERE o.state IN ('confirmed', 'partially_failed')
GROUP BY o.id, o.order_number, o.state
HAVING log_count < 3; -- Should have 3 successful logs if completed
```
