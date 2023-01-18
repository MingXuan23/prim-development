<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Exports\OutingExport;
use App\Exports\ResidentExport;
use App\Exports\AllRequestExport;
use App\Exports\DormExport;
use App\Imports\DormImport;
use App\Imports\ResidentImport;
use App\Exports\AllStudentlistExport;
use App\Exports\DormStudentlistExport;
use App\Exports\AllCategoryExport;
use App\Exports\CategoryExport;
use Illuminate\Http\Request;
use App\Models\Dorm;
use App\Models\Outing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeacherExport;
use App\Imports\TeacherImport;
use App\Models\Organization;
use App\Models\OrganizationRole;
use App\User;
use Illuminate\Validation\Rule;
use App\Models\TypeOrganization;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyMail;
use App\Mail\NotifyArrive;
use App\Mail\NotifyIn;
use App\Mail\NotifyApproval;
use App\Mail\NotifyOut;
use PDF;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class DormController extends Controller
{
    private $categoryReal = array("BALIK KECEMASAN", "BALIK KHAS", "OUTINGS", "BALIK WAJIB");
    private $roles;

    // public function __construct(Request $request)
    // {
    //     $this->roles = $request->route()->parameter('id');
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    //
    //
    //index functions
    public function indexRequest($id)
    {
        if(Auth::user()->hasRole('Superadmin')){
            $organization = Organization::all();
        }
        else{
            $organization = DB::table('organizations')
            ->join('organization_user as ou', 'ou.organization_id', '=', 'organizations.id')
            ->where([
                ['ou.user_id', Auth::user()->id],
                ['ou.role_id', $id]
            ])
            ->select('organizations.id as id', 'organizations.nama')
            ->get();
        }

        $roles = $id;

        if(count($organization) > 0)
        {
            if(Auth::user()->hasRole('Superadmin')){
                $checkin = DB::table('organization_roles')
                    ->join('organization_user as ou', 'ou.role_id', '=', 'organization_roles.id')
                    ->where([
                        ['ou.user_id', Auth::user()->id],
                    ])
                    ->value('ou.check_in_status');
            }
            else{
                $checkin = DB::table('organization_roles')
                ->join('organization_user as ou', 'ou.role_id', '=', 'organization_roles.id')
                ->where([
                    ['ou.user_id', Auth::user()->id],
                    ['ou.organization_id', $organization[0]->id],
                ])
                ->value('ou.check_in_status');
            }

            $isblacklisted = DB::table('students')
                ->join('class_student as cs', 'cs.student_id', '=', 'students.id')
                ->join('organization_user_student as ous', 'ous.student_id', '=', 'students.id')
                ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
                ->where([
                    // ['ou.organization_id', $organization[0]->id],
                    ['ou.user_id', Auth::user()->id],
                    ['cs.blacklist', 1]
                ])
                ->select('cs.blacklist', 'students.nama')
                ->get();
            
            $checkNum = $roles."|".$checkin;
            // dd($checkNum);
            return view('dorm.index', compact('roles', 'checkin', 'checkNum', 'organization', 'isblacklisted'));
        }

        return view('errors.404');
        
    }

    public function indexReportAll()
    {
        $organization = $this->getOrganizationByRole();
        
        $roles = DB::table('organization_roles')
            ->join('organization_user as ou', 'ou.role_id', '=', 'organization_roles.id')
            ->where([
                ['ou.user_id', Auth::user()->id],
            ])
            ->select('organization_roles.nama', 'ou.organization_id')
            ->get();
        return view('dorm.report.allStudent', compact('organization', 'roles'));
    }

    public function indexOuting()
    {
        // 
        $organization = $this->getOrganizationByRole();
        $roles = DB::table('organization_roles')
            ->join('organization_user as ou', 'ou.role_id', '=', 'organization_roles.id')
            ->where([
                ['ou.user_id', Auth::user()->id],
            ])
            ->select('organization_roles.nama', 'ou.organization_id')
            ->get();

        return view('dorm.outing.index', compact('organization', 'roles'));
    }

    public function indexReasonOuting()
    {
        // 
        $organization = $this->getOrganizationByUserId();

        return view('dorm.classification.index', compact('organization'));
    }

    public function indexResident($id)
    {
        // 
        $organization = $this->getOrganizationByRole();

        $roles = DB::table('organization_roles')
            ->join('organization_user as ou', 'ou.role_id', '=', 'organization_roles.id')
            ->where([
                ['ou.user_id', Auth::user()->id],
            ])
            ->select('organization_roles.nama', 'ou.organization_id')
            ->get();

        if (Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Warden')) 
        {
            $dorm = DB::table('dorms')
                ->where([
                    ['dorms.id', $id],
                ])
                ->select('dorms.organization_id', 'dorms.id as id', 'dorms.name')
                ->orderBy('dorms.name')
                ->first();
        }

        if(isset($dorm) || Auth::user()->hasRole('Superadmin')){
            $organ = $organization->toArray();
            foreach($organ as $organ){
                if(in_array($dorm->organization_id, $organ) || Auth::user()->hasRole('Superadmin')){
                    foreach($roles as $roles){
                        if(($dorm->organization_id == $roles->organization_id && $roles->nama != "Penjaga" && $roles->nama != "Guard") || Auth::user()->hasRole('Superadmin')){
                            return view("dorm.resident.index", compact('dorm', 'organization', 'roles'));
                        }
                    }
                }
            }
        }

        return view('errors.404');
    }

    public function indexDorm()
    {
        // 
        $organization = $this->getOrganizationByUserId();


        $dormlist = DB::table('dorms')
            ->select('id', 'name')
            ->get();


        return view('dorm.management.index', compact('organization', 'dormlist'));
    }

    public function indexStudentlist()
    {
        // 
        $organization = $this->getOrganizationByUserId();

        $studentlist = DB::table('class_student')
            ->whereNotNull('dorm_id')
            ->get();

        $dormlist = DB::table('dorms')
            ->select('id', 'name')
            ->get();


        // return redirect('/sekolah/dorm/getAllStudentlistDatatable')->with('success', 'Dorms have been added successfully');

        return view('dorm.studentlist.index', compact('organization', 'studentlist', 'dormlist'));
    }

    //
    //
    //import and export functions
    public function outingexport(Request $request)
    {
        $this->validate($request, [
            'organ'      =>  'required',
        ]);

        $filename = DB::table('organizations')
            ->where('organizations.id', $request->organ)
            ->value('organizations.nama');

        return Excel::download(new OutingExport($request->organ), $filename . ' masa outing.xlsx');
    }

    public function allrequestexport(Request $request)
    {
        $this->validate($request, [
            'organExport'      =>  'required',
            'from'             =>  'required',
            'to'               =>  'required',
        ]);
        return Excel::download(new AllRequestExport($request->organExport, $request->from, $request->to), 'Laporan Permintaan Keluar(Kategori).xlsx');
    }

    public function dormexport(Request $request)
    {
        return Excel::download(new DormExport($request->organ), 'dorm.xlsx');
    }

    public function allstudentlistexport(Request $request)
    {
        return Excel::download(new AllStudentlistExport($request->organ), 'studentlist.xlsx');
    }

    public function dormstudentlistexport(Request $request)
    {
        return Excel::download(new DormStudentlistExport($request->organ, $request->dorm), 'studentlist.xlsx');
    }

    public function residentexport(Request $request)
    {
        $this->validate($request, [
            'organExport'      =>  'required',
            'dormExport'      =>  'required',
        ]);

        $filename = DB::table('dorms')
            ->where('dorms.id', $request->dormExport)
            ->value('dorms.name');

        // dd($filename);

        return Excel::download(new ResidentExport($request->organExport, $request->dormExport), $filename . ' pelajar.xlsx');
    }

    public function dormimport(Request $request)
    {
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        if (!in_array($etx, $formats)) {

            return redirect('/sekolah/dorm/indexDorm')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }

        Excel::import(new DormImport($request->organ), public_path('/uploads/excel/' . $namaFile));

        return redirect('/sekolah/dorm/indexDorm')->with('success', 'Dorms have been added successfully');
    }

    public function residentimport(Request $request)
    {
        // dd($request->dorm);
        $file       = $request->file('file1');
        $namaFile   = $file->getClientOriginalName();
        $check = 0;
        if ($check == 0) {
            $file->move('uploads/excel/', $namaFile);
        }

        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        if (!in_array($etx, $formats)) {
            return redirect('/sekolah/dorm/indexDorm')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }

        //get the accomodate number for the particular dorm
        $accomodate_number = DB::table('dorms')
            ->where('id', $request->dorm)
            ->value('accommodate_no');

        $student_inside = DB::table('dorms')
            ->where('id', $request->dorm)
            ->value('student_inside_no');

        if ($student_inside <= $accomodate_number) {
            Excel::import(new ResidentImport($request->dorm), public_path('/uploads/excel/' . $namaFile));
            return redirect('/sekolah/dorm/indexDorm')->with('success', 'Residents have been added successfully');
        } else
            return redirect('/sekolah/dorm/indexDorm')->with('fail', 'Residents have not been added successfully because the dorm is full');
    }

    public function allcategoryexport(Request $request)
    {
        $studentName = DB::table('class_student')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->where('class_student.id', '=', $request->studentid)
            ->value('students.nama');
        // ->first();

        // dd($studentName);

        return Excel::download(new AllCategoryExport($request->studentid, $request->fromTime, $request->untilTime), $studentName . ' report.xlsx');
    }

    public function categoryexport(Request $request)
    {

        $studentName = DB::table('class_student')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->where('class_student.id', '=', $request->studentid)
            ->value('students.nama');
        // ->first();

        return Excel::download(new CategoryExport($request->studentid, $request->category, $request->fromTime, $request->untilTime), $studentName . ' report.xlsx');
    }

    //report function
    public function reportPerStudent($id)
    {
        
        $studentName = DB::table('class_student')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->where('class_student.id', $id)
            ->select('students.nama as studentName')
            ->first();

        $applicationCat = DB::table('classifications')
            ->get();



        $minDate = date('Y-m-d', strtotime(DB::table('student_outing')
            ->where('student_outing.class_student_id',  '=', $id)
            ->orderBy('student_outing.apply_date_time')
            ->value('student_outing.apply_date_time')));

        $maxDate = date('Y-m-d', strtotime(DB::table('student_outing')
            ->where('student_outing.class_student_id', '=', $id)
            ->orderBy('student_outing.apply_date_time', 'desc')
            ->value('student_outing.apply_date_time')));

        // dd($minDate, $maxDate);


        return view('dorm.report.reportPerStudent', compact('studentName', 'applicationCat', 'id', 'minDate', 'maxDate'));
    }

    public function printcategory(Request $request)
    {
        // $student_id = $request->student_id;

        $details = DB::table('class_student')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->join('organizations', 'organizations.id', '=', 'class_organization.organization_id')
            ->join('dorms', 'dorms.id', '=', 'class_student.dorm_id')
            ->where('class_student.id', '=', $request->studentid)
            ->select(
                'students.nama as studentName',
                'classes.nama as className',
                'organizations.nama as schoolName',
                'organizations.address as schoolAddress',
                'organizations.postcode as schoolPostcode',
                'organizations.state as schoolState',
                'dorms.name as dormName'
            )
            ->first();

        $data = DB::table('student_outing')
            ->join('class_student', 'class_student.id', '=', 'student_outing.class_student_id')
            ->join('organization_user as warden', 'warden.id', '=', 'student_outing.warden_id')
            ->join('users as wardenUser', 'wardenUser.id', '=', 'warden.user_id')
            ->join('organization_user as guard', 'guard.id', '=', 'student_outing.guard_id')
            ->join('users as guardUser', 'guardUser.id', '=', 'guard.user_id')
            ->join('classifications', 'classifications.id', '=', 'student_outing.classification_id')
            ->select(
                'classifications.name as classificationName',
                'student_outing.out_date_time as outTime',
                'student_outing.reason',
                'wardenUser.name as wardenName',
                'student_outing.in_date_time as inTime',
                'guardUser.name as guardName'
            )->orderBy('student_outing.apply_date_time');

        if ($request->fromTime == null || $request->untilTime == null) {
            $data = $data
                ->where('class_student.id', $request->studentid)
                ->where('student_outing.classification_id', '=', $request->category)
                ->get();
        } else {

            $data = $data
                ->where('class_student.id', $request->studentid)
                ->where('student_outing.apply_date_time', '>=', $request->fromTime)
                ->where('student_outing.apply_date_time', '<=', $request->untilTime)
                ->where('student_outing.classification_id', '=', $request->category)
                ->get();
        }

        $pdf = PDF::loadView('dorm.report.reportPerStudentPdfTemplate', compact('data', 'details'));

        return $pdf->download('Report ' . $details->studentName . '.pdf');
    }

    public function printall(Request $request)
    {
        // $student_id = $request->student_id;

        $details = DB::table('class_student')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->join('organizations', 'organizations.id', '=', 'class_organization.organization_id')
            ->join('dorms', 'dorms.id', '=', 'class_student.dorm_id')
            ->where('class_student.id', '=', $request->studentid)
            ->select(
                'students.nama as studentName',
                'classes.nama as className',
                'organizations.nama as schoolName',
                'organizations.address as schoolAddress',
                'organizations.postcode as schoolPostcode',
                'organizations.state as schoolState',
                'dorms.name as dormName'
            )
            ->first();

        // dd($details->studentName);
        $data = DB::table('student_outing')
            ->join('class_student', 'class_student.id', '=', 'student_outing.class_student_id')
            ->join('organization_user as warden', 'warden.id', '=', 'student_outing.warden_id')
            ->join('users as wardenUser', 'wardenUser.id', '=', 'warden.user_id')
            ->join('organization_user as guard', 'guard.id', '=', 'student_outing.guard_id')
            ->join('users as guardUser', 'guardUser.id', '=', 'guard.user_id')
            ->join('classifications', 'classifications.id', '=', 'student_outing.classification_id')
            ->select(
                'classifications.name as classificationName',
                'student_outing.out_date_time as outTime',
                'student_outing.reason',
                'wardenUser.name as wardenName',
                'student_outing.in_date_time as inTime',
                'guardUser.name as guardName'
            )
            ->orderBy('student_outing.apply_date_time');
        if ($request->fromTime == null || $request->untilTime == null) {
            $data = $data
                ->where('class_student.id', $request->studentid)
                ->get();
        } else {
            $data = $data
                ->where('class_student.id', $request->studentid)
                ->where('student_outing.apply_date_time', '>=', $request->fromTime)
                ->where('student_outing.apply_date_time', '<=', $request->untilTime)
                ->get();
        }

        $pdf = PDF::loadView('dorm.report.reportPerStudentPdfTemplate', compact('data', 'details'));

        return $pdf->download('Report ' . $details->studentName . '.pdf');
    }

    public function printallrequest(Request $request)
    {
        // $student_id = $request->student_id;

        $this->validate($request, [
            'organPDF'      =>  'required',
            'pdf_from'      =>  'required',
            'pdf_to'        =>  'required',
        ]);

        $details = DB::table('class_student')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->join('organizations', 'organizations.id', '=', 'class_organization.organization_id')
            ->join('dorms', 'dorms.id', '=', 'class_student.dorm_id')
            ->where('class_organization.organization_id', '=', $request->organPDF)
            ->select(
                'organizations.nama as schoolName',
                'organizations.address as schoolAddress',
                'organizations.postcode as schoolPostcode',
                'organizations.state as schoolState',
            )
            ->first();

        // dd($details->studentName);
        $data = DB::table('students')
            ->join('class_student as cs', 'cs.student_id', '=', 'students.id')
            ->join('student_outing as so', 'so.class_student_id', '=', 'cs.id')
            ->join('organization_user_student as ous', 'ous.student_id', '=', 'students.id')
            ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
            ->join('organization_roles as or', 'or.id', '=', 'ou.role_id')
            ->join('classifications', 'classifications.id', '=', 'so.classification_id')
            ->where([
                ['so.status',1],
                ['ou.organization_id',  $request->organPDF],
            ])
            ->whereBetween('so.apply_date_time', [$request->pdf_from, $request->pdf_to])
            ->select('classifications.Fake_name as catname', DB::raw('count("so.id") as total'))
            ->groupBy('classifications.name')
            ->get();

        $pdf = PDF::loadView('dorm.report.reportAllStudentPdfTemplate', compact('data', 'details'));

        return $pdf->download('Report.pdf');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    //
    //

    //create or add files
    public function create()
    {
        // $organization = $this->getOrganizationByUserId();
        if(Auth::user()->hasRole('Superadmin')){
            $organization = Organization::all();
        }
        else{
            $organization = DB::table('organizations')
            ->join('organization_user as ou', 'ou.organization_id', '=', 'organizations.id')
            ->where([
                ['ou.user_id', Auth::user()->id],
                ['ou.role_id', 6],
            ])
            ->select('organizations.id', 'organizations.nama')
            ->distinct()
            ->get();
        }

        $start = date('Y-m-d', strtotime(DB::table('outings')
            ->where([
                ['outings.organization_id', $organization[0]->id],
                ['outings.end_date_time', '>', now()],
            ])
            ->orderBy("outings.end_date_time")
            ->value("outings.end_date_time as end_date_time")));

        $end = date('Y-m-d', strtotime(DB::table('outings')
            ->where([
                ['outings.organization_id', $organization[0]->id],
                ['outings.end_date_time', '>', now()],
            ])
            ->orderBy("outings.end_date_time")
            ->value("outings.end_date_time as end_date_time")));


        return view('dorm.create', compact('organization', 'start', 'end'));
    }

    public function createOuting()
    {
        //
        $organization = $this->getOrganizationByRole();

        $roles = DB::table('organization_roles')
            ->join('organization_user as ou', 'ou.role_id', '=', 'organization_roles.id')
            ->where([
                ['ou.user_id', Auth::user()->id],
            ])
            ->select('organization_roles.nama', 'ou.organization_id')
            ->get();

        return view('dorm.outing.add', compact('organization', 'roles'));
    }

    public function createReasonOuting()
    {
        //
        $organization = $this->getOrganizationByUserId();

        return view('dorm.classification.add', compact('organization'));
    }

    public function createResident()
    {
        // $userid     = Auth::id();
        $organization = $this->getOrganizationByRole();

        $roles = DB::table('organization_roles')
            ->join('organization_user as ou', 'ou.role_id', '=', 'organization_roles.id')
            ->where([
                ['ou.user_id', Auth::user()->id],
            ])
            ->select('organization_roles.nama', 'ou.organization_id')
            ->get();

        $dormlist =  $this->getDormByOrganizationId();
        return view('dorm.resident.add', compact('dormlist', 'organization', 'roles'));
    }

    public function createDorm()
    {
        //
        $organization = $this->getOrganizationByUserId();
        return view('dorm.management.add', compact('organization'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    //
    //
    // store functions
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name'         =>  'required',
            // 'email'        =>  'required',
            'category'     =>  'required',
            'reason'       =>  'required',
            'start_date'   =>  'required',
            'organization' =>  'required',
        ]);

        $categoryReal = $this->categoryReal;

        $classstudentid = DB::table('students')
            ->join('class_student', 'class_student.id', '=', 'students.id')
            ->where([
                ['students.id', $request->get('name')],
                // ['students.email', $request->get('email')],
                // ['students.parent_tel', Auth::user()->telno],
                ['class_student.outing_status', NULL],
            ])
            ->orWhere([
                ['students.id', $request->get('name')],
                // ['students.email', $request->get('email')],
                // ['students.parent_tel', Auth::user()->telno],
                ['class_student.outing_status', 0],
            ])
            ->value("class_student.id");


        $outingtype = DB::table('classifications')
            ->where([
                ['classifications.id', $request->get('category')],
            ])
            ->value('classifications.name');

        // $categoryReal[2] = "OUTINGS"
        if (strtoupper($outingtype) == $categoryReal[2]) {
            $outingid = DB::table('outings')
                ->where('outings.organization_id', $request->get('organization'))
                ->where([
                    // ['outings.start_date_time', '>=', $request->get('start_date')],
                    ['outings.end_date_time', '>', $request->get('start_date')],
                ])
                ->value('outings.id');

            if ($outingid == NULL) {
                return redirect('/dorm/create')->withErrors('Selected outings date and time is not available');
            }
        } else {
            $outingid = NULL;
        }

        if (isset($classstudentid)) {
            DB::table('student_outing')
                ->insert([
                    'reason'            => $request->get('reason'),
                    'apply_date_time'   => $request->get('start_date'),
                    'status'            => 0,
                    'classification_id' => $request->get('category'),
                    'class_student_id'  => $classstudentid,
                    'outing_id'         => $outingid,
                    'created_at'        => now(),
                ]);

            $arrayRecipientEmail = DB::table('users')
                ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                ->where([
                    ['organization_user.organization_id', $request->get('organization')],
                    ['organization_user.check_in_status', '=', 1]
                ])
                ->orWhere([
                    ['organization_user.organization_id', $request->get('organization')],
                    ['organization_user.role_id', '=', 4]
                ])
                // ->where('organization_user.organization_id', $request->get('organization'))
                // ->where('organization_user.check_in_status', '=', 1)
                // ->orWhere('organization_user.role_id', '=', 4)
                ->select('users.email')
                ->distinct()
                ->get();
            // dd($arrayRecipientEmail);

            if (isset($arrayRecipientEmail)) {
                foreach ($arrayRecipientEmail as $email) {
                    // dd("here inside foreach");
                    // Mail::to($email)->send(new NotifyMail());
                    Mail::to($email)->send(new NotifyMail());

                    if (Mail::failures()) {
                        // dd("fail");
                        return response()->Fail('Sorry! Please try again latter');
                    } else {
                        // return redirect('/dorm')->with('success', 'Great! Successfully send in your mail');

                        // return response()->json(['success' => 'Great! Successfully send in your mail']);
                        // dd("successs", $email);
                    }
                }
            } else {
                // do nothing 1st
            }

            return redirect('/sekolah/dorm/indexRequest/6')->with('success', 'New application has been added successfully');
        } else {
            return redirect('/sekolah/dorm/indexRequest/6')->withErrors('Failed to submit application');
        }
    }

    public function storeOuting(Request $request)
    {
        // 
        $this->validate($request, [
            'start_date'        =>  'required',
            'end_date'          =>  'required',
            'organization'      =>  'required',
        ]);

        DB::table('outings')->insert([
            'start_date_time' => $request->get('start_date'),
            'end_date_time'   => $request->get('end_date'),
            'organization_id' => $request->get('organization'),
        ]);

        return redirect('/sekolah/dorm/indexOuting')->with('success', 'New outing date and time has been added successfully');
    }

    public function storeReasonOuting(Request $request)
    {
        // 
        $this->validate($request, [
            'optionReason'        =>  'required',
            'name'                => 'required',
            'day'                 => 'required'
        ]);

        if ($request->get('optionReason') == 5) {
            $name = $request->get('name');
        } else if ($request->get('optionReason') == 1) {
            $name = "Outings";
        } else if ($request->get('optionReason') == 2) {
            $name = "Balik Wajib";
        } else if ($request->get('optionReason') == 3) {
            $name = "Balik Khas";
        } else if ($request->get('optionReason') == 4) {
            $name = "Balik Kecemasan";
        }


        DB::table('classifications')->insert([
            'name'       => $name,
            'fake_name' => $request->get('name'),
            'description'   => $request->get('description'),
            'limit'   => $request->get('limit'),
            'day_before' => $request->get('day'),
            'organization_id' => $request->get('organization'),
            'time_limit'    => $request->get('time')
        ]);

        return redirect('/sekolah/dorm/indexReasonOuting')->with('success', 'New Reason has been added successfully');
    }

    public function storeResident(Request $request)
    {
        // 
        // find student id in class student and see the student have dorm or not
        // and check the blacklist 
        $this->validate($request, [
            'name'              =>  'required',
            'organization'      =>  'required',
            'dorm'              =>  'required'
        ]);

        $organizationid = $request->get('organization');
        $neworganizationid = (int)$organizationid;

        $dormid = $request->get('dorm');
        $newdormid = (int)$dormid;

        // find student id
        $student = DB::table('students')
            ->join('class_student as cs', 'cs.student_id', '=', 'students.id')
            ->where([
                ['students.id', $request->get('name')],
                ['cs.status', 1],
            ])
            ->select('students.id as id', 'cs.dorm_id', 'cs.blacklist')
            ->get();


        $dorm = DB::table('dorms')
            ->where('dorms.id', $newdormid)
            ->get();


        if (isset($student[0]->id) && ($dorm[0]->student_inside_no < $dorm[0]->accommodate_no)) {
            if ($student[0]->blacklist == 1) {
                $updateDetails = [
                    'cs.dorm_id' => $newdormid,
                    'cs.start_date_time' => now()->toDateTimeString(),
                    'cs.end_date_time' => NULL,
                    'cs.blacklist' => 1,
                    'cs.outing_status' => 0,
                ];
            } else {
                $updateDetails = [
                    'cs.dorm_id' => $newdormid,
                    'cs.start_date_time' => now()->toDateTimeString(),
                    'cs.end_date_time' => NULL,
                    'cs.blacklist' => 0,
                    'cs.outing_status' => 0,
                ];
            }

            $result = DB::table('class_student as cs')
                ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                ->where([
                    ['cs.student_id', $student[0]->id],
                    ['cs.dorm_id', NULL],
                    ['co.organization_id', $neworganizationid],
                    ['cs.status', 1],
                ])
                ->update($updateDetails);
        } else {
            $result = 0;
        }

        if ($result > 0) {
            DB::table('dorms')
                ->where('dorms.id', $newdormid)
                ->update(['student_inside_no' => $dorm[0]->student_inside_no + 1]);

            return redirect()->to('/sekolah/dorm/indexResident/' . $newdormid)->with('success', 'New student has been added successfully');
        }

        return redirect()->to('/sekolah/dorm/indexResident/' . $newdormid)->withErrors(['Failed to add student into dorm', 'Possible problem: Dorm is full  |  Student already has accommodation']);
    }


    public function storeDorm(Request $request)
    {
        // 
        $this->validate($request, [
            'name'        =>  'required|unique:dorms',
            'capacity'    =>  'required',
            'organization'      =>  'required',
            //'name', 'accommodate_no', 'student_inside_no'
        ]);
        //echo ({{ $request->get('organization') }});

        DB::table('dorms')->insert([
            'name' => $request->get('name'),
            'accommodate_no'   => $request->get('capacity'),
            'organization_id' => $request->get('organization'),
            'student_inside_no' => 0
        ]);

        return redirect('/sekolah/dorm/indexDorm')->with('success', 'New dorm has been added successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    //
    //
    // edit and update functions
    public function edit($id)
    {
        //
        $studentouting = DB::table('student_outing')
            ->join('class_student as cs', 'cs.id', '=', 'student_outing.class_student_id')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->join('students', 'students.id', '=', 'cs.student_id')
            ->join('classifications', 'classifications.id', '=', 'student_outing.classification_id')
            ->where([
                ['student_outing.id', $id],
                ['cs.status', 1],
                ['student_outing.status', 0],
            ])
            ->select(
                'student_outing.id',
                'student_outing.apply_date_time',
                'students.nama',
                'students.email',
                'students.parent_tel',
                'student_outing.reason',
                'cs.dorm_id',
                'classifications.id as cid',
                'classifications.day_before',
                'classifications.name as categoryname',
                'classifications.fake_name',
                'classifications.organization_id as oid',
                DB::raw('count("student_outing.id") as total')
            )
            ->groupBy('classifications.name')
            ->first();

        if (isset($studentouting) && str_contains($studentouting->parent_tel, Auth::user()->telno)) {
            $organization = $this->getOrganizationByUserId();
            $categoryReal = $this->categoryReal;

            $category = DB::table('classifications')
                ->where('classifications.organization_id', $organization[0]->id)
                ->get();

            $start = date('Y-m-d', strtotime(DB::table('outings')
                ->where([
                    ['outings.organization_id', $organization[0]->id],
                    ['outings.end_date_time', '>', now()],
                ])
                ->orderBy("outings.start_date_time")
                ->value("outings.start_date_time as start_date_time")));

            $end = date('Y-m-d', strtotime(DB::table('outings')
                ->where([
                    ['outings.organization_id', $organization[0]->id],
                    ['outings.end_date_time', '>', now()],
                ])
                ->orderBy("outings.end_date_time")
                ->value("outings.end_date_time as end_date_time")));

            if (Auth::user()->hasRole('Penjaga')) {
                return view('dorm.update', compact('organization', 'start', 'end', 'studentouting'));
            }
        }
        return view('errors.404');
    }

    public function editOuting($id)
    {
        //  

        $outing = DB::table('outings')
            ->where('outings.id', $id)
            ->select('outings.id', 'outings.start_date_time', 'outings.end_date_time', 'outings.organization_id')
            ->first();

        $organization = $this->getOrganizationByUserId();

        if(isset($outing) || Auth::user()->hasRole('Superadmin')){
            $organ = $organization->toArray();
            foreach($organ as $organ){
                if(in_array($outing->organization_id, $organ) || Auth::user()->hasRole('Superadmin')){
                    $roles = DB::table('organization_roles')
                    ->join('organization_user as ou', 'ou.role_id', '=', 'organization_roles.id')
                    ->where([
                        ['ou.user_id', Auth::user()->id],
                    ])
                    ->select('organization_roles.nama', 'ou.organization_id')
                    ->get();
                    foreach($roles as $roles){
                        if(Auth::user()->hasRole('Superadmin') || ($outing->organization_id == $roles->organization_id && $roles->nama != "Penjaga" && $roles->nama != "Guard")){
                            return view('dorm.outing.update', compact('outing', 'organization', 'id', 'roles'));
                        }
                    }
                }
            }
        }
    
        return view('errors.404');
    }

    public function editOutingReason($id)
    {
        //  

        $reason = DB::table('classifications')
            ->where('classifications.id', $id)
            ->select('classifications.id', 'classifications.time_limit', 'classifications.day_before', 'classifications.fake_name', 'classifications.name as name', 'classifications.description', 'classifications.organization_id', 'classifications.limit')
            ->first();

        $reasonlist = DB::table('classifications')
            ->select('classifications.id', 'classifications.time_limit', 'classifications.day_before', 'classifications.fake_name', 'classifications.name as name', 'classifications.description', 'classifications.organization_id', 'classifications.limit')
            ->get();

        // dd($reason->description);
        // dd($reason->id);

        $organization = $this->getOrganizationByUserId();

        return view('dorm.classification.update', compact('reason', 'organization', 'id', 'reasonlist'));
    }

    public function editResident($id)
    {
        //  
        // dd($id); class_student.id
        $resident = DB::table('dorms')
            ->join('class_student', 'class_student.dorm_id', '=', 'dorms.id')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->select('dorms.id as dorm_id', 'dorms.organization_id', 'dorms.name as dormname', 'class_student.student_id as id', 'students.nama as studentname', 'students.parent_tel')
            ->where([
                ['class_student.id', $id],
                ['class_student.status', 1],
            ])
            ->orderBy('dorms.name')
            ->first();
        
        $organization = $this->getOrganizationByUserId();

        if(isset($resident) || Auth::user()->hasRole('Superadmin')){
            $organ = $organization->toArray();
            foreach($organ as $organ){
                if(in_array($resident->organization_id, $organ) || Auth::user()->hasRole('Superadmin')){
                    
                    $roles = DB::table('organization_roles')
                    ->join('organization_user as ou', 'ou.role_id', '=', 'organization_roles.id')
                    ->where([
                        ['ou.user_id', Auth::user()->id],
                    ])
                    ->select('organization_roles.nama', 'ou.organization_id')
                    ->get();

                    foreach($roles as $roles){
                        if($resident->organization_id == $roles->organization_id && $roles->nama != "Penjaga" && $roles->nama != "Guard" || Auth::user()->hasRole('Superadmin')){
                            $dormlist = DB::table('dorms')
                            ->join('organizations', 'organizations.id', '=', 'dorms.organization_id')
                            ->select('dorms.id as id', 'dorms.name')
                            ->where([
                                ['dorms.organization_id', $resident->organization_id]

                            ])
                            ->orderBy('dorms.name')
                            ->get();

                            return view('dorm.resident.update', compact('resident', 'dormlist', 'organization', 'roles'));
                        }
                    }
                }
            }
        }
  
        return view ('errors.404');
    }

    public function getID($id)
    {
        return response()->json(["string" => $id]);
    }
    public function editDorm($id)
    {
        //  
        $dorm = DB::table('dorms')
            ->where('dorms.id', $id)
            ->select('dorms.id', 'dorms.name', 'dorms.accommodate_no', 'dorms.student_inside_no', 'organization_id')
            ->first();

        //calculate sum of resident inside this dorm
        $dorm_student_inside = DB::table('class_student')
            ->join('dorms', 'dorms.id', '=', 'class_student.dorm_id')
            ->where('class_student.dorm_id', $id)
            ->count();

        $organization = $this->getOrganizationByUserId();

        return view('dorm.management.update', compact('dorm', 'organization', 'dorm_student_inside', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'name'         =>  'required',
            'category'     =>  'required',
            'reason'       =>  'required',
            'start_date'   =>  'required',
            'organization' =>  'required',
        ]);

        $classstudentid = DB::table('students')
            ->join('class_student', 'class_student.id', '=', 'students.id')
            ->join('users', 'users.telno', '=', 'students.parent_tel')
            ->where([
                ['students.nama', $request->get('name')],
                ['students.email', $request->get('email')],
                ['class_student.outing_status', 0],
                ['users.id', Auth::user()->id],
            ])
            ->orWhere([
                ['students.nama', $request->get('name')],
                ['students.email', $request->get('email')],
                ['class_student.outing_status', null],
                ['users.id', Auth::user()->id],
            ])
            ->value("class_student.id");

        $categoryReal = $this->categoryReal;

        $outingtype = DB::table('classifications')
            ->where([
                ['classifications.id', $request->get('category')],
            ])
            ->value('classifications.name');

        if (strtoupper($outingtype) == $categoryReal[2]) {
            $outingid = DB::table('outings')
                ->where('outings.organization_id', $request->get('organization'))
                ->where('outings.start_date_time', '>=', $request->get('start_date'))
                ->where('outings.end_date_time', '>', $request->get('start_date'))
                ->value('outings.id');
        } else {
            $outingid = NULL;
        }

        if (isset($classstudentid)) {
            DB::table('student_outing')
                ->where('id', $id)
                ->update([
                    'reason'            => $request->get('reason'),
                    'apply_date_time'   => $request->get('start_date'),
                    'status'            => 0,
                    'classification_id' => $request->get('category'),
                    'class_student_id'  => $classstudentid,
                    'outing_id'         => $outingid,
                    'updated_at'        => now(),
                ]);

            return redirect('/sekolah/dorm/indexRequest/6')->with('success', 'The application has been updated');
        } else {
            return redirect('/sekolah/dorm/indexRequest/6')->withErrors('Information not matched');
        }
    }

    public function updateOuting(Request $request, $id)
    {
        //
        // dd($id);
        $this->validate($request, [
            'start_date'        =>  'required',
            'end_date'          =>  'required',
            'organization'      =>  'required',
        ]);

        DB::table('outings')
            ->where('id', $id)
            ->update(
                [
                    'start_date_time' => $request->get('start_date'),
                    'end_date_time'   => $request->get('end_date')
                ]
            );

        return redirect('/sekolah/dorm/indexOuting')->with('success', 'The data has been updated!');
    }

    public function updateReasonOuting(Request $request, $id)
    {
        //
        // dd($id);
        $this->validate($request, [
            'name'        =>  'required',
            'organization'      =>  'required',
            'day'           => 'required',
            'reason'    => 'required'
        ]);

        DB::table('classifications')
            ->where('id', $id)
            ->update(
                [
                    'fake_name' => $request->get('name'),
                    'description'   => $request->get('description'),
                    'limit'   => $request->get('limit'),
                    'time_limit'    => $request->get('time'),
                    'day_before'    => $request->get('day')

                ]
            );



        return redirect('/sekolah/dorm/indexReasonOuting')->with('success', 'The data has been updated!');
    }

    public function updateResident(Request $request, $id)
    {
        // dd($id);
        $this->validate($request, [
            'name' => 'required',
            'dorm' => 'required',
            'organization' => 'required'
        ]);

        $organizationid = $request->get('organization');
        $neworganizationid = (int)$organizationid;

        $dormid = $request->get('dorm');
        $newdormid = (int)$dormid;

        $dorm = DB::table('dorms')
            ->where([
                ['dorms.id', $newdormid],
            ])
            ->select('dorms.id as dormid', 'dorms.accommodate_no', 'dorms.student_inside_no')
            ->get();


        $resident = DB::table('class_student')
            ->join('students', 'students.id', '=', 'class_student.student_id')
            ->where([
                ['class_student.id', $id],
                ['students.nama', $request->get('name')],
            ])
            ->select('class_student.id as id', 'class_student.student_id', 'class_student.dorm_id')
            ->get();


        $olddormid = DB::table('dorms')
            ->where([
                ['dorms.id', $resident[0]->dorm_id],
            ])
            ->select('dorms.id as dormid', 'dorms.accommodate_no', 'dorms.student_inside_no')
            ->get();

        if (
            Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir') ||
            Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Warden')
        ) {
            if (isset($resident[0]->id) && ($dorm[0]->student_inside_no < $dorm[0]->accommodate_no) && ($olddormid[0]->dormid != $newdormid)) {
                $updateDetails = [
                    'cs.dorm_id' => $newdormid,
                    'cs.start_date_time' => now()->toDateTimeString(),
                    'cs.end_date_time' => NULL,
                ];

                $result = DB::table('class_student as cs')
                    ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                    ->where([
                        ['cs.id', $id],
                        ['co.organization_id', $neworganizationid],
                        ['cs.student_id', $resident[0]->student_id],
                        ['cs.status', 1],
                    ])
                    ->update($updateDetails);
            } else {
                $result = 0;
            }

            if ($result > 0) {
                DB::table('dorms')
                    ->where('dorms.id', $newdormid)
                    ->update(['student_inside_no' => $dorm[0]->student_inside_no + 1]);

                DB::table('dorms')
                    ->where('dorms.id', $olddormid[0]->dormid)
                    ->update(['student_inside_no' => $olddormid[0]->student_inside_no - 1]);

                return redirect()->to('/sekolah/dorm/indexResident/' . $newdormid)->with('success', 'Student has been added successfully');
            }
        }
        return redirect()->to('/sekolah/dorm/indexResident/' . $newdormid)->withErrors(['Failed to add student into dorm']);
    }

    public function updateDorm(Request $request, $id)
    {
        //
        // dd($id);
        $this->validate($request, [
            'name'        =>  'required|unique:dorms,name,'.$id,
            'capacity'    =>  'required',
            'organization'      =>  'required',
            //'name', 'accommodate_no', 'student_inside_no'

        ]);

        DB::table('dorms')
            ->where('id', $id)
            ->update(
                [
                    'name' => $request->get('name'),
                    'accommodate_no'   => $request->get('capacity'),
                ]
            );

        return redirect('/sekolah/dorm/indexDorm')->with('success', 'The data has been updated!');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //
    //
    // destroy functions
    public function destroy($id)
    {
        //
        $result = DB::table('student_outing')->where('student_outing.id', $id);
        if ($result->delete()) {
            Session::flash('success', 'Permintaan Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Permintaan Gagal Dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    public function destroyOuting($id)
    {
        //
        $result = DB::table('outings')->where('outings.id', $id);

        if ($result->delete()) {
            Session::flash('success', 'Outing Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Outing Gagal Dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    public function destroyReasonOuting($id)
    {
        //
        $result = DB::table('classifications')->where('classifications.id', $id)->delete();

        if ($result) {
            Session::flash('success', 'Sebab Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Sebab Gagal Dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    public function destroyDorm($id)
    {
        //
        $result = DB::table('dorms')->where('dorms.id', $id)->delete();
        //return response()->json($result);
        //$strirng = "asd";
        if ($result) {
            Session::flash('success', 'Dorm Berjaya Dipadam');
            return View::make('layouts/flash-messages');
            //return response()->json(['resultdata' => $result, 'string' => $strirng]);
        } else {
            Session::flash('error', 'Dorm Gagal Dipadam');
            return View::make('layouts/flash-messages');
            //return response()->json(['resultdata' => $result, 'string' => $strirng]);
        }
    }

    public function clearDorm($id)
    {
        //
        $result = DB::table('class_student')->where('dorm_id', $id)->update(['dorm_id' => null, 'end_date_time' => now()->toDateTimeString()]);

        if ($result) {
            DB::table('dorms')->where('id', $id)->update(['student_inside_no' => 0]);

            Session::flash('success', 'Asrama Berjaya Dikosongkan');
            return View::make('layouts/flash-messages');
            //return response()->json(['resultdata' => $result, 'string' => $strirng]);
        } else {
            Session::flash('error', 'Asrama Gagal Dikosongkan');
            return View::make('layouts/flash-messages');
            //return response()->json(['resultdata' => $result, 'string' => $strirng]);
        }
    }

    public function outDorm($id)
    {
        //
        $result = DB::table('class_student')->where('dorm_id', $id)->update(['outing_status' => 1]);

        if ($result) {
            Session::flash('success', 'Pelajar Dalam Asrama Telah Keluar');
            return View::make('layouts/flash-messages');
            //return response()->json(['resultdata' => $result, 'string' => $strirng]);
        } else {
            Session::flash('error', 'Dorm Gagal Dikosongkan');
            return View::make('layouts/flash-messages');
            //return response()->json(['resultdata' => $result, 'string' => $strirng]);
        }
    }

    public function blockStudent($id, $blockStatus)
    {
        // dd("123");
        //if the student is blocked
        if ($blockStatus == 0) {
            $result = DB::table('class_student')
                ->where('class_student.id', '=', $id)
                ->update(['class_student.blacklist' => 0]);
        } else if ($blockStatus == 1) {
            $result = DB::table('class_student')
                ->where('class_student.id', '=', $id)
                ->update(['class_student.blacklist' => 1]);
        }
        if ($result && $blockStatus == 0) {
            Session::flash('success', 'Pelajar Berjaya Unblock');
            return View::make('layouts/flash-messages');
            //return response()->json(['resultdata' => $result, 'string' => $strirng]);
        } else if ($result && $blockStatus == 1) {
            Session::flash('success', 'Pelajar Berjaya Block');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Pelajar Gagal Diproseskan');
            return View::make('layouts/flash-messages');
            //return response()->json(['resultdata' => $result, 'string' => $strirng]);
        }
    }

    public function destroyResident($id)
    {
        $dorm = DB::table('dorms')
            ->join('class_student', 'class_student.dorm_id', '=', 'dorms.id')
            ->where('class_student.id', $id)
            ->get();

        $updateDetails1 = [
            'class_student.end_date_time' => now()->toDateTimeString(),
            'class_student.dorm_id' => NULL,
            'class_student.outing_status' => NULL,
            'dorms.student_inside_no' => $dorm[0]->student_inside_no - 1,
        ];

        $result = DB::table('dorms')
            ->join('class_student', 'class_student.dorm_id', '=', 'dorms.id')
            ->where('class_student.id', $id)
            ->update($updateDetails1);

        if ($result) {
            Session::flash('success', 'Pelajar Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Pelajar Gagal Dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {

            return Organization::all();
        } else {
            // user role pentadbir 
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->Where(function ($query) {
                    $query->whereIn('organization_user.role_id', [4, 5, 6, 7, 8]);
                });
            })->get();
        }
    }

    public function getOrganizationByRole(){
        $userId = Auth::user()->id;
        if(Auth::user()->hasRole('Superadmin')){
            return Organization::all();
        }
        else{
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->Where(function ($query) {
                    $query->whereIn('organization_user.role_id', [2, 4, 5, 7, 8]);
                });
            })
            ->select('organizations.id as id', 'organizations.nama')
            ->distinct()
            ->get();
        }
    }

    public function getDormByOrganizationId()
    {
        $userId = Auth::id();
        $organization = $this->getOrganizationByUserId();

        if (Auth::user()->hasRole('Superadmin')) {

            return Dorm::all();
        } else {
            // user role pentadbir 

            return DB::table('dorms')
                ->where('dorms.organization_id', $organization[0]->id)
                ->select()
                ->get();
        }
    }

    //
    //
    //get datatable functions
    public function getOutingsDatatable(Request $request)
    {
        // dd($request->oid);
        if (request()->ajax()) {
            $oid = $request->oid;
            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('outings')
                    ->select('outings.id', 'outings.start_date_time', 'outings.end_date_time')
                    ->where('outings.organization_id', $oid)
                    ->orderBy('outings.start_date_time');
            }

            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('dorm.editOuting', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }

    public function getReasonOutingDatatable(Request $request)
    {
        // dd($request->oid);
        if (request()->ajax()) {
            $oid = $request->oid;
            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('classifications')
                    ->select('classifications.id', 'classifications.time_limit', 'classifications.name', 'classifications.fake_name', 'classifications.description', 'classifications.limit')
                    ->where('classifications.organization_id', $oid)
                    ->get();
            }

            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('dorm.editOutingReason', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }

    public function getDormDatatable(Request $request)
    {
        // dd($request->oid);
        if (request()->ajax()) {
            $oid = $request->oid;
            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('dorms')
                    ->select('dorms.id', 'dorms.name', 'dorms.accommodate_no', 'dorms.student_inside_no', 'dorms.organization_id')
                    ->where('dorms.organization_id', $oid)
                    ->orderBy('dorms.name');
                //'name', 'accommodate_no', 'student_inside_no'
            }

            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                // $btn = $btn . '<a href="' . route('importresident', $row->id) . '" class="btn btn-primary m-1">Import</a>';

                //try
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" data-toggle="modal" data-target="#modelId3" class="btn btn-primary m-1 importBtn">Import</button>';

                $btn = $btn . '<a href="' . route('dorm.editDorm', $row->id) . '" class="btn btn-primary m-1">Ubah</a>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1 destroyDorm">Buang</button>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1 clearDorm">Kosong</button>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1 outDorm">Keluar</button></div>';

                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }

    public function getStudentOutingByCategory(Request $request)
    {
        if (request()->ajax()) {
            $oid = $request->oid;

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $hasOrganizaton = $request->hasOrganization;

            if ($oid != '' && !is_null($hasOrganizaton) && $start_date != '' && $end_date != '') {

                $data = DB::table('students')
                    ->join('class_student as cs', 'cs.student_id', '=', 'students.id')
                    ->join('student_outing as so', 'so.class_student_id', '=', 'cs.id')
                    ->join('organization_user_student as ous', 'ous.student_id', '=', 'students.id')
                    ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
                    ->join('organization_roles as or', 'or.id', '=', 'ou.role_id')
                    ->join('classifications', 'classifications.id', '=', 'so.classification_id')
                    ->where([
                        ['so.status', 1],
                        ['classifications.organization_id',  $oid],
                    ])
                    ->whereBetween('so.apply_date_time', [$start_date, $end_date])
                    ->select('so.id', 'classifications.fake_name as catname', DB::raw('count("so.id") as total'))
                    ->groupBy('classifications.name')
                    ->distinct()
                    ->get();

                $table = Datatables::of($data);

                // $table->addColumn('action', function ($row) {
                //     $token = csrf_token();
                //     $btn = '<div class="d-flex justify-content-center">';
                //     // $btn = $btn . '<a href="" class="btn btn-primary m-1">Edit</a>';
                //     // $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                //     return $btn;
                // });

                // $table->rawColumns(['action']);
                return $table->make(true);
            }
        }
    }

    public function getResidentsDatatable(Request $request)
    {
        // dd($request->hasOrganization);
        if (request()->ajax()) {
            // $oid = $request->oid;

            $dormid = $request->dormid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($dormid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('students.id as id', 'students.nama as studentname', 'classes.nama as classname', 'class_student.start_date_time', 'class_student.end_date_time', 'class_student.outing_status', 'class_student.blacklist')
                    ->where([
                        ['class_student.dorm_id', $dormid],
                        ['class_student.status', 1],
                    ])
                    ->orderBy('students.nama')
                    ->get();

                $table = Datatables::of($data);

                $table->addColumn('outing_status', function ($row) {
                    if ($row->outing_status == '0') {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success"> Dalam </span></div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Keluar </span></div>';

                        return $btn;
                    }
                });

                $table->addColumn('blacklist', function ($row) {
                    if ($row->blacklist == '1') {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Ya </span></div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success"> Tidak </span></div>';

                        return $btn;
                    }
                });

                $table->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href="' . route('dorm.editResident', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                });

                $table->rawColumns(['outing_status', 'blacklist', 'action']);
                return $table->make(true);
            }
        }
    }

    //for all reasons for particular student
    public function getReportDatatable(Request $request, $id)
    {
        if (request()->ajax()) {
            $catId = $request->catId;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();
            $from = $request->fromTime;
            $until = $request->untilTime;

            $data = DB::table('student_outing')
                ->join('class_student', 'class_student.id', '=', 'student_outing.class_student_id')
                ->join('organization_user as warden', 'warden.id', '=', 'student_outing.warden_id')
                ->join('users as wardenUser', 'wardenUser.id', '=', 'warden.user_id')
                ->join('organization_user as guard', 'guard.id', '=', 'student_outing.guard_id')
                ->join('users as guardUser', 'guardUser.id', '=', 'guard.user_id')
                ->join('classifications', 'classifications.id', '=', 'student_outing.classification_id')
                ->select('student_outing.out_date_time as outTime', 'student_outing.in_date_time as inTime', 'wardenUser.name as wardenName', 'guardUser.name as guardName', 'classifications.name as classificationName', 'student_outing.reason')
                ->orderBy('student_outing.apply_date_time');

            if ($catId != '' && !is_null($hasOrganizaton)) {
                if ($catId == 0 && $from != null && $until != null) {
                    $data = $data
                        ->where('class_student.id', $id)
                        ->where('student_outing.apply_date_time', '>=', $from)
                        ->where('student_outing.apply_date_time', '<=', $until)
                        ->get();
                } else if ($catId != 0 && $from != null && $until != null) {
                    $data = $data
                        ->where('class_student.id', $id)
                        ->where('student_outing.apply_date_time', '>=', $from)
                        ->where('student_outing.apply_date_time', '<=', $until)
                        ->where('student_outing.classification_id', '=', $catId)
                        ->get();
                } else if ($catId == 0) {
                    $data = $data
                        ->where('class_student.id', $id)
                        ->get();
                } else if ($catId != 0) {
                    $data = $data
                        ->where('class_student.id', $id)
                        ->where('student_outing.classification_id', '=', $catId)
                        ->get();
                }

                $table = Datatables::of($data);

                return $table->make(true);
            }
        }
    }

    //for all student 
    public function getAllStudentlistDatatable(Request $request)
    {

        // dd("ser");
        if (request()->ajax()) {
            $oid = $request->oid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->join('dorms', 'dorms.id', '=', 'class_student.dorm_id')
                    ->where('dorms.organization_id',  $oid)
                    ->whereNotNull('class_student.dorm_id')
                    ->select('class_student.id', 'students.nama as studentName', 'classes.nama as className', 'dorms.name as dormName', 'class_student.blacklist as status')
                    ->orderBy('students.nama')
                    ->get();

                $table = Datatables::of($data);

                $table->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Blocked </span></div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success"> Unblock </span></div>';

                        return $btn;
                    }
                });

                $table->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    if($row->status==1)
                        $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger unblockBtn m-1">Unblock</button>';
                    else
                        $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger blockBtn m-1">Block</button>';
                    $btn = $btn . '<a href="' . route('dorm.reportPerStudent', $row->id) . '" class="btn btn-primary reportBtn m-1">Report</a></div>';
                    return $btn;
                });

                $table->rawColumns(['status', 'action']);
                return $table->make(true);
            }
        }
    }

    //for selected dorm student
    public function getDormStudentlistDatatable(Request $request)
    {

        // dd("ser");
        if (request()->ajax()) {
            $dormid = $request->dormid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($dormid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->join('dorms', 'dorms.id', '=', 'class_student.dorm_id')
                    ->where('class_student.dorm_id',  $dormid)
                    // ->whereNotNull('class_student.dorm_id')
                    ->select('class_student.id', 'students.nama as studentName', 'classes.nama as className', 'dorms.name as dormName', 'class_student.blacklist as status')
                    ->orderBy('students.nama')
                    ->get();

                $table = Datatables::of($data);

                $table->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Blocked </span></div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success"> Unblock </span></div>';

                        return $btn;
                    }
                });

                $table->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    if($row->status == 1)
                        $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger unblockBtn m-1">Unblock</button>';
                    else
                        $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger blockBtn m-1">Block</button>';
                    $btn = $btn . '<a href="' . route('dorm.reportPerStudent', $row->id) . '" class="btn btn-primary reportBtn m-1">Report</a></div>';
                    return $btn;
                });

                $table->rawColumns(['status', 'action']);
                return $table->make(true);
            }
        }
    }

    //for selected dorm but blacklist student
    public function getDormBlacklistStudentlistDatatable(Request $request)
    {

        // dd("ser");
        if (request()->ajax()) {
            $dormid = $request->dormid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($dormid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->join('dorms', 'dorms.id', '=', 'class_student.dorm_id')
                    ->where('class_student.dorm_id',  $dormid)
                    ->where('class_student.blacklist', '=', 1)
                    ->select('class_student.id', 'students.nama as studentName', 'classes.nama as className', 'dorms.name as dormName', 'class_student.blacklist as status')
                    ->orderBy('students.nama')
                    ->get();

                $table = Datatables::of($data);

                $table->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Blocked </span></div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success"> Unblock </span></div>';

                        return $btn;
                    }
                });

                $table->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    if($row->status == 1)
                        $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger unblockBtn m-1">Unblock</button>';
                    else
                        $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger blockBtn m-1">Block</button></div>';
                    $btn = $btn . '<a href="' . route('dorm.reportPerStudent', $row->id) . '" class="btn btn-primary reportBtn m-1">Report</a>';
                    return $btn;
                });

                $table->rawColumns(['status', 'action']);
                return $table->make(true);
            }
        }
    }

    //for all blacklist student
    public function getBlacklistStudentlistDatatable(Request $request)
    {
        // dd("inside blacklist controller");
        // dd($request->hasOrganization);
        if (request()->ajax()) {
            $oid = $request->oid;

            // $dormid = $request->dormid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization as co', 'co.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'co.class_id')
                    ->join('dorms', 'dorms.id', '=', 'class_student.dorm_id')
                    ->where('dorms.organization_id', $oid)
                    ->where('class_student.blacklist', '=', 1)
                    ->whereNotNull('class_student.dorm_id')
                    ->select('class_student.id', 'students.nama as studentName', 'classes.nama as className', 'dorms.name as dormName', 'class_student.blacklist as status')
                    ->orderBy('students.nama')
                    ->get();


                $table = Datatables::of($data);

                $table->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Blocked </span></div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success"> Unblock </span></div>';

                        return $btn;
                    }
                });

                $table->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger unblockBtn m-1">Unblock</button></div>';
                    $btn = $btn . '<a href="' . route('dorm.reportPerStudent', $row->id) . '" class="btn btn-primary reportBtn m-1">Report</a>';
                    return $btn;
                });

                $table->rawColumns(['status', 'action']);
                return $table->make(true);
            }
        }
    }

    public function fetchDorm(Request $request)
    {
        $oid = $request->get('oid');

        $list = DB::table('dorms')
            ->where('dorms.organization_id', $oid)
            ->select()
            ->orderBy('dorms.name')
            ->get();

        return response()->json(['success' => $list]);
    }

    public function fetchRole(Request $request)
    {
        $oid = $request->get('oid');
        $rid = $request->get('rid');

        $query = DB::table('organization_roles')
            ->join('organization_user as ou', 'ou.role_id', '=', 'organization_roles.id')
            ->select('organization_roles.id', 'organization_roles.nama as name', 'ou.check_in_status');

        $list = $query
            ->where([
                ['ou.user_id', Auth::user()->id],
                ['ou.organization_id', $oid],
            ])
            ->get();

        $isblacklisted = DB::table('students')
            ->join('class_student as cs', 'cs.student_id', '=', 'students.id')
            ->join('organization_user_student as ous', 'ous.student_id', '=', 'students.id')
            ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
            ->where([
                // ['ou.organization_id', $organization[0]->id],
                ['cs.blacklist', 1],
                ['ou.user_id', Auth::user()->id],
            ])
            ->select('cs.blacklist', 'ou.organization_id as oid', 'students.nama')
            ->get();

        return response()->json(['success' => $list, 'isblacklisted' => $isblacklisted]);
    }


    public function fetchStudent(Request $request)
    {
        $oid = $request->get('oid');
        $query = DB::table('students')
        ->join('organization_user_student as ous', 'ous.student_id', '=', 'students.id')
        ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
        ->join('class_student as cs', 'cs.student_id', '=', 'students.id');
        //get student that is belongs to the logged in parent
        if(Auth::user()->hasRole('Superadmin')){
            $student = $query
            ->where([
                ['ou.organization_id', $oid],
                ['cs.status', 1],
            ])
            ->select('students.nama', 'students.id')
            ->distinct()
            ->get();
        }
        else{
            $student = $query
            ->where([
                ['ou.organization_id', $oid],
                ['ou.user_id', Auth::user()->id],
                ['cs.status', 1],
            ])
            ->select('students.nama', 'students.id')
            ->distinct()
            ->get();
        }

        return response()->json(['success' => $student]);
    }

    public function schoolStudent(Request $request)
    {
        $oid = $request->get('oid');
        $student = DB::table('students')
        ->join('class_student as cs', 'cs.student_id', '=', 'students.id')
        ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
        ->where([
            ['co.organization_id', $oid],
            ['cs.status', 1],
            ['cs.dorm_id',null]
        ])
        ->select('students.nama', 'students.id')
        ->distinct()
        ->get();

        return response()->json(['success' => $student]);
    }

    public function fetchCategory(Request $request)
    {
        $sid = $request->get('sid');
        $oid = $request->get('oid');
        $categoryReal = $this->categoryReal;
        
        // get the category to display for the selected student
        $studentouting = DB::table('student_outing')
            ->join('class_student as cs', 'cs.id', '=', 'student_outing.class_student_id')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->join('students', 'students.id', '=', 'cs.student_id')
            ->join('classifications', 'classifications.id', '=', 'student_outing.classification_id')
            ->where([
                ['students.parent_tel', Auth::user()->telno],
                ['students.id', $sid],
                ['cs.status', 1],
                ['student_outing.status', '>', 0], //get processed application
            ])
            ->whereYear('student_outing.apply_date_time', now()->year)
            ->select(
                'students.id',
                'cs.dorm_id',
                'classifications.id as cid',
                'classifications.name as catname',
                'classifications.fake_name',
                DB::raw('count("student_outing.id") as total')
            )
            ->groupBy('classifications.name')
            ->get();

        $dormid = DB::table("class_student")
        ->where('class_student.student_id', $sid)
        ->value('class_student.dorm_id');

        $category = DB::table('classifications')
                    ->where('classifications.organization_id', $oid)
                    ->get();

        $start = date('Y-m-d', strtotime(DB::table('outings')
            ->where([
                ['outings.organization_id', $oid],
                ['outings.end_date_time', '>', now()],
            ])
            ->orderBy("outings.start_date_time")
            ->value("outings.start_date_time as start_date_time")));

        $end = date('Y-m-d', strtotime(DB::table('outings')
            ->where([
                ['outings.organization_id', $oid],
                ['outings.end_date_time', '>', now()],
            ])
            ->orderBy("outings.end_date_time")
            ->value("outings.end_date_time as end_date_time")));

        // outing history exist
        if(count($studentouting) > 0){
            foreach($studentouting as $studentouting){
                for($i=0, $max=count($category); $i<$max; $i++){
                    if($dormid != null){
                        if($category[$i]->limit > 0){
                            if(strtoupper($category[$i]->name) == strtoupper($studentouting->catname) && $studentouting->total >= $category[$i]->limit){
                                unset($category[$i]);
                            }  
                        }
                    }
                    // student didn't live in dorm
                    else{
                        if(strtoupper($category[$i]->name) != $categoryReal[0]){
                            unset($category[$i]);
                        }
                    }
                }
            }
        }
        // no outing history
        else{
            // student didn't live in dorm
            if($dormid == null){
                for($i=0, $max=count($category); $i<$max; $i++){
                    if(strtoupper($category[$i]->name) != $categoryReal[0]){
                        unset($category[$i]);
                    }
                }
            }
        }
        return response()->json(['success' => $category, 'start' => $start, 'end' => $end, 'studentouting' => $studentouting]);
    }
    
    public function getStudentOutingDatatable(Request $request)
    {
        if (request()->ajax()) {

            $oid = $request->oid;
            $rid = $request->rid;
            $this->roles = $rid;
            $hasOrganizaton = $request->hasOrganization;

            $data = DB::table('students')
                ->join('class_student as cs', 'cs.student_id', '=', 'students.id')
                ->join('student_outing as so', 'so.class_student_id', '=', 'cs.id')
                ->join('organization_user_student as ous', 'ous.student_id', '=', 'students.id')
                ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
                ->join('organization_roles as or', 'or.id', '=', 'ou.role_id')
                ->join('classifications', 'classifications.id', '=', 'so.classification_id');

            if ($oid != '' && !is_null($hasOrganizaton)) {
                //can view all application
                if ($rid == 6) {
                    $data = $data
                        ->where([
                            ['ou.organization_id', $oid],
                            ['ou.user_id', Auth::user()->id],
                        ])
                        ->select(
                            'so.id as id',
                            'students.nama',
                            'students.parent_tel',
                            'so.apply_date_time',
                            'so.out_date_time',
                            'so.arrive_date_time',
                            'so.in_date_time',
                            'so.status as status',
                            'so.reason',
                            'cs.blacklist',
                            'cs.outing_limit',
                            'classifications.name as catname',
                            'classifications.fake_name'
                        )
                        ->orderBy('so.apply_date_time', 'desc')
                        ->groupBy('so.id')
                        ->get();

                }
                //approved or kecemasan && havent expired
                else if ($rid == 8) {
                    $data = $data
                        ->where([
                            ['ou.organization_id', $oid],
                            ['so.in_date_time', NULL],
                            ['so.status', '<>', 2],
                        ])
                        ->select(
                            'so.id as id',
                            'students.nama',
                            'students.parent_tel',
                            'so.apply_date_time',
                            'so.out_date_time',
                            'so.arrive_date_time',
                            'so.in_date_time',
                            'so.status as status',
                            'so.reason',
                            'cs.blacklist',
                            'cs.outing_limit',
                            'classifications.name as catname',
                            'classifications.fake_name'
                        )
                        ->orderBy('so.apply_date_time', 'desc')
                        ->groupBy('so.id')
                        ->get();
                        
                }
                else if($rid == 1){
                    $data = $data
                        ->where([
                            ['ou.organization_id', $oid],
                        ])
                        ->select(
                            'so.id as id',
                            'students.nama',
                            'students.parent_tel',
                            'so.apply_date_time',
                            'so.out_date_time',
                            'so.arrive_date_time',
                            'so.in_date_time',
                            'so.status as status',
                            'so.reason',
                            'cs.blacklist',
                            'cs.outing_limit',
                            'classifications.name as catname',
                            'classifications.fake_name'
                        )
                        ->orderBy('so.apply_date_time')
                        ->groupBy('so.id')
                        ->get();
                }
                // pending && havent expired
                else {
                    $data = $data
                        ->where([
                            ['ou.organization_id', $oid],
                            // ['so.status', 0],
                            // ['so.apply_date_time', '>=', now()->toDateString()],
                        ])
                        ->select(
                            'so.id as id',
                            'students.nama',
                            'students.parent_tel',
                            'so.apply_date_time',
                            'so.out_date_time',
                            'so.arrive_date_time',
                            'so.in_date_time',
                            'so.status as status',
                            'so.reason',
                            'cs.blacklist',
                            'cs.outing_limit',
                            'classifications.name as catname',
                            'classifications.fake_name'
                        )
                        ->orderBy('so.apply_date_time')
                        ->groupBy('so.id')
                        ->get();
                }
            }

            if (isset($data)) {
                $table = Datatables::of($data);

                $table->addColumn('result', function ($row) {
                    $btn = '<div class="d-flex justify-content-center">';
                    if ($row->status == 0) {
                        $btn = $btn . '<span class="badge badge-danger"> Diproses </span></div>';
                    } elseif ($row->status == 1) {
                        $btn = $btn . '<span class="badge badge-success"> Sah </span></div>';
                    } elseif ($row->status == 2) {
                        $btn = $btn . '<span class="badge badge-danger"> Ditolak </span></div>';;
                    }
                    return $btn;
                });

                $table->addColumn('action', function ($row) {

                    $categoryReal = $this->categoryReal;
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $id = $this->roles." ".$row->id;
                    if ($row->status == 0) {  //havent approved
                        //user is penjaga
                        if ($this->roles == 6 || $this->roles == 1) {
                            //
                            if ($row->apply_date_time >= now()->toDateString()) {
                                $btn = $btn . '<a href="' . route('dorm.edit', $row->id) . '" class="btn btn-primary m-1">Ubah</a>';
                            }
                            //del btn
                            $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger deleteBtn m-1">Buang</button></div>';
                        }
                        //user is guard and outing category is balik kecemasan
                        if (($this->roles == 8 && strtoupper($row->catname) == $categoryReal[0]) ||  $this->roles == 1) {
                            if ($row->out_date_time == NULL && $row->in_date_time == NULL && $row->arrive_date_time == NULL && $row->apply_date_time == now()->toDateString()) {
                                $btn = $btn . '<a href="' . route('dorm.updateOutTime', $id) . '" class="btn btn-primary m-1">Keluar</a>';
                            }
                        }
                        //user is warden, teacher, HEM
                        if (
                            $this->roles == 1 || $this->roles == 4 ||  $this->roles == 2
                            || $this->roles == 5 || $this->roles == 7
                        ) {
                            //user choose outings and is inside blacklist
                            if (strtoupper($row->catname) == $categoryReal[2] && $row->blacklist == 1) {
                                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger unblockBtn m-1">Unblock</button></div>';
                            }
                            //
                            else {
                                $btn = $btn . '<a href="' . route('dorm.updateApprove', $id) . '" class="btn btn-primary m-1">Approve</a>';
                                $btn = $btn . '<a href="' . route('dorm.updateTolak', $id) . '" class="btn btn-danger m-1">Tolak</a>';
                            }
                        }
                    } elseif ($row->status == 1) { //approved
                        if ($this->roles == 6 ||  $this->roles == 1) {
                            if ($row->in_date_time == NULL && $row->arrive_date_time == NULL && $row->out_date_time != NULL) {
                                $btn = $btn . '<a href="' . route('dorm.updateArriveTime', $id) . '" class="btn btn-primary m-1">Sampai</a>';
                            }
                        }
                        if ($this->roles == 8 ||  $this->roles == 1) {

                            if ($row->out_date_time == NULL && $row->in_date_time == NULL && $row->arrive_date_time == NULL && $row->apply_date_time >= now()->toDateString()) {

                                $btn = $btn . '<a href="' . route('dorm.updateOutTime', $id) . '" class="btn btn-primary m-1">Keluar</a>';
                            }

                            if ($row->in_date_time == NULL && $row->arrive_date_time != NULL && $row->out_date_time != NULL) {
                                $btn = $btn . '<a href="' . route('dorm.updateInTime', $id) . '" class="btn btn-primary m-1">Masuk</a>';
                            }
                        }
                    } elseif ($row->status == 2) {  //ditolak
                        if ($this->roles == 6 ||  $this->roles == 1) {
                            $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger deleteBtn m-1">Buang</button></div>';
                        }
                    }
                    return $btn;
                });

                $table->rawColumns(['result', 'action']);
                return $table->make(true);
            }
        }
    }

    //application functions
    public function updateApprove($id)
    {
        // [0]roleid [1]studentoutingid
        $id = explode(" ", $id);
        if (
            Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir') ||
            Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Warden')
        ) {
            $studentouting = DB::table('student_outing')
                ->where('student_outing.id', $id[1])
                ->update([
                    'student_outing.status' => 1,
                    'student_outing.warden_id' => Auth::user()->id,
                ]);
        } elseif (Auth::user()->hasRole('Guard')) {
            $studentouting = DB::table('student_outing')
                ->where('student_outing.id', $id[1])
                ->update([
                    'student_outing.status' => 1,
                    'student_outing.guard_id' => Auth::user()->id,
                ]);
        }

        if ($studentouting) {
            $getOrganization = DB::table('users')
                ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                ->where('users.id', '=', Auth::user()->id)
                ->whereIn('organization_user.role_id', [4, 7])
                ->value('organization_user.organization_id');

            $getTelno = DB::table('student_outing')
                ->join('class_student', 'class_student.id', '=', 'student_outing.class_student_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->where('student_outing.id', $id[1])
                ->value('students.parent_tel');

            $parent = DB::table('users')
                ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
                ->join('students', 'students.id', '=', 'organization_user_student.student_id')
                ->where('students.parent_tel', '=', $getTelno)
                ->value('users.id');
            $arrayRecipientEmail = DB::table('users')
                ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                ->where([
                    ['organization_user.role_id', '=', 4],
                    ['organization_user.organization_id', '=', $getOrganization],
                ])
                ->orWhere([
                    ['organization_user.role_id', '=', 6],
                    ['users.id', '=', $parent],
                    ['organization_user.organization_id', '=', $getOrganization],
                ])
                ->select('users.email')
                ->get();


            // dd($arrayRecipientEmail);

            if (isset($arrayRecipientEmail)) {
                foreach ($arrayRecipientEmail as $email) {
                    // dd("here inside foreach");
                    $student = DB::table('student_outing')
                        ->join('class_student', 'class_student.id', '=', 'student_outing.class_student_id')
                        ->join('students', 'students.id', '=', 'class_student.student_id')
                        ->where('student_outing.id', $id[1])
                        ->value('students.nama');
                    $status = DB::table('student_outing')
                        ->where('student_outing.id', $id[1])
                        ->value('student_outing.status');

                    if ($status == 1)
                        $statusApprove = "disahkan";
                    else if ($status == 2)
                        $statusApprove = "ditolakkan";
                    Mail::to($email)->send(new NotifyApproval($student, $statusApprove));


                    if (Mail::failures()) {
                        // dd("fail");
                        return response()->Fail('Sorry! Please try again latter');
                    } else {
                        // return response()->success('Great! Successfully send in your mail');
                        // dd("successs", $email);
                    }
                }
            } else {
                // do nothing 1st
            }
            return redirect('/sekolah/dorm/indexRequest/'.$id[0])->with('success', 'Permintaan pelajar telah disahkan');
        } else
            return redirect('/sekolah/dorm/indexRequest/'.$id[0])->withErrors('Kemaskini data tidak berjaya');
    }

    public function updateTolak($id)
    {
        // [0]roleid [1]studentoutingid
        $id = explode(" ", $id);
        if (
            Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir') ||
            Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Warden')
        ) {
            $studentouting = DB::table('student_outing')
                ->where('student_outing.id', $id[1])
                ->update([
                    'student_outing.status' => 2,
                    'student_outing.warden_id' => Auth::user()->id,
                ]);
        }

        if ($studentouting) {
            $getOrganization = DB::table('users')
                ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                ->where('users.id', '=', Auth::user()->id)
                ->whereIn('organization_user.role_id', [4, 7])
                ->value('organization_user.organization_id');

            $getTelno = DB::table('student_outing')
                ->join('class_student', 'class_student.id', '=', 'student_outing.class_student_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->where('student_outing.id', $id[1])
                ->value('students.parent_tel');

            $parent = DB::table('users')
                ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
                ->join('students', 'students.id', '=', 'organization_user_student.student_id')
                ->where('students.parent_tel', '=', $getTelno)
                ->value('users.id');

            $arrayRecipientEmail = DB::table('users')
                ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                ->where([
                    ['organization_user.role_id', '=', 4],
                    ['organization_user.organization_id', '=', $getOrganization],
                ])
                ->orWhere([
                    ['organization_user.role_id', '=', 6],
                    ['users.id', '=', $parent],
                    ['organization_user.organization_id', '=', $getOrganization],
                ])
                ->select('users.email')
                ->distinct()
                ->get();
            // dd($arrayRecipientEmail);

            if (isset($arrayRecipientEmail)) {
                foreach ($arrayRecipientEmail as $email) {
                    // dd("here inside foreach");
                    // Mail::to($email)->send(new NotifyMail());
                    $student = DB::table('student_outing')
                        ->join('class_student', 'class_student.id', '=', 'student_outing.class_student_id')
                        ->join('students', 'students.id', '=', 'class_student.student_id')
                        ->where('student_outing.id', $id[1])
                        ->value('students.nama');
                    $status = DB::table('student_outing')
                        ->where('student_outing.id', $id[1])
                        ->value('student_outing.status');
                    if ($status == 1)
                        $statusApprove = "disahkan";
                    else if ($status == 2)
                        $statusApprove = "ditolakkan";
                    Mail::to($email)->send(new NotifyApproval($student, $statusApprove));


                    if (Mail::failures()) {
                        // dd("fail");
                        return response()->Fail('Sorry! Please try again latter');
                    } else {
                        // return response()->success('Great! Successfully send in your mail');
                        // dd("successs", $email);
                    }
                }
            } else {
                // do nothing 1st
                // dd("gg");
            }
            return redirect('/sekolah/dorm/indexRequest/'.$id[0])->with('success', 'Permintaan pelajar ditolak');
        } else
            return redirect('/sekolah/dorm/indexRequest/'.$id[0])->withErrors('Kemaskini data tidak berjaya');
    }

    public function updateOutTime($id)
    {
        $id = explode(" ", $id);
        $categoryReal = $this->categoryReal;
        $catname = DB::table('student_outing')
            ->join('classifications', 'classifications.id', '=', 'student_outing.classification_id')
            ->where([
                ['student_outing.id', $id[1]],
            ])
            ->value('classifications.name');

        $outinglimit = (int)DB::table('student_outing')
            ->join('class_student as cs', 'cs.id', '=', 'student_outing.class_student_id')
            ->where([
                ['student_outing.id', $id[1]],
            ])
            ->value('cs.outing_limit');

        if (strtoupper($catname) == $categoryReal[0]) {
            DB::table('student_outing')
                ->join('class_student as cs', 'cs.id', '=', 'student_outing.class_student_id')
                ->where([
                    ['student_outing.id', $id[1]],
                ])
                ->update([
                    'student_outing.status' => 1,
                    'student_outing.out_date_time' => now()->toDateTimeString(),
                    'cs.outing_status' => 1,
                    'student_outing.guard_id' => Auth::user()->id,
                ]);
        } else if (strtoupper($catname) == $categoryReal[1]) {
            DB::table('student_outing')
                ->join('class_student as cs', 'cs.id', '=', 'student_outing.class_student_id')
                ->where([
                    ['student_outing.id', $id[1]],
                    ['student_outing.status', 1],
                ])
                ->update([
                    'student_outing.out_date_time' => now()->toDateTimeString(),
                    'cs.outing_status' => 1,
                    'cs.outing_limit' => $outinglimit + 1,
                    'student_outing.guard_id' => Auth::user()->id,
                ]);
        } else {
            DB::table('student_outing')
                ->join('class_student as cs', 'cs.id', '=', 'student_outing.class_student_id')
                ->where([
                    ['student_outing.id', $id[1]],
                    ['student_outing.status', 1],
                ])
                ->update([
                    'student_outing.out_date_time' => now()->toDateTimeString(),
                    'cs.outing_status' => 1,
                    'student_outing.guard_id' => Auth::user()->id,
                ]);
        }

        $query = DB::table('student_outing')
            ->join('class_student as cs', 'cs.id', '=', 'student_outing.class_student_id')
            ->join('students', 'students.id', '=', 'cs.student_id')
            ->where([
                ['student_outing.id', $id[1]],
                ['student_outing.status', 1],
            ]);

        $telno = $query
            ->value('students.parent_tel');

        $oid = $query->join('classifications', 'classifications.id', '=', 'student_outing.classification_id')
            ->value('classifications.organization_id');

        $arrayRecipientEmail = DB::table('users')
            ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
            // ->where('organization_user.role_id', '=', 6)
            // ->orWhere('organization_user.role_id', '=', 4)
            // ->orWhere('organization_user.check_in_status', '=', 1)
            ->where([
                ['organization_user.organization_id', '=', $oid],
                ['organization_user.check_in_status', '=', 1],
            ])
            ->orWhere([
                ['organization_user.role_id', '=', 4],
                ['organization_user.organization_id', '=', $oid],
            ])
            ->orWhere([
                ['organization_user.organization_id', '=', $oid],
                ['users.telno', '=', $telno],
            ])
            ->select('users.email')
            ->distinct()
            ->get();

        // dd($arrayRecipientEmail);

        if (isset($arrayRecipientEmail)) {
            foreach ($arrayRecipientEmail as $email) {
                // dd("here inside foreach");
                // Mail::to($email)->send(new NotifyMail());
                $student = DB::table('student_outing')
                    ->join('class_student', 'class_student.id', '=', 'student_outing.class_student_id')
                    ->join('students', 'students.id', '=', 'class_student.student_id')
                    ->where('student_outing.id', $id[1])
                    ->value('students.nama');

                Mail::to($email)->send(new NotifyOut($student));


                if (Mail::failures()) {
                    // dd("fail");
                    return response()->Fail('Sorry! Please try again latter');
                } else {
                    // return response()->success('Great! Successfully send in your mail');
                    // dd("successs", $email);
                }
            }
        } else {
            // do nothing 1st
            // dd("gg");
        }
        return redirect('/sekolah/dorm/indexRequest/'.$id[0])->with('success', 'Tarikh dan masa keluar telah dicatatkan');
    }

    public function updateInTime($id)
    {
        $id = explode(" ", $id);
        $intime = now();

        $categoryReal = $this->categoryReal;

        $outingcat = DB::table('student_outing')
            ->join('class_student as cs', 'cs.id', '=', 'student_outing.class_student_id')
            ->join('classifications', 'classifications.id', '=', 'student_outing.classification_id')
            ->where('student_outing.id', $id[1])
            ->select('cs.dorm_id', 'classifications.name as catname', 'classifications.time_limit', 'student_outing.apply_date_time', 'cs.blacklist')
            ->get();

        $blacklist = $outingcat[0]->blacklist;

        // if (strtoupper($outingcat[0]->catname) == $categoryReal[2] && isset($outingcat[0]->dorm_id)) {
        if (isset($outingcat[0]->dorm_id)) {
            if(isset($outingcat[0]->time_limit))
            {
                if (strtotime($intime) > strtotime($outingcat[0]->time_limit))
                {
                    $blacklist = 1;
                }
            }

            if ($intime->toDateString() > $outingcat[0]->apply_date_time)
            {
                $blacklist = 1;
            }
        } 
        else if (!isset($dormid)) {
            $blacklist = NULL;
        }

        $result = DB::table('student_outing')
            ->join('class_student as cs', 'cs.id', '=', 'student_outing.class_student_id')
            ->where([
                ['student_outing.id', $id[1]],
                ['student_outing.status', 1],
                ['cs.outing_status', 1],
            ])
            ->update([
                'student_outing.in_date_time' => $intime,
                'cs.outing_status' => 0,
                'cs.blacklist' => $blacklist,
            ]);

        if ($result) {
            $query = DB::table('student_outing')
                ->join('class_student as cs', 'cs.id', '=', 'student_outing.class_student_id')
                ->join('students', 'students.id', '=', 'cs.student_id')
                ->where([
                    ['student_outing.id', $id[1]],
                    ['student_outing.status', 1],
                ]);

            $student = $query
                ->value('students.nama');

            $telno = $query
                ->value('students.parent_tel');

            $oid = $query->join('classifications', 'classifications.id', '=', 'student_outing.classification_id')
                ->value('classifications.organization_id');

            // function to send user email
            // get penjaga id and send to that penjaga only
            $arrayRecipientEmail = DB::table('users')
                ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                ->where([
                    ['organization_user.organization_id', '=', $oid],
                    ['organization_user.check_in_status', '=', 1],
                ])
                ->orWhere([
                    ['organization_user.role_id', '=', 4],
                    ['organization_user.organization_id', '=', $oid],
                ])
                ->orWhere([
                    ['organization_user.organization_id', '=', $oid],
                    ['users.telno', '=', $telno],
                ])
                ->select('users.email')
                ->distinct()
                ->get();
            
            // dd($arrayRecipientEmail);

            if (isset($arrayRecipientEmail)) {
                foreach ($arrayRecipientEmail as $email) {
                    // dd("here inside foreach");
                    Mail::to($email)->send(new NotifyIn($student));

                    if (Mail::failures()) {
                        // dd("fail");
                        return response()->Fail('Sorry! Please try again latter');
                    } else {
                        // return response()->success('Great! Successfully send in your mail');
                        // dd("successs", $email);
                    }
                }
            } else {
                // do nothing 1st
            }
        }

        return redirect('/sekolah/dorm/indexRequest/'.$id[0])->with('success', 'Tarikh dan masa masuk telah dicatatkan');
    }

    public function updateArriveTime($id)
    {
        $id = explode(" ", $id);
        $query = DB::table('student_outing')
            ->join('class_student as cs', 'cs.id', '=', 'student_outing.class_student_id')
            ->where([
                ['student_outing.id', $id[1]],
                ['student_outing.status', 1],
                ['cs.outing_status', 1],
            ]);

        $result = $query
            ->update(['student_outing.arrive_date_time' => now()->toDateTimeString()]);

        if ($result) {
            $student = $query
                ->join('students', 'students.id', '=', 'cs.student_id')
                ->value('students.nama');

            $oid = $query->join('classifications', 'classifications.id', '=', 'student_outing.classification_id')->value('classifications.organization_id');

            // function to send user email
            $arrayRecipientEmail = DB::table('users')
                ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                ->where([
                    ['organization_user.organization_id', '=', $oid],
                    ['organization_user.check_in_status', '=', 1]
                ])
                ->orWhere([
                    ['organization_user.role_id', '=', 4],
                    ['organization_user.organization_id', '=', $oid],
                ])
                ->select('users.email')
                ->distinct()
                ->get();

            // dd($arrayRecipientEmail);
            if (isset($arrayRecipientEmail)) {
                foreach ($arrayRecipientEmail as $email) {
                    // dd("here inside foreach");
                    Mail::to($email)->send(new NotifyArrive($student));

                    if (Mail::failures()) {
                        // dd("fail");
                        return response()->Fail('Sorry! Please try again latter');
                    } else {
                        // return response()->success('Great! Successfully send in your mail');
                        // dd("successs", $email);
                    }
                }
            } else {
                // do nothing 1st
            }
        }


        return redirect('/sekolah/dorm/indexRequest/'.$id[0])->with('success', 'Tarikh dan masa sampai destinasi telah dicatatkan');
    }

    public function updateBlacklist($id)
    {
        DB::table('class_student')
            ->join('student_outing as so', 'so.class_student_id', '=', 'class_student.id')
            ->where('so.id', $id)
            ->update([
                'class_student.blacklist' => 0,
            ]);

        return redirect('/dorm/index')->with('success', 'Data is successfully updated');
    }

    public function updateCheckIn($id)
    {
        // dd($id);
        preg_match_all('!\d+!', $id, $rolesCheckIn);

        
        $organization = $this->getOrganizationByUserId();

        if(count($rolesCheckIn[0]) == 1){
            $rolesCheckIn[0][1] = 0;
        }

        //is superadmin
        if(Auth::user()->hasRole('Superadmin')){
            if ((int)$rolesCheckIn[0][1] == 1) {
                
                $result = DB::table('organization_user as ou')
                    ->where([
                        ['ou.user_id', Auth::user()->id],
                    ])
                    ->update([
                        'ou.check_in_status' => 0,
                    ]);
            }
            else {
                $result = DB::table('organization_user as ou')
                    ->where([
                        ['ou.user_id', Auth::user()->id],
                    ])
                    ->update([
                        'ou.check_in_status' => 1,
                    ]);
            } 
        }

        if ((int)$rolesCheckIn[0][1] == 1) {
            $result = DB::table('organization_user as ou')
                ->where([
                    ['ou.organization_id', $organization[0]->id],
                    ['ou.user_id', Auth::user()->id],
                ])
                ->update([
                    'ou.check_in_status' => 0,
                ]);
        }
        else {
            $result = DB::table('organization_user as ou')
                ->where([
                    ['ou.organization_id', $organization[0]->id],
                    ['ou.user_id', Auth::user()->id],
                ])
                ->update([
                    'ou.check_in_status' => 1,
                ]);
        } 

        
        if($result){
            return redirect('/sekolah/dorm/indexRequest/'.(int)$rolesCheckIn[0][0])->with('success', 'Data is successfully updated');
        }
        else{
            return redirect('/sekolah/dorm/indexRequest/'.(int)$rolesCheckIn[0][0]);
        }
    }

    public function resetOutingLimit(Request $request)
    {   
        dd($request->oid);
        if (Auth::user()->hasRole('Superadmin')) {
            $result = DB::table('class_student')
                ->join('class_organization as co', 'co.id', '=', 'class_student.organclass_id')
                ->where([
                    ['class_student.status', 1],
                ])
                ->update(['class_student.outing_limit' => NULL]);
        } else {
            $result = DB::table('class_student')
                ->join('class_organization as co', 'co.id', '=', 'class_student.organclass_id')
                ->where([
                    ['class_student.status', 1],
                    ['co.organization_id', $oid],
                ])
                ->update(['class_student.outing_limit' => NULL]);
        }

        if ($result) {
            return redirect('/sekolah/dorm/indexReportAll')->with('success', 'Data is successfully updated');
        } else {
            return redirect('/sekolah/dorm/indexReportAll')->withErrors('Failed to update data');
        }
    }
}
