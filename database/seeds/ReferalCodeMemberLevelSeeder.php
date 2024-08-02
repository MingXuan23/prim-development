<?php

use Illuminate\Database\Seeder;

class ReferalCodeMemberLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        try{
            $levels = [
                ['id' => 1, 'level' => 'Ahli Biasa'],
                ['id' => 2, 'level' => 'Ahli Aktif'],
                ['id' => 3, 'level' => 'Ahli Medal 1'],
                ['id' => 4, 'level' => 'Ahli Medal 2'],
                ['id' => 5, 'level' => 'Ahli Cemerlang 3'],
                ['id' => 6, 'level' => 'Ahli Cemerlang 4'],
                ['id' => 7, 'level' => 'Ahli Cemerlang 5'],
                ['id' => 8, 'level' => 'Ahli Cemerlang 6'],
                ['id' => 9, 'level' => 'Ahli Cemerlang 7'],
                ['id' => 10, 'level' => 'Ahli Cemerlang 8'],
                ['id' => 11, 'level' => 'Ahli Terbilang 9'],
            ];
    
            // Insert the levels into the referal_code_member_level table
            DB::table('referral_code_member_level')->insert($levels);
        }catch(Exception $e){
            return;
        }
    }
}
