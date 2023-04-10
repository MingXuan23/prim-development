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
                "name" => "Hishamudin Bin Ali",
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
                "name" => "Admin Hisham",
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
            3 =>
            array(
                "id" => 4,
                "email" => app()->environment('local') ? 'raziq@gmail.com' : 'raziq@gmail.com',
                "password" => app()->environment('local') ? \Hash::make("test1234") : \Hash::make("test1234"),
                "name" => "Ahmad Raziq Danish Bin Amirruddin",
                "username" => "ajiq",
                "icno" => "991011-14-6137",
                "state" => "Selangor",
                "postcode" => "45600",
                "telno" => "0149547478",
                "address" => "Batu 8, Kampung Ijok",
                "email_verified_at" => "2020-06-07 18:52:01",
                "remember_token" => "",
                "created_at" => "2020-06-07 10:48:33",
                "updated_at" => "2020-06-07 10:52:01"
            ),
            4 =>
            array(
                "id" => 5,
                "email" => app()->environment('local') ? 'siowzheyi@gmail.com' : 'siowzheyi@gmail.com',
                "password" => app()->environment('local') ? \Hash::make("Prim123@") : \Hash::make("Prim123@"),
                "name" => "Siow Zhe Yi",
                "username" => "siowzheyi",
                "icno" => "020325-05-0654",
                "state" => "Selangor",
                "postcode" => "45600",
                "telno" => "0102388325",
                "address" => "Batu 8, Kampung Ijok",
                "email_verified_at" => "2020-06-07 18:52:01",
                "remember_token" => "",
                "created_at" => "2020-06-07 10:48:33",
                "updated_at" => "2020-06-07 10:52:01"
            ),
            5 =>
            array(
                "id" => 6,
                "email" => app()->environment('local') ? 'teacher1@gmail.com' : 'teacher1@gmail.com',
                "password" => app()->environment('local') ? \Hash::make("Prim123@") : \Hash::make("Prim123@"),
                "name" => "teacher 1",
                "username" => "teacher1",
                "icno" => "963256-05-0654",
                "state" => "Selangor",
                "postcode" => "45600",
                "telno" => "0101234567",
                "address" => "Batu 8, Kampung Ijok",
                "email_verified_at" => "2020-06-07 18:52:01",
                "remember_token" => "",
                "created_at" => "2020-06-07 10:48:33",
                "updated_at" => "2020-06-07 10:52:01"
            ),
            6 =>
            array(
                "id" => 7,
                "email" => app()->environment('local') ? 'parentstudent1@gmail.com' : 'parentstudent1@gmail.com',
                "password" => app()->environment('local') ? \Hash::make("Prim123@") : \Hash::make("Prim123@"),
                "name" => "parent 1",
                "username" => "parentstudent1",
                "icno" => "963256-05-0654",
                "state" => "Selangor",
                "postcode" => "45600",
                "telno" => "0106594357",
                "address" => "Batu 8, Kampung Ijok",
                "email_verified_at" => "2020-06-07 18:52:01",
                "remember_token" => "",
                "created_at" => "2020-06-07 10:48:33",
                "updated_at" => "2020-06-07 10:52:01"
            ),
            7 =>
            array(
                "id" => 8,
                "email" => app()->environment('local') ? 'warden1@gmail.com' : 'warden1@gmail.com',
                "password" => app()->environment('local') ? \Hash::make("Prim123@") : \Hash::make("Prim123@"),
                "name" => "warden 1",
                "username" => "warden1",
                "icno" => "963256-05-0654",
                "state" => "Selangor",
                "postcode" => "45600",
                "telno" => "0106594357",
                "address" => "Batu 8, Kampung Ijok",
                "email_verified_at" => "2020-06-07 18:52:01",
                "remember_token" => "",
                "created_at" => "2020-06-07 10:48:33",
                "updated_at" => "2020-06-07 10:52:01"
            ),
        ));
    }
}
