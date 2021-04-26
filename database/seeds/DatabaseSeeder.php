<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(OrganizationsTableSeeder::class);
        $this->call(OrganizationRolesTableSeeder::class);
        $this->call(OrganizationUserTableSeeder::class);
        $this->call(DonationTableSeeder::class);
        $this->call(DonationOrganizationTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(RemindersTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        $this->call(PaymentTypeTableSeeder::class);
        $this->call(TransactionsTableSeeder::class);
        $this->call(DonationTransactionTableSeeder::class);
    }
}
