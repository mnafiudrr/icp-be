<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    private $projects = [
        [
            'name' => 'ECare Phase 2',
            'description' => 'ECare Phase 2'
        ]
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::select('id')->first();
        foreach ($this->projects as $project) {
            Project::create([
                'name' => $project['name'],
                'description' => $project['description'],
                'created_by' => $user->id
            ]);
        }
    }
}
