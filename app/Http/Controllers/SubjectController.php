<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SubjectExport;
use App\Exports\PerananExport;
use App\Imports\SubjectImport;
use App\Imports\WardenImport;
use App\Imports\GuardImport;
use App\Models\Organization;
use App\Models\OrganizationRole;
use App\User;
use Illuminate\Validation\Rule;
use App\Models\TypeOrganization;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class SubjectController extends Controller
{

    private $subject;
    public function __construct(Subject $subject)
    {
        $this->subject = $subject;
    }

    public function index()
    {
        $organization = $this->getOrganizationByUserId();
        return view('subject.index', compact('organization'));
    }


    public function getSubjectDatatable(Request $request){
        //dd("hello");
        if (request()->ajax()) {
            $oid = $request->oid;
            if ($oid != '') {

                $data = DB::table('subject as s')
                    ->select('s.id','s.name', 's.code')
                    //->where('organizations.id', $oid)
                    ->orderBy('s.name');
            }
            
            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('subject.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Delete</button></div>';
                return $btn;
            });

            $table->rawColumns([ 'action']);
            //return response()->json(['data'=>$data]);
           
            return $table->make(true);
        }
    }
    
    // public function subjectExport(Request $request)
    // {
    //     return Excel::download(new SubjectExport($request->organ), 'subject.xlsx');
    // }

    public function subjectImport(Request $request)
    {
        $file = $request->file('file');
        $namaFile = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        if (!in_array($etx, $formats)) {
            return redirect('/subject')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }

        // Import data from Excel file
        Excel::import(new SubjectImport($request->organ), public_path('/uploads/excel/' . $namaFile));

        return redirect('/subject')->with('success', 'Subjects have been added successfully');
    }

    public function create()
    {
        $organization = $this->getOrganizationByUserId();

        return view('subject.add', compact('organization'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'subject_name'  =>  'required',
            'kod'           =>  'required',
            'organization'  => 'required',
        ]);

        $subject_name=trim(strtoupper($request->get('subject_name')));
        $code=trim(strtoupper($request->get('kod')));
        $isExits = DB::table('subject as s')
            ->where('s.organization_id',$request->organization)
            ->where(function ($query) use ($subject_name,$code) {
                $query->where('s.name', '=', $subject_name)
                    ->orWhere('s.code', '=', $code);
            })
            ->exists();

        // dd($ifExits);

        if (!$isExits)
        {
            $newSubject = DB::table('subject')->insert([
                'name'           =>  $subject_name,
                'code'          =>  $code,
                'organization_id' => $request->get('organization'),
            ]);
            
        } else
        {
            return redirect()->back()->with('error', 'This subject or code is already exist');
        }

        return redirect('/subject')->with('success', 'New subject has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
        // $teacher = $this->teacher->getOrganizationByUserId($id);

        $subject = DB::table('subject as s')
            ->join('organizations as o', 'o.id', '=', 's.organization_id')
            ->where('s.id','=', $id)
            ->select('s.*','o.nama as organization_name')
            ->first();

        return view('subject.update', compact('subject'));
    }

    public function update(Request $request, $id)
    {
        //
        $sid = Subject::find($id);
        // dd($id);

        $this->validate($request, [
            'subject_name'    =>  'required',
            'kod'             =>  'required',
        ]);

        $subject_name=trim(strtoupper($request->get('subject_name')));
        $code=trim(strtoupper($request->get('kod')));
        $existCount = DB::table('subject as s')
        ->where('s.organization_id',$request->organization_id)
        ->where(function ($query) use ($subject_name,$code) {
            $query->where('s.name', '=', $subject_name)
                ->orWhere('s.code', '=', $code);
        })
        ->count();

        if ($existCount<2)
        {
            $subjectupdate    = DB::table('subject')
                ->where('id', $id)
                ->update(
                    [
                        'name'      => $subject_name,
                        'code'      => $code,
                    ]
                );
        } else
        {
            return redirect()->back()->with('error', 'This subject or code is already exist');
        }
       

        return redirect('/subject')->with('success', 'The subject data has been updated!');
    }

    public function destroy($id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            Session::flash('error', 'Subject not found');
        } else {
            $subject->delete();
            Session::flash('success', 'Subject deleted successfully');
        }

    }

    public function getOrganizationByUserId()
    {

        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {

            return Organization::all();
        } else {
            // user role pentadbir 
            //micole try
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->whereIn('role_id', [4, 5, 13, 14, 20]);
            })->get();
        }
    }
}
