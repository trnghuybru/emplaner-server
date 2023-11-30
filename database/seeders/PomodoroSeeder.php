<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PomodoroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            for ($i = 0; $i < 5; $i++) {
                \App\Models\Pomodoro::factory()->create([
                    "user_id"=> $user->id
                ]);
            }
        }
    }
}
