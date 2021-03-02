<?php

use Illuminate\Database\Seeder;

class RemindersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('donation_reminder')->delete();

        \DB::table('donation_reminder')->insert(array(
            0 =>
            array(
                'id' => 1,
                'date' => 24,
                'time' => '8:00',
                'day' => '',
                'recurrence' => 'monthly',
            ),
            1 =>
            array(
                'id' => 2,
                'date' => '',
                'time' => '8:00',
                'day' => '',
                'recurrence' => 'daily',
            ),
            2 =>
            array(
                'id' => 3,
                'date' => '',
                'time' => '8:00',
                'day' => 1,
                'recurrence' => 'weekly',
            ),
        ));
    }
}
