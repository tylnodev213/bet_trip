<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;
use Illuminate\Support\Facades\DB;

class DestinationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Destination::factory()->count(100)->create();
        DB::table('destinations')->insert([
            [
                'name' => 'Hà Nội',
                'slug' => 'ha-noi',
                'image' => 'tmp/iPXjUBSJCb',
                'status' => 1
            ],
            [
                'name' => 'Hà Nam',
                'slug' => 'ha-nam',
                'image' => 'tmp/jcJuIAKDNE',
                'status' => 1
            ],
            [
                'name' => 'Nam Định',
                'slug' => 'nam-dinh',
                'image' => 'tmp/ERWwKKxKgH',
                'status' => 1
            ],
            [
                'name' => 'Thanh Hoá',
                'slug' => 'thanh-hoa',
                'image' => 'tmp/wiSVcOWLRF',
                'status' => 1
            ],
            [
                'name' => 'Hà Giang',
                'slug' => 'ha-giang',
                'image' => 'tmp/IhUukIvvAb',
                'status' => 2
            ],
        ]);
    }
}
