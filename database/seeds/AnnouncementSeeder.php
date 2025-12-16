<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $adminUserId = 17151;

        //org announcement
        $targetOrgId = 161;

        DB::table('organization_announcements')->insert([
            'organization_id' => $targetOrgId,
            'user_id' => $adminUserId,
            'title' => 'Makluman Cuti Umum',
            'content' => 'Dimaklumkan bahawa pihak sekolah akan ditutup sempena Hari Krismas. Semua aktiviti pengajaran dan pembelajaran akan disambung semula seperti biasa selepas cuti.',
            'is_pinned' => 1,
            'send_notification' => 1,
            'status' => 'published',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->command->info("✔ Organization announcement inserted (Org ID: $targetOrgId)");

        //class anouncement
        $classIdPJ = 579;

        DB::table('class_announcements')->insert([
            'class_id' => $classIdPJ,
            'user_id' => $adminUserId,
            'title' => 'Makluman Kelas Pendidikan Jasmani',
            'content' => 'Pelajar diminta untuk memakai pakaian sukan yang sesuai semasa kelas Pendidikan Jasmani. Sila pastikan kasut sukan dan pakaian lengkap dibawa pada hari tersebut.',
            'is_pinned' => 0,
            'send_notification' => 1,
            'status' => 'published',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->command->info("✔ Class announcement inserted (Class ID: $classIdPJ)");

        //class announcement
        $classIdArt = 575;

        DB::table('class_announcements')->insert([
            'class_id' => $classIdArt,
            'user_id' => $adminUserId,
            'title' => 'Makluman Kelas Pendidikan Seni',
            'content' => 'Pelajar diminta membawa set cat air dan peralatan melukis yang lengkap bagi tujuan aktiviti pembelajaran dalam kelas Pendidikan Seni.',
            'is_pinned' => 0,
            'send_notification' => 1,
            'status' => 'published',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->command->info("✔ Class announcement inserted (Class ID: $classIdArt)");
    }
}
