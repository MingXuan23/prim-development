<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Participant;
use App\Imports\ParticipantImport;
use App\Models\Institution;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Team;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegistrationCompetitionController extends Controller
{
    public function validator(Request $req)
    {
        $isTeam = $req->input('participant_type') === 'team';

        if ($isTeam)
        {
            $competition = Competition::findOrFail($req->input('competitionId'));
            $min = $competition->minParticipateGroup;
            $max = $competition->maxParticipateGroup;

            $validate = [
                'groupName' => 'required|string|max:255',
                'members' => "required|array|min:{$min}|max:{$max}",
                'members.*.name' => 'required|string|max:255',
                'members.*.icNo' => 'required|string|max:25',
                'members.*.birthOfDate' => 'required|date|before:today',
                'members.*.gender' => 'required|in:Lelaki,Perempuan',
                'members.*.email' => 'required|email',
                'members.*.noTel' => 'required|string|max:20',
                'members.*.shirtSize' => 'nullable|string|max:10',
            ];

            $message = [
                'members.*.name.required' => 'Sila masukkan nama penuh.',
                'groupName.required' => 'Sila masukkan nama kumpulan.',
                'members.required' => 'Sila masukkan sekurang-kurangnya seorang ahli kumpulan.',
                'members.min' => "Sila masukkan sekurang-kurangnya {$min} ahli kumpulan.",
                'members.max' => "Sila masukkan tidak melebihi {$max} ahli kumpulan.",
                'members.*.icNo.required' => 'Sila masukkan no ic / no pasport.',
                'members.*.birthOfDate.required' => 'Sila masukkan tarikh lahir anda.',
                'members.*.birthOfDate.before' => 'Sila masukkan tarikh lahir sebelum hari ini.',
                'members.*.gender.required' => 'Sila pilih jantina anda (Lelaki atau Perempuan).',
                'members.*.email.required' => 'Sila masukkan alamat email.',
                'members.*.email.email' => 'Sila masukkan format email yang sah.',
                'members.*.noTel.required' => 'Sila masukkan nombor telefon.',
            ];
        }
        else
        {
            $validate = [
                'name' => 'required|string|max:255',
                'icNo' => 'required|string|max:25',
                'birthOfDate' => 'required|date|before:today',
                'gender' => 'required|in:Lelaki,Perempuan',
                'email' => 'required|email',
                'noTel' => 'required|string|max:20',
                'shirtSize' => 'nullable|string|max:10',
            ];

            $message = [
                'name.required' => 'Sila masukkan nama penuh.',
                'icNo.required' => 'Sila masukkan no ic / no pasport.',
                'birthOfDate.required' => 'Sila masukkan tarikh lahir anda.',
                'birthOfDate.before' => 'Sila masukkan tarikh lahir sebelum hari ini.',
                'gender.required' => 'Sila pilih jantina anda (Lelaki atau Perempuan).',
                'email.required' => 'Sila masukkan alamat email.',
                'email.email' => 'Sila masukkan format email yang sah.',
                'noTel.required' => 'Sila masukkan nombor telefon.',
            ];
        }
        
        $validate += [
            'competitionId' => 'required|exists:competitions,id',
            'instituteName' => 'required|string',
            'add-institute-name' => ['required_if:instituteName,Lain-Lain', 'nullable', 'string', 'max:255', 
                                    function ($attribute, $value, $fail) use ($req) {
                                        if ($req->input('instituteName') === 'Lain-Lain' && !empty($value)) 
                                        {
                                            $cleanInput = strtolower(trim($value));
                                            $type = $req->input('instituteType');

                                            $IsExist = Institution::where(function ($q) use ($cleanInput) {
                                                $q->whereRaw('LOWER(instituteName) = ?', [$cleanInput])
                                                        ->orWhereRaw('LOWER(instituteName) LIKE ?', ['%(' . $cleanInput . ')%'])
                                                        ->orWhereRaw('LOWER(instituteName) LIKE ?', ['%' . $cleanInput . '%']);
                                            })
                                            ->when($type, function ($query) use ($type) {
                                                return $query->where('instituteType', $type);
                                            })
                                            ->exists();

                                            if ($IsExist) 
                                            {
                                                $fail('Nama institusi ini sudah wujud dalam sistem. Sila pilih dari senarai sedia ada.');
                                            }
                                        }
                                    }],
            'instituteType' => 'required|string',
            'currentGrade' => 'required|string',    
            'supervisorName' => 'nullable|string|max:255',
            'supervisorEmail' => 'nullable|email',
            'supervisorNoTel' => 'nullable|string',
        ];

        $message += [
            'instituteType.required' => 'Sila pilih jenis institusi pembelajaran.',
            'instituteName.required' => 'Sila pilih nama institusi pembelajaran.',
            'add-institute-name.required_if' => 'Sila masukkan nama institusi.',
            'add-institute-name.unique' => 'Nama institusi ini sudah wujud dalam sistem. Sila pilih dari senarai sedia ada.',
            'currentGrade.required' => 'Sila pilih pengajian semasa.',
            // 'supervisorName.required' => 'Sila masukkan nama penyelia.',
            // 'supervisorEmail.required' => 'Sila masukkan alamat email penyelia.',
            'supervisorEmail.email' => 'Sila masukkan format email penyelia yang sah.',
            // 'supervisorNoTel.required' => 'Sila masukkan nombor telefon penyelia.',
        ];

        return[$validate, $message];
    }

    public function storeRegister(Request $req)
    {
        if ($req->hasFile('file') || $req->input('register_type') === 'bulk')
        {
            $validate = $req->validate ([
                'file' => 'required|file|max:5120|mimes:xlsx,xls',
            ], [
                'file.required' => 'Sila pilih fail Excel untuk dimuat naik.',
                'file.max' => 'Saiz fail tidak boleh melebihi 5MB.',
                'file.mimes' => 'Format fail mestilah .xlsx atau .xls.',
            ]);

            try {
                Excel::import(new ParticipantImport($req->competitionId), $req->file('file'));

                $success = ParticipantImport::$successCount;
                $total = ParticipantImport::$totalCount;
                $alreadyRegister = ParticipantImport::$alreadyRegister;
                $notRegisterFail = ParticipantImport::$notRegisterFail;

                if ($total === 0)
                {
                    return response()->json(['status' => 'error', 
                                             'title' => 'Tidak Berjaya', 
                                             'message' => 'Tiada data pendaftaran yang dijumpai dalam fail Excel.']);
                }

                $detail = [];
                if (!empty($alreadyRegister))
                {
                    $name = implode(', ', $alreadyRegister);
                    $detail[] = "($name) telah mendaftar untuk pertandingan ini!";
                }

                if (!empty($notRegisterFail))
                {
                    $detail[] = "Sila semak semula fail Excel bagi baris: " . implode(', ', $notRegisterFail) . '.';
                }

                $detailMessage = !empty($detail) ? ' ' . implode(' ', $detail) : '';

                if ($success >= 1)
                {
                    $isPartial = $success < $total;
                    return response()->json(['status' => $isPartial ? 'warning' : 'success', 
                                             'title' => $isPartial ? 'Perhatian' : 'Berjaya',
                                             'message' => "{$success}/{$total} pendaftaran berjaya disimpan!" . $detailMessage,
                                             'redirect' => route('competition.userrecord')]);
                }

                return response()->json(['status' => 'error', 'title' => 'Tidak Berjaya', 'message' => "0/{$total} pendaftaran disimpan." . $detailMessage]);
            } 
            catch (\Exception $e)
            {
                return response()->json(['status' => 'error', 'title' => 'Ralat', 'message' => 'Ralat semasa membaca fail: ' . $e->getMessage()]);
            }
        }
        else
        {
            $isTeam = $req->input('participant_type') === 'team';
            
            [$validate, $message] = $this->validator($req);
            $validator = Validator::make($req->all(), $validate, $message);

            if ($validator->fails())
            {
                return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $competition = Competition::findOrFail($req->input('competitionId'));
            DB::beginTransaction();

            try {
                if ($isTeam)
                {
                    $list = $data['members'] ?? [];
                }
                else
                {
                    $list = [
                        ['name' => $data['name'], 
                         'icNo' => $data['icNo'],
                         'birthOfDate' => $data['birthOfDate'],
                         'gender' => $data['gender'], 
                         'email' => $data['email'], 
                         'noTel' => $data['noTel'],
                         'shirtSize' => $data['shirtSize'],]
                    ];
                }

                foreach($list as $p)
                {
                    if (!is_array($p)) continue;

                    $checkRegister = Participant::where(function ($q) use($p) {
                        $q->where('email', $p['email'])
                        ->orWhere('icNo', $p['icNo']);
                    })->whereHas('team.team_registration', function ($q) use($data) {
                        $q->where('competitionId', $data['competitionId']);
                    })->exists();

                    if ($checkRegister)
                    {
                        DB::rollBack();
                        return response()->json(['status' => false, 'message' => "({$p['name']}) telah mendaftar untuk pertandingan ini!"]);
                    }

                    if (!empty($competition->minimumAge) || !empty($competition->maximumAge))
                    {
                        $dob = $p['birthOfDate'] ?? null;

                        if (!empty($dob))
                        {
                            $age = Carbon::parse($dob)->age;

                            if (!empty($competition->maximumAge) && $age > $competition->maximumAge)
                            {
                                $errorMessage = $isTeam > 1 ? "{$p['name']} telah melepasi syarat umur maksimum." : 'Umur anda telah melepasi syarat umur maksimum.';

                                DB::rollBack();
                                return response()->json(['status' => false, 'message' => $errorMessage], 422);
                            }

                            if (!empty($competition->minimumAge) && $age < $competition->minimumAge)
                            {
                                $errorMessage = $isTeam ? "{$p['name']} tidak melepasi syarat umur minimum." : 'Umur anda tidak melepasi syarat umur minimum.';

                                DB::rollBack();
                                return response()->json(['status' => false, 'message' => $errorMessage], 422);
                            }
                        }
                    }
                }

                $team = Team::create(['groupName' => $isTeam ? $data['groupName'] : '---Individu---']);
                $selectId = null;

                if ($req->input('instituteName') === 'Lain-Lain')
                {
                    $institution = Institution::create([
                        'instituteType' => $data['instituteType'],
                        'instituteName' => trim($data['add-institute-name']),
                        'instituteLogo' => ''
                    ]);

                    $selectId = $institution->id;
                }
                else
                {
                    $selectId = $req->input('instituteName');
                }
                
                if ($isTeam)
                {
                    foreach($list as $index => $p)
                    {
                        if (!is_array($p)) continue;

                        Participant::create([
                            'name' => $p['name'], 
                            'icNo' => $p['icNo'],
                            'birthOfDate' => $p['birthOfDate'],
                            'gender' => $p['gender'], 
                            'email' => $p['email'], 
                            'noTel' => $p['noTel'],
                            'shirtSize' => $p['shirtSize'],
                            'currentGrade' => $data['currentGrade'],
                            'supervisorName' => $data['supervisorName'],
                            'supervisorEmail' => $data['supervisorEmail'],
                            'supervisorNoTel' => $data['supervisorNoTel'],
                            'isLeader' => ($isTeam && $index === 0) ? 'Leader' : 'Member',
                            'teamId' => $team->id,
                            'userId' => Auth::id(),
                            'institutionId' => $selectId
                        ]);
                    }
                }
                else
                {
                    Participant::create([
                        'name' => $data['name'], 
                        'icNo' => $data['icNo'], 
                        'birthOfDate' => $data['birthOfDate'],
                        'gender' => $data['gender'], 
                        'email' => $data['email'], 
                        'noTel' => $data['noTel'],
                        'shirtSize' => $data['shirtSize'],
                        'currentGrade' => $data['currentGrade'],
                        'supervisorName' => $data['supervisorName'],
                        'supervisorEmail' => $data['supervisorEmail'],
                        'supervisorNoTel' => $data['supervisorNoTel'],
                        'isLeader' => null,
                        'teamId' => $team->id,
                        'userId' => Auth::id(),
                        'institutionId' => $selectId
                    ]);
                }
                    $competition = Competition::findOrFail($req->input('competitionId'));
                    
                    $IcCheck = $isTeam ? ($list[0]['icNo'] ?? '') : $data['icNo'];
                    $cleanIc = str_replace('-', '', $IcCheck ?? '');
                    $isMalaysia = ctype_digit($cleanIc) && strlen($cleanIc) === 12;
                    
                    $team->team_registration()->attach($data['competitionId'], [
                    'statusPayment' => (($isMalaysia ? $competition->nationalFees : $competition->internationalFees) == 0) ? 'Free' : 'Pending',
                    'registeredDate' => Carbon::now(),
                ]);
            
                DB::commit();
                return response()->json(['status' => true, 'message' => $isTeam ? 'Pendaftaran berkumpulan berjaya disimpan!' : 'Pendaftaran berjaya disimpan!']);
            }
            catch(\Exception $e)
            {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Ralat sistem: ' . $e->getMessage()], 500);
            }
        }
    }

    public function editStoreRegister(Request $req, int $id)
    {
        $participant = Participant::with('team')->where('id', $id)->where('userId', Auth::id())->firstOrFail();

        $isTeam = $req->input('participant_type') === 'team';
        [$validate, $message] = $this->validator($req);

        $validator = Validator::make($req->all(), $validate, $message);

        if ($validator->fails())
        {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $competition = Competition::findOrFail($req->input('competitionId'));

        if ($isTeam)
        {
            $list = $data['members'] ?? [];
        }
        else
        {
            $list = [
                ['id' => $participant->id, 
                 'name' => $data['name'], 
                 'icNo' => $data['icNo'], 
                 'birthOfDate' => $data['birthOfDate'], 
                 'gender' => $data['gender'], 
                 'email' => $data['email'], 
                 'noTel' => $data['noTel'],
                 'shirtSize' => $data['shirtSize'],]
                ];
        }

        foreach($list as $p)
        {
            if (!is_array($p)) continue;

            $checkRegister = Participant::where(function ($q) use($p) {
                $q->where('email', $p['email'])
                    ->orWhere('icNo', $p['icNo']);
            })->whereHas('team.team_registration', function ($q) use($data) {
                $q->where('competitionId', $data['competitionId']);
            })->where('teamId', '!=', $participant->teamId)->exists();

            if ($checkRegister)
            {
                return response()->json(['status' => false, 'message' => "({$p['name']}) telah mendaftar untuk pertandingan ini!"], 422);
            }

            if (!empty($competition->minimumAge) || !empty($competition->maximumAge))
            {
                $dob = $p['birthOfDate'] ?? null;

                if (!empty($dob))
                {
                    $age = Carbon::parse($dob)->age;

                    if (!empty($competition->maximumAge) && $age > $competition->maximumAge)
                    {
                        $errorMessage = $isTeam > 1 ? "{$p['name']} telah melepasi syarat umur maksimum." : 'Umur anda telah melepasi syarat umur maksimum.';

                        DB::rollBack();
                        return response()->json(['status' => false, 'message' => $errorMessage], 422);
                    }

                    if (!empty($competition->minimumAge) && $age < $competition->minimumAge)
                    {
                        $errorMessage = $isTeam ? "{$p['name']} tidak melepasi syarat umur minimum." : 'Umur anda tidak melepasi syarat umur minimum.';

                        DB::rollBack();
                        return response()->json(['status' => false, 'message' => $errorMessage], 422);
                    }
                }
            }
        }

        DB::beginTransaction();

        try {
            if ($participant->team)
            {
                $participant->team->update(['groupName' => $isTeam ? $data['groupName'] : '---Individu---']);
            }

            $selectId = null;

            if ($req->input('instituteName') === 'Lain-Lain')
            {
                $institution = Institution::create([
                    'instituteType' => $data['instituteType'],
                    'instituteName' => trim($data['add-institute-name']),
                    'instituteLogo' => ''
                ]);

                $selectId = $institution->id;
            }
            else
            {
                $selectId = $req->input('instituteName');
            }

            $participantId = [];

            if ($isTeam)
            {
                foreach($list as $index => $p)
                {
                    if (!is_array($p)) continue;

                    $data = [
                        'name' => $p['name'], 
                        'icNo' => $p['icNo'],
                        'birthOfDate' => $p['birthOfDate'], 
                        'gender' => $p['gender'], 
                        'email' => $p['email'], 
                        'noTel' => $p['noTel'],
                        'shirtSize' => $p['shirtSize'], 
                        'currentGrade' => $data['currentGrade'],
                        'supervisorName' => $data['supervisorName'],
                        'supervisorEmail' => $data['supervisorEmail'],
                        'supervisorNoTel' => $data['supervisorNoTel'],
                        'isLeader' => ($isTeam && $index === 0) ? 'Leader' : 'Member',
                        'teamId' => $participant->teamId,
                        'userId' => Auth::id(),
                        'institutionId' => $selectId
                    ];

                    if (!empty($p['id']))
                    {
                        $exist = Participant::where('id', $p['id'])->where('teamId', $participant->teamId)->first();

                        if ($exist)
                        {
                            $exist->update($data);
                            $participantId[] = $exist->id;
                        }
                    }
                    else
                    {
                        $new = Participant::create($data);
                        $participantId[] = $new->id;
                    }
                }
                if ($isTeam)
                {
                    Participant::where('teamId', $participant->teamId)->whereNotIn('id', $participantId)->delete();
                }
            }
            else
            {
                $participant->update([
                    'name' => $data['name'], 
                    'icNo' => $data['icNo'], 
                    'birthOfDate' => $data['birthOfDate'], 
                    'gender' => $data['gender'], 
                    'email' => $data['email'], 
                    'noTel' => $data['noTel'],
                    'shirtSize' => $data['shirtSize'], 
                    'currentGrade' => $data['currentGrade'],
                    'supervisorName' => $data['supervisorName'],
                    'supervisorEmail' => $data['supervisorEmail'],
                    'supervisorNoTel' => $data['supervisorNoTel'],
                    'isLeader' => null,
                    'userId' => Auth::id(),
                    'institutionId' => $selectId
                ]);
            }
    
            DB::commit();
            return response()->json(['status' => true, 'message' => $isTeam ? 'Kemaskini pendaftaran berkumpulan berjaya disimpan!' : 'Kemaskini pendaftaran berjaya disimpan!']);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Ralat sistem: ' . $e->getMessage()], 500);
        }
    }

    public function recordRegister(Request $req)
    {
        $query = Participant::with('user', 'team.team_registration.organization')->where('userId', Auth::id());

        if ($req->filled('search'))
        {
            $cari = trim($req->search);

            $query->where(function ($q) use($cari) 
            {
                $q->where('name', 'like', "%{$cari}%")

                ->orWhereHas('team.team_registration', function($qCompetition) use ($cari) 
                {
                    $qCompetition->where('competitionTitle', 'like', "%{$cari}%");
                })
                ->orWhereHas('team', function($qCompetition) use ($cari) 
                {
                    $qCompetition->where('groupName', 'like', "%{$cari}%");
                });
            });
        }

        if ($req->filled('status') && $req->status !== 'all')
        {
            $status = $req->status;
            $query->whereHas('team.team_registration', function($q) use ($status) 
            {
                if ($status === 'pending')
                {
                    $q->where('statusPayment', 'Pending');
                }
                else if ($status === 'paid')
                {
                    $q->where('statusPayment', 'Paid');
                }
                else if ($status === 'free')
                {
                    $q->where('statusPayment', 'Free');
                }
            });
        }

        $participate = $query->get();
        $participants = $participate->groupBy('teamId')->map(function ($q) {
            $q->formatted_members = $q->map (function ($p) {
                $isLeader = ($p->isLeader === 'Leader');
                return $p->name . ($isLeader ? ' (K)' : '');
            })->implode(', ');
            return $q;
            
        })->sortByDesc(function ($group) {
            return $group->first()->team->team_registration->first()->pivot->statusPayment ?? 'Pending';
        })->values();
        
        return view('competition.userrecord', compact('participants'));
    }

    public function GenerateSijil(Request $req)
    {
        $req->validate([
            'participantId' => 'required|exists:participants,id',
            'templateId' => 'required|in:Template1,Template2,Template3,Template4'
        ]);

        $participantId = $req->input('participantId');
        $templateId = $req->input('templateId');

        $participant = Participant::with('team.team_registration')->findOrFail($participantId);

        $orientation = ($req->input('templateId') > 'Template2') ? 'portrait' : 'landscape';

        $data = [
            'participant' => $participant,
            'templateId' => $templateId,
            'orientation' => $orientation,
        ];

        $pdf = Pdf::loadView('competition.pdftemplate', $data)->setPaper('a4', $orientation)->setOption([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'dpi' => 150,
        ]);

        $fileName = 'Sijil_' . str_replace(' ', '_', $participant->name) . '.pdf';
        
        return $pdf->stream($fileName);
    }
     
    public function viewRegister(int $id)
    {
        $orgId = Auth::user()->organization->pluck('id')->toArray() ?? null;

        $participant = Participant::with(['user', 'team.participant', 'team.team_registration'])->where('id', $id)
        ->where(function ($q) use ($orgId) {
            $q->where('userId', Auth::id())
            ->orWhereHas('team.team_registration', function ($p) use ($orgId) {
                $p->whereIn('organizationId', $orgId);
            });
        })->firstOrFail();
        
        $member = $participant->team->participant;
        $competition = $participant->team->team_registration->first();
        
        return view('competition.viewregister', compact('competition', 'participant', 'member'));
    }

    public function viewReceipt($id)
    {
        $registerId = [(int) $id];
        $loginUser = Auth::user();

        $get_transaction = DB::table('participants')
            ->join('teams', 'participants.teamId', '=', 'teams.id')
            ->join('competition_registration', 'teams.id', '=', 'competition_registration.teamId')
            ->join('transaction_competition', 'competition_registration.id', '=', 'transaction_competition.registerId')
            ->join('transactions', 'transaction_competition.transactionId', '=', 'transactions.id')
            ->whereIn('competition_registration.id', $registerId)
            ->where('transactions.status', 'Success')
            ->select('transactions.*')
            ->first();
            
        $registerIds = DB::table('transaction_competition')
            ->where('transactionId', $get_transaction->id)
            ->pluck('registerId')
            ->toArray();

        $get_participant_to_organization = DB::table('participants')
            ->join('teams', 'participants.teamId', '=', 'teams.id')
            ->join('competition_registration', 'teams.id', '=', 'competition_registration.teamId')
            ->join('competitions', 'competition_registration.competitionId', '=', 'competitions.id')
            ->join('organizations', 'competitions.organizationId', '=', 'organizations.id')
            ->whereIn('competition_registration.id', $registerIds)
            ->select('organizations.*', 'competitions.*', 'participants.name', 'participants.icNo', 'teams.groupName')
            ->get();

        return view('competition.receipt', compact('get_participant_to_organization', 'get_transaction', 'loginUser'));
    }

    public function editRegister(int $id)
    {
        $orgId = Auth::user()->organization->pluck('id')->toArray() ?? null;

        $participant = Participant::with(['user', 'team.team_registration'])->where('id', $id)
        ->where(function ($q) use ($orgId) {
            $q->where('userId', Auth::id())
            ->orWhereHas('team.team_registration', function ($p) use ($orgId) {
                $p->whereIn('organizationId', $orgId);
            });
        })->firstOrFail();

        $competition = $participant->team->team_registration->first();
        $institution = Institution::all();

        return view('competition.editregister', compact('competition', 'participant', 'institution'));
    }
    
    public function deleteRegister(int $id)
    {
        $orgId = Auth::user()->organization->pluck('id')->toArray() ?? null;

        $participant = Participant::with('team.team_registration')->where('id', $id)
        ->where(function ($q) use ($orgId) {
            $q->where('userId', Auth::id())
            ->orWhereHas('team.team_registration', function ($p) use ($orgId) {
                $p->whereIn('organizationId', $orgId);
            });
        })->firstOrFail();
        
        $team = $participant->team;
        
        if ($team)
        {
            $type = $team->team_registration->first()->participateType;
            $team->team_registration()->detach();

            if ($type === 'Individual')
            {
                $participant->delete();
            }
            else
            {   
                Participant::where('teamId', $team->id)->delete();
            }
            $team->delete();
        }
        else
        {
            $participant->delete();
        }
        
        return response()->json(['status' => 'success', 'message' => 'Pendaftaran berjaya dipadam!']);
    }
}
?>