<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->delete();

        \DB::table('users')->insert(array(
            0 =>
            array(
                "id" => 1,
                "email" => app()->environment('local') ? 'admin_masjid@gmail.com' : 'admin_masjid@gmail.com',
                "password" => app()->environment('local') ? \Hash::make("Prim1234") : \Hash::make("Prim1234"),
                "name" => "Muhammad Hafiz Bin Jamil",
                "username" => "admin",
                "icno" => "9810101-08-6262",
                "state" => "Perak",
                "postcode" => "34000",
                "telno" => "01139893143",
                "address" => "UTeM, Ayer Keroh",
                "email_verified_at" => "2020-06-07 18:52:01",
                "remember_token" => "",
                "created_at" => "2020-06-07 10:48:33",
                "updated_at" => "2020-06-07 10:52:01"
            ),
            1 =>
            array(
                "id" => 2,
                "email" => app()->environment('local') ? 'admin_sekolah@gmail.com' : 'admin_sekolah@gmail.com',
                "password" => app()->environment('local') ? \Hash::make("Prim1234") : \Hash::make("Prim1234"),
                "name" => "Hisahmudin Bin Ali",
                "username" => "admin",
                "icno" => "981313-10-2424",
                "state" => "Selangor",
                "postcode" => "34000",
                "telno" => "01139893143",
                "address" => "UTeM, Ayer Keroh",
                "email_verified_at" => "2020-06-07 18:52:01",
                "remember_token" => "",
                "created_at" => "2020-06-07 10:48:33",
                "updated_at" => "2020-06-07 10:52:01"
            ),
            2 =>
            array(
                "id" => 3,
                "email" => app()->environment('local') ? 'admin@gmail.com' : 'admin@gmail.com',
                "password" => app()->environment('local') ? \Hash::make("Prim1234") : \Hash::make("Prim1234"),
                "name" => "Yahya Bin Ibrahim",
                "username" => "admin",
                "icno" => "981313-10-2424",
                "state" => "Selangor",
                "postcode" => "34000",
                "telno" => "01139893143",
                "address" => "UTeM, Ayer Keroh",
                "email_verified_at" => "2020-06-07 18:52:01",
                "remember_token" => "",
                "created_at" => "2020-06-07 10:48:33",
                "updated_at" => "2020-06-07 10:52:01"
            ),
        ));
    }
}
