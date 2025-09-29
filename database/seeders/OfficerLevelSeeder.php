<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class OfficerLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('officer_levels')->insert([
            [
                'level_en' => 'Senior Level',
                'level_si' => 'ජේ්‍යෂ්ඨ මට්ටම',
                'level_ta' => 'மூத்த நிலை',
            ],
            [
                'level_en' => 'Tertiary Level',
                'level_si' => 'තෘතීය මට්ටම',
                'level_ta' => 'மூன்றாம் நிலை',
            ],
            [
                'level_en' => 'Secondary level',
                'level_si' => 'ද්විතීය මට්ටම',
                'level_ta' => 'இரண்டாம் நிலை',
            ],
            [
                'level_en' => 'Primary level',
                'level_si' => 'ප්‍රාථමික මට්ටම',
                'level_ta' => 'முதன்மை நிலை',
            ],
        ]);
    }
}
