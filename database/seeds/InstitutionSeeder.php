<?php

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $institutions = [

            //UNIVERSITI
            [
                'instituteName' => 'Universiti Teknikal Malaysia Melaka (UTeM)',
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UTeM_logo.png',
            ],
            [
                'instituteName' => 'Universiti Teknologi Malaysia (UTM)',
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UTM_logo.png',
            ],
            [
                'instituteName' => 'Universiti Kebangsaan Malaysia (UKM)',
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UKM_logo.png',
            ],
            [
                'instituteName' => 'Universiti Malaya (UM)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UM_logo.png',
            ],
            [
                'instituteName' => 'Universiti Sains Malaysia (USM)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'USM_logo.png',
            ],
            [
                'instituteName' => 'Universiti Putra Malaysia (UPM)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UPM_logo.png',
            ],
            [
                'instituteName' => 'Universiti Teknologi MARA (UiTM)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UiTM_logo.png',
            ],
            [
                'instituteName' => 'Universiti Utara Malaysia (UUM)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UUM_logo.png',
            ],
            [
                'instituteName' => 'Universiti Islam Antarabangsa Malaysia (UIAM)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UIAM_logo.png',
            ],
            [
                'instituteName' => 'Universiti Malaysia Sarawak (UNIMAS)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UNIMAS_logo.png',
            ],
            [
                'instituteName' => 'Universiti Malaysia Sabah (UMS)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UMS_logo.png',
            ],
            [
                'instituteName' => 'Universiti Pendidikan Sultan Idris (UPSI)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UPSI_logo.png',
            ],
            [
                'instituteName' => 'Universiti Sains Islam Malaysia (USIM)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'USIM_logo.png',
            ],
            [
                'instituteName' => 'Universiti Malaysia Terengganu (UMT)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UMT_logo.png',
            ],
            [
                'instituteName' => 'Universiti Tun Hussein Onn Malaysia (UTHM)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UTHM_logo.png',
            ],
            [
                'instituteName' => 'Universiti Malaysia Pahang Al-Sultan Abdullah (UMPSA)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UMPSA_logo.png',
            ],
            [
                'instituteName' => 'Universiti Malaysia Perlis (UniMAP)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UniMAP_logo.png',
            ],
            [
                'instituteName' => 'Universiti Sultan Zainal Abidin (UniSZA)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UniSZA_logo.png',
            ],
            [
                'instituteName' => 'Universiti Malaysia Kelantan (UMK)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UMK_logo.png',
            ],
            [
                'instituteName' => 'Universiti Pertahanan Nasional Malaysia (UPNM)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UPNM_logo.png',
            ],
            [
                'instituteName' => 'Multimedia University (MMU)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'MMU_logo.png',
            ],
            [
                'instituteName' => 'Universiti Tenaga Nasional (UNITEN)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UNITEN_logo.png',
            ],
            [
                'instituteName' => 'Universiti Teknologi PETRONAS (UTP)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UTP_logo.png',
            ],
            [
                'instituteName' => 'Universiti Kuala Lumpur (UniKL)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'UniKL_logo.png',
            ],
            [
                'instituteName' => 'Tunku Abdul Rahman University of Management and Technology (TAR UMT)', 
                'instituteType' => 'Universiti',
                'instituteLogo' => 'TAR_UMT_logo.png',
            ],

            //POLITEKNIK
            [
                'instituteName' => 'Politeknik Ungku Omar', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Sultan Salahuddin Abdul Aziz Shah', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Sultan Abdul Halim Mu\'adzam Shah', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],            
            [
                'instituteName' => 'Politeknik Tuanku Syed Sirajuddin', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Tuanku Sultanah Bahiyah', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik METrO Tasek Gelugor', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Seberang Perai', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Balik Pulau', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Sultan Azlan Shah', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Taiping', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Sultan Idris Shah', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Banting Selangor', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik METrO Kuala Lumpur', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Nilai', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Port Dickson', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Merlimau', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Kota Melaka', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Ibrahim Sultan', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Tun Syed Nasir Ismail', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Mersing', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik METrO Johor Bahru', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Sultan Haji Ahmad Shah', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Muadzam Shah', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik METrO Kuantan', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Sultan Mizan Zainal Abidin', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Kuala Terengganu', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Kota Bharu', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],
            [
                'instituteName' => 'Politeknik Jeli', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => 'POLITEKNIK_logo.png'
            ],

            //MATRIKULASI
            [
                'instituteName' => 'Kolej Matrikulasi Perlis (KMP)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => '',
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Kedah (KMK)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Pulau Pinang (KMPP)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Perak (KMPk)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Selangor (KMS)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Negeri Sembilan (KMNS)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Melaka (KMM)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Johor (KMJ)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Pahang (KMPH)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Kelantan (KMKt)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Terengganu (KMT)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Sarawak (KMSar)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Labuan (KML)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Kejuruteraan Kedah (KMKK)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Kejuruteraan Pahang (KMKP)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Matrikulasi Kejuruteraan Johor (KMKJ)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej MARA Kuala Nerang', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej MARA Kulim', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],

            //PUSAT ASASI
            [
                'instituteName' => 'Pusat Asasi Sains Universiti Malaya (PASUM)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Pusat Asasi UiTM Dengkil', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Pusat Asasi Sains Pertanian UPM', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Pusat Asasi Universiti Kebangsaan Malaysia (UKM)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Pusat Asasi Universiti Islam Antarabangsa Malaysia (UIAM Gambang)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Pusat Asasi Universiti Sains Islam Malaysia (USIM)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Pusat Asasi Universiti Malaysia Sarawak (UNIMAS)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Pusat Asasi Universiti Malaysia Sabah (UMS)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Pusat Asasi Universiti Pertahanan Nasional Malaysia (UPNM)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Pusat Asasi Universiti Malaysia Terengganu (UMT)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Pusat Asasi Universiti Sultan Zainal Abidin (UniSZA)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Pusat Asasi Universiti Utara Malaysia (UUM)', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],

            //KOLEJ TINGKATAN 6
            [
                'instituteName' => 'Kolej Tingkatan 6 Desa Mahkota', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Tingkatan 6 Sri Istana', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Tingkatan 6 Pontian', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Tingkatan 6 Haji Zainul Abidin', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Tingkatan 6 Tun Fatimah', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Pusat Tingkatan 6 Moda', 
                'instituteType' => 'Pra-Universiti', 
                'instituteLogo' => ''
            ],

            //KOLEJ VOKASIONAL
            [
                'instituteName' => 'Kolej Vokasional Melaka Tengah', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Vokasional Datok Seri Mohd Zin', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'Kolej Vokasional Jasin', 
                'instituteType' => 'Kolej Vokasional & Politeknik', 
                'instituteLogo' => ''
            ],

            //SMK MELAKA
            [
                'instituteName' => 'SMK Tinggi Melaka (Malacca High School)',
                'instituteType' => 'Sekolah Menengah',
                'instituteLogo' => '',
            ],
            [
                'instituteName' => 'SMK Saint Francis', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Infant Jesus Convent', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Gajah Berang', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Bukit Baru', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Bukit Katil', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Tun Mutahir', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Munshi Abdullah', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Seri Kota', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Padang Temu', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Seri Tanjung', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Klebang Besar', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Malim', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMJK Katholik', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMJK Yok Bin', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMJK Tinggi Cina Melaka', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Dato\' Dol Said', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Seri Pengkalan', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Durian Tunggal', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Ghafar Baba', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Masjid Tanah', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Hang Kasturi', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Pulau Sebang', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Rahmat', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Tebong', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMJK Pulau Sebang', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Iskandar Shah', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Tun Perak', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Datuk Bendahara', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Merlimau', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Dang Anum', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Seri Bemban', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Sungai Rambai', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SMK Nyalas', 
                'instituteType' => 'Sekolah Menengah', 
                'instituteLogo' => ''
            ],

            //SK MELAKA
            [
                'instituteName' => 'SK Alor Gajah 1', 
                'instituteType' => 'Sekolah Rendah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SK Alor Gajah 2', 
                'instituteType' => 'Sekolah Rendah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SK Durian Tunggal', 
                'instituteType' => 'Sekolah Rendah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SK Gangsa', 
                'instituteType' => 'Sekolah Rendah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SK Belimbing Dalam', 
                'instituteType' => 'Sekolah Rendah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SK Masjid Tanah', 
                'instituteType' => 'Sekolah Rendah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SK Pengkalan Balak', 
                'instituteType' => 'Sekolah Rendah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SK Pulau Sebang', 
                'instituteType' => 'Sekolah Rendah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SJK (C) Alor Gajah', 
                'instituteType' => 'Sekolah Rendah', 
                'instituteLogo' => ''
            ],
            [
                'instituteName' => 'SJK (T) Alor Gajah', 
                'instituteType' => 'Sekolah Rendah', 
                'instituteLogo' => ''
            ],
        ];

        foreach ($institutions as $institution) {
            Institution::updateOrCreate(
                ['instituteName' => $institution['instituteName']],
                [
                    'instituteType' => $institution['instituteType'],
                    'instituteLogo' => $institution['instituteLogo']
                ]
            );
        }
    }
}
