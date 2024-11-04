<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Position;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $positions = Position::factory(10)->create();
        $users = User::factory(45)->afterMaking(function($user) use ($positions) {
            $position = $positions->random();
            $user['position_id'] = $position->id;
        })->create();
    }
}
