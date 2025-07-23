<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserstableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $imagePath = 'https://pottertar.framiq.com/assets/examples/pottertar00.png';

        User::create([
            'name' => 'Administator',
            'email' => 'admin@ark-studio.com',
            'password' => bcrypt('admin123'),
            'salary' => 0,
            'role' => 99,
			'hire_date' => '2012-10-01',
			'position' => 'Administator'
        ]);
    }
}
