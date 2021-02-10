<?php

namespace App\Http\Controllers;

use App\Exports\ClassExport;
use App\Imports\ClassImport;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class ClassController extends Controller
{
   
    public function index()
    {
        //
        $userid = Auth::id();

        $school = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->select('organizations.id as schoolid')
            ->where('organization_user.user_id', $userid)
            ->first();

        // dd($userid);

        $listclass = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('classes.id as id', 'classes.nama', 'classes.levelid')
            ->where([
                ['class_organization.organization_id', $school->schoolid]
            ])
            ->orderBy('classes.nama')
            ->get();
        // dd($listclass);
        // $kelas = Kelas::all()->toArray();
        return view('pentadbir.class.index', compact('listclass'));
    }

    public function classexport()
    {
        return Excel::download(new ClassExport, 'class.xlsx');
    }

    public function classimport(Request $request)
    {
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        Excel::import(new ClassImport, public_path('/uploads/excel/' . $namaFile));

        return redirect('/class')->with('success', 'New class has been added successfully');

    }
    
    public function create()
    {
        //
        return view('pentadbir.class.add');
    }

    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name'          =>  'required',
            'level'         =>  'required',
        ]);

        $class = new ClassModel([
            'nama'          =>  $request->get('name'),
            'levelid'       =>  $request->get('level'),
        ]);
        $class->save();

        $userid     = Auth::id();

        $school = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->select('organizations.id as schoolid')
            ->where('organization_user.user_id', $userid)
            ->first();
        // dd($school);

        DB::table('class_organization')->insert([
            'organization_id' => $school->schoolid,
            'class_id'        => $class->id,
            'start_date'      => now(),
        ]);

        return redirect('/class')->with('success', 'New class has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
        $class       = DB::table('classes')->where('id', $id)->first();

        return view('pentadbir.class.update', compact('class'));
    }

    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'name'          =>  'required',
            'level'         =>  'required',
        ]);

        $school    = DB::table('classes')
            ->where('id', $id)
            ->update(
                [
                    'nama'      => $request->get('name'),
                    'levelid'   => $request->get('level')
                ]
            );

        return redirect('/class')->with('success', 'The data has been updated!');
    }

    public function destroy($id)
    {
        //
    }
}
