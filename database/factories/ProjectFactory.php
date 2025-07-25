<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        $icons = [
            'fas fa-laptop-code',
            'fas fa-paint-brush', 
            'fas fa-chart-line',
            'fas fa-mobile-alt',
            'fas fa-server',
            'fas fa-database',
            'fas fa-shield-alt',
            'fas fa-cogs',
            'fas fa-rocket',
            'fas fa-bug'
        ];

        $colors = [
            '#007bff', // Blue
            '#6f42c1', // Purple  
            '#e83e8c', // Pink
            '#fd7e14', // Orange
            '#ffc107', // Yellow
            '#20c997', // Teal
            '#28a745', // Green
            '#dc3545', // Red
            '#6c757d', // Gray
            '#17a2b8'  // Cyan
        ];

        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'po_date' => $this->faker->date(),
            'due_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'on_production_date' => $this->faker->optional()->dateTimeBetween('+1 month', '+6 months'),
            'status' => $this->faker->randomElement(['planning', 'active', 'on_hold', 'completed']),
            'icon' => $this->faker->randomElement($icons),
            'color' => $this->faker->randomElement($colors),
        ];
    }
}
