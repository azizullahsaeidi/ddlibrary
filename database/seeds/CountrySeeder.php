<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
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
                'id' => '57',
                'vid' => '15',
                'name' => 'Afghanistan',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '256',
            ],
            [
                'id' => '58',
                'vid' => '15',
                'name' => 'Albania',
                'weight' => '0',
                'language' => 'en',
                'tnid' => '259',
            ],
        ];

        DB::table('taxonomy_term_data')->insert($data);
    }
}
