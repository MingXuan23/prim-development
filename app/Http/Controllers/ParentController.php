<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ParentController extends Controller
{
    public function index()
    {
        //
        $userId = Auth::id();

        // condition type sekolah pagi n jaim
        $school = DB::table('organizations')->get();
        $role   = DB::table('organization_roles')
            ->where('id', '!=', 1)
            ->where('id', '!=', 2)
            ->get();


        $list = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->join('users', 'users.id', '=', 'organization_user.user_id')
            ->join('organization_roles', 'organization_roles.id', '=', 'organization_user.role_id')
            ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            ->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.nama as nschool', 'students.nama as studentname', 'classes.nama as classname', 'organization_roles.nama as rolename')
            ->where([
                ['users.id', $userId],
            ])
            ->orderBy('students.nama')
            ->get();

        return view('parent.dependent.index', compact('list', 'school', 'role'));
    }

    public function fetchClass(Request $request)
    {

        // dd($request->get('schid'));
        $schid = $request->get('schid');

        $list = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.nama as nschool', 'classes.id as cid', 'classes.nama as cname')
            ->where('organizations.id', $schid)
            ->get();

        return response()->json(['success' => $list]);
    }

    public function fetchStd(Request $request)
    {

        // dd($request);
        $classid = $request->get('cid');

        $list = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->select('students.nama as namestd', 'students.id as sid')
            ->where('classes.id', $classid)
            ->get();

        return response()->json(['success' => $list]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'school'        =>  'required',
            'classes'       =>  'required',
            'student'       =>  'required',
        ]);

        $userId = Auth::id();
        $schid = $request->get('school');
        $roles = $request->get('roles');

        $ouid = DB::table('organization_user')->insertGetId([
            'organization_id'   => $schid,
            'user_id'           => $userId,
            'role_id'           => $roles,
            'start_date'        => now(),
            'status'            => 1,
        ]);

        // dd($schid);
        $stdid = $request->get('student');

        $list = DB::table('organization_user_student')
            ->insert([
                'organization_user_id'  => $ouid,
                'student_id'            => $stdid

            ]);

        $user = Auth::user();
        $role = Role::create(['name' => 'parent']);

        $user->assignRole('parent');
        return redirect('/parent')->with('success', 'New dependents has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
