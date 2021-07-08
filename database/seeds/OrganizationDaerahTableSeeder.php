<?php

use Illuminate\Database\Seeder;

class OrganizationDaerahTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('organization_daerah')->delete();

        \DB::table('organization_daerah')->insert(array(
            0 =>
            array(
                "nama" => "Pejabat Pendidikan Melaka Tengah",
                "organization_negeri_id" => 1
            ),
            1 =>
            array(
                "nama" => "Pejabat Pendidikan Melaka Jasin",
                "organization_negeri_id" => 1
            ),
            2 =>
            array(
                "nama" => "Pejabat Pendidikan Melaka Alor Gajah",
                "organization_negeri_id" => 1
            ),
            3 =>
            array(
                "nama" => "Pejabat Agama Islam Melaka Tengah",
                "organization_negeri_id" => 2
            )
        ));
    }
}
