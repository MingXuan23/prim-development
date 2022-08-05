<?php

use Illuminate\Database\Seeder;

class DishTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('dish_type')->delete();
        DB::table('dish_type')->insert(array(
            0 =>
            array(
                "id" => 1,
                "name" => "Halal",
            ),
            1 =>
            array(
                "id" => 2,
                "name" => "Seafood",
            ),
            2 =>
            array(
                "id" => 3,
                "name" => "Fried Chicken",
            ),
            3 =>
            array(
                "id" => 4,
                "name" => "Western",
            ),
            4 =>
            array(
                "id" => 5,
                "name" => "Fast Food",
            ),
            5 =>
            array(
                "id" => 6,
                "name" => "Dessert",
            ),
            6 =>
            array(
                "id" => 7,
                "name" => "Local",
            ),
            7 =>
            array(
                "id" => 8,
                "name" => "Chicken",
            ),
            8 =>
            array(
                "id" => 9,
                "name" => "Asian",
            ),
            9 =>
            array(
                "id" => 10,
                "name" => "Snack",
            ),
            10 =>
            array(
                "id" => 11,
                "name" => "Noodles",
            ),
        ));
    }
}
