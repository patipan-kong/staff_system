<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get first user for testing
$user = App\Models\User::first();
if (!$user) {
    echo "No users found!" . PHP_EOL;
    exit;
}

echo "Creating test leave data for user: " . $user->name . PHP_EOL;

// Create some approved leaves for this year
$currentYear = now()->year;

// Create vacation leave
App\Models\Leave::updateOrCreate([
    'user_id' => $user->id,
    'type' => 'vacation',
    'start_date' => $currentYear . '-06-15',
    'end_date' => $currentYear . '-06-19'
], [
    'days' => 5,
    'reason' => 'Summer vacation',
    'status' => 'approved',
    'approved_by' => 1,
    'approved_at' => now()
]);

// Create personal leave
App\Models\Leave::updateOrCreate([
    'user_id' => $user->id,
    'type' => 'personal',
    'start_date' => $currentYear . '-08-10',
    'end_date' => $currentYear . '-08-10'
], [
    'days' => 1,
    'reason' => 'Personal appointment',
    'status' => 'approved',
    'approved_by' => 1,
    'approved_at' => now()
]);

// Create sick leave
App\Models\Leave::updateOrCreate([
    'user_id' => $user->id,
    'type' => 'sick',
    'start_date' => $currentYear . '-05-20',
    'end_date' => $currentYear . '-05-21'
], [
    'days' => 2,
    'reason' => 'Flu',
    'status' => 'approved',
    'approved_by' => 1,
    'approved_at' => now()
]);

// Create pending vacation leave
App\Models\Leave::updateOrCreate([
    'user_id' => $user->id,
    'type' => 'vacation',
    'start_date' => $currentYear . '-12-20',
    'end_date' => $currentYear . '-12-24'
], [
    'days' => 3,
    'reason' => 'Christmas vacation',
    'status' => 'pending'
]);

echo "Test leave data created successfully!" . PHP_EOL;
echo "Vacation quota for user: " . ($user->vacation_quota ?? 'Not set') . PHP_EOL;

// Update user vacation quota if not set
if ($user->vacation_quota === null) {
    $user->update(['vacation_quota' => 15]);
    echo "Set vacation quota to 15 days" . PHP_EOL;
}
?>
