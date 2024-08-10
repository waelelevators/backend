<?php

namespace Database\Seeders;

use App\Models\InstallationLocationDetection;
use Illuminate\Database\Seeder;

class InstallationLocationDetectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 50 InstallationLocationDetection records
        InstallationLocationDetection::factory()->count(2500)->create();
    }
}
