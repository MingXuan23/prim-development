<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SocialMediaSeeder extends Seeder
{
    public function run()
    {
        $teacherId = 17151;

        $imagePath = 'organization-picture/SMK-MingXuan.jpg';

        if (!DB::table('users')->where('id', $teacherId)->exists()) {
            return;
        }

        $parentIds = DB::table('users')->where('id', '!=', $teacherId)->limit(5)->pluck('id')->toArray();


        $this->command->info('start creating post');


        $postActiveId = DB::table('class_posts')->insertGetId([
            'class_id'   => 579,
            'user_id'    => $teacherId,
            'content'    => "Selamat pagi semua murid dan ibu bapa. Berikut adalah gambar logo SMK MingXuan. Harap dengan pembaruan logo ini, sekolah dapat menjadi sekolah harian unggul!",
            'media_url'  => $imagePath,
            'media_type' => 'image',
            'created_at' => Carbon::now()->subHours(4),
            'updated_at' => Carbon::now(),
        ]);


        $postQuietId = DB::table('class_posts')->insertGetId([
            'class_id'   => 575,
            'user_id'    => $teacherId,
            'content'    => "Perhatian murid-murid Kelas 1 Beta. Sila pastikan anda membawa buku latihan Matematik esok. Kita akan buat perbincangan topik Pecahan. Sekian, terima kasih.",
            'media_url'  => null,
            'media_type' => null,
            'created_at' => Carbon::now()->subMinutes(30),
            'updated_at' => Carbon::now(),
        ]);


        $this->command->info('Completed');
    }
}
