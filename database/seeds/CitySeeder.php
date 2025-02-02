<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => '1',
                'vid' => '12',
                'name' => 'Badakhshan',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '261'
            ],
            [
                'id' => '2',
                'vid' => '12',
                'name' => 'Badghis',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '263'
            ],
            [
                'id' => '3',
                'vid' => '12',
                'name' => 'Baghlan',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '265'
            ],
            [
                'id' => '4',
                'vid' => '12',
                'name' => 'Balkh',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '267'
            ],
            [
                'id' => '5',
                'vid' => '12',
                'name' => 'Bamyan',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '269'
            ],
            [
                'id' => '6',
                'vid' => '12',
                'name' => 'Daykundi',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '271'
            ],
            [
                'id' => '7',
                'vid' => '12',
                'name' => 'Farah',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '273'
            ],
            [
                'id' => '8',
                'vid' => '12',
                'name' => 'Faryab',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '274'
            ],
            [
                'id' => '9',
                'vid' => '12',
                'name' => 'Ghazni',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '275'
            ],
            [
                'id' => '10',
                'vid' => '12',
                'name' => 'Ghor',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '276'
            ],
            [
                'id' => '11',
                'vid' => '12',
                'name' => 'Helmand',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '277'
            ],
            [
                'id' => '12',
                'vid' => '12',
                'name' => 'Herat',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '278'
            ],
            [
                'id' => '13',
                'vid' => '12',
                'name' => 'Jowzjan',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '279'
            ],
            [
                'id' => '14',
                'vid' => '12',
                'name' => 'Kabul',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '280'
            ],
            [
                'id' => '15',
                'vid' => '12',
                'name' => 'Kandahar',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '281'
            ],
            [
                'id' => '16',
                'vid' => '12',
                'name' => 'Kapisa',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '282'
            ],
            [
                'id' => '17',
                'vid' => '12',
                'name' => 'Khost',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '283'
            ],
            [
                'id' => '18',
                'vid' => '12',
                'name' => 'Kunar',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '284'
            ],
            [
                'id' => '19',
                'vid' => '12',
                'name' => 'Kunduz',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '285'
            ],
            [
                'id' => '20',
                'vid' => '12',
                'name' => 'Laghman',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '286'
            ],
            [
                'id' => '21',
                'vid' => '12',
                'name' => 'Logar',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '287'
            ],
            [
                'id' => '22',
                'vid' => '12',
                'name' => 'Nangarhar',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '288'
            ],
            [
                'id' => '23',
                'vid' => '12',
                'name' => 'Nimruz',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '289'
            ],
            [
                'id' => '24',
                'vid' => '12',
                'name' => 'Nuristan',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '290'
            ],
            [
                'id' => '25',
                'vid' => '12',
                'name' => 'Paktia',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '291'
            ],
            [
                'id' => '26',
                'vid' => '12',
                'name' => 'Paktika',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '292'
            ],
            [
                'id' => '27',
                'vid' => '12',
                'name' => 'Panjshir',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '293'
            ],
            [
                'id' => '28',
                'vid' => '12',
                'name' => 'Parwan',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '294'
            ],
            [
                'id' => '29',
                'vid' => '12',
                'name' => 'Samangan',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '295'
            ],
            [
                'id' => '30',
                'vid' => '12',
                'name' => 'Sar-e Pol',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '296'
            ],
            [
                'id' => '31',
                'vid' => '12',
                'name' => 'Takhar',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '297'
            ],
            [
                'id' => '32',
                'vid' => '12',
                'name' => 'Uruzgan',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '298'
            ],
            [
                'id' => '33',
                'vid' => '12',
                'name' => 'Zabul',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '300'
            ]
        ];

        DB::table('taxonomy_term_data')->insert($data);
    }
}
