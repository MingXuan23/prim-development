<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Participant;
use App\Exports\ParticipantExport;
use App\Models\Institution;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageParticipantController extends Controller
{
    public function exportParticipant(Request $req)
    {
        return Excel::download(new ParticipantExport($req), 'Senarai_Peserta_Pertandingan.xlsx');
    }

    public function manageRegister(Request $req)
    {
        $userId = Auth::id();
        $competitions = Competition::where('userId', $userId)->where('status', 'Published')->pluck('competitionTitle')->unique();

        $filter = function ($q) use($req, $userId) {
            $q->whereHas('team.team_registration', function($qcomp) use ($userId) {
                $qcomp->where('userId', $userId);
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
