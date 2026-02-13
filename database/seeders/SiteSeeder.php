<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\SiteType;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = SiteType::pluck('id', 'code');

        $sites = [
            [
                'name' => 'Basilique Notre-Dame de la Paix',
                'description' => 'Plus grande basilique du monde située à Yamoussoukro, inspirée de la basilique Saint-Pierre de Rome. Consacrée en 1990 par le Pape Jean-Paul II.',
                'lat' => '6.8128',
                'lon' => '-5.2767',
                'city' => 'Yamoussoukro',
                'site_creation_date' => '1990-09-10',
                'site_type_id' => $types['RELIGIOUS'],
            ],
            [
                'name' => 'Parc National de Taï',
                'description' => 'Forêt tropicale primaire classée au patrimoine mondial de l\'UNESCO. Abrite des chimpanzés, des éléphants de forêt et une biodiversité exceptionnelle.',
                'lat' => '5.8500',
                'lon' => '-7.1500',
                'city' => 'Taï',
                'site_creation_date' => '1972-01-01',
                'site_type_id' => $types['PARK'],
            ],
            [
                'name' => 'Parc National de la Comoé',
                'description' => 'Plus grande réserve naturelle d\'Afrique de l\'Ouest, classée au patrimoine mondial de l\'UNESCO. Savanes, forêts-galeries et faune variée.',
                'lat' => '9.1000',
                'lon' => '-3.7500',
                'city' => 'Bouna',
                'site_creation_date' => '1968-01-01',
                'site_type_id' => $types['PARK'],
            ],
            [
                'name' => 'Mosquée de Kong',
                'description' => 'Mosquée historique de style soudanais construite au XVIIe siècle. Témoignage de l\'architecture traditionnelle mandingue.',
                'lat' => '9.1500',
                'lon' => '-4.6167',
                'city' => 'Kong',
                'site_creation_date' => '1741-01-01',
                'site_type_id' => $types['RELIGIOUS'],
            ],
            [
                'name' => 'Grand-Bassam',
                'description' => 'Ancienne capitale coloniale française, classée au patrimoine mondial de l\'UNESCO. Architecture coloniale préservée et plages.',
                'lat' => '5.1939',
                'lon' => '-3.7342',
                'city' => 'Grand-Bassam',
                'site_creation_date' => '1893-01-01',
                'site_type_id' => $types['UNESCO'],
            ],
            [
                'name' => 'Cascade de Man',
                'description' => 'Chutes d\'eau spectaculaires situées dans la région montagneuse de l\'ouest. Site sacré pour les peuples Dan et Yacouba.',
                'lat' => '7.4000',
                'lon' => '-7.5500',
                'city' => 'Man',
                'site_creation_date' => null,
                'site_type_id' => $types['NATURAL'],
            ],
            [
                'name' => 'Pont de Lianes de Man',
                'description' => 'Pont traditionnel construit en lianes naturelles par les populations locales. Prouesse d\'ingénierie traditionnelle yacouba.',
                'lat' => '7.3833',
                'lon' => '-7.5333',
                'city' => 'Man',
                'site_creation_date' => null,
                'site_type_id' => $types['CULTURAL'],
            ],
            [
                'name' => 'Île Boulay',
                'description' => 'Île située dans la lagune Ébrié près d\'Abidjan. Village de pêcheurs traditionnel et plages tranquilles.',
                'lat' => '5.2833',
                'lon' => '-4.0167',
                'city' => 'Abidjan',
                'site_creation_date' => null,
                'site_type_id' => $types['BEACH'],
            ],
            [
                'name' => 'Mont Nimba',
                'description' => 'Réserve naturelle intégrale classée au patrimoine mondial de l\'UNESCO. Point culminant de la Côte d\'Ivoire à 1752m.',
                'lat' => '7.6167',
                'lon' => '-8.4000',
                'city' => 'Danané',
                'site_creation_date' => '1944-01-01',
                'site_type_id' => $types['UNESCO'],
            ],
            [
                'name' => 'Cathédrale Saint-Paul d\'Abidjan',
                'description' => 'Cathédrale moderne conçue par l\'architecte italien Aldo Spirito. Architecture contemporaine remarquable inaugurée en 1985.',
                'lat' => '5.3167',
                'lon' => '-4.0167',
                'city' => 'Abidjan',
                'site_creation_date' => '1985-08-10',
                'site_type_id' => $types['ARCHITECTURAL'],
            ],
        ];

        foreach ($sites as $site) {
            Site::create($site);
        }
    }
}
