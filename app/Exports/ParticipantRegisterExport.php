<?php

namespace App\Exports;

use App\Models\Institution;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ParticipantRegisterExport implements WithEvents, WithTitle, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function title(): string
    {
        return 'Borang Peserta';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', '1. Jangan ubah susunan atau nama lajur yang sedia ada.');
                $sheet->setCellValue('A2', '2. Pastikan semua maklumat peserta adalah lengkap dan tepat.');
                $sheet->setCellValue('A3', '3. Pilih jenis institusi terlebih dahulu sebelum memilih nama institusi & pengajian semasa.');

                $sheet->getStyle('A1:A3')->getFont()->getColor()->setARGB(Color::COLOR_RED);

                $headers = [
                    'A7' => 'Nama Penuh',
                    'B7' => 'Jantina',
                    'C7' => 'No IC / No Pasport',
                    'D7' => 'Alamat Email',
                    'E7' => 'No Telefon',
                    'F7' => 'Jenis Institusi',
                    'G7' => 'Nama Institusi',
                    'H7' => 'Pengajian Semasa',
                    'I7' => 'Nama Penyelia',
                    'J7' => 'Alamat Email Penyelia',
                    'K7' => 'No Telefon Penyelia',
                ];

                foreach ($headers as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                }

                $headerStyle = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => '1b64a6'],
                        'setAutoSize' => true,
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => Color::COLOR_WHITE],
                    ],
                ];
                $sheet->getStyle('A7:K7')->applyFromArray($headerStyle);

                foreach(range('A', 'K') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                foreach(range('N', 'S') as $column) {
                    $sheet->getColumnDimension($column)->setVisible(false);
                }

                //DATA PENGAJIAN SEMASA
                $lookupHeaders = [
                    'N4' => 'Institusi',
                    'O4' => 'Sekolah Rendah',
                    'P4' => 'Sekolah Menengah',
                    'Q4' => 'Pra-Universiti',
                    'R4' => 'Kolej Vokasional & Politeknik',
                    'S4' => 'Universiti',
                ];
                foreach ($lookupHeaders as $cell => $val) {
                    $sheet->setCellValue($cell, $val);
                }

                //Jenis Institusi
                $jenisInstitusi = [
                    'Sekolah Rendah',
                    'Sekolah Menengah',
                    'Pra-Universiti',
                    'Kolej Vokasional & Politeknik',
                    'Universiti'
                ];
                foreach ($jenisInstitusi as $i => $val) {
                    $sheet->setCellValue('N' . ($i + 5), $val);
                }

                //Sekolah Rendah
                $sekolahRendah = ['Tahun 1', 'Tahun 2', 'Tahun 3', 'Tahun 4', 'Tahun 5', 'Tahun 6'];
                foreach ($sekolahRendah as $i => $val) {
                    $sheet->setCellValue('O' . ($i + 5), $val);
                }

                //Sekolah Menengah
                $sekolahMenengah = ['Tingkatan 1', 'Tingkatan 2', 'Tingkatan 3', 'Tingkatan 4', 'Tingkatan 5'];
                foreach ($sekolahMenengah as $i => $val) {
                    $sheet->setCellValue('P' . ($i + 5), $val);
                }

                //Pra-Universiti
                $praUni = ['Tingkatan 6', 'Matrikulasi', 'Asasi'];
                foreach ($praUni as $i => $val) {
                    $sheet->setCellValue('Q' . ($i + 5), $val);
                }

                //KV&Poli
                $KVEdu = [
                    'Sijil Vokasional Malaysia (SVM) Tahun 1',
                    'Sijil Vokasional Malaysia (SVM) Tahun 2',
                    'Diploma Vokasional Malaysia (DVM) Tahun 1',
                    'Diploma Vokasional Malaysia (DVM) Tahun 2',
                    'Politeknik / Kolej Komuniti Diploma Tahun 1',
                    'Politeknik / Kolej Komuniti Diploma Tahun 2',
                ];
                foreach ($KVEdu as $i => $val) {
                    $sheet->setCellValue('R' . ($i + 5), $val);
                }

                //University
                $UniEdu = [
                    'Diploma Tahun 1',
                    'Diploma Tahun 2',
                    'Diploma Tahun 3',
                    'Degree Tahun 1',
                    'Degree Tahun 2',
                    'Degree Tahun 3',
                    'Degree Tahun 4',
                    'Degree Tahun 5',
                ];
                foreach ($UniEdu as $i => $val) {
                    $sheet->setCellValue('S' . ($i + 5), $val);
                }

                //DYNAMIC UNIVERSITIY
                $institutionTypes = [
                    'O' => 'Sekolah Rendah',
                    'P' => 'Sekolah Menengah',
                    'Q' => 'Pra-Universiti',
                    'R' => 'Kolej Vokasional & Politeknik',
                    'S' => 'Universiti',
                ];

                foreach ($institutionTypes as $col => $typeName) {
                    $sheet->setCellValue($col . '14', $typeName);

                    $names = Institution::where('instituteType', $typeName)
                        ->pluck('instituteName')
                        ->toArray();

                    if (empty($names)) {
                        $sheet->setCellValue($col . '15', 'Sila Taip Nama ' . $typeName);
                    } else {
                        foreach ($names as $i => $name) {
                            $sheet->setCellValue($col . ($i + 15), $name);
                        }
                    }
                }

                $maxRowU = Institution::where('instituteType', 'Sekolah Rendah')->count();
                $maxRowV = Institution::where('instituteType', 'Sekolah Menengah')->count();
                $maxRowW = Institution::where('instituteType', 'Pra-Universiti')->count();
                $maxRowX = Institution::where('instituteType', 'Kolej Vokasional & Politeknik')->count();
                $maxRowY = Institution::where('instituteType', 'Universiti')->count();

                $lastInstRow = max(5, $maxRowU + 4, $maxRowV + 4, $maxRowW + 4, $maxRowX + 4, $maxRowY + 4);

                $jantinaValidation = new DataValidation();
                $jantinaValidation->setType(DataValidation::TYPE_LIST);
                $jantinaValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $jantinaValidation->setAllowBlank(false);
                $jantinaValidation->setShowDropDown(true);
                $jantinaValidation->setFormula1('"Lelaki,Perempuan"');

                $jenisValidation = new DataValidation();
                $jenisValidation->setType(DataValidation::TYPE_LIST);
                $jenisValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $jenisValidation->setAllowBlank(false);
                $jenisValidation->setShowDropDown(true);
                $jenisValidation->setFormula1('=$N$5:$N$9');

                $namaValidation = new DataValidation();
                $namaValidation->setType(DataValidation::TYPE_LIST);
                $namaValidation->setErrorStyle(DataValidation::STYLE_WARNING);
                $namaValidation->setAllowBlank(false);
                $namaValidation->setShowDropDown(true);

                $pengajianValidation = new DataValidation();
                $pengajianValidation->setType(DataValidation::TYPE_LIST);
                $pengajianValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $pengajianValidation->setAllowBlank(false);
                $pengajianValidation->setShowDropDown(true);

                $emailValidation = new DataValidation();
                $emailValidation->setType(DataValidation::TYPE_CUSTOM);
                $emailValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $emailValidation->setAllowBlank(false);
                $emailValidation->setShowErrorMessage(true);
                $emailValidation->setErrorTitle('Error');
                $emailValidation->setError('Invalid email');

                $phoneValidation = new DataValidation();
                $phoneValidation->setType(DataValidation::TYPE_CUSTOM);
                $phoneValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $phoneValidation->setAllowBlank(false);
                $phoneValidation->setShowErrorMessage(true);
                $phoneValidation->setErrorTitle('Error');
                $phoneValidation->setError('Invalid phone number');

                for ($row = 8; $row <= 100; $row++) {
                    $sheet->getCell('B' . $row)->setDataValidation(clone $jantinaValidation);

                    $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                    $sheet->getCell('C' . $row)->setDataType(DataType::TYPE_STRING);

                    $emailValidationRowD = clone $emailValidation;
                    $emailValidationRowD->setFormula1('AND(ISNUMBER(FIND("@", D' . $row . ')), ISNUMBER(FIND(".", D' . $row . ')))');
                    $sheet->getCell('D' . $row)->setDataValidation($emailValidationRowD);

                    $phoneValidationRowE = clone $phoneValidation;
                    $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                    $sheet->getCell('E' . $row)->setDataType(DataType::TYPE_STRING);
                    $phoneValidationRowE->setFormula1('ISNUMBER(VALUE(E' . $row . '))');
                    $sheet->getCell('E' . $row)->setDataValidation($phoneValidationRowE);

                    $sheet->getCell('F' .$row)->setDataValidation(clone $jenisValidation);

                    $namaValidationRow = clone $namaValidation;
                    $namaValidationRow->setFormula1('=INDEX($o$15:$S$' . $lastInstRow . ', 0, MATCH(F' . $row . ', $O$14:$S$14, 0))');
                    $sheet->getCell('G' . $row)->setDataValidation($namaValidationRow);

                    $pengajianValidationRow = clone $pengajianValidation;
                    $pengajianValidationRow->setFormula1('=INDEX($O$5:$S$13, 0, MATCH(F' . $row . ', $O$4:$S$4, 0))');
                    $sheet->getCell('H' . $row)->setDataValidation($pengajianValidationRow);

                    $emailValidationRowJ = clone $emailValidation;
                    $emailValidationRowJ->setFormula1('AND(ISNUMBER(FIND("@", J' . $row . ')), ISNUMBER(FIND(".", J' . $row . ')))');
                    $sheet->getCell('J' . $row)->setDataValidation($emailValidationRowJ);

                    $phoneValidationRowK = clone $phoneValidation;
                    $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                    $sheet->getCell('K' . $row)->setDataType(DataType::TYPE_STRING);
                    $phoneValidationRowK->setFormula1('ISNUMBER(VALUE(K' . $row . '))');
                    $sheet->getCell('K' . $row)->setDataValidation($phoneValidationRowK);
                }
            },
        ];
    }
}
