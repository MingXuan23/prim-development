<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Fee;
use App\Models\Fee_New;
use App\Models\Category;
use App\Models\ClassModel;
use App\Models\Transaction;
use App\Models\Organization;
use Illuminate\Http\Request;
use Psy\Command\WhereamiCommand;
use Yajra\DataTables\DataTables;
use App\Exports\ExportYuranStatus;
use App\Exports\ExportYuranStatusSwasta;

use App\Exports\ExportJumlahBayaranIbuBapa;
use App\Exports\ExportJumlahBayaranIbuBapaSwasta;
use App\Exports\ExportClassTransaction;
use App\Exports\ExportTransaction;

use App\Exports\ExportYuranOverview;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AppBaseController;
use Symfony\Component\VarDumper\Cloner\Data;

use App\Imports\AssignFeeByParentIncome;
use SebastianBergmann\Type\TrueType;

class FeesController extends AppBaseController
{
    function __construct()
    {
        $this->updateStatusFees();
    }

    public function index()
    {
        //
        $fees = DB::table('fees')->orderBy('nama')->get();
        $organization = $this->getOrganizationByUserId();
        $listcategory = DB::table('categories')->get();
        return view('pentadbir.fee.index', compact('fees', 'listcategory', 'organization'));
    }

    public function updateStatusFees()
    {
        DB::table('fees_new')
            ->whereDate('end_date', '<', Carbon::now()->toDateString())
            ->update([
                'status' => '0'
            ]);
    }


    public function create()
    {
        $organization = $this->getOrganizationByUserId();

        return view('pentadbir.fee.add', compact('organization'));
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $fee = DB::table('fees_new as fn')
            ->join('organizations as o', 'o.id', '=', 'fn.organization_id')
            ->select(
                'fn.id as feeid',
                'fn.name as feename',
                'fn.category',
                'fn.totalamount',
                'fn.start_date',
                'fn.end_date',
                'fn.desc',
                'o.id as organization_id',
                'o.type_org'
            )
            ->where('fn.id', $id)
            ->first();

        $organization = $this->getOrganizationByUserId();
        return view('pentadbir.fee.update', compact('fee', 'organization'));
    }

    public function update(Request $request, $id)
    {
        $feeCategory = DB::table("fees_new")
            ->where("id", $id)
            ->select("category")
            ->first();

        $date_started = Carbon::createFromFormat(config('app.date_format'), $request->get('date_started'))->format('Y-m-d');
        $date_end = Carbon::createFromFormat(config('app.date_format'), $request->get('date_end'))->format('Y-m-d');

        DB::table("fees_new")
            ->where("id", $id)
            ->update([
                "name" => $request->get('name'),
                "desc" => $request->get("description"),
                "start_date" => $date_started,
                "end_date" => $date_end
            ]);

        if ($feeCategory->category == "Kategori A") {
            return redirect()->route('fees.A');
        } else if ($feeCategory->category == "Kategori B") {
            return redirect()->route('fees.B');
        } else if ($feeCategory->category == "Kategori C") {
            return redirect()->route('fees.C');
        } else {
            return redirect()->route('fees.Recurring');
        }
    }

    public function destroy($id)
    {
        Session::flash('error', 'Yuran Gagal Dibuang');
        return View::make('layouts/flash-messages');

        $result = DB::table('fees_new')
            ->where('id', '=', $id)
            ->delete();

        /*  $result = DB::table('fees_new')
            ->where('id', '=', $id)
            ->update([
                'status'        =>  '0'
            ]); */

        if ($result) {
            Session::flash('success', 'Yuran Berjaya Dibuang');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Yuran Gagal Dibuang');
            return View::make('layouts/flash-messages');
        }
    }

