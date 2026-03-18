<?php

namespace Database\Seeders;

use App\Models\Interest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interests = [
            'Web Development',
            'Data Science',
            'UI/UX Design',
            'Cybersecurity',
            'Mobile Development',
            'Cloud Computing',
        ];

        foreach ($interests as $interest)
        {
            Interest::create([
                'name' => $interest
            ]);
        }
    }
}
