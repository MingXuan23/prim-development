<?php

use Illuminate\Database\Seeder;

class OrganizationNegeriTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('organization_negeri')->delete();

        \DB::table('organization_negeri')->insert(array(
            0 =>
            array(
                "nama" => "Jabatan Pendidikan Negeri Melaka",
                "organization_parent_id" => 1
            ),
            1 =>
            array(
                "nama" => "Majlis Agama Islam Melaka",
                "organization_parent_id" => 2
            ),
            2 =>
            array(
                "nama" => "Pejabat Agama Islam Melaka",
                "organization_parent_id" => 2
            ),
            3 =>
            array(
                "nama" => "Universiti Teknikal Malaysia Melaka",
                "organization_parent_id" => 3
            )
        ));
    }
}
