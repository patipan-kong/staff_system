<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Timesheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class TimesheetOverlapTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $project;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create();
        
        // Assign user to project
        $this->user->projects()->attach($this->project->getKey());
    }

    /** @test */
    public function it_detects_overlapping_timesheets()
    {
        // Create an existing timesheet from 9:00 to 12:00
        Timesheet::create([
            'user_id' => $this->user->getKey(),
            'project_id' => $this->project->getKey(),
            'date' => '2024-01-15',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-01-15 09:00'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-01-15 12:00'),
            'description' => 'First timesheet',
        ]);

        // Test overlap detection method
        $hasOverlap = Timesheet::hasOverlap(
            $this->user->getKey(),
            '2024-01-15',
            Carbon::createFromFormat('Y-m-d H:i', '2024-01-15 10:00'),
            Carbon::createFromFormat('Y-m-d H:i', '2024-01-15 14:00')
        );

        $this->assertTrue($hasOverlap);
    }

    /** @test */
    public function it_allows_non_overlapping_timesheets()
    {
        // Create an existing timesheet from 9:00 to 12:00
        Timesheet::create([
            'user_id' => $this->user->getKey(),
            'project_id' => $this->project->getKey(),
            'date' => '2024-01-15',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-01-15 09:00'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-01-15 12:00'),
            'description' => 'First timesheet',
        ]);

        // Test non-overlap detection method
        $hasOverlap = Timesheet::hasOverlap(
            $this->user->getKey(),
            '2024-01-15',
            Carbon::createFromFormat('Y-m-d H:i', '2024-01-15 13:00'),
            Carbon::createFromFormat('Y-m-d H:i', '2024-01-15 16:00')
        );

        $this->assertFalse($hasOverlap);
    }

    /** @test */
    public function it_prevents_creating_overlapping_timesheets()
    {
        // Create an existing timesheet from 9:00 to 12:00
        Timesheet::create([
            'user_id' => $this->user->getKey(),
            'project_id' => $this->project->getKey(),
            'date' => '2024-01-15',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-01-15 09:00'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-01-15 12:00'),
            'description' => 'First timesheet',
        ]);

        // Try to create an overlapping timesheet via HTTP request
        $response = $this->actingAs($this->user)->post(route('timesheets.store'), [
            'project_id' => $this->project->getKey(),
            'date' => '2024-01-15',
            'start_time' => '10:00',
            'end_time' => '14:00',
            'description' => 'Overlapping timesheet',
        ]);

        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHasErrors(['time_overlap']);
        
        // Verify timesheet was not created
        $this->assertEquals(1, Timesheet::count());
    }

    /** @test */
    public function it_allows_updating_timesheet_without_overlap()
    {
        // Create an existing timesheet from 9:00 to 12:00
        $timesheet = Timesheet::create([
            'user_id' => $this->user->getKey(),
            'project_id' => $this->project->getKey(),
            'date' => '2024-01-15',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-01-15 09:00'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-01-15 12:00'),
            'description' => 'Original timesheet',
        ]);

        // Update the same timesheet to a different time (should work)
        $response = $this->actingAs($this->user)->put(route('timesheets.update', $timesheet), [
            'project_id' => $this->project->getKey(),
            'date' => '2024-01-15',
            'start_time' => '13:00',
            'end_time' => '16:00',
            'description' => 'Updated timesheet',
        ]);

        $response->assertRedirect(route('timesheets.index'));
        $response->assertSessionHas('success');
        
        // Verify timesheet was updated
        $timesheet->refresh();
        $this->assertEquals('13:00', $timesheet->start_time->format('H:i'));
    }
}
