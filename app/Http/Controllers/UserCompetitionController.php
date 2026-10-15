<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Institution;
use Illuminate\Http\Request;
use App\Exports\ParticipantRegisterExport;
use App\Models\CompetitionRegistration;
use App\Models\Participant;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class UserCompetitionController extends Controller
{
    public function competition()
    {
        return view('competition.index');
    }

    public function userCompetition(Request $req)
    {
        $query = Competition::query();

        $query->when($req->filled('search'), fn($q) =>
            $q->where('competitionTitle', 'like', '%' . $req->search . '%'));

        $query->when($req->filled('category'), fn($q) =>
            $q->where('category', $req->category));

        if ($req->filled('status'))
        {
            $today = now()->toDateString();

            $query->when($req->status === 'Upcoming', fn ($q) =>
                $q->where('competitionStart', '>', $today))
            ->when($req->status === 'Ongoing', fn ($q) =>
                $q->where('competitionStart', '<=', $today)->where('competitionEnd', '>=', $today))
            ->when($req->status === 'Completed', fn ($q) =>
                $q->where('competitionEnd', '<', $today));
        }
        $query->where('statusCompetition', 'Published');

        $competitions = $query->orderByRaw("CASE WHEN registerOpen <= NOW() AND registerClose >= NOW() THEN 1 
                                                 WHEN competitionStart <= NOW() AND competitionEnd >= NOW() THEN 2 
                                                 WHEN competitionEnd < NOW() OR registerClose < NOW() THEN 3
                                                 ELSE 4 
                                            END ASC")->orderBy('registerOpen', 'desc')->orderBy('competitionStart', 'asc')->get();
        return view('competition.user', compact('competitions'));
    }

    public function infoCompetition(Request $req, $id)
    {
        $type = $req->query('type');
        $competition = Competition::with(['organization.user', 'participant_registration'])->findOrFail($id);
        $institutions = Institution::all();

        if ($type === 'individual' || $type === 'team')
        {
            return view('competition.registerindividual', compact('competition', 'institutions'));
        }
        else if ($type === 'bulk')
        {
            return view('competition.registerbulk', compact('competition'));
        }

        return view('competition.info', compact('competition'));
    }

    public function downloadTemplateCompetition(int $id)
    {
        return Excel::download(new ParticipantRegisterExport(), 'bulk_register_competition.xlsx');
    }

    public function displayResult(Request $req)
    {
        $id = Auth::id();
        $search = $req->input('search');
        $category = $req->input('category');
        $activeTab = $req->input('tab', 'your');

        //Get Participant By User Login
        $participant = Participant::with(['user', 'team.team_registration'])->where('userId', $id)->get();

        //Display Competition By User Register
        $competitionById = $participant->pluck('team.team_registration')->flatten()->unique('id');
        
        if ($search) {
            $competitionById = $competitionById->filter(function($comp) use ($search) {
                return stripos($comp->competitionTitle, $search) !== false;
            });
        }
         
        if ($category) {
            $competitionById = $competitionById->filter(function($comp) use ($category) {
                return $comp->category === $category;
            });
        }
              
        //Display All Participant By Competition
        $competitionId = $competitionById->pluck('id');

        $participantByCompetition = Participant::with(['user', 'team.team_registration'])
            ->whereHas('team.team_registration', function($query) use ($competitionId) {
                $query->whereIn('competitions.id', $competitionId);
        })->get()->groupBy(function($item) {
            return $item->team->team_registration->first()->id;
        });

        //Display All Competition
        $query = Competition::where('statusCompetition', 'Published')->whereNotIn('id', $competitionId);

        if ($search)
        {
            $query->where('competitionTitle', 'like', '%' . $req->search . '%');
        }

        if ($category)
        {
            $query->where('category', $category);
        }   
        $allCompetition = $query->get();

        return view('competition.resultcompetition', compact('allCompetition', 'competitionById', 'participant', 'participantByCompetition', 'activeTab'));
    }
}