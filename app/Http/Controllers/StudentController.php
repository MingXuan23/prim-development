<?php

namespace App\Http\Controllers;

use App\Exports\StudentExport;
use App\Imports\StudentImport;
use App\Imports\StudentCompare;
use App\Imports\StudentSwastaImport;
use App\Imports\StudentSwastaCompare;
use App\Models\ClassModel;
use App\Models\Organization;
use App\Models\Student;
use App\Models\Parents;
use Illuminate\Http\JsonResponse;
use PDF;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Arabic;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Models\OrganizationRole;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $organization = $this->getOrganizationByUserId();

        if (Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir')) {
            $listclass = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select('classes.id as id', 'classes.nama', 'classes.levelid')
                ->where([
                    ['class_organization.organization_id', $organization[0]->id],
                    ['classes.status', 1]
                ])
                ->orderBy('classes.nama')
                ->get();
        } else {
            $listclass = DB::table('class_organization')
                ->leftJoin('classes', 'class_organization.class_id', '=', 'classes.id')
                ->leftJoin('organization_user', 'class_organization.organ_user_id', 'organization_user.id')
                ->select('classes.id as id', 'classes.nama', 'classes.levelid')
                ->where([
                    ['class_organization.organization_id', $organization[0]->id],
                    ['classes.status', 1],
                    ['organization_user.user_id', $userId]
                ])
                ->orderBy('classes.nama')
                ->get();
        }

        return view("student.index", compact('listclass', 'organization'));
    }

    public function indexSwasta()
    {
        $userId = Auth::id();
        $organization = $this->getOrganizationByUserId();

        if (Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Pentadbir Swasta')) {
            $listclass = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select('classes.id as id', 'classes.nama', 'classes.levelid')
                ->where([
                    ['class_organization.organization_id', $organization[0]->id],
                    ['classes.status', 1]
                ])
                ->orderBy('classes.nama')
                ->get();
        } else {
            $listclass = DB::table('class_organization')
                ->leftJoin('classes', 'class_organization.class_id', '=', 'classes.id')
                ->leftJoin('organization_user', 'class_organization.organ_user_id', 'organization_user.id')
                ->select('classes.id as id', 'classes.nama', 'classes.levelid')
                ->where([
                    ['class_organization.organization_id', $organization[0]->id],
                    ['classes.status', 1],
                    ['organization_user.user_id', $userId]
                ])
                ->orderBy('classes.nama')
                ->get();
        }

        return view("private-school.student.index", compact('listclass', 'organization'));
    }

    public function studentexport(Request $request)
    {
        $this->validate($request, [
            'export_type' => "required"
        ]);

        $name = "";

        switch ($request->get("export_type")) {
            case "tanpa_data":
                $name = "export_tanpa_data";
                break;
            case "data_dalam_organisasi":
                // make sure that user chooses each of the required options
                $this->validate($request, [
                    'organExport' => "required"
                ]);

                $name = Organization::find($request->organExport)->nama;
                break;
            case "data_dalam_tahun":
                // make sure that user chooses each of the required options
                $this->validate($request, [
                    'organExport' => "required",
                    "yearExport" => "required"
                ]);

                $name = Organization::find($request->organExport)->nama . " Year " . $request->yearExport;
                break;
            case "data_dalam_kelas":
                // make sure that user chooses each of the required options
                $this->validate($request, [
                    'organExport' => "required",
                    "classExport" => "required"
                ]);

                $name = ClassModel::find($request->classExport)->nama;
                break;
            default:
                break;
        }

        return Excel::download(new StudentExport($request->organExport, $request->classExport, $request->yearExport), $name . '.xlsx');
    }

    public function studentimport(Request $request)
    {
        $this->validate($request, [
            'classImport' => 'required',
            'organImport' => 'required',
        ]);

        // dd($request->classImport);

        $classID = $request->get('classImport');
        $organID = $request->get('organImport');
        //dd($organID);

        $ifSwasta = DB::table('organizations as o')
            ->where('o.id', $organID)
            ->where('o.type_org', 15) //swasta
            ->get();
        //dd($ifSwasta);

        $file = $request->file('file');
        $namaFile = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);
        $public_path = $_SERVER['DOCUMENT_ROOT'];

        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];

        if (!in_array($etx, $formats)) {

            return redirect('/student')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }
        if ($request->compareOption == true) {

            if (count($ifSwasta) == 0) {
                //Excel::import(new StudentCompare($classID), $public_path . '/uploads/excel/' . $namaFile);
                $import = new StudentCompare($classID);
                Excel::import($import, $public_path . '/uploads/excel/' . $namaFile);

                $studentArray = $import->getStudentArray();
                $sameClassStudents = $studentArray['sameClassStudents'];
                $differentClassStudents = $studentArray['differentClassStudents'];
                $differentOrgStudents = $studentArray['differentOrgStudents'];
                $newStudents = $studentArray['newStudents'];
                //dd($differentClassStudents);
                return view('student.compare', compact('sameClassStudents', 'differentClassStudents', 'differentOrgStudents', 'newStudents'));
            } else //if swasta, import using another controller
            {
                $import = new StudentSwastaCompare($classID);
                Excel::import($import, $public_path . '/uploads/excel/' . $namaFile);

                $studentArray = $import->getStudentArray();
                $sameClassStudents = $studentArray['sameClassStudents'];
                $differentClassStudents = $studentArray['differentClassStudents'];
                $differentOrgStudents = $studentArray['differentOrgStudents'];
                $newStudents = $studentArray['newStudents'];
                //dd($differentClassStudents);
                return view('private-school.student.compare', compact('sameClassStudents', 'differentClassStudents', 'differentOrgStudents', 'newStudents'));
            }
        } else {
            Excel::import(new StudentImport($classID), $public_path . '/uploads/excel/' . $namaFile);
        }

        return redirect('/student')->with('success', 'New student has been added successfully');
    }

    public function create()
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

        $organization = $this->getOrganizationByUserId();


        return view('student.add', compact('listclass', 'organization'));
    }

    public function createSwasta()
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

        $organization = $this->getOrganizationByUserId();


        return view('private-school.student.add', compact('listclass', 'organization'));
    }

    public function trimString($text)
    {
        $text = trim($text);
        $text = preg_replace('/^\s+|\s+$/u', '', $text);
        return $text;
    }
    public function store(Request $request)
    {
        $classid = $request->get('classes');
        $class = ClassModel::find($classid);

        $co = DB::table('class_organization')
            ->select('id', 'organization_id as oid')
            ->where('class_id', $classid)
            ->first();

        $this->validate($request, [
            'name' => 'required',
            'classes' => 'required',
            'parent_name' => 'required',
            'parent_icno' => 'required',
        ]);

        $icno = $this->trimString(str_replace('-', '', $request->get('parent_icno')));

        $parentname = $this->trimString(strtoupper($request->get('parent_name')));

        $ifExits = DB::table('users as u')
            ->leftJoin('organization_user as ou', 'u.id', '=', 'ou.user_id')
            ->where('u.telno', '=', $icno)
            ->where('ou.organization_id', $co->oid)
            ->whereIn('ou.role_id', [5, 6, 21])
            ->get();

        if (count($ifExits) == 0) { // if not teacher or parent

            $newparent = DB::table('users')
                ->where('telno', '=', $icno)
                ->first();

            // dd($newparent);

            if (empty($newparent)) {
                $this->validate($request, [
                    'parent_icno' => 'required|unique:users,telno',
                ]);

                if ($request->parent_email != null) {
                    $this->validate($request, [
                        'parent_email' => 'required|email|unique:users,email',
                    ]);
                }

                $newparent = new Parents([
                    'name' => $parentname,
                    'email' => $request->get('parent_email'),
                    'password' => Hash::make('abc123'),
                    'telno' => $icno,
                    'remember_token' => Str::random(40),
                ]);
                $newparent->save();
            }

            // add parent role
            $parentRole = DB::table('organization_user')
                ->where('user_id', $newparent->id)
                ->where('organization_id', $co->oid)
                ->where('role_id', 6)
                ->first();

            if (empty($parentRole)) {
                DB::table('organization_user')->insert([
                    'organization_id' => $co->oid,
                    'user_id' => $newparent->id,
                    'role_id' => 6,
                    'start_date' => now(),
                    'status' => 1,
                ]);
            }
        } else {
            $newparent = DB::table('users')
                ->where('telno', '=', "{$icno}")
                ->first();

            $parentRole = DB::table('organization_user')
                ->where('user_id', $newparent->id)
                ->where('organization_id', $co->oid)
                ->where('role_id', 6)
                ->first();

            // dd($parentRole);

            if (empty($parentRole)) {
                DB::table('organization_user')->insert([
                    'organization_id' => $co->oid,
                    'user_id' => $newparent->id,
                    'role_id' => 6,
                    'start_date' => now(),
                    'status' => 1,
                ]);
            }
        }
        $this->assignStudentToParent($newparent->id, $icno, $request, $classid, $ifExits);
        return redirect('/student')->with('success', 'New student has been added successfully');
    }

    public function storeSwasta(Request $request)
    {
        $classid = $request->get('classes');
        $class = ClassModel::find($classid);

        $co = DB::table('class_organization')
            ->select('id', 'organization_id as oid')
            ->where('class_id', $classid)
            ->first();

        $this->validate($request, [
            'name' => 'required',
            'classes' => 'required',
            'parent_name' => 'required',
            'parent_icno' => 'required',
        ]);

        $icno = $this->trimString(str_replace('-', '', $request->get('parent_icno')));

        $parentname = $this->trimString(strtoupper($request->get('parent_name')));

        $ifExits = DB::table('users as u')
            ->leftJoin('organization_user as ou', 'u.id', '=', 'ou.user_id')
            ->where('u.telno', '=', $icno)
            ->where('ou.organization_id', $co->oid)
            ->whereIn('ou.role_id', [5, 6, 21])
            ->get();

        if (count($ifExits) == 0) { // if not teacher or parent

            $newparent = DB::table('users')
                ->where('telno', '=', $icno)
                ->first();

            // dd($newparent);

            if (empty($newparent)) {
                $this->validate($request, [
                    'parent_icno' => 'required|unique:users,telno',
                ]);

                if ($request->parent_email != null) {
                    $this->validate($request, [
                        'parent_email' => 'required|email|unique:users,email',
                    ]);
                }

                $newparent = new Parents([
                    'name' => $parentname,
                    'email' => $request->get('parent_email'),
                    'password' => Hash::make('abc123'),
                    'telno' => $icno,
                    'remember_token' => Str::random(40),
                ]);
                $newparent->save();
            }

            // add parent role
            $parentRole = DB::table('organization_user')
                ->where('user_id', $newparent->id)
                ->where('organization_id', $co->oid)
                ->where('role_id', 6)
                ->first();

            if (empty($parentRole)) {
                DB::table('organization_user')->insert([
                    'organization_id' => $co->oid,
                    'user_id' => $newparent->id,
                    'role_id' => 6,
                    'start_date' => now(),
                    'status' => 1,
                ]);
            }
        } else {
            $newparent = DB::table('users')
                ->where('telno', '=', "{$icno}")
                ->first();

            $parentRole = DB::table('organization_user')
                ->where('user_id', $newparent->id)
                ->where('organization_id', $co->oid)
                ->where('role_id', 6)
                ->first();

            // dd($parentRole);

            if (empty($parentRole)) {
                DB::table('organization_user')->insert([
                    'organization_id' => $co->oid,
                    'user_id' => $newparent->id,
                    'role_id' => 6,
                    'start_date' => now(),
                    'status' => 1,
                ]);
            }
        }
        $this->assignStudentToParent($newparent->id, $icno, $request, $classid, $ifExits);
        return redirect('/private-school/student')->with('success', 'New student has been added successfully');
    }

    public function assignStudentToParent($parentId, $telno, $studentData, $classId, $ifExits)
    {

        $co = DB::table('class_organization')
            ->select('id', 'organization_id as oid')
            ->where('class_id', $classId)
            ->first();
        $class = ClassModel::find($classId);

        $ou = DB::table('organization_user')
            ->where('user_id', $parentId)
            ->where('organization_id', $co->oid)
            ->where('role_id', 6)
            ->first();
        // dd($newparent->id);
        $user = User::find($parentId);

        // role parent
        $rolename = OrganizationRole::find(6);
        $user->assignRole($rolename->nama);

        $student = new Student([
            'nama' => empty($studentData->name) ? $this->trimString(strtoupper($studentData->studentName)) : $this->trimString(strtoupper($studentData->name)),
            // 'icno'          =>  $request->get('icno'),
            'gender' => $studentData->gender,
            //'email'         =>  $studentData->email,
        ]);

        $student->save();
        // 
        DB::table('class_student')->insert([
            'organclass_id' => $co->id,
            'student_id' => $student->id,
            'start_date' => now(),
            'status' => 1,
        ]);

        $classStu = DB::table('class_student')
            ->where('student_id', $student->id)
            ->first();


        DB::table('organization_user_student')->insert([
            'organization_user_id' => $ou->id,
            'student_id' => $student->id
        ]);

        // dd($ou);

        /* 
            - this has to change after all the features have done.
            - delete parent tel column in table `students`
        */
        DB::table('students')
            ->where('id', $student->id)
            ->update(['parent_tel' => $telno]);

        // check fee for new in student
        // check category A fee
        $ifExitsCateA = DB::table('fees_new')
            ->where('category', 'Kategori A')
            ->where('organization_id', $co->oid)
            ->where('status', 1)
            ->get();

        $ifExitsCateBC = DB::table('fees_new')
            ->whereIn('category', ['Kategori B', 'Kategori C'])
            ->where('organization_id', $co->oid)
            ->where('status', 1)
            ->get();

        // check category Recurring fee
        $ifExitsCateRecurring = DB::table('fees_new')
            ->where('category', 'Kategori Berulang')
            ->where('organization_id', $co->oid)
            ->where('status', 1)
            ->get();

        if (!$ifExitsCateA->isEmpty() && !empty($ifExits)) {
            // get the organization_user and organization_user_student for this parent
            $parentKategoriAFees = DB::table("organization_user as ou")
                ->join('fees_new_organization_user as fou', 'fou.organization_user_id', '=', 'ou.id')
                ->where('ou.user_id', '=', $parentId)
                ->where('ou.organization_id', '=', $co->oid)
                ->get();

            foreach ($ifExitsCateA as $kateA) {
                // check if this parent already have the current fee
                $parentFee = $parentKategoriAFees->where('fees_new_id', '=', $kateA->id)->first();

                // if result is null (parent does not have this fee), assign kategori A fee for this parent
                if (!isset($parentFee)) {
                    DB::table('fees_new_organization_user')->insert([
                        'status' => 'Debt',
                        'fees_new_id' => $kateA->id,
                        'organization_user_id' => $ou->id,
                        'transaction_id' => NULL
                    ]);
                }
            }
        }

        if (!$ifExitsCateBC->isEmpty()) {
            foreach ($ifExitsCateBC as $kateBC) {
                $target = json_decode($kateBC->target);

                if (isset($target->gender)) {
                    if ($target->gender != $studentData->gender) {
                        continue;
                    }
                }

                if ($target->data == "All_Level" || $target->data == $class->levelid) {
                    DB::table('student_fees_new')->insert([
                        'status' => 'Debt',
                        'fees_id' => $kateBC->id,
                        'class_student_id' => $classStu->id
                    ]);
                } else if (is_array($target->data)) {
                    if (in_array($class->id, $target->data)) {
                        DB::table('student_fees_new')->insert([
                            'status' => 'Debt',
                            'fees_id' => $kateBC->id,
                            'class_student_id' => $classStu->id
                        ]);
                    }
                }
            }
        }

        if (!$ifExitsCateRecurring->isEmpty()) {
            foreach ($ifExitsCateRecurring as $kateRec) {
                if ($kateRec->end_date > $classStu->start_date) {
                    $target = json_decode($kateRec->target);

                    if (isset($target->gender)) {
                        if ($target->gender != $studentData->gender) {
                            continue;
                        }
                    }

                    if ($target->data == "All_Level" || $target->data == $class->levelid) {
                        // DB::table('student_fees_new')->insert([
                        //     'status'            => 'Debt',
                        //     'fees_id'           =>  $kateRec->id,
                        //     'class_student_id'  =>  $classStu->id
                        // ]);

                        $student_fees_new = DB::table('student_fees_new')->insertGetId([
                            'status' => 'Debt',
                            'fees_id' => $kateRec->id,
                            'class_student_id' => $classStu->id
                        ]);

                        $datestarted = Carbon::parse($kateRec->start_date); //back to original date without format (string to datetime)
                        $dateend = Carbon::parse($kateRec->end_date);
                        $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                        $cs_startdate = Carbon::parse($classStu->start_date);
                        $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                        if ($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day) {
                            $totalDayLeft = $totalDay;
                        }
                        $finalAmount = $kateRec->totalAmount * ($totalDayLeft / $totalDay);
                        if ($finalAmount > $kateRec->totalAmount) {
                            $finalAmount = $kateRec->totalAmount;
                        }

                        DB::table('fees_recurring')->insert([
                            'student_fees_new_id' => $student_fees_new,
                            'totalDay' => $totalDay,
                            'totalDayLeft' => $totalDayLeft,
                            'finalAmount' => $finalAmount,
                            'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                            'created_at' => now(),
                        ]);
                    } else if (is_array($target->data)) {
                        if (in_array($class->id, $target->data)) {
                            // DB::table('student_fees_new')->insert([
                            //     'status'            => 'Debt',
                            //     'fees_id'           =>  $kateRec->id,
                            //     'class_student_id'  =>  $classStu->id
                            // ]);

                            $student_fees_new = DB::table('student_fees_new')->insertGetId([
                                'status' => 'Debt',
                                'fees_id' => $kateRec->id,
                                'class_student_id' => $classStu->id
                            ]);

                            $datestarted = Carbon::parse($kateRec->start_date); //back to original date without format (string to datetime)
                            $dateend = Carbon::parse($kateRec->end_date);
                            $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                            $cs_startdate = Carbon::parse($classStu->start_date);
                            $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                            if ($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day) {
                                $totalDayLeft = $totalDay;
                            }
                            $finalAmount = $kateRec->totalAmount * ($totalDayLeft / $totalDay);
                            if ($finalAmount > $kateRec->totalAmount) {
                                $finalAmount = $kateRec->totalAmount;
                            }

                            DB::table('fees_recurring')->insert([
                                'student_fees_new_id' => $student_fees_new,
                                'totalDay' => $totalDay,
                                'totalDayLeft' => $totalDayLeft,
                                'finalAmount' => $finalAmount,
                                'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                                'created_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }

        $child_organs = DB::table('organizations')
            ->where('parent_org', $co->oid)
            ->get();

        foreach ($child_organs as $child_organ) {

            $organ_user_id = DB::table('organization_user')->insertGetId([
                'organization_id' => $child_organ->id,
                'user_id' => $parentId,
                'role_id' => 6,
                'start_date' => now(),
                'status' => 1,
            ]);

            $ifExitsCateA = DB::table('fees_new')
                ->where('category', 'Kategori A')
                ->where('organization_id', $child_organ->id)
                ->where('status', 1)
                ->get();

            $ifExitsCateBC = DB::table('fees_new')
                ->whereIn('category', ['Kategori B', 'Kategori C'])
                ->where('organization_id', $child_organ->id)
                ->where('status', 1)
                ->get();

            // check category Recurring fee
            $ifExitsCateRecurring = DB::table('fees_new')
                ->where('category', 'Kategori Berulang')
                ->where('organization_id', $child_organ->id)
                ->where('status', 1)
                ->get();

            if (!$ifExitsCateA->isEmpty() && !empty($ifExits)) {
                foreach ($ifExitsCateA as $kateA) {
                    DB::table('fees_new_organization_user')->insert([
                        'status' => 'Debt',
                        'fees_new_id' => $kateA->id,
                        'organization_user_id' => $organ_user_id,
                        'transaction_id' => NULL
                    ]);
                }
            }

            if (!$ifExitsCateBC->isEmpty()) {
                foreach ($ifExitsCateBC as $kateBC) {
                    $target = json_decode($kateBC->target);

                    if (isset($target->gender)) {
                        if ($target->gender != $request->get('gender')) {
                            continue;
                        }
                    }

                    if ($target->data == "All_Level" || $target->data == $class->levelid) {
                        DB::table('student_fees_new')->insert([
                            'status' => 'Debt',
                            'fees_id' => $kateBC->id,
                            'class_student_id' => $classStu->id
                        ]);
                    } else if (is_array($target->data)) {
                        if (in_array($class->id, $target->data)) {
                            DB::table('student_fees_new')->insert([
                                'status' => 'Debt',
                                'fees_id' => $kateBC->id,
                                'class_student_id' => $classStu->id
                            ]);
                        }
                    }
                }
            }

            if (!$ifExitsCateRecurring->isEmpty()) {
                foreach ($ifExitsCateRecurring as $kateRec) {
                    if ($kateRec->end_date > $classStu->start_date) {
                        $target = json_decode($kateRec->target);

                        if (isset($target->gender)) {
                            if ($target->gender != $request->get('gender')) {
                                continue;
                            }
                        }

                        if ($target->data == "All_Level" || $target->data == $class->levelid) {
                            // DB::table('student_fees_new')->insert([
                            //     'status'            => 'Debt',
                            //     'fees_id'           =>  $kateRec->id,
                            //     'class_student_id'  =>  $classStu->id
                            // ]);

                            $student_fees_new = DB::table('student_fees_new')->insertGetId([
                                'status' => 'Debt',
                                'fees_id' => $kateRec->id,
                                'class_student_id' => $classStu->id
                            ]);

                            $datestarted = Carbon::parse($kateRec->start_date); //back to original date without format (string to datetime)
                            $dateend = Carbon::parse($kateRec->end_date);
                            $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                            $cs_startdate = Carbon::parse($classStu->start_date);
                            $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                            if ($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day) {
                                $totalDayLeft = $totalDay;
                            }
                            $finalAmount = $kateRec->totalAmount * ($totalDayLeft / $totalDay);
                            if ($finalAmount > $kateRec->totalAmount) {
                                $finalAmount = $kateRec->totalAmount;
                            }

                            DB::table('fees_recurring')->insert([
                                'student_fees_new_id' => $student_fees_new,
                                'totalDay' => $totalDay,
                                'totalDayLeft' => $totalDayLeft,
                                'finalAmount' => $finalAmount,
                                'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                                'created_at' => now(),
                            ]);
                        } else if (is_array($target->data)) {
                            // if(in_array($class->id, $target->data))
                            // {
                            //     DB::table('student_fees_new')->insert([
                            //         'status'            => 'Debt',
                            //         'fees_id'           =>  $kateRec->id,
                            //         'class_student_id'  =>  $classStu->id
                            //     ]);
                            // }

                            $student_fees_new = DB::table('student_fees_new')->insertGetId([
                                'status' => 'Debt',
                                'fees_id' => $kateRec->id,
                                'class_student_id' => $classStu->id
                            ]);

                            $datestarted = Carbon::parse($kateRec->start_date); //back to original date without format (string to datetime)
                            $dateend = Carbon::parse($kateRec->end_date);
                            $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                            $cs_startdate = Carbon::parse($classStu->start_date);
                            $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                            if ($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day) {
                                $totalDayLeft = $totalDay;
                            }
                            $finalAmount = $kateRec->totalAmount * ($totalDayLeft / $totalDay);
                            if ($finalAmount > $kateRec->totalAmount) {
                                $finalAmount = $kateRec->totalAmount;
                            }

                            DB::table('fees_recurring')->insert([
                                'student_fees_new_id' => $student_fees_new,
                                'totalDay' => $totalDay,
                                'totalDayLeft' => $totalDayLeft,
                                'finalAmount' => $finalAmount,
                                'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                                'created_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    // this is a helper function to help assign all the currently active fees to the newly created parents accounts
    public function assignFeesToParent($organizationId, $organizationUserId)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        // get all the fees_new for the current organization for Kategori A
        $allFees = DB::table('fees_new')
            ->where('organization_id', $organizationId)
            ->where('status', 1)
            ->where('category', "=", "Kategori A")
            ->get();

        if ($allFees->count() <= 0) {
            // if there is no fees from the organization, set the organization_user fees_status to 'Complete'
            DB::table("organization_user")
                ->where("id", "=", $organizationUserId)
                ->update(["fees_status" => "Complete"]);
        } else {
            // if there are fees created by the organization, assign relevant fees to the parent

            // check if the fees match the student's details (e.g. class, gender)
            foreach ($allFees as $fee) {
                // assign the fee to the parent
                DB::table('fees_new_organization_user')->insert([
                    'status' => 'Debt',
                    'fees_new_id' => $fee->id,
                    'organization_user_id' => $organizationUserId,
                    'transaction_id' => null
                ]);

                // update the parent's organization_user and set its fees_status to 'Not Complete'
                DB::table('organization_user')
                    ->where('id', '=', $organizationUserId)
                    ->update(['fees_status' => 'Not Complete']);
            }
        }
    }

    // this is a helper function to help assign all the currently active fees to the newly created students
    public function assignFeesToStudent($organizationId, $classId, $classStudentId, $studentData)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        // get all the fees_new for the current organization
        $allFees = DB::table('fees_new')
            ->where('organization_id', $organizationId)
            ->where('status', 1)
            ->whereNotIn('category', ["Kategori A"])
            ->get();

        foreach ($allFees as $fee) {
            $target = json_decode($fee->target);

            // if the fee has specified gender and the specified gender is not the same as the current student's gender
            // proceed to the next fee 
            if (isset($target->gender) && $target->gender != $studentData->gender) {
                continue;
            }

            if ($allFees->count() <= 0) {
                // if there is no fees from the organization, set the class_student fees_status to 'Complete'
                DB::table("class_student")
                    ->where("id", "=", $classStudentId)
                    ->update(["fees_status" => "Complete"]);
            } else {
                // get the class data of the current student
                $class = DB::table("classes")
                    ->where("id", "=", $classId)
                    ->first();

                if ($fee->category == "Kategori B" || $fee->category == "Kategori C") {
                    // category B or category C

                    // if the fee's class level is the current student's class level and the class level is All_Level
                    if ($target->data == "All_Level" || $target->data == $class->levelid) {
                        // assign this fee to the new student
                        DB::table('student_fees_new')->insert([
                            'status' => 'Debt',
                            'fees_id' => $fee->id,
                            'class_student_id' => $classStudentId
                        ]);

                        DB::table("class_student")
                            ->where("id", "=", $classStudentId)
                            ->update(["fees_status" => "Not Complete"]);
                    } else if (is_array($target->data)) {
                        // if the target's data consists of differenct classes specifically and if the new student's class is in the list
                        if (in_array($class->id, $target->data)) {
                            // assign this fee to the new student
                            DB::table('student_fees_new')->insert([
                                'status' => 'Debt',
                                'fees_id' => $fee->id,
                                'class_student_id' => $classStudentId
                            ]);

                            DB::table("class_student")
                                ->where("id", "=", $classStudentId)
                                ->update(["fees_status" => "Not Complete"]);
                        }
                    }
                } else {
                    // recurring category

                    // if the fee is for all level OR 
                    // the fee specified year level matches the new student's class level OR
                    // the fee's specified classes includes the new student's class
                    if (
                        $target->data == "All_Level" ||
                        $target->data == $class->levelid ||
                        (is_array($target->data) && in_array($class->id, $target->data))
                    ) {
                        $student_fees_new = DB::table('student_fees_new')->insertGetId([
                            'status' => 'Debt',
                            'fees_id' => $fee->id,
                            'class_student_id' => $classStudentId
                        ]);

                        // get class_student by current class_student_id
                        $classStudent = DB::table("class_student")
                            ->where("id", "=", $classStudentId)
                            ->first();

                        // get the data required for storing a new fees_recurring data
                        $recurringDateStarted = Carbon::parse($fee->start_date);
                        $recurringDateEnd = Carbon::parse($fee->end_date);
                        $totalDays = ($recurringDateStarted->diffInDays($recurringDateEnd)) + 1;
                        $classStudentStartDate = Carbon::parse($classStudent->start_date);
                        $totalDaysLeft = ($classStudentStartDate)->diffInDays($recurringDateEnd);

                        // if the total days left by the new student is greater than the initial total days given by the recurring fee duration
                        // (new student started before fee starts)
                        // OR if the new student's start date is the same as the fee's start date (the fee and the student start on the same day)
                        if ($totalDaysLeft > $totalDays || $recurringDateStarted->day == $classStudentStartDate->day) {
                            // set the total days left for the student to the initial total days given to pay the fee
                            $totalDaysLeft = $totalDays;
                        }

                        // this is to ensure if the student started later than the fee start date, then they only pay a portion of the fee
                        // (based on the formula below)
                        $finalAmount = $fee->totalAmount * ($totalDaysLeft / $totalDays);
                        if ($finalAmount > $fee->totalAmount) {
                            $finalAmount = $fee->totalAmount;
                        }

                        // add a new row into fees_recurring
                        DB::table('fees_recurring')->insert([
                            'student_fees_new_id' => $student_fees_new,
                            'totalDay' => $totalDays,
                            'totalDayLeft' => $totalDaysLeft,
                            'finalAmount' => $finalAmount,
                            'desc' => 'RM' . $fee->totalAmount . '*' . $totalDaysLeft . '/' . $totalDays,
                            'created_at' => now(),
                        ]);

                        DB::table("class_student")
                            ->where("id", "=", $classStudentId)
                            ->update(["fees_status" => "Not Complete"]);
                    }
                }
            }
        }
    }

    // route to get all pending registrations
    public function getAllPendingRegistrations(Request $request)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole('Superadmin') && !Auth::user()->hasRole('Pentadbir') && !Auth::user()->hasRole('Pentadbir Swasta') && !Auth::user()->hasRole('Guru') && !Auth::user()->hasRole('Guru Swasta')) {
            // return an empty datatable if the roles do not match
            return Datatables::of(collect([]))->make(true);
        }

        $organizationId = $request->get('oid');

        $allPendingRegistrations = DB::table("registration_requests")
            ->where("organization_id", "=", $organizationId)
            ->where("status", "=", "Pending")
            ->get();

        $table = Datatables::of($allPendingRegistrations);

        $table->addColumn('parent_name', function ($row) {
            $parentInfo = DB::table("users")->where("id", "=", $row->parent_id)->first();
            return $parentInfo->name;
        });

        $table->addColumn('telno', function ($row) {
            $parentInfo = DB::table("users")->where("id", "=", $row->parent_id)->first();
            return $parentInfo->telno;
        });

        $table->addColumn('student_name', function ($row) {
            $studentInfo = json_decode($row->student_info);
            return $studentInfo->name;
        });

        $table->addColumn('student_icno', function ($row) {
            $studentInfo = json_decode($row->student_info);
            return $studentInfo->icno;
        });

        $table->addColumn('student_gender', function ($row) {
            $studentInfo = json_decode($row->student_info);
            return $studentInfo->gender;
        });

        $table->addColumn('student_class', function ($row) {
            $studentInfo = json_decode($row->student_info);
            $className = DB::table("classes")
                ->where("id", "=", $studentInfo->class_id)
                ->first()
                ->nama;

            return $className;
        });

        // add a button for the actions column
        $table->addColumn('actions', function ($row) {
            return "<div class='text-center'>
                 <a style='margin: 5px auto;' class='btn btn-primary' href='" .
                route('student.parentRegisterStudents.acceptOrRejectRegistrations', [
                    'decision' => 1,
                    'registration_request_id' => $row->id,
                ]) .
                "'>
                 <i class='fa-solid fa-pen-to-square'></i> Terima
                 </a>

                 <a style='margin: 5px auto;' class='btn btn-danger' href='" .
                route('student.parentRegisterStudents.acceptOrRejectRegistrations', [
                    'decision' => 0,
                    'registration_request_id' => $row->id,
                ]) .
                "'>
                 <i class='fa-solid fa-pen-to-square'></i> Tolak
                 </a>
            </div>";
        });

        $table->rawColumns(['parent_name', 'email', 'student_name', 'student_icno', 'student_gender', 'student_class', 'actions']);

        return $table->make(true);
    }

    // show the page with pending registrations requests made by (users) parents
    public function parentRegisterStudentsIndex()
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole("Superadmin") && !Auth::user()->hasRole("Pentadbir") && !Auth::user()->hasRole("Pentadbir Swasta") && !Auth::user()->hasRole("Guru") && !Auth::user()->hasRole("Guru Swasta")) {
            return redirect('/home');
        }

        // set the initial organizations to an empty collectables (just in case if any user without the roles below access it, it will return nothing)
        $organizations = collect([]);

        if (Auth::user()->hasRole('Superadmin')) {
            $organizations = DB::table("organizations")->get();
        } else if (Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Pentadbir Swasta') || Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Guru Swasta')) {
            $organizations = DB::table("organizations as o")
                ->join("organization_user as ou", "ou.organization_id", "=", "o.id")
                ->select("o.*")
                ->where("ou.user_id", "=", Auth::id())
                ->distinct()
                ->get();
        }

        return view('student.parent_register_students.index', compact('organizations'));
    }

    // show the page where parents register their children
    public function parentRegisterStudentsCreate()
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole("Penjaga") && !Auth::user()->hasRole("Superadmin")) {
            return redirect('/home');
        }

        $organizations = DB::table("organizations as o")
            ->join("type_organizations as to", "to.id", "=", "o.type_org")
            ->whereIn("to.nama", ["SK /SJK", "SRA /SRAI", "SMK /SMJK", "Sekolah Swasta /Tadika"])
            ->select("o.*")
            ->get();

        return view('student.parent_register_students.create', compact("organizations"));
    }

    public function parentRegisterStudentsStore(Request $request)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole('Penjaga')) {
            return redirect('/home');
        }

        // get all students info
        foreach ($request->get("names") as $index => $studentName) {
            // validate input for student info
            $validator = Validator::make([
                "name" => $studentName,
                "email" => $request->get("emails")[$index],
                "icno" => str_replace("-", "", $request->get("icnos")[$index]),
                "gender" => $request->get("genders")[$index],
                "class_id" => $request->get("class_ids")[$index],
            ], [
                "name" => ["required", "string"],
                "email" => ["nullable", "email", "unique:students,email"],
                "icno" => ['required', 'string', 'min:12', 'max:14', 'unique:students,icno'],
                "gender" => ["required", Rule::in(["L", "P"])],
                "class_id" => ["required"]
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // convert the student info to json object
            $studentInfo = json_encode([
                "name" => $studentName,
                "icno" => str_replace("-", "", $request->get("icnos")[$index]),
                "gender" => $request->get("genders")[$index],
                "email" => $request->get("emails")[$index],
                "class_id" => $request->get("class_ids")[$index],
            ]);

            // insert a new registration_requests
            DB::table("registration_requests")->insert([
                "student_info" => $studentInfo,
                "status" => "Pending",
                "parent_id" => Auth::id(),
                "organization_id" => $request->get("org_ids")[$index]
            ]);
        }

        return redirect()
            ->route("student.parentRegisterStudents.create")
            ->with("success", "Permohonan pendaftaran pelajar telah dihantar kepada pentadbir untuk disemak dan diterima.");
    }

    // this is a route method to accept or reject the registration requests
    public function acceptOrRejectRegistrations(Request $request)
    {
        if (Auth::id() == null) {
            return redirect("/login");
        }

        if (!Auth::user()->hasRole("Superadmin") && !Auth::user()->hasRole("Pentadbir") && !Auth::user()->hasRole("Pentadbir Swasta") && !Auth::user()->hasRole("Guru") && !Auth::user()->hasRole("Guru Swasta")) {
            return redirect('/home');
        }

        // accepetOrReject will be integer (1 = accept, 0 = reject)
        $acceptOrReject = $request->get('decision');

        // get the registration_request based on id
        $currentPendingRegistration = DB::table("registration_requests")
            ->where("id", "=", $request->get("registration_request_id"))
            ->first();

        // $parentInfo = json_decode($currentPendingRegistration->parent_info);
        $studentInfo = json_decode($currentPendingRegistration->student_info);

        if ($acceptOrReject == 1) {
            // if admin accepts the registration request

            DB::beginTransaction();

            $parentInfo = DB::table("users")->where("id", "=", $currentPendingRegistration->parent_id)->first();

            $organizationUser = DB::table("organization_user")
                ->where("user_id", "=", $parentInfo->id)
                ->where("organization_id", "=", $currentPendingRegistration->organization_id)
                ->first();

            $organizationUserId = null;

            // check whether the user is binded to the organization 
            // (sometimes users might have already register a child and wants to register the child again)
            if (!isset($organizationUser)) {
                // get the organization_roles id where the name of the role is 'Penjaga'
                $organizationRoleId = DB::table("organization_roles")->where("nama", "=", "Penjaga")->first()->id;

                $organizationUserId = DB::table("organization_user")->insertGetId([
                    "organization_id" => $currentPendingRegistration->organization_id,
                    "user_id" => $parentInfo->id,
                    "role_id" => $organizationRoleId,
                    "start_date" => now(),
                    "status" => 1,
                    "fees_status" => 'Not Complete'
                ]);

                // assign the kategori A fees to parent
                $this->assignFeesToParent($currentPendingRegistration->organization_id, $organizationUserId);
            } else {
                $organizationUserId = $organizationUser->id;
            }

            // check if ic no to be registered is already in db
            $icnosExisted = DB::table("students")->select("icno")->get()->pluck("icno")->toArray();

            if (in_array($studentInfo->icno, $icnosExisted)) {
                DB::rollBack();
                return redirect()->back()->withErrors("No. kad pengenalan pelajar sudah didaftar dalam sistem.");
            }

            // check if email to be registered is already in db
            $emailsExisted = DB::table("students")->select("email")->get()->pluck("email")->toArray();

            if (isset($studentInfo->email) && in_array($studentInfo->email, $emailsExisted)) {
                DB::rollBack();
                return redirect()->back()->withErrors("Emel pelajar sudah didaftar dalam sistem.");
            }

            // insert a new student
            $studentId = DB::table("students")->insertGetId([
                "nama" => $studentInfo->name,
                "icno" => str_replace("-", "", $studentInfo->icno),
                "gender" => $studentInfo->gender,
                "email" => $studentInfo->email ?? "",
                "parent_tel" => $parentInfo->telno
            ]);

            // insert a new organization_user_student
            DB::table("organization_user_student")->insert([
                "organization_user_id" => $organizationUserId,
                "student_id" => $studentId
            ]);

            // get the class_organization id based on the class id
            $classOrganizationId = DB::table("class_organization")
                ->where("organization_id", "=", $currentPendingRegistration->organization_id)
                ->where("class_id", "=", $studentInfo->class_id)
                ->first()
                ->id;

            // insert a new class_student
            $classStudentId = DB::table("class_student")->insertGetId([
                "organclass_id" => $classOrganizationId,
                "student_id" => $studentId,
                "status" => 1,
                "start_date" => now(),
                "fees_status" => "Not Complete"
            ]);

            $this->assignFeesToStudent(
                $currentPendingRegistration->organization_id,
                $studentInfo->class_id,
                $classStudentId,
                $studentInfo
            );

            // update the registration_requests to 'Approved'
            DB::table("registration_requests")
                ->where("id", "=", $currentPendingRegistration->id)
                ->update([
                    "status" => "Approved"
                ]);

            DB::commit();

            return redirect()->route('student.parentRegisterStudents.index')->with('success', 'Permohonan pendaftaran pelajar telah diterima.');
        } else {
            // if admin rejects the registration request

            // update the registration_requests to 'Rejected'
            DB::table("registration_requests")
                ->where("id", "=", $currentPendingRegistration->id)
                ->update([
                    "status" => "Rejected"
                ]);

            return redirect()->route('student.parentRegisterStudents.index')->with('success', 'Permohonan pendaftaran pelajar telah ditolak.');
        }
    }
    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $organization = $this->getOrganizationByUserId();
        $organizationId = collect($organization)->pluck('id');

        $student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->join('organization_user_student as ous', 'ous.student_id', 'students.id')
            ->join('organization_user as ou', 'ou.id', 'ous.organization_user_id')
            ->join('users as u', 'u.id', 'ou.user_id')
            ->select(
                'class_organization.organization_id',
                'students.id as id',
                'students.nama as studentname',
                'students.icno',
                'students.gender',
                'classes.id as classid',
                'classes.nama as classname',
                'class_student.status',
                'students.email',
                'u.name as parentName',
                'u.telno as parentIC'
            )
            ->where([
                ['students.id', $id],
                ['class_student.status', 1]
            ])
            ->whereIn('class_organization.organization_id', $organizationId)
            ->orderBy('classes.nama')
            ->first();

        $listclass = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('classes.id as id', 'classes.nama', 'classes.levelid')
            ->where([
                ['class_organization.organization_id', $student->organization_id]
            ])
            ->orderBy('classes.nama')
            ->get();


        return view('student.update', compact('student', 'organization', 'listclass'));
    }

    public function transferClass($co, $classid, $student)
    {
        //dd($co,$classid,$student);
        $class = DB::table('classes as c')
            ->where('c.id', $classid)
            ->first();
        $class_student = DB::table('class_organization as co')
            ->join('class_student as cs', 'cs.organclass_id', 'co.id')
            ->where('cs.student_id', $student->id)
            ->where('co.class_id', $student->class_id)
            ->where('cs.status', 1)
            ->select('co.*', 'cs.*', 'cs.id as class_student_id');

        $class_student_details = $class_student->first();
        //dd( $student->id,$student->class_id,$classid);
        $class_student->update([
            //'cs.organclass_id'=>$co->id,
            'cs.end_date' => now(),
            'cs.status' => 0,
        ]);
        //dd($class_student_details);
        $new_class_student_id = DB::table('class_student')->insertGetId([
            'organclass_id' => $co->id,
            'student_id' => $student->id,
            'status' => 1,
            'start_date' => now(),
            //'fee_status'=>'Not Complete'

        ]);

        if ($class->levelid > 0) {
            $ifExitsCateBC = DB::table('fees_new')
                ->whereIn('category', ['Kategori B', 'Kategori C'])
                ->where('organization_id', $co->organization_id)
                ->where('status', 1)
                ->get();

            $studentHaveFees = DB::table('student_fees_new as sfn')
                ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                ->join('students as s', 's.id', 'cs.student_id')
                ->where('s.id', $student->id)
                ->get();

            $studentFeesIDs = $studentHaveFees->pluck('fees_id')->toArray();

            $this->checkFeesBCWhenClassChanged($ifExitsCateBC, $studentFeesIDs, $student, $class->levelid, $new_class_student_id, $class_student_details->id);


            $ifExitsCateRecurring = DB::table('fees_new')
                ->where('category', 'Kategori Berulang')
                ->where('organization_id', $co->organization_id)
                ->where('status', 1)
                ->get();

            $cs_after_update = DB::table('class_student as cs')
                ->where('cs.student_id', $student->id)
                ->first();

            if (!$ifExitsCateRecurring->isEmpty()) {
                foreach ($ifExitsCateRecurring as $kateRec) {

                    if ($kateRec->end_date > $cs_after_update->start_date) {
                        $target = json_decode($kateRec->target);

                        if (isset($target->gender)) {
                            if ($target->gender != $studentData->gender) {
                                continue;
                            }
                        }

                        if ($target->data == "All_Level" || $target->data == $class->levelid) {

                            $datestarted = Carbon::parse($kateRec->start_date); //back to original date without format (string to datetime)
                            $dateend = Carbon::parse($kateRec->end_date);
                            $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                            $cs_startdate = Carbon::parse($cs_after_update->start_date);
                            $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                            if ($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day) {
                                $totalDayLeft = $totalDay;
                            }
                            $finalAmount = $kateRec->totalAmount * ($totalDayLeft / $totalDay);
                            if ($finalAmount > $kateRec->totalAmount) {
                                $finalAmount = $kateRec->totalAmount;
                            }

                            if (in_array($kateRec->id, $studentFeesIDs)) {
                                //continue;
                                $ifStudentFeesNewExist = DB::table('student_fees_new as sfn')
                                    ->where('sfn.fees_id', $kateRec->id)
                                    ->where('sfn.class_student_id', $student->id)
                                    ->first();

                                DB::table('fees_recurring as fr')
                                    ->where('fr.student_fees_new_id', $ifStudentFeesNewExist->id)
                                    ->update([
                                        'totalDayLeft' => $totalDayLeft,
                                        'finalAmount' => $finalAmount,
                                        'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                                        'created_at' => now(),
                                    ]);
                            } else {
                                // DB::table('student_fees_new')->insert([
                                //     'status'            => 'Debt',
                                //     'fees_id'           =>  $kateRec->id,
                                //     'class_student_id'  =>  $class_student_details->id
                                // ]);

                                $student_fees_new = DB::table('student_fees_new')->insertGetId([
                                    'status' => 'Debt',
                                    'fees_id' => $kateRec->id,
                                    'class_student_id' => $new_class_student_id
                                ]);

                                DB::table('fees_recurring')->insert([
                                    'student_fees_new_id' => $student_fees_new,
                                    'totalDay' => $totalDay,
                                    'totalDayLeft' => $totalDayLeft,
                                    'finalAmount' => $finalAmount,
                                    'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                                    'created_at' => now(),
                                ]);
                            }
                        } else if (is_array($target->data)) {
                            if (in_array($classid, $target->data)) {

                                $datestarted = Carbon::parse($kateRec->start_date); //back to original date without format (string to datetime)
                                $dateend = Carbon::parse($kateRec->end_date);
                                $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                                $cs_startdate = Carbon::parse($cs_after_update->start_date);
                                $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                                if ($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day) {
                                    $totalDayLeft = $totalDay;
                                }
                                $finalAmount = $kateRec->totalAmount * ($totalDayLeft / $totalDay);
                                if ($finalAmount > $kateRec->totalAmount) {
                                    $finalAmount = $kateRec->totalAmount;
                                }

                                if (in_array($kateRec->id, $studentFeesIDs)) {
                                    //continue;
                                    $ifStudentFeesNewExist = DB::table('student_fees_new as sfn')
                                        ->where('sfn.fees_id', $kateRec->id)
                                        ->where('sfn.class_student_id', $student->id)
                                        ->first();

                                    DB::table('fees_recurring as fr')
                                        ->where('fr.student_fees_new_id', $ifStudentFeesNewExist->id)
                                        ->update([
                                            'totalDayLeft' => $totalDayLeft,
                                            'finalAmount' => $finalAmount,
                                            'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                                            'created_at' => now(),
                                        ]);
                                } else {
                                    // DB::table('student_fees_new')->insert([
                                    //     'status'            => 'Debt',
                                    //     'fees_id'           =>  $kateRec->id,
                                    //     'class_student_id'  =>  $class_student_details->id
                                    // ]);

                                    $student_fees_new = DB::table('student_fees_new')->insertGetId([
                                        'status' => 'Debt',
                                        'fees_id' => $kateRec->id,
                                        'class_student_id' => $new_class_student_id
                                    ]);

                                    DB::table('fees_recurring')->insert([
                                        'student_fees_new_id' => $student_fees_new,
                                        'totalDay' => $totalDay,
                                        'totalDayLeft' => $totalDayLeft,
                                        'finalAmount' => $finalAmount,
                                        'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                                        'created_at' => now(),
                                    ]);
                                }
                            } else {

                                $Debt = "Debt";
                                $delete = DB::table('student_fees_new as sfn')
                                    ->where([
                                        ['sfn.fees_id', $kateRec->id],
                                        ['sfn.class_student_id', $class_student_details->id],
                                        ['sfn.status', '=', 'Debt'],
                                    ])
                                    ->get()->pluck('id');
                                DB::table('student_fees_new')->whereIn('id', $delete)->delete();
                            }
                        }
                    } else {
                        $Debt = "Debt";
                        $delete = DB::table('student_fees_new as sfn')
                            ->where([
                                ['sfn.fees_id', $kateRec->id],
                                ['sfn.class_student_id', $class_student_details->id],
                                ['sfn.status', '=', 'Debt'],
                            ])
                            ->get()->pluck('id');
                        DB::table('student_fees_new')->whereIn('id', $delete)->delete();
                    }
                }
            }
        }
    }
    public function update(Request $request, $id)
    {
        //
        $classid = $request->get('classes');

        $this->validate($request, [
            'name' => 'required',
            //'icno'          =>  'required',
            'classes' => 'required',
        ]);

        $getOrganizationClass = DB::table('class_organization')
            ->where('class_id', $classid)
            ->first();

        // dd($getOrganizationClass);
        $student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('students.id as id', 'students.nama as studentname', 'students.icno', 'classes.nama as classname', 'class_student.status', 'classes.id as class_id')
            ->where([
                ['students.id', $id],
                ['class_student.status', 1]
            ]);
        if ($student->first()->class_id != $classid) {
            $this->transferClass($getOrganizationClass, $classid, $student->first());
        }
        $student->update(
            [
                'students.nama' => $request->get('name'),
                //'students.icno' => $request->get('icno'),
                'students.gender' => $request->get('gender'),
                'students.email' => $request->get('email'),
                //'class_student.organclass_id'    => $getOrganizationClass->id,
            ]
        );

        return redirect()->back()->with('success', 'The data has been updated!')->with('closeTab', true);
        ;
    }

    public function destroy($id)
    {
        //
        $result = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('students.id as id', 'students.nama as studentname', 'students.icno', 'classes.nama as classname', 'class_student.status')
            ->where([
                ['students.id', $id],
            ])
            ->update(
                [
                    'class_student.status' => 0,
                ]
            );


        if ($result) {
            Session::flash('success', 'Murid Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Murid Gagal Dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    public function getStudentDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $oid = $request->oid;

            $classid = $request->classid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            $data = null;

            if (!is_null($hasOrganizaton)) {
                if ($classid == '') {
                    $data = DB::table('students')
                        ->join('class_student', 'class_student.student_id', '=', 'students.id')
                        ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                        ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                        ->select('students.id as id', 'students.nama as studentname', 'students.icno', 'classes.nama as classname', 'class_student.status', 'class_student.start_date')
                        ->where([
                            ['class_organization.organization_id', $oid],
                            ['class_student.status', 1],
                        ])
                        ->orderBy('students.nama')
                        ->get();
                } else {
                    $data = DB::table('students')
                        ->join('class_student', 'class_student.student_id', '=', 'students.id')
                        ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                        ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                        ->select('students.id as id', 'students.nama as studentname', 'students.icno', 'classes.nama as classname', 'class_student.status', 'class_student.start_date')
                        ->where([
                            ['classes.id', $classid],
                            ['class_organization.organization_id', $oid],
                            ['class_student.status', 1],
                        ])
                        ->orderBy('students.nama')
                        ->get();
                }
            }

            $table = Datatables::of($data);

            $table->addColumn('status', function ($row) {
                if ($row->status == '1') {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-success">Aktif</span></div>';

                    return $btn;
                } else {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-danger"> Tidak Aktif </span></div>';

                    return $btn;
                }
            });

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center"></div>';
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('student.edit', $row->id) . '" class="btn btn-primary m-1" target="_blank">Edit</a>';
                // $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button>';
                $btn = $btn . '</div>';
                return $btn;
            });

            $table->rawColumns(['status', 'action']);
            return $table->make(true);
        }
    }

    public function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {

            return Organization::all();
        } else {
            // user role pentadbir n guru 
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->Where(function ($query) {
                    $query->where('organization_user.role_id', '=', 4)
                        ->Orwhere('organization_user.role_id', '=', 5)
                        ->Orwhere('organization_user.role_id', '=', 20)
                        ->Orwhere('organization_user.role_id', '=', 21);
                });
            })->get();
        }
    }

    // public function fetchOrgType(Request $request)
    // {
    //     $organ_id = $request->oid;

    //     $organ_type = DB::table('organizations as o')
    //             ->where('o.id', $organ_id)
    //             ->select('o.type_org as type_org')
    //             ->get();

    //     return response()->json(['success' => $organ_type]);
    // }

    public function fetchClass(Request $request)
    {
        $userId = Auth::id();
        $organ = Organization::find($request->get('oid'));

        if (Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Koop Admin') || Auth::user()->hasRole('Pentadbir Swasta')) {
            $list = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select('classes.id as cid', 'classes.nama as cname')
                ->where([
                    ['class_organization.organization_id', ($organ->parent_org != null ? $organ->parent_org : $organ->id)],
                    ['classes.status', 1]
                ])
                ->orderBy('classes.nama')
                ->get();
        } else {
            $list = DB::table('class_organization')
                ->leftJoin('classes', 'class_organization.class_id', '=', 'classes.id')
                ->leftJoin('organization_user', 'class_organization.organ_user_id', 'organization_user.id')
                ->select('classes.id as cid', 'classes.nama as cname')
                ->where([
                    ['class_organization.organization_id', ($organ->parent_org != null ? $organ->parent_org : $organ->id)],
                    ['classes.status', 1],
                    ['organization_user.user_id', $userId]
                ])
                ->orderBy('classes.nama')
                ->get();
        }
        return response()->json(['success' => $list]);
    }

    public function validateStatus($data)
    {
        $update = false;
        foreach ($data as $d) {
            $check_debt = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
                ->select('students.*')
                ->where('class_student.id', $d->csid)
                ->where('student_fees_new.status', 'Debt')
                ->count();

            if ($check_debt == 0) {
                $update = true;
                DB::table('class_student')
                    ->where('id', $d->csid)
                    ->update(['fees_status' => 'Completed']);
            }
        }
        return $update;
    }

    public function getStudentDatatableFees(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            // $oid = $request->oid;
            $classid = $request->classid;
            $orgId = $request->orgId;
            $hasOrganizaton = $request->hasOrganization;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            //dd($request);
            // dd($end_date);

            $userId = Auth::id();

            if ($classid != '' && !is_null($hasOrganizaton)) {
                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('students.*', 'class_student.fees_status', 'class_student.id as csid', 'class_student.start_date', 'class_student.end_date')
                    ->where([
                        ['classes.id', $classid],
                    ])
                    ->where(function ($query) use ($start_date, $end_date) {
                        $query->whereBetween('class_student.start_date', [$start_date, $end_date])
                            ->orWhere(function ($query) use ($end_date) {
                                $query->whereNull('class_student.end_date')
                                    ->where('class_student.start_date', '<=', $end_date);
                            })
                            ->orWhere(function ($query) use ($start_date, $end_date) {
                                $query->whereNotNull('class_student.end_date')
                                    ->whereBetween('class_student.end_date', [$start_date, $end_date]);
                            })
                            ->orWhere(function ($query) use ($start_date, $end_date) {
                                $query->whereNotNull('class_student.end_date')
                                    ->where('class_student.end_date', '>=', $start_date)
                                    ->where('class_student.start_date', '<=', $end_date);
                            });
                    })
                    ->orderBy('students.nama');

                $update = $this->validateStatus($data->get());
                if ($update) {
                    $data = DB::table('students')
                        ->join('class_student', 'class_student.student_id', '=', 'students.id')
                        ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                        ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                        ->select('students.*', 'class_student.fees_status', 'class_student.id as csid', 'class_student.start_date', 'class_student.end_date')
                        ->where([
                            ['classes.id', $classid],
                        ])
                        ->where(function ($query) use ($start_date, $end_date) {
                            $query->whereBetween('class_student.start_date', [$start_date, $end_date])
                                ->orWhere(function ($query) use ($end_date) {
                                    $query->whereNull('class_student.end_date')
                                        ->where('class_student.start_date', '<=', $end_date);
                                })
                                ->orWhere(function ($query) use ($start_date, $end_date) {
                                    $query->whereNotNull('class_student.end_date')
                                        ->whereBetween('class_student.end_date', [$start_date, $end_date]);
                                })
                                ->orWhere(function ($query) use ($start_date, $end_date) {
                                    $query->whereNotNull('class_student.end_date')
                                        ->where('class_student.end_date', '>=', $start_date)
                                        ->where('class_student.start_date', '<=', $end_date);
                                });

                            //   ->orWhere(function($query) use ($start_date, $end_date) {
                            //       $query->whereNotNull('class_student.end_date')
                            //             ->whereBetween($start_date, ['class_student.start_date', 'class_student.end_date']);
                            //   })
                            //   ->orWhere(function($query) use ($start_date, $end_date) {
                            //     $query->whereNotNull('class_student.end_date')
                            //           ->whereBetween($end_date, ['class_student.start_date', 'class_student.end_date']);
                            // })
    
                        })
                        ->orderBy('students.nama');
                }
                // dd($data->get());
                $table = Datatables::of($data);

                $table->addColumn('gender', function ($row) {
                    if ($row->gender == 'L') {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . 'Lelaki</div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . 'Perempuan</div>';

                        return $btn;
                    }
                });

                $table->addColumn('status', function ($row) use ($orgId, $start_date, $end_date, $request) {

                    if ($row->end_date == null) {
                        $row_end_date = Carbon::tomorrow()->format('Y-m-d');
                        //$row->end_date = Carbon::now()->format('Y-m-d');
                    } else {
                        $row_end_date = Carbon::parse($row->end_date)->endOfDay()->format('Y-m-d H:i:s');
                        ;
                    }

                    // dd($row,$row_end_date);
                    $tranB = DB::table('class_student as cs')
                        ->join('student_fees_new as sfn', 'sfn.class_student_id', 'cs.id')
                        ->join('fees_transactions_new as ftn', 'ftn.student_fees_id', 'sfn.id')
                        ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                        ->join('transactions as t', 't.id', 'ftn.transactions_id')
                        ->where('fn.organization_id', $orgId)
                        ->where('cs.id', $row->csid)
                        ->where('t.status', "Success")
                        ->whereBetween('t.datetime_created', [$row->start_date, $row_end_date])
                        //this line should be disable at 2025
                        //->whereYear('t.datetime_created',substr($row_end_date,0,4))
                        //end
                        ->select('t.id as transaction_id', 't.amount', 't.datetime_created')
                        ->get();

                    $tranA = DB::table('transactions as t')
                        ->leftJoin('fees_new_organization_user as fou', 't.id', 'fou.transaction_id')
                        ->leftJoin('organization_user as ou', 'ou.id', 'fou.organization_user_id')
                        ->leftJoin('organization_user_student as ous', 'ous.organization_user_id', 'ou.id')
                        ->leftJoin('fees_new as fn', 'fn.id', 'fou.fees_new_id')
                        ->distinct()
                        ->where('ous.student_id', $row->id)
                        ->where('fn.organization_id', $orgId)
                        ->where('t.status', 'Success')
                        ->whereBetween('t.datetime_created', [$row->start_date, $row_end_date])
                        //this line should be disable at 2025
                        ->whereYear('t.datetime_created', substr($row_end_date, 0, 4))
                        //end
                        ->select('t.id as transaction_id', 't.amount', 't.datetime_created')
                        ->get();


                    $combined = $tranA->concat($tranB);

                    $unique = $combined->unique('transaction_id');


                    if ($request->show_all_payments != "true") {
                        $unique = $unique->filter(function ($item) use ($start_date, $end_date) {
                            return $item->datetime_created >= $start_date && $item->datetime_created <= $end_date;
                        });
                    }

                    if (count($unique) > 0) {
                        $btn = '<div class="d-flex  align-items-center flex-column">';
                        foreach ($unique as $t) {

                            $href = route('receipttest', ['transaction_id' => $t->transaction_id]);
                            $btn = $btn . '<a href ="' . $href . '" target="_blank" >RM ' . number_format($t->amount, 2, '.', '') . '</a>';
                        }
                        $btn = $btn . '</div>';
                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Belum Bayar </span></div>';
                        return $btn;
                    }
                });



                $table->rawColumns(['gender', 'status']);
                $table->removeColumn('start_date');
                $table->removeColumn('end_date');

                //  dd($table->make(true));
                return $table->make(true);
            }

            // dd($data->oid);
        }
    }

    public function getStudentSwastaDatatableFees(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            // $oid = $request->oid;
            $classid = $request->classid;
            $orgId = $request->orgId;
            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($classid != '' && !is_null($hasOrganizaton)) {
                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('students.*', 'class_student.fees_status', 'class_student.id as csid', 'class_student.start_date as cs_startdate')
                    ->where([
                        ['classes.id', $classid],
                        ['class_student.status', 1],
                    ])
                    ->orderBy('students.nama');
                $update = $this->validateStatus($data->get());
                if ($update) {
                    $data = DB::table('students')
                        ->join('class_student', 'class_student.student_id', '=', 'students.id')
                        ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                        ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                        ->select('students.*', 'class_student.fees_status', 'class_student.id as csid', 'class_student.start_date as cs_startdate')
                        ->where([
                            ['classes.id', $classid],
                            ['class_student.status', 1],
                        ])
                        ->orderBy('students.nama');
                }
                $table = Datatables::of($data);

                $table->addColumn('gender', function ($row) {
                    if ($row->gender == 'L') {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . 'Lelaki</div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . 'Perempuan</div>';

                        return $btn;
                    }
                });

                $table->addColumn('yuran', function ($row) {
                    $fees_list = DB::table('fees_new as fn')
                        ->join('student_fees_new as sfn', 'sfn.fees_id', '=', 'fn.id')
                        ->join('fees_recurring as fr', 'fr.student_fees_new_id', '=', 'sfn.id')
                        ->where('sfn.class_student_id', $row->csid)
                        ->select('fn.*', 'fr.finalAmount as fr_finalAmount', 'sfn.status as sfn_status')
                        ->get();

                    if ($fees_list) {
                        $btn = '<select id="select" name="select" class="form-control">';
                        foreach ($fees_list as $fl) {
                            $status = 'Belum Bayar';
                            if ($fl->sfn_status == 'Paid') {
                                $status = 'Telah Bayar';
                            }
                            $btn = $btn . '<option>' . $fl->name . ' : RM' . number_format((float) $fl->fr_finalAmount, 2, '.', '') . ' ( ' . $status . ' )</option>';
                        }
                        $btn = $btn . '</select>';
                        return $btn;
                    } else {
                        $btn = '<select id="select" name="select" class="form-control"><option>-- Tiada Yuran --</option></select>';
                        return $btn;
                    }
                });

                $table->addColumn('status', function ($row) use ($orgId) {

                    $tranB = DB::table('class_student as cs')
                        ->join('student_fees_new as sfn', 'sfn.class_student_id', 'cs.id')
                        ->join('fees_transactions_new as ftn', 'ftn.student_fees_id', 'sfn.id')
                        ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                        ->join('transactions as t', 't.id', 'ftn.transactions_id')
                        ->where('fn.organization_id', $orgId)
                        ->where('cs.id', $row->csid)
                        ->where('t.status', "Success")
                        ->select('t.id as transaction_id', 't.amount')
                        ->get();

                    $tranA = DB::table('transactions as t')
                        ->leftJoin('fees_new_organization_user as fou', 't.id', 'fou.transaction_id')
                        ->leftJoin('organization_user as ou', 'ou.id', 'fou.organization_user_id')
                        ->leftJoin('organization_user_student as ous', 'ous.organization_user_id', 'ou.id')
                        ->leftJoin('fees_new as fn', 'fn.id', 'fou.fees_new_id')
                        ->distinct()
                        ->where('ous.student_id', $row->id)
                        ->where('fn.organization_id', $orgId)
                        ->where('t.status', 'Success')
                        ->select('t.id as transaction_id', 't.amount')
                        ->get();


                    $combined = $tranA->concat($tranB);

                    $unique = $combined->unique('transaction_id');

                    if (count($unique) > 0) {
                        $btn = '<div class="d-flex  align-items-center flex-column">';
                        foreach ($unique as $t) {

                            $href = route('receipttest', ['transaction_id' => $t->transaction_id]);
                            $btn = $btn . '<a href ="' . $href . '" target="_blank" >RM ' . number_format($t->amount, 2, '.', '') . '</a>';
                        }
                        $btn = $btn . '</div>';
                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Belum Selesai </span></div>';
                        return $btn;
                    }
                });



                $table->rawColumns(['gender', 'yuran', 'status']);
                return $table->make(true);
            }

            // dd($data->oid);
        }
    }

    public function generatePDFByClass(Request $request)
    {
        try {
            $class_id = $request->class_id;
            $class = ClassModel::where('id', $class_id)->first();

            $get_organization = DB::table('organizations')
                ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('organizations.*', 'classes.nama as classname')
                ->where([
                    ['classes.id', $class_id],
                ])
                ->first();

            $data = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('students.*', 'class_student.fees_status')
                ->where([
                    ['classes.id', $class_id],
                    ['class_student.status', 1],
                ])
                ->orderBy('students.nama')
                ->get();

            $pdf = PDF::loadView('fee.report-search.template-pdf', compact('data', 'get_organization'));

            return $pdf->download($class->nama . '.pdf');
        } catch (\Throwable $e) {
        }
    }

    // method to get user
    // if user does not exist, create new user
    private function getOrCreateUser($student)
    {
        $parentUser = DB::table('users as u')
            ->where(function ($query) use ($student) {
                $query->where('u.telno', $student->parentTelno)
                    ->orWhere('u.icno', $student->parentTelno);
            })
            ->first();

        // if user does not exist
        if (empty($parentUser)) {
            // validation for email and telno
            if (isset($student->parentEmail) && !empty($student->parentEmail)) {
                // if parent have email
                $validator = Validator::make((array) $student, [
                    'parentTelno' => 'required|unique:users,telno',
                    'parentEmail' => 'unique:users,email',
                ]);
            } else {
                $validator = Validator::make((array) $student, [
                    'parentTelno' => 'required|unique:users,telno',
                ]);
            }

            // return back if email or telno invalid
            if ($validator->fails()) {
                // Handle validation failure, return response, or redirect back with errors
                // For example:
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // create new parent 
            $newparent = new Parents([
                'name' => $student->parentName,
                'email' => isset($student->parentEmail) ? $student->parentEmail : null,
                'password' => Hash::make('abc123'),
                'telno' => $student->parentTelno,
                'remember_token' => Str::random(40),
            ]);

            $newparent->save();

            // get the user again after user is created to check and create organization_user
            $parentUser = DB::table('users')
                ->where('id', $newparent->id)
                ->first();
        }

        return $parentUser;
    }

    private function getOrCreateParentRelationship($parentUser, $co)
    {
        // check if organization_user with parent role exists
        $parentRole = DB::table('organization_user')
            ->where('user_id', $parentUser->id)
            ->where('organization_id', $co->oid)
            ->where('role_id', 6)
            ->first();

        // if not create a new organization_user with parent role (role_id of 6)
        if (empty($parentRole)) {
            DB::table('organization_user')->insert([
                'organization_id' => $co->oid,
                'user_id' => $parentUser->id,
                'role_id' => 6,
                'start_date' => now(),
                'status' => 1,
            ]);
        }
    }

    public function compareAddNewStudent(Request $request)
    {
        $student = json_decode($request->student);

        $classid = $student->classId;

        $co = DB::table('class_organization')
            ->select('id', 'organization_id as oid')
            ->where('class_id', $classid)
            ->first();

        // search if parent (or teacher) user exists
        // if not create the user
        $parentUser = $this->getOrCreateUser($student);

        if ($parentUser instanceof JsonResponse) {
            // return the json response to the frontend if error happens
            return $parentUser;
        }

        // check if organization_user with parent role exists
        $this->getOrCreateParentRelationship($parentUser, $co);

        $this->assignStudentToParent($parentUser->id, $student->parentTelno, $student, $classid, $parentUser);
    }

    public function checkFeesBCWhenClassChanged($ifExitsCateBC, $studentFeesIDs, $student, $level_id, $new_class_student_id, $old_class_student_id)
    {

        $new_class = DB::table('class_student as cs')
            ->join('class_organization as co', 'co.id', 'cs.organclass_id')
            ->where('cs.id', $new_class_student_id)->select('co.class_id')->first()->class_id;

        if (!$ifExitsCateBC->isEmpty()) {
            foreach ($ifExitsCateBC as $kateBC) {
                $target = json_decode($kateBC->target);

                if (isset($target->gender)) {
                    if ($target->gender != $student->gender) {
                        continue;
                    }
                }

                if ($target->data == "All_Level" || $target->data == $level_id) {
                    if (in_array($kateBC->id, $studentFeesIDs)) {
                        $res = DB::table('student_fees_new')
                            ->where('class_student_id', $old_class_student_id)
                            ->where('fees_id', $kateBC->id)
                            ->where('status', 'Debt')
                            ->update([
                                'class_student_id' => $new_class_student_id
                            ]);
                    } else {
                        DB::table('student_fees_new')->insert([
                            'status' => 'Debt',
                            'fees_id' => $kateBC->id,
                            'class_student_id' => $new_class_student_id
                        ]);
                    }
                } else if (is_array($target->data)) {
                    if (in_array($new_class, $target->data)) {
                        if (in_array($kateBC->id, $studentFeesIDs)) {
                            DB::table('student_fees_new')
                                ->where('class_student_id', $old_class_student_id)
                                ->where('fees_id', $kateBC->id)
                                ->where('status', 'Debt')
                                ->update([
                                    'class_student_id' => $new_class_student_id
                                ]);
                        } else {
                            DB::table('student_fees_new')->insert([
                                'status' => 'Debt',
                                'fees_id' => $kateBC->id,
                                'class_student_id' => $new_class_student_id
                            ]);
                        }
                    } else {

                        $delete = DB::table('student_fees_new as sfn')
                            ->where([
                                ['sfn.fees_id', $kateBC->id],
                                ['sfn.class_student_id', $old_class_student_id],
                                ['sfn.status', '=', 'Debt'],
                            ])
                            ->get()->pluck('id');
                        DB::table('student_fees_new')->whereIn('id', $delete)->delete();
                    }
                }
            }
        }
    }

    private function updateOrAssignFeesRecurring($kateRec, $studentFeesIDs, $student, $cs_after_update, $new_class_student_id)
    {
        $datestarted = Carbon::parse($kateRec->start_date); //back to original date without format (string to datetime)
        $dateend = Carbon::parse($kateRec->end_date);
        $totalDay = ($datestarted->diffInDays($dateend)) + 1;
        $cs_startdate = Carbon::parse($cs_after_update->start_date);
        $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
        if ($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day) {
            $totalDayLeft = $totalDay;
        }
        $finalAmount = $kateRec->totalAmount * ($totalDayLeft / $totalDay);
        if ($finalAmount > $kateRec->totalAmount) {
            $finalAmount = $kateRec->totalAmount;
        }

        if (in_array($kateRec->id, $studentFeesIDs)) {
            $ifStudentFeesNewExist = DB::table('student_fees_new as sfn')
                ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                ->where('sfn.fees_id', $kateRec->id)
                ->where('cs.student_id', $student->studentId)
                ->first();

            DB::table('fees_recurring as fr')
                ->where('fr.student_fees_new_id', $ifStudentFeesNewExist->id)
                ->update([
                    'totalDayLeft' => $totalDayLeft,
                    'finalAmount' => $finalAmount,
                    'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                    'created_at' => now(),
                ]);
        } else {
            $student_fees_new = DB::table('student_fees_new')->insertGetId([
                'status' => 'Debt',
                'fees_id' => $kateRec->id,
                'class_student_id' => $new_class_student_id
            ]);

            DB::table('fees_recurring')->insert([
                'student_fees_new_id' => $student_fees_new,
                'totalDay' => $totalDay,
                'totalDayLeft' => $totalDayLeft,
                'finalAmount' => $finalAmount,
                'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                'created_at' => now(),
            ]);
        }
    }

    private function deleteRecurringFee($kateRec, $class_student_details)
    {
        // get the id student_fees_new to delete
        $delete = DB::table('student_fees_new as sfn')
            ->where([
                ['sfn.fees_id', $kateRec->id],
                ['sfn.class_student_id', $class_student_details->id],
                ['sfn.status', '=', 'Debt'],
            ])
            ->get()->pluck('id');

        // delete fees_recurring
        DB::table("fees_recurring")
            ->whereIn('student_fees_new_id', $delete)
            ->delete();

        // delete the student_fees_new
        DB::table('student_fees_new')->whereIn('id', $delete)->delete();
    }

    public function compareTransferStudent(Request $request)
    {
        set_time_limit(300);

        $student = json_decode($request->student);

        $co = DB::table('class_organization as co')
            ->where('co.class_id', $student->newClass)
            ->first();

        $class = DB::table('classes as c')
            ->where('c.id', $student->newClass)
            ->first();

        // old class_student before moving class
        $class_student = DB::table('class_organization as co')
            ->join('class_student as cs', 'cs.organclass_id', 'co.id')
            ->join('students as s', 's.id', '=', 'cs.student_id')
            ->where('s.nama', $student->studentName)
            ->where('s.parent_tel', '=', $student->parentTelno)
            ->where('cs.status', 1)
            ->select('cs.id')
            ->pluck('cs.id')
            ->toArray();

        // make the old class_student inactive
        DB::table('class_student')
            ->whereIn('id', $class_student)
            ->update([
                'end_date' => now(),
                'status' => 0
            ]);

        // getting the first in the list
        $class_student_details = DB::table('class_organization as co')
            ->join('class_student as cs', 'cs.organclass_id', 'co.id')
            ->join('students as s', 's.id', '=', 'cs.student_id')
            ->whereIn('cs.id', $class_student)
            ->where('co.class_id', $student->oldClassId)
            ->select('co.*', 'cs.*', 'cs.id as class_student_id')
            ->first();

        // insert a new class_student for the new class
        $new_class_student_id = DB::table('class_student')->insertGetId([
            'organclass_id' => $co->id,
            'student_id' => $student->studentId,
            'status' => 1,
            'start_date' => now(),
        ]);

        // if inactive or graduated, do nothing and return
        if ($class->levelid <= 0) {
            return;
        }

        // get all kategori B & C fees from current organization
        $ifExitsCateBC = DB::table('fees_new')
            ->whereIn('category', ['Kategori B', 'Kategori C'])
            ->where('organization_id', $co->organization_id)
            ->where('status', 1)
            ->get();

        // get the previous student_fees_id
        $studentFeesIDs = DB::table('student_fees_new as sfn')
            ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
            ->whereIn('sfn.class_student_id', [$new_class_student_id, $class_student_details->id])
            ->get()
            ->pluck('fees_id')
            ->toArray();

        // assign appropriate fees for kategori B & C to student
        $this->checkFeesBCWhenClassChanged($ifExitsCateBC, $studentFeesIDs, $student, $class->levelid, $new_class_student_id, $class_student_details->id);

        // get Kategori Berulang fees for current organization
        $ifExitsCateRecurring = DB::table('fees_new')
            ->where('category', 'Kategori Berulang')
            ->where('organization_id', $co->organization_id)
            ->where('status', 1)
            ->get();

        // get the class_student after updating 
        $cs_after_update = DB::table('class_student as cs')
            ->where('cs.student_id', $student->studentId)
            ->first();

        // loop to assign each appropriate recurring fee to student
        foreach ($ifExitsCateRecurring as $kateRec) {
            // if the recurring fee is still active when the student move class
            if ($kateRec->end_date > $cs_after_update->start_date) {
                $target = json_decode($kateRec->target);

                if (isset($target->gender) && $target->gender != $student->gender) {
                    continue;
                }

                if ($target->data == "All_Level" || $target->data == $class->levelid) {
                    // if the target of the recurring fee is all level, update or assign the recurring fee to the student
                    $this->updateOrAssignFeesRecurring($kateRec, $studentFeesIDs, $student, $cs_after_update, $new_class_student_id);

                } else if (is_array($target->data)) {
                    if (in_array($student->newClass, $target->data)) {
                        // if the student's class is in the target of the recurring fee, update or assign the recurring fee to the student
                        $this->updateOrAssignFeesRecurring($kateRec, $studentFeesIDs, $student, $cs_after_update, $new_class_student_id);

                    } else {
                        // TODO: after transfering class, if the student's class is not in the fees_recurring target list, set the student_fees_new status to be 0
                        $this->deleteRecurringFee($kateRec, $class_student_details);
                    }
                }
            } else {
                // TODO: if the recurring fee is inactive, set student_fees_new status to be 0 
                $this->deleteRecurringFee($kateRec, $class_student_details);
            }
        }
    }

    public function compareTransferStudentDiffOrg(Request $request)
    {
        set_time_limit(300);

        if ($request->secureKey != getenv('SECURE_ADMIN_KEY')) {
            //dd($request->secureKey);
            //dd(getenv('SECURE_ADMIN_KEY'));
            // return response()->json(["hello"=>"Success"]);

            return redirect()->back()->with('error', 'Invalid Secure Key');
        }
        //dd('here');

        $student = json_decode($request->student);

        $co = DB::table('class_organization as co')
            ->where('co.class_id', $student->newClass)
            ->first();
        $class = DB::table('classes as c')
            ->where('c.id', $student->newClass)
            ->first();

        $class_student = DB::table('class_organization as co')
            ->join('class_student as cs', 'cs.organclass_id', 'co.id')
            ->join('students as s', 's.id', '=', 'cs.student_id')
            ->where('s.nama', $student->studentName)
            ->where('s.parent_tel', '=', $student->parentTelno)
            ->where('cs.status', 1)
            ->select('cs.id')
            ->pluck('cs.id')
            ->toArray();

        $newparent = DB::table('users')
            ->where('telno', '=', $student->parentTelno)
            ->first();

        // dd($newparent);
        if (empty($newparent)) {

            //if parent have email
            if (isset($student->parentEmail) && !empty($student->parentEmail)) {
                $validator = Validator::make((array) $student, [
                    'parentTelno' => 'required|unique:users,telno',
                    'parentEmail' => 'unique:users,email',
                ]);

                if ($validator->fails()) {
                    // Handle validation failure, return response, or redirect back with errors
                    // For example:
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $newparent = new Parents([
                    'name' => $student->parentName,
                    'email' => $student->parentEmail,
                    'password' => Hash::make('abc123'),
                    'telno' => $student->parentTelno,
                    'remember_token' => Str::random(40),
                ]);
                $newparent->save();
            } else {



                $validator = Validator::make((array) $student, [
                    'parentTelno' => 'required|unique:users,telno',
                ]);

                if ($validator->fails()) {
                    // Handle validation failure, return response, or redirect back with errors
                    // For example:
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $newparent = new Parents([
                    'name' => $student->parentName,
                    //'email'          =>  $request->get('parent_email'),
                    'password' => Hash::make('abc123'),
                    'telno' => $student->parentTelno,
                    'remember_token' => Str::random(40),
                ]);
                $newparent->save();
            }
        }



        // add parent role
        $parentRole = DB::table('organization_user')
            ->where('user_id', $newparent->id)
            ->where('organization_id', $co->organization_id)
            ->where('role_id', 6)
            ->first();


        if (empty($parentRole)) {
            $ou_id = DB::table('organization_user')->insertGetId([
                'organization_id' => $co->organization_id,
                'user_id' => $newparent->id,
                'role_id' => 6,
                'start_date' => now(),
                'status' => 1,
            ]);

            DB::table('organization_user_student')
                ->where('student_id', $student->studentId)
                ->delete();

            DB::table('organization_user_student')
                ->insert([
                    'organization_user_id' => $ou_id,
                    'student_id' => $student->studentId
                ]);
        }

        DB::table('class_student')
            ->whereIn('id', $class_student)
            ->update([
                'end_date' => now(),
                'status' => 0
            ]);

        $class_student_details = DB::table('class_organization as co')
            ->join('class_student as cs', 'cs.organclass_id', 'co.id')
            ->join('students as s', 's.id', '=', 'cs.student_id')
            ->whereIn('cs.id', $class_student)
            ->where('co.class_id', $student->oldClassId)
            ->select('co.*', 'cs.*', 'cs.id as class_student_id')
            ->first();

        $new_class_student_id = DB::table('class_student')->insertGetId([
            'organclass_id' => $co->id,
            'student_id' => $student->studentId,
            'status' => 1,
            'start_date' => now(),
            //'fee_status'=>'Not Complete'

        ]);

        //if inactive or graduated will not run this 
        if ($class->levelid > 0) {
            $ifExitsCateBC = DB::table('fees_new')
                ->whereIn('category', ['Kategori B', 'Kategori C'])
                ->where('organization_id', $co->organization_id)
                ->where('status', 1)
                ->get();

            if (isset($class_student_details) && isset($class_student_details->class_student_id)) {
                $class_student_details = $class_student_details->class_student_id;
            }



            $studentHaveFees = DB::table('student_fees_new as sfn')
                ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                ->whereIn('sfn.class_student_id', [$new_class_student_id, $class_student_details ?? 0])
                ->get();

            $studentFeesIDs = $studentHaveFees->pluck('fees_id')->toArray();



            $this->checkFeesBCWhenClassChanged($ifExitsCateBC, $studentFeesIDs, $student, $class->levelid, $new_class_student_id, $class_student_details->id ?? 0);


            $ifExitsCateRecurring = DB::table('fees_new')
                ->where('category', 'Kategori Berulang')
                ->where('organization_id', $co->organization_id)
                ->where('status', 1)
                ->get();

            $cs_after_update = DB::table('class_student as cs')
                ->where('cs.student_id', $student->studentId)
                ->first();

            if (!$ifExitsCateRecurring->isEmpty()) {
                foreach ($ifExitsCateRecurring as $kateRec) {

                    if ($kateRec->end_date > $cs_after_update->start_date) {
                        $target = json_decode($kateRec->target);

                        if (isset($target->gender)) {
                            if ($target->gender != $studentData->gender) {
                                continue;
                            }
                        }

                        if ($target->data == "All_Level" || $target->data == $class->levelid) {

                            $datestarted = Carbon::parse($kateRec->start_date); //back to original date without format (string to datetime)
                            $dateend = Carbon::parse($kateRec->end_date);
                            $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                            $cs_startdate = Carbon::parse($cs_after_update->start_date);
                            $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                            if ($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day) {
                                $totalDayLeft = $totalDay;
                            }
                            $finalAmount = $kateRec->totalAmount * ($totalDayLeft / $totalDay);
                            if ($finalAmount > $kateRec->totalAmount) {
                                $finalAmount = $kateRec->totalAmount;
                            }

                            if (in_array($kateRec->id, $studentFeesIDs)) {
                                // continue;
                                $ifStudentFeesNewExist = DB::table('student_fees_new as sfn')
                                    ->where('sfn.fees_id', $kateRec->id)
                                    ->where('sfn.class_student_id', $student->studentId)
                                    ->first();

                                DB::table('fees_recurring as fr')
                                    ->where('fr.student_fees_new_id', $ifStudentFeesNewExist->id)
                                    ->update([
                                        'totalDayLeft' => $totalDayLeft,
                                        'finalAmount' => $finalAmount,
                                        'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                                        'created_at' => now(),
                                    ]);
                            } else {
                                // DB::table('student_fees_new')->insert([
                                //     'status'            => 'Debt',
                                //     'fees_id'           =>  $kateRec->id,
                                //     'class_student_id'  =>  $class_student_details->id
                                // ]);

                                $student_fees_new = DB::table('student_fees_new')->insertGetId([
                                    'status' => 'Debt',
                                    'fees_id' => $kateRec->id,
                                    'class_student_id' => $new_class_student_id
                                ]);

                                DB::table('fees_recurring')->insert([
                                    'student_fees_new_id' => $student_fees_new,
                                    'totalDay' => $totalDay,
                                    'totalDayLeft' => $totalDayLeft,
                                    'finalAmount' => $finalAmount,
                                    'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                                    'created_at' => now(),
                                ]);
                            }
                        } else if (is_array($target->data)) {
                            if (in_array($student->newClass, $target->data)) {

                                $datestarted = Carbon::parse($kateRec->start_date); //back to original date without format (string to datetime)
                                $dateend = Carbon::parse($kateRec->end_date);
                                $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                                $cs_startdate = Carbon::parse($cs_after_update->start_date);
                                $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                                if ($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day) {
                                    $totalDayLeft = $totalDay;
                                }
                                $finalAmount = $kateRec->totalAmount * ($totalDayLeft / $totalDay);
                                if ($finalAmount > $kateRec->totalAmount) {
                                    $finalAmount = $kateRec->totalAmount;
                                }

                                if (in_array($kateRec->id, $studentFeesIDs)) {
                                    // continue;
                                    $ifStudentFeesNewExist = DB::table('student_fees_new as sfn')
                                        ->where('sfn.fees_id', $kateRec->id)
                                        ->where('sfn.class_student_id', $student->studentId)
                                        ->first();

                                    DB::table('fees_recurring as fr')
                                        ->where('fr.student_fees_new_id', $ifStudentFeesNewExist->id)
                                        ->update([
                                            'totalDayLeft' => $totalDayLeft,
                                            'finalAmount' => $finalAmount,
                                            'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                                            'created_at' => now(),
                                        ]);
                                } else {
                                    // DB::table('student_fees_new')->insert([
                                    //     'status'            => 'Debt',
                                    //     'fees_id'           =>  $kateRec->id,
                                    //     'class_student_id'  =>  $class_student_details->id
                                    // ]);

                                    $student_fees_new = DB::table('student_fees_new')->insertGetId([
                                        'status' => 'Debt',
                                        'fees_id' => $kateRec->id,
                                        'class_student_id' => $new_class_student_id
                                    ]);

                                    DB::table('fees_recurring')->insert([
                                        'student_fees_new_id' => $student_fees_new,
                                        'totalDay' => $totalDay,
                                        'totalDayLeft' => $totalDayLeft,
                                        'finalAmount' => $finalAmount,
                                        'desc' => 'RM' . $kateRec->totalAmount . '*' . $totalDayLeft . '/' . $totalDay,
                                        'created_at' => now(),
                                    ]);
                                }
                            } else {
                                // $delete=DB::table('student_fees_new as sfn')
                                //         ->where('sfn.fees_id',$kateRec->id)
                                //         ->where('sfn.class_student_id',$class_student_details->id)
                                //         ->where('sfn.status',"Debt")
                                //         ->delete();

                                $delete = DB::table('student_fees_new as sfn')
                                    ->where([
                                        ['sfn.fees_id', $kateRec->id],
                                        ['sfn.class_student_id', $class_student_details->id],
                                        ['sfn.status', '=', 'Debt'],
                                    ])
                                    ->get()->pluck('id');
                                DB::table('student_fees_new')->whereIn('id', $delete)->delete();
                                //return response()->json(['data'=>$delete]);  
                            }
                        }
                    } else {
                        // $delete=DB::table('student_fees_new as sfn')
                        //         ->where('sfn.fees_id',$kateRec->id)
                        //         ->where('sfn.class_student_id',$class_student_details>id)
                        //         ->where('sfn.status',"Debt")
                        //         ->delete();
                        $delete = DB::table('student_fees_new as sfn')
                            ->where([
                                ['sfn.fees_id', $kateRec->id],
                                ['sfn.class_student_id', $class_student_details->id],
                                ['sfn.status', '=', 'Debt'],
                            ])
                            ->get()->pluck('id');
                        DB::table('student_fees_new')->whereIn('id', $delete)->delete();
                        //return response()->json(['data'=>$delete]);
                    }
                }
            }
        }
    }
}
