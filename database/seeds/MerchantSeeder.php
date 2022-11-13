<?php

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\OrganizationHours;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->delete_merchant();

        $this->add_merchant();
        $this->add_product();
    }

    private function delete_merchant()
    {
        User::find(101)->delete();
        User::find(102)->delete();
        User::find(103)->delete();
        User::find(104)->delete();

        Organization::find(1001)->forceDelete();
        Organization::find(1002)->forceDelete();
        Organization::find(1003)->forceDelete();
    }

    private function add_merchant()
    {
        DB::table('users')->insert(array(
            0 => array(
                "id" => 101,
                "email" => app()->environment('local') ? 'test_seed_1@test.com' : 'test_seed_1@test.com',
                "password" => app()->environment('local') ? \Hash::make("abc123") : \Hash::make("abc123"),
                "name" => "Test Seed 1",
                "telno" => "01139893141",
                "remember_token" => "",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ),
            1 => array(
                "id" => 102,
                "email" => app()->environment('local') ? 'test_seed_2@test.com' : 'test_seed_2@test.com',
                "password" => app()->environment('local') ? \Hash::make("abc123") : \Hash::make("abc123"),
                "name" => "Test Seed 2",
                "telno" => "01139893142",
                "remember_token" => "",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ),
            2 => array(
                "id" => 103,
                "email" => app()->environment('local') ? 'test_seed_3@test.com' : 'test_seed_3@test.com',
                "password" => app()->environment('local') ? \Hash::make("abc123") : \Hash::make("abc123"),
                "name" => "Test Seed 3",
                "telno" => "01139893143",
                "remember_token" => "",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ),
            3 => array(
                "id" => 104,
                "email" => app()->environment('local') ? 'test_cust@test.com' : 'test_cust@test.com',
                "password" => app()->environment('local') ? \Hash::make("abc123") : \Hash::make("abc123"),
                "name" => "Test Customer",
                "telno" => "01139893140",
                "remember_token" => "",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            )
        ));
        
        $user1 = User::find(101);
        $user2 = User::find(102);
        $user3 = User::find(103);

        DB::table('organizations')->insert(array(
            0 => array(
                'id' => '1001',
                'code' => 'PBU00010',
                'email' => 'reg_merchant_test_1@test.com',
                'nama' => 'Peniaga Test 1',
                'telno' => '+6075572891',
                'address' => 'alamat test',
                'postcode' => '123',
                'state' => 'Melaka',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'type_org' => '3111',
                'district' => 'Melaka Tengah',
                'city' => 'bandar test',
            ),
            1 => array(
                'id' => '1002',
                'code' => 'PBU00020',
                'email' => 'reg_merchant_test_2@test.com',
                'nama' => 'Peniaga Test 2',
                'telno' => '+6075572892',
                'address' => 'alamat test',
                'postcode' => '123',
                'state' => 'Melaka',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'type_org' => '3111',
                'district' => 'Melaka Tengah',
                'city' => 'bandar test',
            ),
            2 => array(
                'id' => '1003',
                'code' => 'PBU00030',
                'email' => 'reg_merchant_test_3@test.com',
                'nama' => 'Peniaga Test 3',
                'telno' => '+6075572893',
                'address' => 'alamat test',
                'postcode' => '123',
                'state' => 'Melaka',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'type_org' => '3111',
                'district' => 'Melaka Tengah',
                'city' => 'bandar test',
            ),
        ));

        DB::table('organization_user')->insert(array(
            0 => array(
                'organization_id' => '1001',
                'user_id' => '101',
                'role_id' => 3114,
                'start_date' => now(),
                'status' => 1,
            ),
            1 => array(
                'organization_id' => '1002',
                'user_id' => '102',
                'role_id' => 3114,
                'start_date' => now(),
                'status' => 1,
            ),
            2 => array(
                'organization_id' => '1003',
                'user_id' => '103',
                'role_id' => 3114,
                'start_date' => now(),
                'status' => 1,
            ),
        ));

        $user1->assignRole('Regular Merchant Admin');
        $user2->assignRole('Regular Merchant Admin');
        $user3->assignRole('Regular Merchant Admin');

        $this->insertOrganizationHours('1001');
        $this->insertOrganizationHours('1002');
        $this->insertOrganizationHours('1003');
    }

    public function add_product()
    {
        DB::table('product_group')->insert(array(
            0 => array(
                'id' => '10',
                'name' => 'Group 1 S1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'organization_id' => '1001'
            ),
            1 => array(
                'id' => '20',
                'name' => 'Group 1 S2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'organization_id' => '1002'
            ),
            2 => array(
                'id' => '30',
                'name' => 'Group 2 S2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'organization_id' => '1002'
            ),
            3 => array(
                'id' => '40',
                'name' => 'Group 3 S2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'organization_id' => '1002'
            ),
        ));

        DB::table('product_item')->insert(array(
            0 => array(
                'name' => 'Item 1 G1',
                'type' => 'have inventory',
                'quantity_available' => 20,
                'price' => '5.10',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'product_group_id' => '10'
            ),
            1 => array(
                'name' => 'Item 2 G1',
                'type' => 'no inventory',
                'price' => '4.30',
                'quantity_available' => null,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'product_group_id' => '10'
            ),
            2 => array(
                'name' => 'Item 3 G1',
                'type' => 'have inventory',
                'price' => '4.30',
                'quantity_available' => 10,
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'product_group_id' => '10'
            ),
            3 => array(
                'name' => 'Item 1 G1',
                'type' => 'have inventory',
                'price' => '5.00',
                'quantity_available' => 10,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'product_group_id' => '20'
            ),
            4 => array(
                'name' => 'Item 1 G2',
                'type' => 'have inventory',
                'price' => '5.10',
                'quantity_available' => 5,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'product_group_id' => '30'
            ),
            5 => array(
                'name' => 'Item 2 G2',
                'type' => 'no inventory',
                'price' => '4.10',
                'quantity_available' => null,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'product_group_id' => '30'
            ),
            6 => array(
                'name' => 'Item 3 G2',
                'type' => 'have inventory',
                'price' => '1.50',
                'quantity_available' => 5,
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'product_group_id' => '30'
            ),
        ));
    }

    private function insertOrganizationHours($id)
    {
        OrganizationHours::insert([
            [
                'day' => 1,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'day' => 2,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 3,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 4,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 5,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 6,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 0,
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}


