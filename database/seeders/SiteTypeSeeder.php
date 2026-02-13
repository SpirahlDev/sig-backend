<?php

namespace Database\Seeders;

use App\Models\SiteType;
use Illuminate\Database\Seeder;

class SiteTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['code' => 'RELIGIOUS', 'label' => 'Site religieux'],
            ['code' => 'NATURAL', 'label' => 'Site naturel'],
            ['code' => 'HISTORICAL', 'label' => 'Site historique'],
            ['code' => 'CULTURAL', 'label' => 'Site culturel'],
            ['code' => 'ARCHITECTURAL', 'label' => 'Site architectural'],
            ['code' => 'UNESCO', 'label' => 'Patrimoine UNESCO'],
            ['code' => 'PARK', 'label' => 'Parc national'],
            ['code' => 'BEACH', 'label' => 'Plage'],
        ];

        foreach ($types as $type) {
            SiteType::create($type);
        }
    }
}
