<?php

use Illuminate\Database\Seeder;

class CallFeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ClassTableSeeder::class);
        $this->call(ClassOrganizationTableSeeder::class);
        $this->call(StudentTableSeeder::class);
        $this->call(ClassStudentTableSeeder::class);
        $this->call(Year_FeesTableSeeder::class);
        $this->call(FeesTableSeeder::class);
        $this->call(DetailsTableSeeder::class);
        $this->call(FeesDetailsTableSeeder::class);
        $this->call(StudentFeesTableSeeder::class);
    }
}
