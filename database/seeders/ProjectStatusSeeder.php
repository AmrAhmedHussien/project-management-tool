<?php

namespace Database\Seeders;

use App\Models\ProjectStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = ['new','pending-approval','completed'];
        foreach($statuses as $i => $status)
        {
            ProjectStatus::query()->create([
                'name' => $status,
                'color' => fake()->hexColor(),
                'is_default' => $i==0
            ]);
        }
    }
}
