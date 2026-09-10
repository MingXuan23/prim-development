<?php

namespace App\Imports;

use App\Models\Institution;
use App\models\Participant;
use App\models\Team;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Exception;

class ParticipantImport implements ToCollection, WithStartRow, WithValidation, SkipsOnFailure
{
    /**
    * @param Collection $collection
    */

    use SkipsFailures;

    protected int $competitionId;
    public static $successCount = 0;
    public static $totalCount = 0;
    public static array $alreadyRegister = [];
    public static array $notRegisterFail = [];

    public function __construct(int $competitionId)
    {
        $this->competitionId = $competitionId;
        self::$totalCount = 0;
        self::$successCount = 0;
        self::$alreadyRegister = [];
        self::$notRegisterFail = [];
    }

    public function startRow(): int
    {
        return 8;
    }

    public function rules():array {
        return [
            '0' => ['required', 'string', 'max:255'],
            '1' => ['required'],
            '2' => ['required'],
            '3' => ['required', 'email'],
            '4' => ['required'],
            '5' => ['required'],
            '6' => ['required'],
            '7' => ['required'],
            '8' => ['required'],
            '9' => ['required', 'email'],
            '10' => ['required'],
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach($failures as $fail)
        {
            $row = $fail->row();
            self::$notRegisterFail[] = $row;
            Log::warning("Gagal validation pada baris ke-{$row} " . implode(', ', $fail->errors()));
        }
    }

    public function collection(Collection $collection)
    {
        foreach($collection as $rowIndex => $row)
        {
            if (empty($row[0]))
            {
                continue;
            }

            self::$totalCount++;

            $checkRegister = Participant::where(function($q) use($row) {
                $q->where('email', $row[3])
                ->orWhere('icNo', $row[2]);
            })->whereHas('team.team_registration', function($q) {
                $q->where('competitionId', $this->competitionId);
            })->exists();

            if ($checkRegister)
            {
                Log::info("Skip: Peserta {$row[3]} pada baris ke- . ($rowIndex + 1) . sudah mendaftar.");
                self::$alreadyRegister[] = $row[0];
                continue;
            }

            try {
            $selectId = null;
            $cleanInput = strtolower(trim($row[6]));
            $type = $row[5];

            $IsExist = Institution::where(function ($q) use ($cleanInput) {
                $q->whereRaw('LOWER(instituteName) = ?', [$cleanInput])
                  ->orWhereRaw('LOWER(instituteName) LIKE ?', ['%(' . $cleanInput . ')%'])
                  ->orWhereRaw('LOWER(instituteName) LIKE ?', ['%' . $cleanInput . '%']);
            })
            ->when($type, function ($query) use ($type) {
                return $query->where('instituteType', $type);
            })
            ->exists();
            
            if (!$IsExist)
            {
                $institution = Institution::create([
                    'instituteType' => $row[5],
                    'instituteName' => trim($row[6]),
                    'instituteLogo' => ''
                ]);

                $selectId = $institution->id;
            }
            else
            {
                $selectId = $IsExist;
            }
            
                $team = Team::create(['groupName' => '---Individu---']);

                $participant = Participant::create([
                    'name' => $row[0],
                    'gender' => $row[1],
                    'icNo' => $row[2],
                    'email' => $row[3],
                    'noTel' => $row[4],
                    'currentGrade' => $row[7],
                    'supervisorName' => $row[8],
                    'supervisorEmail' => $row[9],
                    'supervisorNoTel' => $row[10],
                    'isLeader' => null,
                    'teamId' => $team->id,
                    'userId' => Auth::id(),
                    'institutionId' => $selectId
                ]);

                $participant->team->team_registration()->attach($this->competitionId, [
                    'statusPayment' => 'Pending',
                    'registeredDate' => Carbon::now(),
                ]);

                self::$successCount++;
            }
            catch (Exception $e)
            {
                self::$notRegisterFail[] = $rowIndex + 8;
                Log::error("Ralat semasa menyimpan data Excel pada baris ke-" . ($rowIndex + 8) . ": " . $e->getMessage());
            }
        }
    }
}
