<?php

use Illuminate\Database\Seeder;

class OrganizationParentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('organization_parent')->delete();

        \DB::table('organization_parent')->insert(array(
            0 =>
            array(
                "id" => 1,
                "nama" => "Kementerian Pendidikan Malaysia",
            ),
            1 =>
            array(
                "id" => 2,
                "nama" => "Jabatan Kemajuan Islam Malaysia",
            ),
            2 =>
            array(
                "id" => 3,
                "nama" => "Kementerian Pengajian Tinggi",
            )
        ));
    }
}
