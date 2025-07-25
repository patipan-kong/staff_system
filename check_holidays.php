<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Company Holidays ===" . PHP_EOL;
$holidays = App\Models\CompanyHoliday::all();
foreach ($holidays as $holiday) {
    echo $holiday->name . ': ' . $holiday->date->format('Y-m-d (D)') . PHP_EOL;
}

echo PHP_EOL . "=== Approved Leaves ===" . PHP_EOL;
$leaves = App\Models\Leave::with('user')->where('status', 'approved')->get();
foreach ($leaves as $leave) {
    echo $leave->user->name . ': ' . $leave->start_date->format('Y-m-d') . ' to ' . $leave->end_date->format('Y-m-d') . ' (' . $leave->type . ')' . PHP_EOL;
}

$currentWeekStart = \Carbon\Carbon::now()->startOfWeek();
echo PHP_EOL . "Current week starts: " . $currentWeekStart->format('Y-m-d (D)') . PHP_EOL;
echo "Current week ends: " . $currentWeekStart->copy()->addDays(4)->format('Y-m-d (D)') . PHP_EOL;

// Add a holiday for this week if there isn't one
$thisWeekHoliday = App\Models\CompanyHoliday::whereBetween('date', [
    $currentWeekStart->format('Y-m-d'),
    $currentWeekStart->copy()->addDays(4)->format('Y-m-d')
])->first();

if (!$thisWeekHoliday) {
    $tomorrow = $currentWeekStart->copy()->addDay();
    echo PHP_EOL . "Creating test holiday for: " . $tomorrow->format('Y-m-d (D)') . PHP_EOL;
    App\Models\CompanyHoliday::create([
        'name' => 'Test Holiday',
        'date' => $tomorrow->format('Y-m-d'),
        'description' => 'Test holiday for staff planning demonstration'
    ]);
    echo "Created test holiday!" . PHP_EOL;
}
?>
