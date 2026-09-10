<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Institution;
use Illuminate\Http\Request;
use App\Exports\ParticipantRegisterExport;
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

        if ($req->filled('search'))
        {
            $query->where('competitionTitle', 'like', '%' . $req->search . '%');
        }

        if ($req->filled('category'))
        {
            $query->where('category', $req->category);
        }

        if ($req->filled('status'))
        {
            $today = now()->toDateString();

            if ($req->status === 'Upcoming')
            {
                $query->where('competitionStart', '>', $today);
            }
            else if ($req->status === 'Ongoing')
            {
                $query->where('competitionStart', '<=', $today)->where('competitionEnd', '>=', $today);
            }
            else if ($req->status === 'Completed')
            {
                $query->where('competitionEnd', '<', $today);
            }
        }
        $query->where('status', 'Published');

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
        $competition = Competition::with('user')->findOrFail($id);
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
}
