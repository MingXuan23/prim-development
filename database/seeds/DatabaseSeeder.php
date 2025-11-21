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
        $this->call(TypeOrganizationsTableSeeder::class);
        $this->call(OrganizationsTableSeeder::class);
        $this->call(OrganizationRolesTableSeeder::class);
        $this->call(OrganizationUserTableSeeder::class);
        $this->call(DonationTypeSeeder::class);
        $this->call(DonationTableSeeder::class);
        $this->call(DonationOrganizationTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(RemindersTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        $this->call(PaymentTypeTableSeeder::class);
        $this->call(TransactionsTableSeeder::class);
        $this->call(DonationTransactionTableSeeder::class);
        $this->call(OrganizationParentTableSeeder::class);
        $this->call(OrganizationNegeriTableSeeder::class);
        $this->call(OrganizationDaerahTableSeeder::class);
        $this->call(StudentTableSeeder::class);
        $this->call(ClassTableSeeder::class);
        $this->call(ClassOrganizationTableSeeder::class);
        $this->call(ClassStudentTableSeeder::class);
        $this->call(ReferalCodeMemberLevelSeeder::class);
        $this->call(ApplicationSeeder::class);
    }
}
