<?php

namespace Database\Seeders;

use App\Models\RankingLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RankingLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RankingLevel::insert([
           ['id' => 1, 'uuid' => Str::uuid()->toString(), 'name' => 'Level 1', 'badge_image' => 'uploads_demo/ranking/level-1.png', 'earning' => 0, 'student' => 0, 'serial_no' => 1, 'created_at' => now(), 'updated_at' => now()],
           ['id' => 2, 'uuid' => Str::uuid()->toString(), 'name' => 'Level 2', 'badge_image' => 'uploads_demo/ranking/level-2.png', 'earning' => 100, 'student' => 10, 'serial_no' => 2, 'created_at' => now(), 'updated_at' => now()],
           ['id' => 3, 'uuid' => Str::uuid()->toString(), 'name' => 'Level 3', 'badge_image' => 'uploads_demo/ranking/level-3.png', 'earning' => 200, 'student' => 20, 'serial_no' => 3, 'created_at' => now(), 'updated_at' => now()],
           ['id' => 4, 'uuid' => Str::uuid()->toString(), 'name' => 'Level 4', 'badge_image' => 'uploads_demo/ranking/level-4.png', 'earning' => 300, 'student' => 30, 'serial_no' => 4, 'created_at' => now(), 'updated_at' => now()],
           ['id' => 5, 'uuid' => Str::uuid()->toString(), 'name' => 'Level 5', 'badge_image' => 'uploads_demo/ranking/level-5.png', 'earning' => 400, 'student' => 40, 'serial_no' => 5, 'created_at' => now(), 'updated_at' => now()],
           ['id' => 6, 'uuid' => Str::uuid()->toString(), 'name' => 'Level 6', 'badge_image' => 'uploads_demo/ranking/level-6.png', 'earning' => 500, 'student' => 50, 'serial_no' => 6, 'created_at' => now(), 'updated_at' => now()],
           ['id' => 7, 'uuid' => Str::uuid()->toString(), 'name' => 'Level 7', 'badge_image' => 'uploads_demo/ranking/level-7.png', 'earning' => 600, 'student' => 60, 'serial_no' => 7, 'created_at' => now(), 'updated_at' => now()],
           ['id' => 8, 'uuid' => Str::uuid()->toString(), 'name' => 'Level 8', 'badge_image' => 'uploads_demo/ranking/level-8.png', 'earning' => 700, 'student' => 70, 'serial_no' => 8, 'created_at' => now(), 'updated_at' => now()],
           ['id' => 9, 'uuid' => Str::uuid()->toString(), 'name' => 'Level 9', 'badge_image' => 'uploads_demo/ranking/level-9.png', 'earning' => 800, 'student' => 80, 'serial_no' => 9, 'created_at' => now(), 'updated_at' => now()],
           ['id' => 10, 'uuid' => Str::uuid()->toString(), 'name' => 'Level 10', 'badge_image' => 'uploads_demo/ranking/level-10.png', 'earning' => 900, 'student' => 90, 'serial_no' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
