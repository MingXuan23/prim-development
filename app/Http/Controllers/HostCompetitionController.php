<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class HostCompetitionController extends Controller
{    
    public function hostCompetition(Request $req)
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

            if ($req->status === 'Draft')
            {
                $query->where(function ($e) {
                    $e->whereNull('competitionStart')->orWhere('status', 'Draft');
                });
            }
            else if ($req->status === 'Upcoming')
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

        $competitions = $query->where('userId', Auth::id())->with(['user', 'participant_registration'])->withCount('participant_registration')->latest('id')->get()
            ->map(function ($competition)
            {
                $competition->totalFees = $competition->participant_registration->sum(function ($q) use ($competition)
                {
                    if ($q->pivot->statusPayment !== 'Paid')
                    {
                        return 0;
                    }

                    $cleanIc = str_replace('-', '', $q->icNo ?? '');
                    $isMalaysia = ctype_digit($cleanIc) && strlen($cleanIc) === 12;

                    return ($isMalaysia) ? $competition->nationalFees : $competition->internationalFees;
                });
                return $competition;
            });

        return view('competition.host', compact('competitions'));
    }

    public function addCompetition()
    {
        return view('competition.addcompetition');
    }

    public function validator(Request $req, $id = null)
    {
        $isEdit = $req->filled('id') || !empty($id);    
        $isPublish = $req->input('submit') === 'publish' || $req->input('action') === 'publish';
        $isTeam = $req->input('participateType') === 'Team';

        $validate = [
            'competitionTitle' => 'required|string|max:255',
            'category' => $isPublish ?  'required|string|max:255' : 'nullable|string|max:255',
            'venue' => $isPublish ? 'required|string|max:255' : 'nullable|string|max:255',
            'registerOpen' => $isPublish ? 'required|date|after_or_equal:today' : 'nullable|date|after_or_equal:today',
            'registerClose' => $isPublish ? 'required|date|after_or_equal:registerOpen' : 'nullable|date|after_or_equal:registerOpen',
            'competitionStart' => $isPublish ? 'required|date|after_or_equal:registerClose' : 'nullable|date|after_or_equal:registerClose',
            'competitionEnd' => $isPublish ? 'required|date|after_or_equal:competitionStart' : 'nullable|date|after_or_equal:competitionStart',
            'minimumAge' => 'nullable|integer|min:1',
            'maximumAge' => ['nullable', 'integer', 'min:1', $req->filled('minimumAge') ? 'gte:minimumAge' : ''],
            'nationalFees' =>'nullable|numeric|min:0',
            'internationalFees' => 'nullable|numeric|min:0',
            'participateType' => $isPublish ? 'required|in:Individual,Team' : 'nullable|in:Individual,Team',
            'minimumParticipate' => $isTeam && $isPublish ? 'required|integer|min:2' : 'nullable|integer|min:2',
            'maximumParticipate' => $isTeam && $isPublish ? 'required|integer|gte:minimumParticipate' : 'nullable|integer|gte:minimumParticipate',
            'description' => $isPublish ? 'required' : 'nullable',
            'imagePoster' => ($isPublish && !$isEdit) ? 'required|image|mimes:jpeg,png,jpg|max:2048' : 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ];
        
        $message = [
            'competitionTitle.required' => 'Sila masukkan nama pertandingan.',
            'category.required' => 'Sila pilih kategori pertandingan.',
            'venue.required' => 'Sila masukkan tempat bertanding.',
            'registerOpen.required' => 'Sila pilih tarikh buka pendaftaran.',
            'registerClose.required' => 'Sila pilih tarikh tutup pendaftaran.',
            'competitionStart.required' => 'Sila pilih tarikh mula pertandingan.',
            'competitionEnd.required' => 'Sila pilih tarikh akhir pertandingan.',
            // 'minimumAge.required' => 'Sila masukkan minimum umur.',
            // 'maximumAge.required' => 'Sila masukkan maksimum umur.',
            // 'nationalFees.required' => 'Sila masukkan yuran untuk warganegara.',
            // 'internationalFees.required' => 'Sila masukkan yuran untuk bukan warganegara.',
            'participateType.required' => 'Sila pilih jenis penyertaan (Individu atau Berkumpulan).',
            'minimumParticipate.required' => 'Sila masukkan minimum penyertaan.',
            'maximumParticipate.required' => 'Sila masukkan maksimum penyertaan.',
            'description.required' => 'Sila tulis penerangan mengenai pertandingan.',
            'imagePoster.required' => "Sila muat naik poster pertandingan.",

            'registerOpen.after_or_equal' => 'Tarikh buka pendaftaran tidak boleh sebelum tarikh hari ini.',
            'registerClose.after_or_equal' => 'Tarikh tutup pendaftaran tidak boleh sebelum tarikh buka pendaftaran.',
            'competitionStart.after_or_equal' => 'Tarikh mula pertandingan tidak boleh sebelum tarikh tutup pendaftaran.',
            'competitionEnd.after_or_equal' => 'Tarikh akhir pertandingan tidak boleh sebelum tarikh mula pertandingan.',
            'maximumAge.gte' => 'Umur maksimum tidak boleh kurang daripada umur minimum.',
            'maximumParticipate.gte' => 'Penyertaan maksimum tidak boleh kurang daripada penyertaan minimum.',
            'imagePoster.image' => 'Fail poster mestilah dalam bentuk gambar.',
            'imagePoster.mimes' => 'Format gambar yang dibenarkan ialah JPEG, PNG, JPG, atau GIF.',
            'imagePoster.max' => 'Saiz gambar poster tidak boleh melebihi 2MB.',
        ];
        return[$validate, $message];
    }

    public function storeCompetition(Request $req)
    {   
        $isPublish = $req->input('submit') === 'publish' || $req->input('action') === 'publish';
        [$validate, $message] = $this->validator($req);
        
        $validator = Validator::make($req->all(), $validate, $message);

        if ($validator->fails())
        {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
    
        $data = $validator->validated();

        if ($req->hasFile('imagePoster'))
        {
            $file = $req->file('imagePoster');
            $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();

            $file->move(public_path('competition-image'), $fileName);
            $data['imagePoster'] = $fileName;
        }

        if (Carbon::parse($req->registerOpen)->isToday())
        {
            $data['registerOpen'] = Carbon::parse($req->registerOpen)->now();
        }
        else
        {
            $data['registerOpen'] = Carbon::parse($req->registerOpen)->startOfDay();
        }

        if ($req->input('registerClose'))
        {
            $data['registerClose'] = Carbon::parse($req->registerClose)->endOfDay();
        }

        $data['status'] = $isPublish ? 'Published' : 'Draft';
        $data['userId'] = Auth::id();
        Competition::create($data);

        $message = $data['status'] == 'Published' ? 'Pertandingan berjaya disimpan!' : 'Draf berjaya disimpan!';
        return response()->json(['status' => true, 'message' => $message]);
    }

    public function editStoreCompetition(Request $req, int $id)
    {
        $isPublish = $req->input('submit') === 'publish' || $req->input('action') === 'publish';

        $competition = Competition::with('user')->findOrFail($id);
        [$validate, $message] = $this->validator($req, $id);
        $validator = Validator::make($req->all(), $validate, $message);

        if ($validator->fails())
        {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
        
        $data = $validator->validated();

        if ($req->hasFile('imagePoster'))
        {
            if ($competition->imagePoster && file_exists(public_path('competition-image/' . $competition->imagePoster)))
            {
                unlink(public_path('competition-image/' . $competition->imagePoster));
            }
            $file = $req->file('imagePoster');
            $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();

            $file->move(public_path('competition-image'), $fileName);
            $data['imagePoster'] = $fileName;
        }
        else
        {
            unset($data['imagePoster']);
        }

        $data['status'] = $isPublish ? 'Published' : 'Draft';
        $competition->update($data);

        $message = $isPublish ? 'Pertandingan berjaya disimpan!' : 'Draf berjaya disimpan!';
        return response()->json(['status' => true, 'message' => $message, 'redirect' => route('competition.viewcompetition', $competition->id)]);
    }

    public function viewCompetition(int $id)
    {
        $competition = Competition::with('user', 'participant_registration')->findOrFail($id);
        $pendingCount = $competition->participant_registration()
            ->wherePivot('statusPayment', 'Pending')
            ->count();
        $paidCount = $competition->participant_registration()
            ->wherePivot('statusPayment', 'Paid')
            ->count();
        return view('competition.viewcompetition', compact('competition', 'pendingCount', 'paidCount'));
    }

    public function editCompetition(int $id)
    {
        $competition = Competition::with('user', 'participant_registration')->findOrFail($id);
        return view('competition.editcompetition', compact('competition'));
    }

    public function deleteCompetition(int $id)
    {
        $delCompetition = Competition::findOrFail($id);

        if ($delCompetition->imagePoster)
        {
            $filePath = public_path('competition-image/' . $delCompetition->imagePoster);
            File::delete($filePath);
        }

        $delCompetition->delete();
        return response()->json(['status' => 'success', 'message' => 'Pertandingan berjaya dipadam!', 'redirect' => route('competition.host')]);
    }
}
?>