<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'name' => 'First Timer',
                'slug' => 'first-event',
                'description' => 'Completed your first volunteer event',
                'icon' => 'first-timer-icon.png',
                'color' => '#FF0000',
                'type' => 'events',
                'criteria' => ['events_attended' => 1],
                'points' => 50,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Five Events',
                'slug' => 'five-events',
                'description' => 'Participated in five volunteer events',
                'icon' => 'five-events-icon.png',
                'color' => '#00FF00',
                'type' => 'events',
                'criteria' => ['events_attended' => 5],
                'points' => 100,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Ten Hours',
                'slug' => 'ten-hours',
                'description' => 'Volunteered for ten hours',
                'icon' => 'ten-hours-icon.png',
                'color' => '#0000FF',
                'type' => 'hours',
                'criteria' => ['volunteer_hours' => 10],
                'points' => 75,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Fifty Hours',
                'slug' => 'fifty-hours',
                'description' => 'Volunteered for fifty hours',
                'icon' => 'fifty-hours-icon.png',
                'color' => '#FFFF00',
                'type' => 'hours',
                'criteria' => ['volunteer_hours' => 50],
                'points' => 200,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'First Certificate',
                'slug' => 'first-certificate',
                'description' => 'Earned your first certificate',
                'icon' => 'first-certificate-icon.png',
                'color' => '#FF00FF',
                'type' => 'achievement',
                'criteria' => ['certificates_earned' => 1],
                'points' => 60,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Community Champion',
                'slug' => 'community-champion',
                'description' => 'Outstanding community contribution',
                'icon' => 'champion-icon.png',
                'color' => '#00FFFF',
                'type' => 'special',
                'criteria' => ['volunteer_hours' => 100, 'events_attended' => 20],
                'points' => 500,
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($badges as $badgeData) {
            Badge::updateOrCreate(
                ['slug' => $badgeData['slug']],
                $badgeData
            );
        }
    }
}