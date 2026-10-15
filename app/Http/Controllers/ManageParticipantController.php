<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Participant;
use App\Exports\ParticipantExport;
use App\Models\Institution;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManageParticipantController extends Controller
{
    public function exportParticipant(Request $req)
    {
        return Excel::download(new ParticipantExport($req), 'Senarai_Peserta_Pertandingan.xlsx');
    }

    public function resultCompetition(int $id)
    {
        $competition = Competition::findOrFail($id);
        return view('competition.hostresult', compact('competition'));
    }

    private function winner(int $index):string
    {
        $suffix = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

        if (($index % 100) >= 11 && ($index % 100) <= 13)
        {
            $text = $index . 'th';
        }
        else
        {
            $text = $index . ($suffix[$index % 10] ?? 'th');
        }
        
        return $text;
    }

    public function updateResult(Request $req, int $id)
    {
        $competition = Competition::findOrFail($id);
        $isPublish = $req->input('submit') === 'publish' || $req->input('action') === 'publish';
        $status = $isPublish ? 'Published' : 'Draft';

        $emas = (array) $req->input('winners.Emas', $req->input('Emas', []));
        $perak = (array) $req->input('winners.Perak', $req->input('Perak', []));
        $gangsa = (array) $req->input('winners.Gangsa', $req->input('Gangsa', []));

        foreach($competition->participant_registration as $register) {
            $teamId = $register->participant->first()->teamId;
            $placement = 'Participation';

            if ($competition->winnerType === 'Medal')
            {
                if (in_array($teamId, $emas)) 
                {
                    $placement = 'Gold Medal';
                }
                else if (in_array($teamId, $perak))
                {
                    $placement = 'Silver Medal';
                }
                else if (in_array($teamId, $gangsa))
                {
                    $placement = 'Bronze Medal';
                }
                else
                {
                    $winner = (array) $req->input('winners', $req->input('Tempat_', []));

                    foreach($winner as $key => $value)
                    {
                        if (str_starts_with($key, 'Tempat_') && !empty($value))
                        {
                            $array = (array) $value;

                            if (in_array($teamId, $array))
                            {
                                $index = str_replace('Tempat_', '', $key);
                                $number = $this->winner((int)$index);
                                $placement = $number . ' Place Medal';
                                break;
                            }
                        }
                    }   
                }
            }
            else
            {
                $winner = (array) $req->input('winners', []);

                foreach($winner as $index => $value)
                {
                    $array = (array) $value;

                    if (in_array($teamId, $array))
                    {
                        $number = $this->winner((int)$index);
                        $placement = $number . ' Place';
                        break;
                    }
                }
            }

            DB::table('competition_registration')
            ->where('competitionId', $competition->id)
            ->where('teamId', $teamId)
            ->update(['winnerPlacement' => $placement]);        
        }

        DB::table('competitions')
        ->where('id', $competition->id)
        ->update(['statusResult' => $status]);

        $message = $status === 'Published' ? 'Kemaskini keputusan berjaya disimpan.' : 'Draf keputusan berjaya disimpan.';
        return response()->json(['status' => 'success', 'message' => $message]);
    }

    public function manageRegister(Request $req)
    {
        $userId = Auth::user()->organization->first()->id ?? null;
        $competitions = Competition::where('organizationId', $userId)->where('statusCompetition', 'Published')->pluck('competitionTitle')->unique();

        $filter = function ($q) use($req, $userId) {
            $q->whereHas('team.team_registration', function($qcomp) use ($userId) {
                $qcomp->where('organizationId', $userId);
            });

            if ($req->filled('title') && $req->title !== 'all')
            {
                $title = $req->title;
                $q->whereHas('team.team_registration', function ($qComp) use ($title) {
                    $qComp->where('competitionTitle', $title);
                });
            }

            if ($req->filled('search'))
            {
                $cari = trim($req->search);

                $q->where(function ($q) use($cari) 
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
                $q->whereHas('team.team_registration', function($q) use ($status) 
                {
                    if ($status === 'pending')
                    {
                        $q->where('statusPayment', 'Pending');
                    }
                    else if ($status === 'paid')
                    {
                        $q->where('statusPayment', 'Paid');
                    }
                });
            }
        };

        $query = Participant::with(['user', 'team.team_registration']);
        $filter($query);

        $participate = $query->get();
        $participants = $participate->groupBy('teamId')->map(function ($q) {
            $item = $q->first();

            $item->formatted_members = $q->map (function ($p) {
                $isLeader = ($p->isLeader === 'Leader');
                return $p->name . ($isLeader ? ' (K)' : '');
            })->implode(', ');
            return $item;

        })->sortByDesc(function ($group) {
            return $group->team->team_registration->first()->pivot->statusPayment ?? 'Pending';
        })->values();

        $totalIndividu = $participants->filter(function($p) {
            return optional(optional(optional($p->team)->team_registration)->first())->participateType === 'Individual';
        })->count();

        $totalTeam = $participants->filter(function($p) {
            return optional(optional(optional($p->team)->team_registration)->first())->participateType === 'Team';
        })->count();

        $totalRegisterByInstitution = Institution::withCount([
            'participant' => function($q) use($filter) {
                $filter($q);
            }
        ])->having('participant_count', '>', 0)->orderBy('participant_count', 'desc')->get();

        return view('competition.hostrecord', compact('participate', 'participants', 'competitions', 'totalIndividu', 'totalTeam', 'totalRegisterByInstitution'));
    }
}