    // temporary page for admins to look at their shirt bought
    public function adminUpdateShirtSizeIndex()
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole('Superadmin') && !Auth::user()->hasRole('Pentadbir') && !Auth::user()->hasRole('Guru')) {
            return redirect('/home');
        }

        if (Auth::user()->hasRole('Superadmin')) {
            $organizations = DB::table("organizations as o")
                ->where("nama", "PIBG SMS Muzaffar Syah")
                ->select('o.*')
                ->get();
        } else {
            $organizations = DB::table("organizations as o")
                ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
                ->where('ou.user_id', Auth::id())
                ->where("nama", "PIBG SMS Muzaffar Syah")
                ->select('o.*')
                ->distinct()
                ->get();
        }

        return view('fee.update_shirt_size.admin.index', compact("organizations"));
    }

    // temporary page for users to look at their shirt bought
    public function buyerUpdateShirtSizeIndex()
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole('Superadmin') && !Auth::user()->hasRole('Penjaga')) {
            return redirect('/home');
        }

        if (Auth::user()->hasRole('Superadmin')) {
            $organizations = DB::table("organizations as o")
                ->where("nama", "PIBG SMS Muzaffar Syah")
                ->select('o.*')
                ->get();
        } else {
            $organizations = DB::table("organizations as o")
                ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
                ->where('ou.user_id', Auth::id())
                ->where("nama", "PIBG SMS Muzaffar Syah")
                ->select('o.*')
                ->get();
        }

        return view('fee.update_shirt_size.buyer.index', compact("organizations"));
    }

    // temporary page for buyers to choose their shirt size
    public function editShirtSize(Request $request)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole('Superadmin') && !Auth::user()->hasRole('Penjaga')) {
            return redirect('/home');
        }

        $fee = DB::table("fees_new")
            ->where("id", $request->get('fees_id'))
            ->select('id', 'name', 'desc', 'quantity', 'price', 'totalAmount')
            ->first();

        $organization = DB::table("organizations as o")
            ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
            ->where('ou.user_id', Auth::id())
            ->where("nama", "PIBG SMS Muzaffar Syah")
            ->select('o.*')
            ->get();

        $studentId = $request->get("student_id");
        $shirtSize = null;
        $responseId = null;
        $shirtSizeResponses = DB::table("shirt_size_responses")->get();

        foreach ($shirtSizeResponses as $response) {
            $data = json_decode($response->response);

            if ($fee->id == $data->fees_id && $data->student_id == $studentId && $data->user_id == Auth::id()) {
                $shirtSize = $data->shirt_size;
                $responseId = $response->id;
                break;
            }
        }


        return view('fee.update_shirt_size.buyer.edit', compact("fee", "organization", "studentId", "shirtSize", "responseId"));
    }

    // temporary function for users to update their shirt size
    public function updateShirtSize(Request $request)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole('Superadmin') && !Auth::user()->hasRole('Penjaga')) {
            return redirect('/home');
        }

        $responseId = $request->get('response_id');
        if ($responseId == null) {
            // if user has not chosen shirt size before, create new response
            $response = array(
                "shirt_size" => $request->get("shirt_size"),
                "fees_id" => $request->get("fees_id"),
                "user_id" => Auth::id(),
                "student_id" => $request->get('student_id')
            );

            $jsonResponse = json_encode($response);

            DB::table("shirt_size_responses")
                ->insert([
                    "response" => $jsonResponse
                ]);
        } else {
            // if user has already chose shirt size before, update their previous response
            $response = array(
                "shirt_size" => $request->get("shirt_size"),
                "fees_id" => $request->get("fees_id"),
                "user_id" => Auth::id(),
                "student_id" => $request->get('student_id')
            );

            DB::table("shirt_size_responses")
                ->update([
                    "response" => $response
                ]);
        }

        return redirect()->route('fees.updateShirtSize.buyer.index')->with('success', 'Kemaskini baju anda telah disimpan.');
    }

    // temporary method for frontend to get data about shirt yuran
    public function getShirtYuranDatatable(Request $request)
    {
        $user_id = Auth::id();

        if ($user_id == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole('Superadmin') && !Auth::user()->hasRole('Penjaga')) {
            return redirect('/home');
        }

        $organization_id = $request->get('oid');

        if (Auth::user()->hasRole('Superadmin')) {
            $feesPaid = DB::table("fees_new as fn")
                ->join('student_fees_new as sfn', 'fn.id', '=', 'sfn.fees_id')
                ->where("fn.organization_id", $organization_id)
                ->where('sfn.status', 'Paid')
                ->where('fn.name', 'LIKE', '%BAJU%')
                ->select('fn.id', 'fn.name', 'fn.desc', 'fn.quantity', 'fn.price', 'fn.totalAmount')
                ->get();
        } else {
            $feesPaid = DB::table("fees_new as fn")
                ->join('student_fees_new as sfn', 'fn.id', '=', 'sfn.fees_id')
                ->join('class_student as cs', 'cs.id', '=', 'sfn.class_student_id')
                ->join('students as s', 'cs.student_id', '=', 's.id')
                ->join('organization_user_student as ous', 'ous.student_id', '=', 's.id')
                ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
                ->where("fn.organization_id", $organization_id)
                ->where('sfn.status', 'Paid')
                ->where('ou.user_id', $user_id)
                ->where('fn.name', 'LIKE', '%BAJU%')
                ->select('fn.id', 'fn.name as fee_name', 'fn.desc', 'fn.quantity', 'fn.price', 'fn.totalAmount', 's.id as student_id', 's.nama as student_name')
                ->get();
        }

        $shirtSizeResponses = DB::table("shirt_size_responses")->get();

        $table = Datatables::of($feesPaid);

        $table->addColumn('action', function ($row) {
            return "<div class='d-flex justify-content-center'>" .
                "<a class='btn btn-primary' href='" .
                route('fees.updateShirtSize.buyer.editShirtSize', ['fees_id' => $row->id, 'student_id' => $row->student_id]) .
                "'>Kemaskini</a>" .
                "</div>";
        });

        return $table->make(true);
    }

    // temporary method for frontend to get responses (updated shirt size)
    public function getShirtSizeResponsesDatatable()
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole('Superadmin') && !Auth::user()->hasRole('Pentadbir') && !Auth::user()->hasRole('Guru')) {
            return redirect('/home');
        }

        $responses = DB::table("shirt_size_responses")->get();

        $table = Datatables::of($responses)
            ->addColumn('fee_name', function ($row) {
                $response = json_decode($row->response);

                $feeName = DB::table("fees_new")
                    ->where("id", $response->fees_id)
                    ->select("name")
                    ->value('name');

                return $feeName;
            })
            ->addColumn('desc', function ($row) {
                $response = json_decode($row->response);

                $desc = DB::table("fees_new")
                    ->where("id", $response->fees_id)
                    ->select("desc")
                    ->value('desc');

                return $desc;
            })
            ->addColumn('quantity', function ($row) {
                $response = json_decode($row->response);

                $feeQuantity = DB::table("fees_new")
                    ->where("id", $response->fees_id)
                    ->select("quantity")
                    ->value('quantity');

                return $feeQuantity;
            })
            ->addColumn('price', function ($row) {
                $response = json_decode($row->response);

                $price = DB::table("fees_new")
                    ->where("id", $response->fees_id)
                    ->select("price")
                    ->value('price');

                return $price;
            })
            ->addColumn('total_amount', function ($row) {
                $response = json_decode($row->response);

                $totalAmount = DB::table("fees_new")
                    ->where("id", $response->fees_id)
                    ->select("totalAmount")
                    ->value('totalAmount');

                return $totalAmount;
            })
            ->addColumn('penjaga_name', function ($row) {
                $response = json_decode($row->response);

                $penjagaName = DB::table("users")
                    ->where("id", $response->user_id)
                    ->select("name")
                    ->value('name');

                return $penjagaName;
            })
            ->addColumn('student_name', function ($row) {
                $response = json_decode($row->response);

                $studentName = DB::table("students")
                    ->where("id", $response->student_id)
                    ->select("nama")
                    ->value('nama');

                return $studentName;
            })
            ->addColumn('shirt_size', function ($row) {
                $response = json_decode($row->response);

                return $response->shirt_size;
            });

        return $table->make(true);
    }

    public function getOrganizationByUserId()
    {
        $userId = Auth::id();

        if (Auth::user()->hasRole('Superadmin')) {
            return Organization::all();

            //wan add pentadbir swasta
        } elseif (Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Koop Admin') || Auth::user()->hasRole('Pentadbir Swasta') || Auth::user()->hasRole('Guru Swasta')) {

            // user role pentadbir n guru
            /* $temp_organs= Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->Where(function ($query) {
                    $query->where('organization_user.role_id', '=', 4)
                        ->Orwhere('organization_user.role_id', '=', 5)
                        ->Orwhere('organization_user.role_id', '=', 12);
                });
            })->get(); */

            $organs = DB::table('organizations as o')
                ->leftJoin('organization_user as ou', 'o.id', 'ou.organization_id')
                ->select('o.*')
                ->where('ou.user_id', $userId)
                ->whereNull('o.deleted_at')
                ->whereIn('ou.role_id', [4, 5, 12, 20, 21])
                ->get();

            $organizations = [];

            foreach ($organs as $organ) {
                array_push($organizations, $organ);
                $organ_children = DB::table('organizations')->where('parent_org', $organ->id)->get();

                if ($organ != null) {
                    foreach ($organ_children as $organ_child) {
                        array_push($organizations, $organ_child);
                    }
                }
            }

            return $organizations;
        } else {
            // user role ibu bapa
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('role_id', '6')->OrWhere('role_id', '7')->OrWhere('role_id', '8');
            })->get();
        }
    }

    public function fetchYear(Request $request)
    {
        $oid = $request->get('oid');
        $category = Category::where('organization_id', $oid)->get();

        $list = DB::table('organizations')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
            ->where('organizations.id', $oid)
            ->first();

        return response()->json(['success' => $list, 'category' => $category]);
    }


    public function fetchClass(Request $request)
    {

        // dd($request->get('schid'));
        $oid = $request->get('oid');
        $year = $request->get('year');

        $organization = Organization::find($oid);

        if ($organization->parent_org != null) {
            $oid = $organization->parent_org;
        }

        $list = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'classes.id as cid', 'classes.nama as cname')
            ->where('organizations.id', $oid)
            ->where('classes.nama', 'LIKE', $year . '%')
            ->where('classes.status', 1)
            ->orderBy('classes.nama')
            ->get();

        return response()->json(['success' => $list]);
    }

    public function feesReport()
    {
        $organization = $this->getOrganizationByUserId();

        $all_student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', 22)
            ->count();

        // dd($all_student);
        $student_complete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', 22)
            ->where('class_student.fees_status', 'Completed')
            ->count();

        $student_notcomplete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', 22)
            ->where('class_student.fees_status', 'Not Complete')
            ->count();

        $all_parent = DB::table('organization_user')
            ->where('organization_id', 22)
            ->where('role_id', 6)
            ->where('status', 1)
            ->count();

        $parent_complete = DB::table('organization_user')
            ->where('organization_id', 22)
            ->where('role_id', 6)
            ->where('status', 1)
            ->where('fees_status', 'Completed')
            ->count();

        $parent_notcomplete = DB::table('organization_user')
            ->where('organization_id', 22)
            ->where('role_id', 6)
            ->where('status', 1)
            ->where('fees_status', 'Not Complete')
            ->count();

        // dd($all_student);

        return view('fee.report', compact('organization', 'all_student', 'student_complete', 'student_notcomplete', 'all_parent', 'parent_complete', 'parent_notcomplete'));
    }

    public function feesReportByOrganizationId(Request $request)
    {
        set_time_limit(120);
        $organization = Organization::find($request->oid);
        $oid = $organization->parent_org != null ? $organization->parent_org : $organization->id;
        //makesure student from parent_org is fetched
        $all_student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')

            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', 'class_organization.class_id')
            ->where('classes.levelid', '>', 0)
            ->where('class_organization.organization_id', $oid)
            ->where('class_student.status', 1)
            ->select('class_student.id as csid');

        foreach ($all_student->get() as $s) {
            $check_debt = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
                ->join('fees_new', 'fees_new.id', 'student_fees_new.fees_id')
                ->select('students.*')
                ->where('fees_new.status', 1)

                ->where('class_student.id', $s->csid)
                ->where('student_fees_new.status', 'Debt')
                ->count();

            if ($check_debt == 0) {
                DB::table('class_student')
                    ->where('id', $s->csid)
                    ->update(['fees_status' => 'Completed']);
            } else {
                DB::table('class_student')
                    ->where('id', $s->csid)
                    ->update(['fees_status' => 'Not Complete']);
            }
        }
        // dd($all_student);
        // $student_complete = DB::table('students')
        //     ->join('class_student', 'class_student.student_id', '=', 'students.id')
        //     ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
        //     ->where('class_organization.organization_id', $oid)
        //     ->where('class_student.fees_status', 'Completed')
        //     ->count();
        $student_complete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->where('classes.levelid', '>', 0)
            ->where('class_organization.organization_id', $oid)
            ->where('class_student.fees_status', 'Completed')
            ->where('class_student.status', 1)
            ->count();
        $student_notcomplete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->where('classes.levelid', '>', 0)
            ->where('class_organization.organization_id', $oid)
            ->where('class_student.status', 1)
            ->where('class_student.fees_status', 'Not Complete')
            ->count();
        $all_student = $student_complete + $student_notcomplete;

        $oid = $request->oid; //change back the children org if necessary
        $all_parent = DB::table('organization_user')
            ->where('organization_id', $oid)
            //->whereIn('organization_id',[160,159,154,153,152,151,150,149,148,147,146,145,144,143,142,141,137,127,107,106,93,88,80])
            ->where('role_id', 6)
            ->where('status', 1);


        foreach ($all_parent->get() as $p) {
            $check_debt = DB::table('organization_user')
                ->join('fees_new_organization_user', 'fees_new_organization_user.organization_user_id', '=', 'organization_user.id')
                ->join('fees_new', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id') // Ensure column name is correct here
                ->where('organization_user.id', $p->id)
                ->where('organization_user.role_id', 6)
                ->where('organization_user.status', 1)
                ->where('fees_new.status', 1)
                ->where('fees_new_organization_user.status', 'Debt')
                ->count();

            if ($check_debt == 0) {

                DB::table('organization_user')
                    ->where('id', $p->id)
                    ->where('role_id', 6)
                    ->where('status', 1)
                    ->update(['fees_status' => 'Completed']);
            }
        }



        // $parent_complete =  DB::table('organization_user')
        //     ->where('organization_id', $oid)
        //     ->where('role_id', 6)
        //     ->where('status', 1)
        //     ->where('fees_status', 'Completed')
        //     ->count();
        $parent_complete = DB::table('organization_user')
            ->join('organization_user_student', 'organization_user.id', '=', 'organization_user_student.organization_user_id')
            ->join('students', 'students.id', 'organization_user_student.student_id')
            ->join('class_student', 'class_student.student_id', 'students.id')
            ->join('class_organization', 'class_organization.id', 'class_student.organclass_id')
            ->join('classes', 'classes.id', 'class_organization.class_id')
            ->where('classes.levelid', '>', 0)
            ->where('organization_user.organization_id', $oid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('organization_user.fees_status', 'Completed')
            ->distinct('organization_user.user_id')
            ->count();

        $parent_notcomplete = DB::table('organization_user')
            ->join('organization_user_student', 'organization_user.id', '=', 'organization_user_student.organization_user_id')
            ->join('students', 'students.id', 'organization_user_student.student_id')
            ->join('class_student', 'class_student.student_id', 'students.id')
            ->join('class_organization', 'class_organization.id', 'class_student.organclass_id')
            ->join('classes', 'classes.id', 'class_organization.class_id')
            ->where('classes.levelid', '>', 0)
            ->where('organization_user.organization_id', $oid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('organization_user.fees_status', 'Not Complete')
            ->distinct('organization_user.user_id')
            ->count();
        $all_parent = $parent_complete + $parent_notcomplete;
        return response()->json(['all_student' => $all_student, 'student_complete' => $student_complete, 'student_notcomplete' => $student_notcomplete, 'all_parent' => $all_parent, 'parent_complete' => $parent_complete, 'parent_notcomplete' => $parent_notcomplete]);
    }

    public function feesReportByClassId(Request $request)
    {
        //$organId = $request->oid;
        $classId = $request->cid;
        $feeId = $request->fid;

        $total_student = DB::table('class_student as cs')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->join('student_fees_new as sfn', 'sfn.class_student_id', '=', 'cs.id')
            ->where('cs.status', 1)
            //->where('co.organization_id', $organId)
            ->where('co.class_id', $classId)
            ->where('sfn.fees_id', $feeId)
            ->count();

        $total_student_paid = DB::table('class_student as cs')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->join('student_fees_new as sfn', 'sfn.class_student_id', '=', 'cs.id')
            ->where('cs.status', 1)
            //->where('co.organization_id', $organId)
            ->where('co.class_id', $classId)
            ->where('sfn.fees_id', $feeId)
            ->where('sfn.status', 'Paid')
            ->count();

        $total_student_debt = DB::table('class_student as cs')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->join('student_fees_new as sfn', 'sfn.class_student_id', '=', 'cs.id')
            ->where('cs.status', 1)
            //->where('co.organization_id', $organId)
            ->where('co.class_id', $classId)
            ->where('sfn.fees_id', $feeId)
            ->where('sfn.status', 'Debt')
            ->count();

        return response()->json(['total_student' => $total_student, 'total_student_paid' => $total_student_paid, 'total_student_debt' => $total_student_debt]);
    }

    public function reportByClass($type, $class_id)
    {
        $class = DB::table('classes')
            ->where('id', $class_id)->first();

        return view('fee.reportbyclass', compact('type', 'class'));
    }

    public function getTypeDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $type = $request->type;
            $oid = $request->oid;
            // dd($type);
            $userId = Auth::id();

            if ($type == 'Selesai') {

                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('class_organization.organization_id as oid', 'classes.id', 'classes.nama', DB::raw('COUNT(students.id) as totalstudent'), 'class_student.fees_status')
                    ->where('class_organization.organization_id', $oid)
                    ->where('class_student.fees_status', 'Completed')
                    ->where('classes.levelid', '>', 0)
                    ->where('class_student.status', 1)
                    ->groupBy('classes.nama')
                    ->orderBy('classes.nama')
                    ->get();
            } else {
                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('class_organization.organization_id as oid', 'classes.id', 'classes.nama', DB::raw('COUNT(students.id) as totalstudent'), 'class_student.fees_status')
                    ->where('class_organization.organization_id', $oid)
                    ->where('class_student.fees_status', 'Not Complete')
                    ->where('class_student.status', 1)
                    ->where('classes.levelid', '>', 0)
                    ->groupBy('classes.nama')
                    ->orderBy('classes.nama')
                    ->get();
            }

            // dd($first);
            $table = Datatables::of($data);

            $table->addColumn('total', function ($row) {

                $first = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('classes.nama', DB::raw('COUNT(students.id) as totalallstudent'))
                    ->where('class_organization.organization_id', $row->oid)
                    ->where('classes.id', $row->id)
                    ->where('class_student.status', 1)
                    ->groupBy('classes.nama')
                    ->orderBy('classes.nama')
                    ->first();

                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . $row->totalstudent . '/' . $first->totalallstudent . '</div>';
                return $btn;
            });

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('fees.reportByClass', ['type' => $row->fees_status, 'class_id' => $row->id]) . '"" class="btn btn-primary m-1">Butiran</a></div>';
                // $btn = $btn . '<a href="' . route('fees.edit', $row->feeid) . '" class="btn btn-primary m-1">Edit</a>';
                // $btn = $btn . '<button id="' . $row->feeid . '" data-token="' . $token . '" class="btn btn-danger m-1">Details</button></div>';
                return $btn;
            });

            $table->rawColumns(['total', 'action']);
            return $table->make(true);
        }
    }

    public function getParentDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $type = $request->type;
            $oid = $request->oid;
            // dd($type);
            $userId = Auth::id();

            if ($type == 'Selesai') {

                // $data = DB::table('users')
                //     ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                //     ->select('users.*', 'organization_user.organization_id')
                //     ->where('organization_user.organization_id', $oid)
                //     ->where('organization_user.role_id', 6)
                //     ->where('organization_user.status', 1)
                //     ->where('organization_user.fees_status', 'Completed')
                //     ->get();
                $data = DB::table('users')
                    ->join('organization_user', 'users.id', '=', 'organization_user.user_id')
                    ->join('organization_user_student', 'organization_user.id', '=', 'organization_user_student.organization_user_id')
                    ->join('students', 'students.id', 'organization_user_student.student_id')
                    ->join('class_student', 'class_student.student_id', 'students.id')
                    ->join('class_organization', 'class_organization.id', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', 'class_organization.class_id')
                    ->where('classes.levelid', '>', 0)
                    ->where('organization_user.organization_id', $oid)
                    ->where('organization_user.role_id', 6)
                    ->where('organization_user.status', 1)
                    ->where('organization_user.fees_status', 'Completed')
                    ->select('users.*', 'organization_user.organization_id')
                    ->distinct('users.id')
                    ->get();
            } else {
                // $data = DB::table('users')
                //     ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                //     ->select('users.*', 'organization_user.organization_id')
                //     ->where('organization_user.organization_id', $oid)
                //     ->where('organization_user.role_id', 6)
                //     ->where('organization_user.status', 1)
                //     ->where('organization_user.fees_status', 'Not Complete')
                //     ->get();

                $data = DB::table('users')
                    ->join('organization_user', 'users.id', '=', 'organization_user.user_id')
                    ->join('organization_user_student', 'organization_user.id', '=', 'organization_user_student.organization_user_id')
                    ->join('students', 'students.id', 'organization_user_student.student_id')
                    ->join('class_student', 'class_student.student_id', 'students.id')
                    ->join('class_organization', 'class_organization.id', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', 'class_organization.class_id')
                    ->where('classes.levelid', '>', 0)
                    ->where('organization_user.organization_id', $oid)
                    ->where('organization_user.role_id', 6)
                    ->where('organization_user.status', 1)
                    ->where('organization_user.fees_status', 'Not Complete')
                    ->select('users.*', 'organization_user.organization_id')
                    ->distinct('users.id')
                    ->get();
            }

            // dd($first);
            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a class="btn btn-primary m-1 user-id" id="' . $row->id . '-' . $row->organization_id . '">Butiran</a></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }


    public function getstudentDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $status = $request->status;
            $class_id = $request->class_id;
            // dd($type);
            $userId = Auth::id();

            $data = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('students.*')
                ->where('classes.id', $class_id)
                ->where('class_student.fees_status', 'LIKE', isset($status) ? $status : '%%')
                ->where('class_student.status', 1)
                ->orderBy('students.nama')
                ->get();
            //$this->validateStatus($data);
            // dd($first);
            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a class="btn btn-primary m-1 student-id" id="' . $row->id . '">Butiran</a></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }

    public function CategoryA()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_A.index', compact('organization'));
    }

    public function createCategoryA()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_A.add', compact('organization'));
    }

    public function StoreCategoryA(Request $request)
    {
        $price = $request->get('price');
        $quantity = $request->get('quantity');
        $oid = $request->get('organization');
        $date_started = Carbon::createFromFormat(config('app.date_format'), $request->get('date_started'))->format('Y-m-d');
        $date_end = Carbon::createFromFormat(config('app.date_format'), $request->get('date_end'))->format('Y-m-d');
        $total = $price * $quantity;

        $target = ['data' => 'ALL'];

        // $target = json_encode($data);

        // dd($target);

        $fee = new Fee_New([
            'name' => $request->get('name'),
            'desc' => $request->get('description'),
            'category' => "Kategori A",
            'quantity' => $request->get('quantity'),
            'price' => $request->get('price'),
            'totalAmount' => $total,
            'start_date' => $date_started,
            'end_date' => $date_end,
            'status' => "1",
            'target' => $target,
            'organization_id' => $oid,
        ]);

        // dd($fee);

        if ($fee->save()) {
            $parent_id = DB::table('organization_user as ou')
                ->where('organization_id', $oid)
                ->where('role_id', 6)
                ->where('status', 1)
                ->get();

            // to make sure one parent would recieve one only katagory fee if he or she hv more than children in school
            for ($i = 0; $i < count($parent_id); $i++) {
                $activeChildren = DB::table('organization_user_student as ous')
                    ->join('students as s', 's.id', 'ous.student_id')
                    ->join('class_student as cs', 'cs.student_id', 's.id')
                    ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->join('classes as c', 'c.id', 'co.class_id')
                    ->where('c.levelid', '>', 0)
                    ->where('ous.organization_user_id', $parent_id[$i]->id)
                    ->select('s.id')
                    ->distinct()
                    ->get();
                $parent_org = DB::table('organizations')->where('id', $parent_id[$i]->organization_id)->first();
                $activeChildrenInParent = [];
                if (isset($parent_org->parent_org)) {
                    $activeChildrenInParent = DB::table('organization_user_student as ous')
                        ->join('students as s', 's.id', 'ous.student_id')
                        ->join('class_student as cs', 'cs.student_id', 's.id')
                        ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                        ->join('organization_user as ou', 'ou.id', 'ous.organization_user_id')
                        ->join('classes as c', 'c.id', 'co.class_id')
                        ->where('c.levelid', '>', 0)
                        ->where('ou.user_id', $parent_id[$i]->user_id)
                        ->where('ou.organization_id', $parent_org->parent_org)
                        ->select('s.id')
                        ->distinct()
                        ->get();
                }
                if (count($activeChildren) > 0 || count($activeChildrenInParent) > 0) {
                    $fees_parent = DB::table('organization_user')
                        ->where('id', '=', $parent_id[$i]->id)
                        ->update(['fees_status' => 'Not Complete']);

                    DB::table('fees_new_organization_user')->insert([
                        'status' => 'Debt',
                        'fees_new_id' => $fee->id,
                        'organization_user_id' => $parent_id[$i]->id,
                    ]);
                }
            }

            return redirect('/fees/A')->with('success', 'Yuran Kategori A telah berjaya dimasukkan');
        }
    }

    public function updateFeeDetails()
    {

    }

    public function getCategoryDatatable(Request $request)
    {
        if (request()->ajax()) {
            $oid = $request->oid;
            $category = $request->category;
            $userId = Auth::id();

            if ($oid != '') {

                // $data = DB::table('fees')->orderBy('nama')->get();

                if ($category == "A") {
                    $data = DB::table('fees_new')
                        ->where('organization_id', $oid)
                        ->where('category', "Kategori A")
                        // ->where('status', "1")
                        ->get();

                    foreach ($data as $d) {
                        $d->target = "Setiap Keluarga";
                    }
                } elseif ($category == "B") {
                    $data = DB::table('fees_new')
                        ->where('organization_id', $oid)
                        ->where('category', "Kategori B")
                        // ->where('status', "1")
                        ->get();

                    foreach ($data as $d) {
                        $level = json_decode($d->target);
                        if ($level->data == "All_Level") {
                            $d->target = "Semua Tahap";
                        } elseif ($level->data == 1) {
                            $d->target = "Kelas : Tahap 1";
                        } elseif ($level->data == 2) {
                            $d->target = "Kelas : Tahap 2";
                        } elseif (is_array($level->data)) {
                            $classes = DB::table('classes')
                                ->whereIN('id', $level->data)
                                ->get();

                            $d->target = "Kelas : ";
                            foreach ($classes as $i => $class) {
                                $d->target = $d->target . $class->nama . (sizeof($classes) - 1 == $i ? "" : ", ");
                            }
                        }
                    }
                } elseif ($category == "C") {
                    $data = DB::table('fees_new')
                        ->where('organization_id', $oid)
                        ->where('category', "Kategori C")
                        // ->where('status', "1")
                        ->get();

                    foreach ($data as $d) {
                        $level = json_decode($d->target);
                        $d->target = "Jantina : " . ($level->gender == 'L' ? "Lelaki<br>" : "Perempuan<br>");
                        if ($level->data == "All_Level") {
                            $d->target = $d->target . "Kelas : Semua Tahap";
                        } elseif ($level->data == 1) {
                            $d->target = $d->target . "Kelas : Tahap 1";
                        } elseif ($level->data == 2) {
                            $d->target = $d->target . "Kelas : Tahap 2";
                        } elseif (is_array($level->data)) {
                            $classes = DB::table('classes')
                                ->whereIN('id', $level->data)
                                ->get();

                            $d->target = $d->target . $d->target = "Kelas : ";
                            foreach ($classes as $i => $class) {
                                $d->target = $d->target . $class->nama . (sizeof($classes) - 1 == $i ? "" : ", ");
                            }
                        }
                    }
                } elseif ($category == "Recurring") {
                    $data = DB::table('fees_new')
                        ->where('organization_id', $oid)
                        ->where('category', "Kategori Berulang")
                        // ->where('status', "1")
                        ->get();

                    foreach ($data as $d) {
                        $level = json_decode($d->target);
                        if ($level->data == "All_Level") {
                            $d->target = "Semua Tahap";
                        } elseif ($level->data == 1) {
                            $d->target = "Kelas : Tahap 1";
                        } elseif ($level->data == 2) {
                            $d->target = "Kelas : Tahap 2";
                        } elseif (is_array($level->data)) {
                            $classes = DB::table('classes')
                                ->whereIN('id', $level->data)
                                ->get();

                            $d->target = "Kelas : ";
                            foreach ($classes as $i => $class) {
                                $d->target = $d->target . $class->nama . (sizeof($classes) - 1 == $i ? "" : ", ");
                            }
                        }
                    }
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
            //to do update status btn
            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                if ($row->status == '1') {
                    // add a new edit button in the actions column
                    $btn = $btn . '<a href="' . route('fees.edit', $row->id) . '" class="btn btn-primary m-1">Ubah Butiran</a>';
                    $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-info m-1">Tutup yuran</button></div>';
                } else {
                    // $btn = $btn . '<a href="' . route('fees.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                }
                return $btn;
            });

            $table->rawColumns(['status', 'action']);
            // $table->rawColumns(['target', 'status']);
            return $table->make(true);
        }
    }

    public function CategoryB()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_B.index', compact('organization'));
    }

    public function createCategoryB()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_B.add', compact('organization'));
    }


    public function StoreCategoryB(Request $request)
    {
        $gender = "";
        $class = $request->get('cb_class');
        $level = $request->get('level');
        $year = $request->get('year');
        $name = $request->get('name');
        $price = $request->get('price');
        $quantity = $request->get('quantity');
        $desc = $request->get('description');
        $oid = $request->get('organization');
        $date_started = Carbon::createFromFormat(config('app.date_format'), $request->get('date_started'))->format('Y-m-d');
        $date_end = Carbon::createFromFormat(config('app.date_format'), $request->get('date_end'))->format('Y-m-d');
        $total = $price * $quantity;
        $category = "Kategori B";

        if ($level == "All_Level") {
            return $this->allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } elseif ($year == "All_Year") {
            return $this->allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } else {
            return $this->allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category);
        }
    }

    public function CategoryC()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_C.index', compact('organization'));
    }

    public function createCategoryC()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_C.add', compact('organization'));
    }

    public function StoreCategoryC(Request $request)
    {
        // dd($request->toArray());
        $gender = $request->get('gender');
        $class = $request->get('cb_class');
        $level = $request->get('level');
        $year = $request->get('year');
        $name = $request->get('name');
        $price = $request->get('price');
        $quantity = $request->get('quantity');
        $desc = $request->get('description');
        $oid = $request->get('organization');
        $date_started = Carbon::createFromFormat(config('app.date_format'), $request->get('date_started'))->format('Y-m-d');
        $date_end = Carbon::createFromFormat(config('app.date_format'), $request->get('date_end'))->format('Y-m-d');
        $total = $price * $quantity;
        $category = "Kategori C";

        if ($level == "All_Level") {
            return $this->allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } elseif ($year == "All_Year") {
            return $this->allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } else {
            return $this->allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category);
        }
    }

    public function CategoryRecurring()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.recurring.index', compact('organization'));
    }

    public function createCategoryRecurring()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.recurring.add', compact('organization'));
    }


    public function StoreCategoryRecurring(Request $request)
    {
        $gender = "";
        $class = $request->get('cb_class');
        $level = $request->get('level');
        $year = $request->get('year');
        $name = $request->get('name');
        $price = $request->get('price');
        $quantity = $request->get('quantity');
        $desc = $request->get('description');
        $oid = $request->get('organization');
        $date_started = Carbon::createFromFormat(config('app.date_format'), $request->get('date_started'))->format('Y-m-d');
        $date_end = Carbon::createFromFormat(config('app.date_format'), $request->get('date_end'))->format('Y-m-d');
        $total = $price * $quantity;
        $category = "Kategori Berulang";

        if ($level == "All_Level") {
            return $this->allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } elseif ($year == "All_Year") {
            return $this->allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } else {
            return $this->allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category);
        }
    }

    public function insertNewFeesRecurring($recurringFee, $classStudent, $studentFeesNewId)
    {
        // get the data required for storing a new fees_recurring data
        $recurringDateStarted = Carbon::parse($recurringFee->start_date);
        $recurringDateEnd = Carbon::parse($recurringFee->end_date);
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
        $finalAmount = $recurringFee->totalAmount * ($totalDaysLeft / $totalDays);
        if ($finalAmount > $recurringFee->totalAmount) {
            $finalAmount = $recurringFee->totalAmount;
        }

        // insert a new fees_recurring
        DB::table('fees_recurring')->insert([
            'student_fees_new_id' => $studentFeesNewId,
            'totalDay' => $totalDays,
            'totalDayLeft' => $totalDaysLeft,
            'finalAmount' => $finalAmount,
            'desc' => 'RM' . $recurringFee->totalAmount . '*' . $totalDaysLeft . '/' . $totalDays,
            'created_at' => now(),
        ]);
    }

    // show the main page for yuran pelajar (assign yuran for one student)
    public function AssignFeesToStudentIndex()
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole("Superadmin") && !Auth::user()->hasRole("Pentadbir") && !Auth::user()->hasRole("Pentadbir Swasta")) {
            return redirect('/home');
        }

        $organization = $this->getOrganizationByUserId();

        return view('fee.assign_fees_to_student.index', compact("organization"));
    }

    // show the edit page for adding or removing yuran from student (assign yuran for one student)
    public function AssignFeesToStudentEdit(Request $request)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole("Superadmin") && !Auth::user()->hasRole("Pentadbir") && !Auth::user()->hasRole("Pentadbir Swasta")) {
            return redirect('/home');
        }

        $oid = $request->get("oid");
        $organization = Organization::findOrFail($oid);

        $classId = $request->get("classId");
        $class = ClassModel::findOrFail($classId);

        $studentId = $request->get("studentId");

        // get the student details with fees data
        $currentStudentFeesData = $this->fetchOneStudentToManyFees($oid, $classId, $studentId)->first();

        return view('fee.assign_fees_to_student.edit', compact("organization", "class", "currentStudentFeesData"));
    }

    // update data for adding or removing yuran from student (assign yuran for one student) into database
    public function AssignFeesToStudentUpdate(Request $request)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole("Superadmin") && !Auth::user()->hasRole("Pentadbir") && !Auth::user()->hasRole("Pentadbir Swasta")) {
            return redirect('/home');
        }

        $feesSelected = $request->get("fees_selected");
        $classId = $request->get("class_id");
        $oid = $request->get("oid");
        $studentId = $request->get("student_id");

        // get the class_student by student id to obtain its class_student_id to be assigned to student_fees_new
        $class_student = DB::table("class_student as cs")
            ->join("class_organization as co", "cs.organclass_id", "=", "co.id")
            ->select("cs.*")
            ->where("co.class_id", "=", $classId)
            ->where("co.organization_id", "=", $oid)
            ->where("cs.student_id", "=", $studentId)
            ->get()
            ->first();

        // check if the fees are already assigned to the student
        $studentFeesNewData = DB::table("student_fees_new")
            ->where("class_student_id", "=", $class_student->id)
            ->get();

        // if fee selected is not in the student_fees_new table, add a new student_fees_new (which means assign the fee to the student)
        if (isset($feesSelected))
            foreach ($feesSelected as $feeId) {
                // get the fees_new details
                $fee = DB::table("fees_new")
                    ->where("id", "=", $feeId)
                    ->first();

                if (!in_array($feeId, $studentFeesNewData->pluck("fees_id")->toArray())) {
                    // insert a new student_fees_new with status of debt if the student_fees_new does not contain the fees 
                    $studentFeesNewId = DB::table("student_fees_new")->insertGetId([
                        "status" => "Debt",
                        "fees_id" => $feeId,
                        "class_student_id" => $class_student->id
                    ]);

                    // update fees status for class_student to 'Not Complete'
                    DB::table("class_student")
                        ->where("id", "=", $class_student->id)
                        ->update(["fees_status" => "Not Complete"]);

                    // if the kategori is kategori berulang, insert a new data into fees_recurring
                    if ($fee->category == "Kategori Berulang") {
                        $this->insertNewFeesRecurring($fee, $class_student, $studentFeesNewId);
                    }
                }
            }

        // if the student_fees_new contains fees that are not in the fees_selected, remove it (means that admin has removed the fee from this student) 
        foreach ($studentFeesNewData as $sfn) {
            if (!in_array($sfn->fees_id, $feesSelected ?? [])) {
                // get the fees_new data for this student_fees_new
                $fee = DB::table("fees_new")
                    ->where("id", "=", $sfn->fees_id)
                    ->first();

                // if the fee category is Kategori Berulang, delete it from the fees_recurring
                if ($fee->category == "Kategori Berulang") {
                    DB::table("fees_recurring")
                        ->where("student_fees_new_id", "=", $sfn->id)
                        ->delete();
                }

                // delete data in student_fees_new
                DB::table("student_fees_new")
                    ->where("fees_id", "=", $sfn->fees_id)
                    ->where("class_student_id", "=", $class_student->id)
                    ->where("status", "=", "Debt")
                    ->delete();
            }
        }

        // redirect back to yuran pelajar page
        return redirect()->route("fees.assignFeesToStudentIndex")->with("success", "Yuran pelajar berjaya diubahkan.");
    }

    // show the main page for pelajar yuran (assign students for one yuran)
    public function AssignStudentsToFeeIndex()
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole("Superadmin") && !Auth::user()->hasRole("Pentadbir") && !Auth::user()->hasRole("Pentadbir Swasta")) {
            return redirect('/home');
        }

        $organization = $this->getOrganizationByUserId();

        return view('fee.assign_students_to_fee.index', compact("organization"));
    }

    // show the edit page for adding or removing students from fees (assign students for one yuran)
    public function AssignStudentsToFeeEdit(Request $request)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole("Superadmin") && !Auth::user()->hasRole("Pentadbir") && !Auth::user()->hasRole("Pentadbir Swasta")) {
            return redirect('/home');
        }

        $oid = $request->get("oid");
        $organization = Organization::findOrFail($oid);

        // get the current fee
        $feeId = $request->get("feeId");
        $currentFee = $this->fetchOneFeeToManyStudents($oid, $feeId)->first();

        // dump($oid);
        // dump($feeId);
        // dd($currentFee);

        return view('fee.assign_students_to_fee.edit', compact("organization", "currentFee"));
    }

    // update data for adding or removing students from yuran (assign students for one yuran) into database
    public function AssignStudentsToFeeUpdate(Request $request)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole("Superadmin") && !Auth::user()->hasRole("Pentadbir") && !Auth::user()->hasRole("Pentadbir Swasta")) {
            return redirect('/home');
        }

        $oid = $request->get("oid");
        $feeId = $request->get("fee_id");
        $selectedStudentIds = $request->get("students_selected");
        $classId = $request->get("classes");

        if (!isset($classId)) {
            return redirect()->back()->withErrors("Sila pilih kelas.");
        }

        // get the fees_new data
        $fee = DB::table("fees_new")
            ->where("id", "=", $feeId)
            ->first();

        // get the class_student by student id to obtain its class_student_id to be assigned to student_fees_new
        $existingStudentIds = DB::table("class_student as cs")
            ->join("class_organization as co", "cs.organclass_id", "=", "co.id")
            ->join("student_fees_new as sfn", "cs.id", "=", "sfn.class_student_id")
            ->select("cs.student_id as student_id")
            ->where("co.organization_id", "=", $oid)
            ->where("co.class_id", "=", $classId)
            ->where("sfn.fees_id", "=", $feeId)
            ->get()
            ->pluck("student_id")
            ->toArray();

        if (isset($selectedStudentIds)) {
            // check if the student is already assigned to the fee
            foreach ($selectedStudentIds as $selectedStudentId) {
                if (!in_array($selectedStudentId, $existingStudentIds)) {
                    // if no, create a new student fees new for that student

                    // search for the class_student for that specific student id
                    $classStudent = DB::table("class_student as cs")
                        ->join("class_organization as co", "co.id", "=", "cs.organclass_id")
                        ->select("cs.*")
                        ->where("co.organization_id", "=", $oid)
                        ->where("co.class_id", "=", $classId)
                        ->where("cs.student_id", "=", $selectedStudentId)
                        ->first();

                    // update fees status for class_student to 'Not Complete'
                    DB::table("class_student")
                        ->where("id", "=", $classStudent->id)
                        ->update(["fees_status" => "Not Complete"]);

                    $studentFeesNewId = DB::table("student_fees_new")->insertGetId([
                        "status" => "Debt",
                        "fees_id" => $feeId,
                        "class_student_id" => $classStudent->id
                    ]);

                    // insert a new fees_recurring if the fee is Kategori Berulang
                    if ($fee->category == "Kategori Berulang") {
                        $this->insertNewFeesRecurring($fee, $classStudent, $studentFeesNewId);
                    }
                }
            }
        }

        // if the student is previously assigned to the fee and now being removed, delete the student fees new for that student
        foreach ($existingStudentIds as $existingStudentId) {
            if (!in_array($existingStudentId, $selectedStudentIds ?? [])) {
                // find the class_student_id to remove
                $classStudent = DB::table("class_student as cs")
                    ->join("class_organization as co", "co.id", "=", "cs.organclass_id")
                    ->select("cs.id as id")
                    ->where("co.organization_id", "=", $oid)
                    ->where("co.class_id", "=", $classId)
                    ->where("cs.student_id", "=", $existingStudentId)
                    ->first();

                // find the student_fees_new to remove it
                $studentFeesNew = DB::table("student_fees_new")
                    ->where("class_student_id", "=", $classStudent->id)
                    ->where("fees_id", "=", $feeId)
                    ->where("status", "Debt");

                // delete fees_recurring data for recurring fees
                if ($fee->category == "Kategori Berulang") {
                    DB::table("fees_recurring")
                        ->where("student_fees_new_id", "=", $studentFeesNew->first()->id)
                        ->delete();
                }

                // remove student_fees_new
                $studentFeesNew->delete();
            }
        }

        // redirect back to the assign students to fee index
        return redirect()->route("fees.assignStudentsToFeeIndex")->with("success", "Pelajar yang perlu bayar yuran tersebut telah berjaya diubah.");
    }

    // helper method to fetch all fees within an organization
    public function fetchAllFeesByOrgAndCategories($oid, $categories)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        return DB::table("fees_new as fn")
            ->join("organizations as o", "fn.organization_id", "=", "o.id")
            ->select("fn.id as fee_id", "fn.name as fee_name", "fn.category as fee_category", "fn.status as fee_status")
            ->where("o.id", "=", $oid)
            ->whereIn("fn.category", $categories)
            ->where("fn.status", "=", 1)
            ->orderBy("fn.category", "asc")
            ->orderBy("fn.name", "asc")
            ->get();
    }

    // route method to fetch all fees datatable within an organization
    public function fetchAllFeesDatatableByOrg(Request $request)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole("Superadmin") && !Auth::user()->hasRole("Pentadbir") && !Auth::user()->hasRole("Pentadbir Swasta")) {
            return redirect('/home');
        }

        $oid = $request->get("oid");
        $data = $this->fetchAllFeesByOrgAndCategories($oid, ["Kategori B", "Kategori C", "Kategori Berulang"]);

        $table = Datatables::of($data);

        $table->addColumn('action', function ($row) use ($oid) {
            return "<div style='text-align: center;'>
                 <a style='margin: 0 auto;' href='" .
                route("fees.assignStudentsToFeeEdit", ["oid" => $oid, "feeId" => $row->fee_id]) .
                "' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i> Ubah Pelajar</a>
            </div>";
        });

        $table->rawColumns(["action"]);

        return $table->make(true);
    }

    // helper method to fetch a fee details with their asscociated students by fee id and return as collection
    public function fetchOneFeeToManyStudents($oid, $feeId, $classId = null)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        $fees = DB::table("fees_new as fn")
            ->leftJoin("student_fees_new as sfn", "fn.id", "=", "sfn.fees_id")
            ->leftJoin("class_student as cs", "cs.id", "=", "sfn.class_student_id")
            ->leftJoin("class_organization as co", "co.id", "=", "cs.organclass_id")
            ->leftJoin("students as s", "s.id", "=", "cs.student_id")
            ->select(
                "fn.id as fee_id",
                "fn.name as fee_name",
                "fn.desc as fee_desc",
                "fn.category as fee_category",
                "fn.status as fee_status",
                "s.id as student_id",
                "s.nama as student_name",
                "s.gender as gender",
                "sfn.status as student_fee_status"
            )
            ->where("fn.organization_id", "=", $oid)
            ->where("fn.id", "=", $feeId);

        if (isset($classId)) {
            $fees = $fees->where("co.class_id", "=", $classId);
        }

        return $fees
            ->get()
            ->groupBy("fee_id")
            ->map(function ($group) {
                $firstGroup = $group->first();

                return [
                    "fee_id" => $firstGroup->fee_id,
                    "fee_name" => $firstGroup->fee_name,
                    "fee_desc" => $firstGroup->fee_desc,
                    "fee_category" => $firstGroup->fee_category,
                    "fee_status" => $firstGroup->fee_status,
                    "students" => $group->map(function ($item) {
                        return [
                            "student_id" => $item->student_id,
                            "student_name" => $item->student_name,
                            "gender" => $item->gender,
                            "student_fee_status" => $item->student_fee_status
                        ];
                    })
                ];
            });
    }

    public function fetchOneFeeToManyStudentsJson(Request $request)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole("Superadmin") && !Auth::user()->hasRole("Pentadbir") && !Auth::user()->hasRole("Pentadbir Swasta")) {
            return redirect('/home');
        }

        $oid = $request->get("oid");
        $feeId = $request->get("feeId");
        $classId = $request->get("classId");

        $data = $this->fetchOneFeeToManyStudents($oid, $feeId, $classId)->values();

        return response()->json($data);
    }

    // helper method to fetch a student details with their fees by student's class id and return as collection
    public function fetchOneStudentToManyFees($oid, $classId, $studentId = null)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        return DB::table("students as s")
            ->join("class_student as cs", "s.id", "=", "cs.student_id")
            ->join("class_organization as co", "co.id", "=", "cs.organclass_id")
            ->leftJoin("student_fees_new as sfn", "cs.id", "=", "sfn.class_student_id")
            ->leftJoin("fees_new as fn", "sfn.fees_id", "=", "fn.id")
            ->select(
                "s.id as student_id",
                "s.nama as student_name",
                "s.gender as gender",
                "cs.status as student_status",
                "fn.id as fee_id",
                "fn.name as fee_name",
                "fn.category as fee_category",
                "sfn.status as fee_status"
            )
            ->where("co.organization_id", "=", $oid)
            ->where("co.class_id", "=", $classId)
            ->where("s.id", "LIKE", isset($studentId) ? $studentId : "%%")
            ->where("cs.status", "!=", -1)
            ->where("fn.status", 1)
            ->orderBy("fee_category", "asc")
            ->orderBy("fee_name", "asc")
            ->get()
            ->groupBy("student_id")
            // groupBy will return [
            // 0 => Collection([
            //      "student_id" => 2,
            //      "student_name" => "student 1",
            //      "gender" => "Lelaki",
            //      "fee_id" => "1",
            //      "fees_name" => "Yuran B",
            //      "fees_category" => "Kategori B",
            //      "fees_status" => "Debt",
            //      ],
            //      [
            //      "student_id" => 2,
            //      "student_name" => "student 1",
            //      "gender" => "Lelaki",
            //      "fee_id" => "2",
            //      "fees_name" => "Yuran C",
            //      "fees_category" => "Kategori C",
            //      "fees_status" => "Debt",
            //      ])
            // ]
            ->map(function ($group) {
                // get the first item in the collection (to get the details of students) 
                // each collection is grouped by student id, so each group will have the same student details
                $firstGroup = $group->first();

                // returns array of data with fees within a single student object, example:
                // [
                //       0 => [
                //          "student_id" => 2,
                //          "student_name" => "student 1",
                //          "gender" => "Lelaki",
                //          "fees" => [
                //              {
                //                  "fee_id" => "1",
                //                  "fees_name" => "Yuran B",
                //                  "fees_category" => "Kategori B",
                //                  "fees_status" => "Debt",
                //              },
                //              {
                //                  "fee_id" => "2",
                //                  "fees_name" => "Yuran C",
                //                  "fees_category" => "Kategori C",
                //                  "fees_status" => "Debt",
                //              }
                //          ]
                //      ]
                // ]
    
                return [
                    // get common details of students
                    "student_id" => $firstGroup->student_id,
                    "student_name" => $firstGroup->student_name,
                    "gender" => $firstGroup->gender,
                    "student_status" => $firstGroup->student_status,
                    // get fee details from each item in the collection 
                    "fees" => $group->map(function ($item) {
                        return [
                            "fee_id" => $item->fee_id,
                            "fee_name" => $item->fee_name,
                            "fee_category" => $item->fee_category,
                            "fee_status" => $item->fee_status
                        ];
                    })
                ];
            });
    }

    // method to return Datatable for the student fees retrieved in fetchStudentFeesByClass method
    public function fetchOneStudentToManyFeesDatatable(Request $request)
    {
        if (Auth::id() == null) {
            return redirect('/login');
        }

        if (!Auth::user()->hasRole("Superadmin") && !Auth::user()->hasRole("Pentadbir") && !Auth::user()->hasRole("Pentadbir Swasta")) {
            return redirect('/home');
        }

        $oid = $request->get('oid');
        $classId = $request->get('classid');
        $routeName = $request->get("routeName");

        $data = $this->fetchOneStudentToManyFees($oid, $classId);

        $table = Datatables::of($data);

        $table->addColumn('action', function ($row) use ($oid, $classId, $routeName) {
            return "<div style='text-align: center;'>
                 <a style='margin: 0 auto;' href='" .
                route($routeName, ["oid" => $oid, "classId" => $classId, "studentId" => $row["student_id"]]) .
                "'" .
                (($row["student_status"] != 1)
                    ? " class='btn btn-primary disabled' aria-disabled='true'>"
                    : " class='btn btn-primary'>") .
                "<i class='fa-solid fa-pen-to-square'></i> Ubah Suai Yuran</a>
            </div>";
        });

        $table->rawColumns(['action']);
        return $table->make(true);
    }

    public function fetchClassYear(Request $request)
    {

        // dd($request->get('level'));
        $level = $request->get('level');
        $oid = $request->get('oid');

        $organization = Organization::find($oid);

        if ($organization->parent_org != null) {
            $oid = $organization->parent_org;
        }

        if ($level == "1") {
            $list = DB::table('organizations')
                ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
                ->where('organizations.id', $oid)
                ->first();

            $class_organization = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select(DB::raw('substr(classes.nama, 1, 1) as year'))
                ->distinct()
                ->where('classes.status', 1)
                ->where('classes.levelid', $level)
                ->where('class_organization.organization_id', $oid)
                ->get();

            // dd($class_organization);

            return response()->json(['data' => $list, 'datayear' => $class_organization]);
        } elseif ($level == "2") {
            $list = DB::table('organizations')
                ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
                ->where('organizations.id', $oid)
                ->first();

            $class_organization = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select(DB::raw('substr(classes.nama, 1, 1) as year'))
                ->distinct()
                ->where('classes.status', 1)
                ->where('classes.levelid', $level)
                ->where('class_organization.organization_id', $oid)
                ->get();

            // dd($class_organization);

            return response()->json(['data' => $list, 'datayear' => $class_organization]);
        }
    }

    public function allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category)
    {
        $organization = Organization::find($oid);
        // dd($organization->parent_org != null ? $organization->parent_org: $oid);
        if ($gender) {
            $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org : $oid)
                ->where('classes.levelid', '>', 0)
                ->where('classes.status', "1")
                ->where('students.gender', $gender)
                ->get();

            $data = array(
                'data' => $level,
                'gender' => $gender
            );
        } else {
            if ($category == "Kategori Berulang") {
                $list = DB::table('class_organization')
                    ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('class_student.id as class_student_id', 'class_student.start_date as class_student_start_date')
                    ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org : $oid)
                    ->where('classes.status', "1")
                    ->where('class_student.start_date', '<', $date_end)
                    ->get();

                $data = array(
                    'data' => $level
                );
            } else {
                $list = DB::table('class_organization')
                    ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('class_student.id as class_student_id')
                    ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org : $oid)
                    ->where('classes.levelid', '>', 0)
                    ->where('classes.status', "1")
                    ->get();

                $data = array(
                    'data' => $level
                );
            }
        }

        $target = json_encode($data);

        $fees = DB::table('fees_new')->insertGetId([
            'name' => $name,
            'desc' => $desc,
            'category' => $category,
            'quantity' => $quantity,
            'price' => $price,
            'totalAmount' => $total,
            'start_date' => $date_started,
            'end_date' => $date_end,
            'status' => "1",
            'target' => $target,
            'organization_id' => $oid,

        ]);

        for ($i = 0; $i < count($list); $i++) {

            $fees_student = DB::table('class_student')
                ->where('id', $list[$i]->class_student_id)
                ->update(['fees_status' => 'Not Complete']);

            // DB::table('student_fees_new')->insert([
            //     'status' => 'Debt',
            //     'fees_id' => $fees,
            //     'class_student_id' => $list[$i]->class_student_id,
            // ]);

            $student_fees_new = DB::table('student_fees_new')->insertGetId([
                'status' => 'Debt',
                'fees_id' => $fees,
                'class_student_id' => $list[$i]->class_student_id,
            ]);

            if ($category == "Kategori Berulang") {
                $datestarted = Carbon::parse($date_started); //back to original date without format (string to datetime)
                $dateend = Carbon::parse($date_end);
                $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                $cs_startdate = Carbon::parse($list[$i]->class_student_start_date);
                $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                if ($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day) {
                    $totalDayLeft = $totalDay;
                }
                $finalAmount = $total * ($totalDayLeft / $totalDay);
                if ($finalAmount > $total) {
                    $finalAmount = $total;
                }

                DB::table('fees_recurring')->insert([
                    'student_fees_new_id' => $student_fees_new,
                    'totalDay' => $totalDay,
                    'totalDayLeft' => $totalDayLeft,
                    'finalAmount' => $finalAmount,
                    'desc' => 'RM' . $total . '*' . $totalDayLeft . '/' . $totalDay,
                    'created_at' => now(),
                ]);

                //dd($total * ($totalDayLeft / $totalDay));
            }
        }

        // dd($list);

        if ($category == "Kategori B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } else if ($category == "Kategori C") {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
        } else {
            return redirect('/fees/Recurring')->with('success', 'Yuran Kategori Berulang telah berjaya dimasukkan');
        }
    }

    public function samuraFeeForm()
    {
        return view('fee.test.samuraForm');
    }

    public function submitSamuraForm(Request $request)
    {


        $file = $request->file('file');

        // Data to be passed to the import class
        $organization_id = $request->input('organization_id');
        $fee1_id = $request->input('fee1_id');
        $fee2_id = $request->input('fee2_id');
        $income_threshold = $request->input('income_threshold');

        try {
            Excel::import(new AssignFeeByParentIncome($organization_id, $fee1_id, $fee2_id, $income_threshold), $file);
            return redirect()->back()->with('success', 'File processed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category)
    {
        $organization = Organization::find($oid);

        if ($gender) {
            $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org : $oid)
                ->where('classes.levelid', $level)
                ->where('classes.status', "1")
                ->where('class_student.status', 1)
                ->where('students.gender', $gender)
                ->get();
            $data = array(
                'data' => $level,
                'gender' => $gender
            );
        } else {
            if ($category == "Kategori Berulang") {
                $list = DB::table('class_organization')
                    ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('class_student.id as class_student_id', 'class_student.start_date as class_student_start_date')
                    ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org : $oid)
                    ->where('classes.levelid', $level)
                    ->where('classes.status', "1")
                    ->where('class_student.status', 1)

                    ->where('class_student.start_date', '<', $date_end)
                    ->get();
                $data = array(
                    'data' => $level
                );
            } else {
                $list = DB::table('class_organization')
                    ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('class_student.id as class_student_id')
                    ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org : $oid)
                    ->where('classes.levelid', $level)
                    ->where('classes.status', "1")
                    ->where('class_student.status', 1)
                    ->get();
                $data = array(
                    'data' => $level
                );
            }
        }

        $target = json_encode($data);

        $fees = DB::table('fees_new')->insertGetId([
            'name' => $name,
            'desc' => $desc,
            'category' => $category,
            'quantity' => $quantity,
            'price' => $price,
            'totalAmount' => $total,
            'start_date' => $date_started,
            'end_date' => $date_end,
            'status' => "1",
            'target' => $target,
            'organization_id' => $oid,

        ]);

        for ($i = 0; $i < count($list); $i++) {

            $fees_student = DB::table('class_student')
                ->where('id', $list[$i]->class_student_id)
                ->update(['fees_status' => 'Not Complete']);

            // DB::table('student_fees_new')->insert([
            //     'status' => 'Debt',
            //     'fees_id' => $fees,
            //     'class_student_id' => $list[$i]->class_student_id,
            // ]);

            $student_fees_new = DB::table('student_fees_new')->insertGetId([
                'status' => 'Debt',
                'fees_id' => $fees,
                'class_student_id' => $list[$i]->class_student_id,
            ]);

            if ($category == "Kategori Berulang") {
                $datestarted = Carbon::parse($date_started); //back to original date without format (string to datetime)
                $dateend = Carbon::parse($date_end);
                $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                $cs_startdate = Carbon::parse($list[$i]->class_student_start_date);
                $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                if ($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day) {
                    $totalDayLeft = $totalDay;
                }
                $finalAmount = $total * ($totalDayLeft / $totalDay);
                if ($finalAmount > $total) {
                    $finalAmount = $total;
                }

                DB::table('fees_recurring')->insert([
                    'student_fees_new_id' => $student_fees_new,
                    'totalDay' => $totalDay,
                    'totalDayLeft' => $totalDayLeft,
                    'finalAmount' => $finalAmount,
                    'desc' => 'RM' . $total . '*' . $totalDayLeft . '/' . $totalDay,
                    'created_at' => now(),
                ]);

                //dd($total * ($totalDayLeft / $totalDay));
            }
        }

        if ($category == "Kategori B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } else if ($category == "Kategori C") {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
        } else {
            return redirect('/fees/Recurring')->with('success', 'Yuran Kategori Berulang telah berjaya dimasukkan');
        }
    }

    public function allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category)
    {
        // get list class checked from checkbox
        $organization = Organization::find($oid);

        $list = DB::table('classes')
            ->where('status', "1")
            ->whereIn('id', $class)
            ->get();

        // dd(count($list));
        for ($i = 0; $i < count($list); $i++) {
            $class_arr[] = $list[$i]->id;
        }

        if ($gender) {
            $list_student = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org : $oid)
                ->where('classes.status', "1")
                ->where('class_student.status', 1)
                ->where('students.gender', $gender)
                ->whereIn('classes.id', $class)
                ->get();
            $data = array(
                'data' => $class_arr,
                'gender' => $gender
            );
        } else {
            if ($category == "Kategori Berulang") {
                $list_student = DB::table('class_organization')
                    ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('class_student.id as class_student_id', 'class_student.start_date as class_student_start_date')
                    ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org : $oid)
                    ->where('classes.status', "1")
                    ->where('class_student.status', 1)
                    ->whereIn('classes.id', $class)
                    ->where('class_student.start_date', '<', $date_end)
                    ->get();
                $data = array(
                    'data' => $class_arr
                );
            } else {
                $list_student = DB::table('class_organization')
                    ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('class_student.id as class_student_id')
                    ->where('class_organization.organization_id', $organization->parent_org != null ? $organization->parent_org : $oid)
                    ->where('classes.status', "1")
                    ->where('class_student.status', 1)
                    ->whereIn('classes.id', $class)
                    ->get();
                $data = array(
                    'data' => $class_arr
                );
            }
        }

        $target = json_encode($data);

        $fees = DB::table('fees_new')->insertGetId([
            'name' => $name,
            'desc' => $desc,
            'category' => $category,
            'quantity' => $quantity,
            'price' => $price,
            'totalAmount' => $total,
            'start_date' => $date_started,
            'end_date' => $date_end,
            'status' => "1",
            'target' => $target,
            'organization_id' => $oid,
        ]);

        for ($i = 0; $i < count($list_student); $i++) {
            $fees_student = DB::table('class_student')
                ->where('id', $list_student[$i]->class_student_id)
                ->update(['fees_status' => 'Not Complete']);

            // DB::table('student_fees_new')->insert([
            //     'status' => 'Debt',
            //     'fees_id' => $fees,
            //     'class_student_id' => $list_student[$i]->class_student_id,
            // ]);

            $student_fees_new = DB::table('student_fees_new')->insertGetId([
                'status' => 'Debt',
                'fees_id' => $fees,
                'class_student_id' => $list_student[$i]->class_student_id,
            ]);

            if ($category == "Kategori Berulang") {
                $datestarted = Carbon::parse($date_started); //back to original date without format (string to datetime)
                $dateend = Carbon::parse($date_end);
                $totalDay = ($datestarted->diffInDays($dateend)) + 1;
                $cs_startdate = Carbon::parse($list_student[$i]->class_student_start_date);
                $totalDayLeft = ($cs_startdate)->diffInDays($dateend);
                if ($totalDayLeft > $totalDay || $datestarted->day == $cs_startdate->day) {
                    $totalDayLeft = $totalDay;
                }
                $finalAmount = $total * ($totalDayLeft / $totalDay);
                if ($finalAmount > $total) {
                    $finalAmount = $total;
                }

                DB::table('fees_recurring')->insert([
                    'student_fees_new_id' => $student_fees_new,
                    'totalDay' => $totalDay,
                    'totalDayLeft' => $totalDayLeft,
                    'finalAmount' => $finalAmount,
                    'desc' => 'RM' . $total . '*' . $totalDayLeft . '/' . $totalDay,
                    'created_at' => now(),
                ]);

                //dd($total * ($totalDayLeft / $totalDay));
            }
        }

        if ($category == "Kategori B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } else if ($category == "Kategori C") {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
        } else {
            return redirect('/fees/Recurring')->with('success', 'Yuran Kategori Berulang telah berjaya dimasukkan');
        }
    }

    public function dependent_fees()
    {
        $userid = Auth::id();

        // ************************* get list dependent from user id  *******************************

        $list = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->join('users', 'users.id', '=', 'organization_user.user_id')
            ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            ->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as nschool', 'organizations.parent_org as parent_org', 'students.id as studentid', 'students.nama as studentname', 'classes.nama as classname', 'classes.levelid', 'organizations.type_org as type_org', 'class_student.start_date as student_startdate')
            ->where('organization_user.user_id', $userid)
            ->where('class_student.status', 1)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->orderBy('organizations.id')
            ->orderBy('classes.nama')
            ->get();
        //dd($list);
        $list_dependent = [];

        foreach ($list as $key => $dependent) {
            array_push($list_dependent, $dependent->studentid);
        }

        // ************************* get list organization by parent  *******************************

        $organizations = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            //->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            //->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->select('organizations.*', 'organization_user.user_id')
            ->distinct()
            ->where('organization_user.user_id', $userid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->whereNull('organizations.deleted_at')
            //->where('organizations.type_org','<>',10)
            ->orderBy('organizations.nama')
            ->get();

        // dd($organizations);
        // ************************* get list fees  *******************************

        $getfees = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_student.organclass_id', 'class_organization.id')
            ->join('classes', 'class_organization.class_id', 'classes.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            ->select('fees_new.category', 'fees_new.organization_id', 'students.id as studentid', 'classes.levelid')
            ->distinct()
            ->orderBy('students.id')
            ->orderBy('fees_new.category')
            ->where('fees_new.status', 1)
            ->where('class_student.status', 1)
            ->whereIn('students.id', $list_dependent)
            ->where('student_fees_new.status', 'Debt')
            ->get();

        $getfees_bystudent = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            //->join('fees_recurring as fr', 'fr.student_fees_new_id', '=', 'student_fees_new.id')
            ->select('fees_new.*', 'students.id as studentid')
            ->orderBy('fees_new.name')
            ->where('fees_new.status', 1)
            ->where('class_student.status', 1)
            ->where('student_fees_new.status', 'Debt')
            ->whereIn('students.id', $list_dependent)
            ->get();

        $getfees_bystudentSwasta = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            ->join('fees_recurring as fr', 'fr.student_fees_new_id', '=', 'student_fees_new.id')
            ->select('fees_new.*', 'students.id as studentid', 'fr.*', 'fees_new.id as feesnew_id')
            ->orderBy('fees_new.name')
            ->where('class_student.status', 1)
            ->where('fees_new.status', 1)
            ->where('class_student.status', 1)
            ->where('student_fees_new.status', 'Debt')
            ->whereIn('students.id', $list_dependent)
            ->get();

        //dd($getfees,$getfees_bystudent);
        // ************************* get fees category A  *******************************
        $getfees_category_A = DB::table('fees_new')
            ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
            ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
            ->select('fees_new.category', 'organization_user.organization_id')
            ->distinct()
            ->orderBy('fees_new.category')
            ->where('fees_new.status', 1)
            ->where('organization_user.user_id', $userid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('fees_new_organization_user.status', 'Debt')
            ->get();

        $getfees_category_A_byparent = DB::table('fees_new')
            ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
            ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
            ->select('fees_new.*')
            ->orderBy('fees_new.category')
            ->where('fees_new.status', 1)
            ->where('organization_user.user_id', $userid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('fees_new_organization_user.status', 'Debt')
            ->distinct()
            ->get();
        //dd($list,$organizations,$getfees,$getfees_bystudent,$getfees_category_A,$getfees_category_A_byparent);
        //dd($getfees_category_A_byparent);
        return view('fee.pay.index', compact('list', 'organizations', 'getfees', 'getfees_bystudent', 'getfees_bystudentSwasta', 'getfees_category_A', 'getfees_category_A_byparent'));
    }

    public function student_fees(Request $request)
    {
        $student_id = $request->student_id;
        $getfees_bystudent = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            ->select('fees_new.*', 'students.id as studentid', 'students.nama as studentnama', 'student_fees_new.status')
            ->where('students.id', $student_id)
            ->where('fees_new.status', 1)
            ->where('class_student.status', 1)

            ->orderBy('fees_new.name')
            ->get();

        return response()->json($getfees_bystudent, 200);
    }

    public function parent_dependent(Request $request)
    {
        $case = explode("-", $request->data);

        $user_id = $case[0];
        $organization_id = $case[1];

        $get_dependents = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            ->join('users', 'users.id', 'organization_user.user_id')
            ->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('students.*', 'classes.nama as classname', 'users.name as username')
            ->where('organization_user.user_id', $user_id)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.organization_id', $organization_id)
            ->where('organization_user.status', 1)
            ->where('class_student.status', 1)
            ->get();

        return response()->json($get_dependents, 200);
    }

    public function searchreport()
    {
        $organization = $this->getOrganizationByUserId();

        $listclass = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('classes.id as id', 'classes.nama', 'classes.levelid')
            ->where([
                ['class_organization.organization_id', $organization[0]->id]
            ])
            ->orderBy('classes.nama')
            ->get();

        return view('fee.report-search.index', compact('organization', 'listclass'));
    }

    public function generateExcelClassTransaction(Request $request)
    {
        $class_id = $request->classes;
        $orgId = $request->organization;
        $start_date = $request->date_started;
        $end_date = $request->date_end;
        $show_all_payment = $request->show_all_payments == "true" ? true : false;

        $org = DB::table('organizations')->where('id', $orgId)->first();

        if ($org == null) {
            return redirect()->back()->withError('Invalid Organization!');
        }

        return Excel::download(
            new ExportClassTransaction($class_id, $org, $start_date, $end_date, $show_all_payment),
            'class_transaction.xlsx'
        );
    }



    public function searchreportswasta()
    {
        $organization = $this->getOrganizationByUserId();

        $listclass = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('classes.id as id', 'classes.nama', 'classes.levelid')
            ->where([
                ['class_organization.organization_id', $organization[0]->id]
            ])
            ->orderBy('classes.nama')
            ->get();

        return view('fee.report-search-swasta.index', compact('organization', 'listclass'));
    }

    public function getFeesReceiptDataTable(Request $request)
    {
        if (Auth::id() == null) {
            return redirect("/login");
        }

        if (Auth::user()->hasRole('Superadmin')) {
            if ($request->oid === NULL) {
                $listHisotry = DB::table('transactions as t')
                    ->where(function ($query) {
                        $query->where('t.description', 'like', 'YS%')
                            ->orWhere('t.nama', 'like', 'School_Fees%');
                    })
                    ->where('t.status', 'success')
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date', 't.username as username', 't.transac_no as transac_no');
            } else {
                $listHisotry = DB::table('transactions as t')
                    ->join('fees_transactions_new as ftn', 'ftn.transactions_id', 't.id')
                    ->join('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                    ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                    ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                    ->where(function ($query) {
                        $query->where('t.description', 'like', 'YS%')
                            ->orWhere('t.nama', 'like', 'School_Fees%');
                    })
                    ->where('t.status', 'success')
                    ->where('fn.organization_id', $request->oid)
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date', 't.username as username', 't.transac_no as transac_no')
                    ->distinct('name');
            }
        } else {
            if ($request->oid === NULL) {
                $listHisotry = DB::table('transactions as t')
                    ->where('t.user_id', Auth::id())
                    ->where(function ($query) {
                        $query->where('t.description', 'like', 'YS%')
                            ->orWhere('t.nama', 'like', 'School_Fees%');
                    })
                    ->where('t.status', 'success')
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date', 't.username as username', 't.transac_no as transac_no');
            } else if (Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Koop Admin') || Auth::user()->hasRole('Pentadbir Swasta')) {
                $listHisotry = DB::table('transactions as t')
                    ->join('fees_transactions_new as ftn', 'ftn.transactions_id', 't.id')
                    ->join('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                    ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                    ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                    ->where(function ($query) {
                        $query->where('t.description', 'like', 'YS%')
                            ->orWhere('t.nama', 'like', 'School_Fees%');
                    })
                    ->where('t.status', 'success')
                    ->where('fn.organization_id', $request->oid)
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date', 't.username as username', 't.transac_no as transac_no')
                    ->distinct('name');
            } else if (Auth::user()->hasRole('Guru') || Auth::user()->hasRole('Pentadbir Swasta') || Auth::user()->hasRole('Guru Swasta')) {
                $listHisotry = DB::table('transactions as t')
                    ->join('fees_transactions_new as ftn', 'ftn.transactions_id', 't.id')
                    ->join('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                    ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                    ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->join('organization_user', 'co.organ_user_id', 'organization_user.id')
                    ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                    ->where(function ($query) {
                        $query->where('t.description', 'like', 'YS%')
                            ->orWhere('t.nama', 'like', 'School_Fees%');
                    })
                    ->where('t.status', 'success')
                    ->where('organization_user.user_id', Auth::id())
                    ->where('fn.organization_id', $request->oid)
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date', 't.username as username', 't.transac_no as transac_no')
                    ->distinct('name');
            } else {
                $listHisotry = DB::table('transactions as t')
                    ->join('fees_transactions_new as ftn', 'ftn.transactions_id', 't.id')
                    ->join('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                    ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                    ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                    ->where('t.user_id', Auth::id())
                    ->where(function ($query) {
                        $query->where('t.description', 'like', 'YS%')
                            ->orWhere('t.nama', 'like', 'School_Fees%');
                    })
                    ->where('t.status', 'success')
                    ->where('fn.organization_id', $request->oid)
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date', 't.username as username', 't.transac_no as transac_no')
                    ->distinct('name');
            }
        }

        if ($request->start_date != null && $request->end_date != null) {
            $listHisotry = $listHisotry->whereBetween('datetime_created', [$request->start_date, $request->end_date]);
        }
        $listHisotry = $listHisotry->get();

        //  dd($listHisotry,$request->start_date);
        if (request()->ajax()) {
            return datatables()->of($listHisotry)
                ->editColumn('amount', function ($data) {
                    return number_format($data->amount, 2);
                })
                ->editColumn('date', function ($data) {
                    $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->date)->format('d/m/Y');
                    return $formatedDate;
                })
                ->addColumn('action', function ($data) {

                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href=" ' . route('receipttest', $data->id) . ' " class="btn btn-primary m-1" target="_blank">Papar Resit</a></div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return $listHisotry;
    }


    public function getFeeHistoryExport(Request $request)
    {
        $list = $this->getFeesReceiptDataTable($request);
        // dd($list);
        $organization = DB::table('organizations')->where('id', $request->oid)->first();
        $date = "_all";
        if ($request->start_date != null && $request->end_date != null) {
            $date = '_' . $request->start_date . '_ ' . $request->end_date;
        }
        return Excel::download(new ExportTransaction($organization, $list), $organization->nama . $date . '.xlsx');
    }
    public function cetegoryReportIndex()
    {

        $organization = $this->getOrganizationByUserId();
        // $student_user = DB::table('students as s')
        // ->leftJoin('organization_user_student as ous', 'ous.student_id', 's.id')
        // ->leftJoin('organization_user as ou', 'ou.id', 'ous.organization_user_id', 'ou.id')
        // ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
        // ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
        // ->where('co.class_id', 530)
        // ->select('ou.user_id','s.*')
        // ->orderBy('s.nama')
        // ->get()
        // ->keyBy('user_id');

        // $feeA=DB::table('fees_new_organization_user as fou')
        //         ->leftJoin('organization_user as ou','ou.id','fou.organization_user_id')
        //         ->where('ou.organization_id',159)
        //         ->where('fou.fees_new_id',565)
        //         ->select('ou.user_id','fou.status')
        //         ->get()
        //         ->keyBy('user_id');
        // $data = $student_user->map(function ($student) use ($feeA) {
        //     $user_id = $student->user_id;
        //     if ($feeA->has($user_id)) {
        //         $fee_data = $feeA->get($user_id);
        //         $student->status = $fee_data->status; // Add the status from $feeA to $student_user
        //     }
        //     return $student;
        // });


        return view('fee.categoryReport.index', compact('organization'));
    }

    public function cetegoryReportIndexSwasta()
    {

        $organization = $this->getOrganizationByUserId();
        return view('fee.categoryReport-swasta.index', compact('organization'));
    }

    public function fetchClassForCateYuran(Request $request)
    {
        if (Auth::id() == null) {
            return redirect("/login");
        }

        // dd($request->get('schid'));
        $organ = Organization::find($request->get('oid'));

        if (Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Koop Admin') || Auth::user()->hasRole('Pentadbir Swasta')) {

            $list = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select('classes.id as cid', 'classes.nama as cname')
                ->where([
                    ['class_organization.organization_id', $organ->parent_org != null ? $organ->parent_org : $organ->id],
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
                    ['class_organization.organization_id', $organ->parent_org != null ? $organ->parent_org : $organ->id],
                    ['classes.status', 1],
                    ['organization_user.user_id', Auth::id()]
                ])
                ->orderBy('classes.nama')
                ->get();
        }

        $years = DB::table('fees_new')
            ->where('organization_id', $organ->id)
            ->selectRaw('DISTINCT YEAR(start_date) as year')
            ->orderByDesc('year')
            ->get();

        return response()->json(['success' => $list, 'years' => $years]);
    }

    public function fetchYuran(Request $request)
    {
        $class = ClassModel::find($request->classid);
        $oid = $request->oid;
        $year = $request->fee_year;
        $lists = DB::table('fees_new')
            ->select('fees_new.*', DB::raw("CONCAT(fees_new.category, ' - ', fees_new.name) AS name"))
            ->where('organization_id', $oid)
            ->whereYear('start_date', $year)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        //dd($lists,$year);

        foreach ($lists as $key => $list) {
            $target = json_decode($list->target);
            // dd($target->data);

            if ($target->data == "All_Level" || $target->data == "ALL" || $target->data == $class->levelid) {
                continue;
            }

            if (is_array($target->data)) {
                if (in_array($class->id, $target->data)) {
                    continue;
                }
            }

            unset($lists[$key]);
        }

        return response()->json(['success' => $lists]);
    }

    public function fecthYuranByOrganizationId(Request $request)
    {
        $oid = $request->oid;
        $year = $request->fee_year;
        //dd($year);
        $yurans = DB::table('fees_new as fn')
            ->leftJoin('student_fees_new as sfn', 'sfn.fees_id', '=', 'fn.id')
            ->leftJoin('fees_new_organization_user as fou', 'fou.fees_new_id', '=', 'fn.id')
            ->where(function ($query) {
                $query->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('student_fees_new')
                        ->whereColumn('student_fees_new.fees_id', 'fn.id')
                        ->where('student_fees_new.status', 'paid');
                })->orWhereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('fees_new_organization_user')
                        ->whereColumn('fees_new_organization_user.fees_new_id', 'fn.id')
                        ->where('fees_new_organization_user.status', 'paid');
                });
            })
            ->where('fn.organization_id', $oid)
            ->whereYear('fn.start_date', $year)
            ->groupBy('fn.id')
            ->select('fn.id', DB::raw("CONCAT(fn.category, ' - ', fn.name) AS name"))
            ->orderBy('fn.category')
            ->orderBy('name')
            ->get();

        //dd($yurans);
        return response()->json(['success' => $yurans]);
    }

    public function studentDebtDatatable(Request $request)
    {
        $fees = Fee_New::find($request->feeid);

        if (request()->ajax()) {
            if ($fees->category == "Kategori A") {
                $student_user = DB::table('students as s')
                    ->leftJoin('organization_user_student as ous', 'ous.student_id', 's.id')
                    ->leftJoin('organization_user as ou', 'ou.id', 'ous.organization_user_id', 'ou.id')
                    ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                    ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->where('co.class_id', $request->classid)
                    ->select('ou.user_id', 's.*')
                    ->orderBy('s.nama')
                    ->get()
                    ->keyBy('user_id');

                $feeA = DB::table('fees_new_organization_user as fou')
                    ->leftJoin('organization_user as ou', 'ou.id', 'fou.organization_user_id')
                    ->where('ou.organization_id', $request->orgId)
                    ->where('fou.fees_new_id', $request->feeid)
                    ->select('ou.user_id', 'fou.status')
                    ->get()
                    ->keyBy('user_id');
                $data = $student_user->map(function ($student) use ($feeA) {
                    $user_id = $student->user_id;
                    if ($feeA->has($user_id)) {
                        $fee_data = $feeA->get($user_id);
                        $student->status = $fee_data->status; // Add the status from $feeA to $student_user
                    }
                    return $student;
                });
            } else {
                if ($fees->category != "Kategori Berulang") {
                    $data = DB::table('students as s')
                        ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                        ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                        ->leftJoin('student_fees_new as sfn', 'sfn.class_student_id', 'cs.id')
                        ->where('sfn.fees_id', $fees->id)
                        ->where('cs.status', 1)
                        ->where('co.class_id', $request->classid)
                        ->select('s.*', 'sfn.status')
                        ->orderBy('s.nama')
                        ->get();
                } else {
                    $data = DB::table('students as s')
                        ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                        ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                        ->leftJoin('student_fees_new as sfn', 'sfn.class_student_id', 'cs.id')
                        ->leftJoin('fees_recurring as fr', 'fr.student_fees_new_id', 'sfn.id')
                        ->where('sfn.fees_id', $fees->id)
                        ->where('co.class_id', $request->classid)
                        ->where('cs.status', 1)
                        ->select('s.*', 'sfn.status', 'cs.start_date as cs_startdate', 'fr.*')
                        ->orderBy('s.nama')
                        ->get();
                }
            }

            $table = Datatables::of($data);

            $table->addColumn('status', function ($row) {
                if (property_exists($row, 'status')) {
                    if ($row->status == 'Debt') {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Masih Berhutang </span></div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success"> Telah Bayar </span></div>';

                        return $btn;
                    }
                } else {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-danger"> Masih Berhutang </span></div>';

                    return $btn;
                }
            });


            $table->rawColumns(['status']);

            return $table->make(true);
        }
    }

    public function ExportAllYuranStatus(Request $request)
    {

        if ($request->yuranExport == 0) {
            $filename = "LaporanSemuaYuran";

            if ($request->yuranStatus == 'both_status' || $request->yuranStatus == '') {
                $statusToExport = [1, 0];
            } else {
                $statusToExport = array($request->yuranStatus);
                $filename = $request->yuranStatus == '1' ? "LaporanYuranAktif" : "LaporanYuranTidakAktif";
            }

            $yuran = DB::table('fees_new')
                ->where('organization_id', $request->organExport)
                ->whereIn('status', $statusToExport)
                ->orderBy('status', 'desc')
                ->get();
        } else {
            $yuran = DB::table('fees_new')
                ->where('id', $request->yuranExport)
                ->get();
            $filename = str_replace('/', '-', $yuran[0]->name);
        }


        $orgtypeSwasta = DB::table('organizations as o')
            ->where('id', $request->organExport)
            ->where('o.type_org', 15)
            ->get();


        if (!$orgtypeSwasta || count($orgtypeSwasta) == 0) {
            return Excel::download(new ExportYuranStatus($yuran), $filename . '.xlsx');
        } else {
            return Excel::download(new ExportYuranStatusSwasta($yuran), $filename . '.xlsx');
        }
    }

    public function ExportJumlahBayaranIbuBapa(Request $request)
    {
        $org = DB::table('organizations')
            ->where('id', $request->organExport1)
            ->first();

        if ($request->yuranExport1 != 0) {
            $kelas = DB::table('classes')
                ->where('id', $request->yuranExport1)
                ->first();
            $filename = $kelas->nama;
        } else {
            $filename = $org->nama;
        }

        $filename = str_replace(['/', '\\'], '', $filename);
        $kelasId = $request->yuranExport1;

        $orgtypeSwasta = DB::table('organizations as o')
            ->where('id', $request->organExport1)
            ->where('o.type_org', 15)
            ->get();

        $start_date = $request->date_started;
        $end_date = $request->date_end;
        if (!$orgtypeSwasta || count($orgtypeSwasta) == 0) {
            return Excel::download(new ExportJumlahBayaranIbuBapa($request->yuranExport1, $org, $start_date, $end_date), $filename . '.xlsx');
        } else {
            return Excel::download(new ExportJumlahBayaranIbuBapaSwasta($request->yuranExport1, $org), $filename . '.xlsx');
        }
    }

    public function exportYuranOverview(Request $request)
    {

        $org = DB::table('organizations')
            ->where('id', $request->organization)
            ->first();
        //dd($request);
        $filename = "Yuran_Overview_" . $org->nama;
        return Excel::download(new ExportYuranOverview($org->id), $filename . '.xlsx');
    }

    public function closeFee($id)
    {
        $userId = Auth::id();
        $oid = DB::table('fees_new')
            ->where("id", $id)
            ->value('organization_id');

        $result = DB::table('organization_user')
            ->where('organization_id', $oid)
            ->where('user_id', $userId)
            ->whereIn('role_id', [2, 4])
            ->exists();

        if ($result) {

            DB::table('fees_new')
                ->where('id', '=', $id)
                ->update([
                    'status' => '0'
                ]);

            Session::flash('success', 'Yuran Berjaya Ditutup');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Yuran Gagal Ditutup');
            return View::make('layouts/flash-messages');
        }
    }
}


// set_time_limit(500);
//         $users=DB::table('organization_user as ou')
//                 ->leftJoin('organization_user_student as ous', 'ous.organization_user_id', 'ou.id')
//                 ->leftJoin('students as s', 's.id', 'ous.student_id')
//                 ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
//                 ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
//                 ->leftJoin('classes as c','c.id','co.class_id')
//                 ->leftJoin('users as u','u.id','ou.user_id')
//                 ->whereIn('c.id',[538,539,540])

//                 ->select('u.*')
//                 ->get();
//         //dd($users);
//         foreach($users as $u){

//             DB::table('users')

//             ->where('id', $u->id)
//             ->update([
//                 'name'=> preg_replace('/^\s+|\s+$/u', '', $u->name)
//             ]);
//         }
//         dd("success");