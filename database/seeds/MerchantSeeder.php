<?php

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\OrganizationHours;
use App\Models\OrganizationRole;
use App\Models\PgngOrder;
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
        
        $this->add_role();
        // $this->add_merchant();
        // $this->add_product();
        // $this->testing();
    }

    private function delete_merchant()
    {
        $user1 = User::where('email', 'test_seed_mer_1@test.com')->first()->id;
        $user2 = User::where('email', 'test_seed_mer_2@test.com')->first()->id;
        $user3 = User::where('email', 'test_seed_mer_3@test.com')->first()->id;

        $org1 = Organization::where('email', 'reg_merchant_test_1@test.com')->first()->id;
        $org2 = Organization::where('email', 'reg_merchant_test_2@test.com')->first()->id;
        $org3 = Organization::where('email', 'reg_merchant_test_3@test.com')->first()->id;

        User::find($user1)->delete();
        User::find($user2)->delete();
        User::find($user3)->delete();

        Organization::find($org1)->forceDelete();
        Organization::find($org2)->forceDelete();
        Organization::find($org3)->forceDelete();
    }

    private function add_role()
    {
        DB::table('roles')->insert(array(
            // 0 => 
            // array(
            //     "name" => "Koop Admin",
            //     "guard_name" => "web"
            // ),
            // 1 => 
            // array(
            //     "name" => "Schedule Merchant Admin",
            //     "guard_name" => "web"
            // ),
            0 => 
            array(
                "name" => "Regular Merchant Admin",
                "guard_name" => "web"
            ),
        ));

        DB::table('organization_roles')->insert(array(
            // 0 => 
            // array(
            //     "nama" => "Koop Admin",
            // ),
            // 1 => 
            // array(
            //     "nama" => "Schedule Merchant Admin",
            // ),
            0 => 
            array(
                "nama" => "Regular Merchant Admin",
            ),
        ));

        DB::table('type_organizations')->insert(array(
            // 0 =>
            // array(
            //     "nama" => "Koperasi",
            // ),
            // 1 =>
            // array(
            //     "nama" => "Peniaga Barang Berjadual",
            // ),
            0 =>
            array(
                "nama" => "Peniaga Barang Umum",
            ),
        ));
    }

    private function add_merchant()
    {
        DB::table('users')->insert(array(
            0 => array(
                "email" => app()->environment('local') ? 'test_seed_mer_1@test.com' : 'test_seed_mer_1@test.com',
                "password" => app()->environment('local') ? \Hash::make("abc123") : \Hash::make("abc123"),
                "name" => "Test Seed 1",
                "telno" => "01139893141",
                "remember_token" => "",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ),
            1 => array(
                "email" => app()->environment('local') ? 'test_seed_mer_2@test.com' : 'test_seed_mer_2@test.com',
                "password" => app()->environment('local') ? \Hash::make("abc123") : \Hash::make("abc123"),
                "name" => "Test Seed 2",
                "telno" => "01139893142",
                "remember_token" => "",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ),
            2 => array(
                "email" => app()->environment('local') ? 'test_seed_mer_3@test.com' : 'test_seed_mer_3@test.com',
                "password" => app()->environment('local') ? \Hash::make("abc123") : \Hash::make("abc123"),
                "name" => "Test Seed 3",
                "telno" => "01139893143",
                "remember_token" => "",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ),
        ));
        
        $user1 = User::where('email', 'test_seed_mer_1@test.com')->first();
        $user2 = User::where('email', 'test_seed_mer_2@test.com')->first();
        $user3 = User::where('email', 'test_seed_mer_3@test.com')->first();

        $type_org_id = DB::table('type_organizations')->where('nama', 'Peniaga Barang Umum')->first()->id;

        DB::table('organizations')->insert(array(
            0 => array(
                'code' => 'PBU00010',
                'email' => 'reg_merchant_test_1@test.com',
                'nama' => 'Peniaga Test 1',
                'telno' => '+6075572891',
                'address' => 'alamat test',
                'postcode' => '123',
                'state' => 'Melaka',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'type_org' => $type_org_id,
                'district' => 'Melaka Tengah',
                'city' => 'bandar test',
            ),
            1 => array(
                'code' => 'PBU00020',
                'email' => 'reg_merchant_test_2@test.com',
                'nama' => 'Peniaga Test 2',
                'telno' => '+6075572892',
                'address' => 'alamat test',
                'postcode' => '123',
                'state' => 'Melaka',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'type_org' => $type_org_id,
                'district' => 'Melaka Tengah',
                'city' => 'bandar test',
            ),
            2 => array(
                'code' => 'PBU00030',
                'email' => 'reg_merchant_test_3@test.com',
                'nama' => 'Peniaga Test 3',
                'telno' => '+6075572893',
                'address' => 'alamat test',
                'postcode' => '123',
                'state' => 'Melaka',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'type_org' => $type_org_id,
                'district' => 'Melaka Tengah',
                'city' => 'bandar test',
            ),
        ));

        $org1 = Organization::where('email', 'reg_merchant_test_1@test.com')->first();
        $org2 = Organization::where('email', 'reg_merchant_test_2@test.com')->first();
        $org3 = Organization::where('email', 'reg_merchant_test_3@test.com')->first();

        $role_id = OrganizationRole::where('nama', 'Regular Merchant Admin')->first()->id;

        DB::table('organization_user')->insert(array(
            0 => array(
                'organization_id' => $org1->id,
                'user_id' => $user1->id,
                'role_id' => $role_id,
                'start_date' => now(),
                'status' => 1,
            ),
            1 => array(
                'organization_id' => $org2->id,
                'user_id' => $user2->id,
                'role_id' => $role_id,
                'start_date' => now(),
                'status' => 1,
            ),
            2 => array(
                'organization_id' => $org3->id,
                'user_id' => $user3->id,
                'role_id' => $role_id,
                'start_date' => now(),
                'status' => 1,
            ),
        ));

        $user1->assignRole('Regular Merchant Admin');
        $user2->assignRole('Regular Merchant Admin');
        $user3->assignRole('Regular Merchant Admin');

        $this->insertOrganizationHours($org1->id);
        $this->insertOrganizationHours($org2->id);
        $this->insertOrganizationHours($org3->id);
    }

    public function add_product()
    {
        $org1 = Organization::where('email', 'reg_merchant_test_1@test.com')->first();
        $org2 = Organization::where('email', 'reg_merchant_test_2@test.com')->first();
        $org3 = Organization::where('email', 'reg_merchant_test_3@test.com')->first();

        DB::table('product_group')->insert(array(
            0 => array(
                'id' => '10',
                'name' => 'Group 1 S1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'organization_id' => $org1->id
            ),
            1 => array(
                'id' => '20',
                'name' => 'Group 1 S2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'organization_id' => $org2->id
            ),
            2 => array(
                'id' => '30',
                'name' => 'Group 2 S2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'organization_id' => $org2->id
            ),
            3 => array(
                'id' => '40',
                'name' => 'Group 3 S2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'organization_id' => $org2->id
            ),
        ));

        DB::table('product_item')->insert(array(
            0 => array(
                'name' => 'Item 1 G1',
                'type' => 'have inventory',
                'quantity_available' => 20,
                'price' => '5.10',
                'selling_quantity' => 1,
                'collective_noun' => 'Unit',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'product_group_id' => '10'
            ),
            1 => array(
                'name' => 'Item 2 G1',
                'type' => 'no inventory',
                'price' => '4.30',
                'selling_quantity' => 1,
                'collective_noun' => 'Unit',
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
                'selling_quantity' => 1,
                'collective_noun' => 'Unit',
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
                'selling_quantity' => 1,
                'collective_noun' => 'Unit',
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
                'selling_quantity' => 1,
                'collective_noun' => 'Unit',
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
                'selling_quantity' => 1,
                'collective_noun' => 'Unit',
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
                'selling_quantity' => 1,
                'collective_noun' => 'Unit',
                'quantity_available' => 5,
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'product_group_id' => '30'
            ),
        ));
    }

    public function testing()
    {
        PgngOrder::insert(array(
            array(
                'id' => 100,
                'order_type' => 'Pick-Up',
                'pickup_date' => '2022-12-24 11:00:00',
                'total_price' => 2.00,
                'status' => 'Paid',
                'user_id' => 8,
                'organization_id' => 12
            ),
            array(
                'id' => 101,
                'order_type' => 'Pick-Up',
                'pickup_date' => '2022-12-31 21:00:00',
                'total_price' => 3.00,
                'status' => 'Paid',
                'user_id' => 9,
                'organization_id' => 12
            ),
            array(
                'id' => 102,
                'order_type' => 'Pick-Up',
                'pickup_date' => '2022-12-24 14:00:00',
                'total_price' => 4.00,
                'status' => 'Paid',
                'user_id' => 10,
                'organization_id' => 12
            ),
            array(
                'id' => 103,
                'order_type' => 'Pick-Up',
                'pickup_date' => '2022-12-31 19:00:00',
                'total_price' => 5.00,
                'status' => 'Paid',
                'user_id' => 11,
                'organization_id' => 12
            ),
        ));
    }

    private function insertOrganizationHours($id)
    {
        OrganizationHours::insert([
            [
                'day' => 1,
                'open_hour' => '12:00:00',
                'close_hour' => '22:00:00',
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'day' => 2,
                'open_hour' => '12:00:00',
                'close_hour' => '22:00:00',
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 3,
                'open_hour' => '12:00:00',
                'close_hour' => '22:00:00',
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 4,
                'open_hour' => '12:00:00',
                'close_hour' => '22:00:00',
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 5,
                'open_hour' => '12:00:00',
                'close_hour' => '22:00:00',
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 6,
                'open_hour' => '12:00:00',
                'close_hour' => '22:00:00',
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'day' => 0,
                'open_hour' => '12:00:00',
                'close_hour' => '22:00:00',
                'status' => 0,
                'organization_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}


