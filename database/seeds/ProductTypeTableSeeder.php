<?php

use Illuminate\Database\Seeder;

class ProductTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('product_type')->delete();

        \DB::table('product_type')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Barang Sekolah'
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Alat Tulis'
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Buku Kerja'
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'Makanan dan Minuman'
            ),
        ));
    }
}
