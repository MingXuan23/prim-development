<?php

use Illuminate\Database\Seeder;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transactions')->delete();

        DB::table('transactions')->insert(array(
            0 =>
            array(
                "id" => 1,
                "nama" => "Donation_20210309204320",
                "description" => "1",
                "transac_no" => "2103092043260398",
                "datetime_created" => "2021-03-09 20:44:00",
                "amount" => 124,
                "status" => "Success",
                "user_id" => 3,
                "email" => null,
                "telno" => "01139893143",
                "username" => "hafiz.jamil"
            ),
            1 =>
            array(
                "id" => 2,
                "nama" => "Donation_20210309210428",
                "description" => "3",
                "transac_no" => "2103092104320408",
                "datetime_created" => "2021-03-09 21:04:43",
                "amount" => 123,
                "status" => "Success",
                "user_id" => 3,
                "email" => null,
                "telno" => "0194208847",
                "username" => "hisham.ali"
            ),
            2 =>
            array(
                "id" => 3,
                "nama" => "Donation_20210309210428",
                "description" => "3",
                "transac_no" => "2103092104320408",
                "datetime_created" => "2021-03-09 21:23:25",
                "amount" => 123,
                "status" => "Success",
                "user_id" => null,
                "email" => null,
                "telno" => "01242384753",
                "username" => "sir.yahya"
            ),
            3 =>
            array(
                "id" => 4,
                "nama" => "Donation_20210315210428",
                "description" => "3",
                "transac_no" => "2103092104320408",
                "datetime_created" => "2021-03-16 21:23:25",
                "amount" => 123,
                "status" => "Success",
                "user_id" => null,
                "email" => null,
                "telno" => "01242384753",
                "username" => "afiq.iskandar"
            ),
            4 =>
            array(
                "id" => 5,
                "nama" => "Donation_20210315210448",
                "description" => "3",
                "transac_no" => "2103092104320408",
                "datetime_created" => "2021-03-22 21:23:25",
                "amount" => 123,
                "status" => "Success",
                "user_id" => null,
                "email" => null,
                "telno" => "01242384753",
                "username" => "ajiq.damian"
            ),
            5 =>
            array(
                "id" => 6,
                "nama" => "Donation_23210315210448",
                "description" => "3",
                "transac_no" => "2103092104320408",
                "datetime_created" => "2021-03-16 21:23:25",
                "amount" => 123,
                "status" => "Success",
                "user_id" => null,
                "email" => null,
                "telno" => "01242384753",
                "username" => "zaki.salleh"
            ),
        ));
    }
}
