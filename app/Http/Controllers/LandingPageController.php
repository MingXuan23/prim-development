<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Transaction;
use App\Models\Donation;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LandingPageController extends AppBaseController
{
    private $organization;
    private $donation;

    public function __construct(Organization $organization, Donation $donation)
    {
        $this->organization = $organization;
        $this->donation = $donation;
    }

    public function index()
    {
        // return view('landing-page.index');
        // return view('custom-errors.500');
        return view('custom-errors.maintenance');
    }

    //wan add
    public function indexPrim()
    {
        //$schoollist = '';

        $schools = DB::table('organization_url')
            ->join('organizations', 'organization_url.organization_id', '=', 'organizations.id')
            ->join('type_organizations', 'organizations.type_org', '=', 'type_organizations.id')
            ->whereIn('type_organizations.id', [1, 2, 3])
            ->where('organization_url.title', 'NOT LIKE', '%Poli%')
            ->get();

        //dd($schools);

        $politeknik = DB::table('organization_url')
        ->join('organizations', 'organization_url.organization_id', '=', 'organizations.id')
        ->join('type_organizations', 'organizations.type_org', '=', 'type_organizations.id')
        ->whereIn('type_organizations.id', [1, 2, 3])
        ->where('organization_url.title', 'LIKE', '%Poli%')
        ->get();

        //dd($politeknik);

        return view('landing-page.prim.index', ['schools' => $schools], ['politeknik' => $politeknik]);
    }
    //end wan add

    //edit by wan
    public function indexDataFess()
    {
        $currentYear = date('Y');
        $lastYear = date('Y', strtotime('-1 year'));
    
        $organization = DB::table('organizations as o')
            ->join('fees_new as fn', 'fn.organization_id', '=', 'o.id')
            ->leftJoin('organization_url as url', 'url.organization_id', '=', 'o.id')
            ->whereIn('o.type_org', [1, 2, 3, 14])
            ->whereNull('o.deleted_at')
            ->where('o.id','<>',161)
            ->whereIn(DB::raw('YEAR(fn.start_date)'), [$currentYear, $lastYear])
            ->distinct()
            ->select('o.nama','o.id','url.url_name as url') // You might want to select specific columns
            ->get();
    
        foreach ($organization as $o) {

            if (stripos($o->nama, 'MAKTAB MAHMUD') !== false) {
                $o->url = 'lmm';
            }
            
            // Find all distinct years for this organization
            $years = DB::table('fees_new')
                ->where('organization_id', $o->id)
                ->whereIn(DB::raw('YEAR(start_date)'), [$currentYear, $lastYear])
                ->selectRaw('DISTINCT YEAR(start_date) as year')
                ->pluck('year');
    
            $o->data = []; // Initialize as array
    
            foreach ($years as $year) {
                $tranA = DB::table('transactions as t')
                    ->leftJoin('fees_new_organization_user as fou', 't.id', '=', 'fou.transaction_id')
                    ->leftJoin('fees_new as fn', 'fn.id', '=', 'fou.fees_new_id')
                    ->where('t.status', 'Success')
                    ->where('fn.organization_id', $o->id)
                    ->whereYear('fn.start_date', $year)
                    ->select('t.id')
                    ->distinct()
                    ->get();
    
                $tranBC = DB::table('transactions as t')
                    ->leftJoin('fees_transactions_new as ftn', 't.id', '=', 'ftn.transactions_id')
                    ->leftJoin('student_fees_new as sfn', 'sfn.id', '=', 'ftn.student_fees_id')
                    ->leftJoin('fees_new as fn', 'fn.id', '=', 'sfn.fees_id')
                    ->where('fn.organization_id', $o->id)
                    ->where('t.status', 'Success')
                    ->whereYear('fn.start_date', $year)
                    ->select('t.id')
                    ->distinct()
                    ->get();
    
                // Combine the two sets
                $combined = $tranA->concat($tranBC);
                $unique = $combined->unique('id');
    
                // Create JSON-like object for year and count
                $o->data[] = [
                    'year' => $year,
                    'tcount' => $unique->count(),
                ];

                
            }
            $o->transaction_sum = collect($o->data)->sum('tcount');
            unset($o->id);


        }
    
        // Assumed you have calculated the below somewhere earlier, or you need to prepare them too
        
        $organization = $organization
        ->sortByDesc(function ($org) {
            return !is_null($org->url);
        })
        ->sortByDesc('transaction_sum')
        ->values();
    
    dd($organization);
       return response()->json($organization);
    }
    

        // $schools = DB::table('organization_url')
        //     ->join('organizations', 'organization_url.organization_id', '=', 'organizations.id')
        //     ->join('type_organizations', 'organizations.type_org', '=', 'type_organizations.id')
        //     ->whereIn('type_organizations.id', [1, 2, 3])
        //     ->where('organization_url.title', 'NOT LIKE', '%Poli%')
        //     ->get();

        // //dd($schools);

        // $politeknik = DB::table('organization_url')
        //     ->join('organizations', 'organization_url.organization_id', '=', 'organizations.id')
        //     ->join('type_organizations', 'organizations.type_org', '=', 'type_organizations.id')
        //     ->whereIn('type_organizations.id', [1, 2, 3])
        //     ->where('organization_url.title', 'LIKE', '%Poli%')
        //     ->get();
      
    
    public function indexFees()
    {
        $organization = DB::table('organization_url as url')
            ->join('organizations as o', 'url.organization_id', '=', 'o.id')

            ->where('url.status',1)
            ->whereNull("o.deleted_at")
            ->whereIn('o.type_org', [1, 2, 3])
            ->get();

        $organizationCount = $organization->count();

        //get the number of students in each of the school
        $organizationStudentCounts =  DB::table('organization_url as url')
            ->join('organizations as o', 'url.organization_id', '=', 'o.id')
            ->whereIn('o.type_org', [1, 2, 3])
            ->whereNull("o.deleted_at")
            ->leftJoin('class_organization', 'o.id', '=', 'class_organization.organization_id')
            ->leftJoin('class_student', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_student.status', 1)
            ->select('o.id', DB::raw('COUNT(DISTINCT class_student.student_id) as student_count'))
            ->groupBy('o.id')
            ->get();

        //get the maktab mahmud school student count

        //get the number of students in each of the school
        $lmmStudentCounts =  DB::table('organizations as o')
            ->whereRaw('LOWER(o.nama) LIKE ?', ['%maktab mahmud%'])
            ->orWhere('o.id', 137)//include MAAHAD TAHFIZ SAINS DARUL AMAN
            ->whereNull("o.deleted_at")
            ->whereIn('o.type_org', [1, 2, 3])
            ->whereNull("o.deleted_at")
            ->leftJoin('class_organization', 'o.id', '=', 'class_organization.organization_id')
            ->leftJoin('class_student', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_student.status', 1)
            ->select('o.id', DB::raw('COUNT(DISTINCT class_student.student_id) as student_count'))
            ->groupBy('o.id')
            ->get();

        $lmmOrganizations =  DB::table('organizations as o')
            ->whereRaw('LOWER(o.nama) LIKE ?', ['%maktab mahmud%'])
            ->orWhere('o.id', 137)//include MAAHAD TAHFIZ SAINS DARUL AMAN
            ->whereNull("o.deleted_at")
            ->whereIn('o.type_org', [1, 2, 3])
            ->whereNull("o.deleted_at")
            ->leftJoin('class_organization', 'o.id', '=', 'class_organization.organization_id')
            ->leftJoin('class_student', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_student.status', 1)
            ->distinct("o.id")
            ->pluck("o.id");

        //find the donation related to the schools
        $organizationDonations = DB::table('organization_url as url')
            ->join('organizations as o', 'url.organization_id', '=', 'o.id')
            ->whereIn('o.type_org', [1, 2, 3])
            ->whereNull("o.deleted_at")
            ->join("donation_organization as do", "o.id", "=", "do.organization_id")
            ->join("donations as d", "do.donation_id", "=", "d.id")
            ->where("d.status", 1 )
            ->where("date_end", ">=", now())
            ->join(DB::raw("(SELECT organization_id, MAX(d2.date_created) as max_date
                    FROM donation_organization do2
                    JOIN donations d2 ON do2.donation_id = d2.id
                    WHERE d2.date_end >= NOW()
                    GROUP BY organization_id) latest"), function($join) {
                $join->on('do.organization_id', '=', 'latest.organization_id')
                    ->on('d.date_created', '=', 'latest.max_date');
            })
            ->select("do.organization_id", "d.*")
            ->get();

        //get the total number of students
        $studentCount = $organizationStudentCounts->sum("student_count") + $lmmStudentCounts->sum("student_count") - $organizationStudentCounts->where('id', 137)->first()->student_count;//minus MAAHAD TAHFIZ SAINS DARUL AMAN because included in lmmStudentCounts

        //get the total number of fees
        $totalFee =  DB::table("fees_new as fn")
            ->join("fees_new_organization_user as fu", "fn.id" , "fu.fees_new_id")
            ->where("fu.status", "Paid")
            ->sum("fn.totalAmount");

        // Get the total number of fees for this year
        $totalFeeThisYear = DB::table("fees_new as fn")
            ->join("fees_new_organization_user as fu", "fn.id" , "fu.fees_new_id")
            ->where("fu.status", "Paid")
            ->whereYear("fn.start_date", date('Y'))  // This filters by the current year
            ->sum("fn.totalAmount");

        $organization = DB::table('organization_url as url')
            ->join('organizations as o', 'url.organization_id', '=', 'o.id')
            ->where('url.status',1)
            ->whereNull("o.deleted_at")
            ->whereIn('o.type_org', [1, 2, 3])
            ->get();

// Create an array to store results for each organization
        $results = [];
        // Get current year and last year
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;

        // Define date ranges
        $currentYearStartDate = "$currentYear-01-01"; // January 1st of current year
        $currentYearEndDate = "$currentYear-12-31"; // December 31st of last year
        $lastYearStartDate = "$lastYear-01-01"; // January 1st of last year
        $lastYearEndDate = "$lastYear-12-31"; // December 31st of last year

        foreach ($organization as $org) {
            $orgId = $org->id;
            $student_complete_this_year = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('students.*', 'class_student.fees_status','class_student.id as csid','class_student.start_date','class_student.end_date')
                ->where([
                      ['class_organization.organization_id', $orgId ]
                ])
                ->where(function($query) use ($currentYearStartDate, $currentYearEndDate) {
                    $query->whereBetween('class_student.start_date', [$currentYearStartDate, $currentYearEndDate])
                        ->orWhere(function($query) use ($currentYearEndDate) {
                            $query->whereNull('class_student.end_date')
                                ->where('class_student.start_date', '<=', $currentYearEndDate);
                        })
                        ->orWhere(function($query) use ($currentYearStartDate, $currentYearEndDate) {
                            $query->whereNotNull('class_student.end_date')
                                ->whereBetween('class_student.end_date',  [$currentYearStartDate, $currentYearEndDate]);
                        })
                        ->orWhere(function($query) use ($currentYearStartDate, $currentYearEndDate) {
                            $query->whereNotNull('class_student.end_date')
                                ->where( 'class_student.end_date','>=', $currentYearStartDate)
                                ->where('class_student.start_date','<=',$currentYearEndDate);

                        });

                })
                ->get();


            $student_complete_last_year = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('students.*', 'class_student.fees_status','class_student.id as csid','class_student.start_date','class_student.end_date')
                ->where([
                    ['class_organization.organization_id', $orgId ]
                ])
                ->where(function($query) use ($lastYearStartDate, $lastYearEndDate) {
                    $query->whereBetween('class_student.start_date', [$lastYearStartDate, $lastYearEndDate])
                        ->orWhere(function($query) use ($lastYearEndDate) {
                            $query->whereNull('class_student.end_date')
                                ->where('class_student.start_date', '<=', $lastYearEndDate);
                        })
                        ->orWhere(function($query) use ($lastYearStartDate, $lastYearEndDate) {
                            $query->whereNotNull('class_student.end_date')
                                ->whereBetween('class_student.end_date',  [$lastYearStartDate, $lastYearEndDate]);
                        })
                        ->orWhere(function($query) use ($lastYearStartDate, $lastYearEndDate) {
                            $query->whereNotNull('class_student.end_date')
                                ->where( 'class_student.end_date','>=', $lastYearStartDate)
                                ->where('class_student.start_date','<=',$lastYearEndDate);

                        });

                })
                ->get();

            $results[$orgId] = [
                'organization_id' => $orgId,
                'this_year' => [
                    'completed_count' => $student_complete_this_year->where("fees_status", "Completed")
                        ->count(),
                ],
                'last_year' => [
                    'completed_count' => $student_complete_last_year->where("fees_status", "Completed")
                        ->count(),
                ],
                'total_students' => $student_complete_this_year->count(),
            ];
        }

//for lmm
        $lmm_results = [
            'this_year' => [
                'completed_count' => 0
            ],
            'last_year' => [
                'completed_count' => 0
            ],
            'total_students' => 0,
        ];
        foreach($lmmOrganizations as $orgId){
            $student_complete_this_year = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('students.*', 'class_student.fees_status','class_student.id as csid','class_student.start_date','class_student.end_date')
                ->where([
                    ['class_organization.organization_id', $orgId ]
                ])
                ->where(function($query) use ($currentYearStartDate, $currentYearEndDate) {
                    $query->whereBetween('class_student.start_date', [$currentYearStartDate, $currentYearEndDate])
                        ->orWhere(function($query) use ($currentYearEndDate) {
                            $query->whereNull('class_student.end_date')
                                ->where('class_student.start_date', '<=', $currentYearEndDate);
                        })
                        ->orWhere(function($query) use ($currentYearStartDate, $currentYearEndDate) {
                            $query->whereNotNull('class_student.end_date')
                                ->whereBetween('class_student.end_date',  [$currentYearStartDate, $currentYearEndDate]);
                        })
                        ->orWhere(function($query) use ($currentYearStartDate, $currentYearEndDate) {
                            $query->whereNotNull('class_student.end_date')
                                ->where( 'class_student.end_date','>=', $currentYearStartDate)
                                ->where('class_student.start_date','<=',$currentYearEndDate);

                        });

                })
                ->get();


            $student_complete_last_year = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('students.*', 'class_student.fees_status','class_student.id as csid','class_student.start_date','class_student.end_date')
                ->where([
                    ['class_organization.organization_id', $orgId ]
                ])
                ->where(function($query) use ($lastYearStartDate, $lastYearEndDate) {
                    $query->whereBetween('class_student.start_date', [$lastYearStartDate, $lastYearEndDate])
                        ->orWhere(function($query) use ($lastYearEndDate) {
                            $query->whereNull('class_student.end_date')
                                ->where('class_student.start_date', '<=', $lastYearEndDate);
                        })
                        ->orWhere(function($query) use ($lastYearStartDate, $lastYearEndDate) {
                            $query->whereNotNull('class_student.end_date')
                                ->whereBetween('class_student.end_date',  [$lastYearStartDate, $lastYearEndDate]);
                        })
                        ->orWhere(function($query) use ($lastYearStartDate, $lastYearEndDate) {
                            $query->whereNotNull('class_student.end_date')
                                ->where( 'class_student.end_date','>=', $lastYearStartDate)
                                ->where('class_student.start_date','<=',$lastYearEndDate);

                        });

                })
                ->get();

            // Instead of storing by organization ID, just add to the totals
            $lmm_results['this_year']['completed_count'] += $student_complete_this_year->where("fees_status", "Completed")->count();
            $lmm_results['last_year']['completed_count'] += $student_complete_last_year->where("fees_status", "Completed")->count();
            $lmm_results['total_students'] += $student_complete_this_year->count();
        }


        // $schools = DB::table('organization_url')
        //     ->join('organizations', 'organization_url.organization_id', '=', 'organizations.id')
        //     ->join('type_organizations', 'organizations.type_org', '=', 'type_organizations.id')
        //     ->whereIn('type_organizations.id', [1, 2, 3])
        //     ->where('organization_url.title', 'NOT LIKE', '%Poli%')
        //     ->get();

        // //dd($schools);

        // $politeknik = DB::table('organization_url')
        //     ->join('organizations', 'organization_url.organization_id', '=', 'organizations.id')
        //     ->join('type_organizations', 'organizations.type_org', '=', 'type_organizations.id')
        //     ->whereIn('type_organizations.id', [1, 2, 3])
        //     ->where('organization_url.title', 'LIKE', '%Poli%')
        //     ->get();
        return view('landing-page.fees.index', ['results'=> $results, 'lmm_results' => $lmm_results ,'organizations' => $organization,'organizationCount' => $organizationCount , 'organizationStudentCounts' => $organizationStudentCounts , 'lmmStudentCounts' => $lmmStudentCounts, 'organizationDonations' => $organizationDonations , 'studentCount' => $studentCount, 'totalFee' => $totalFee, 'totalFeeThisYear' => $totalFeeThisYear]);
    }
    //end edit by wan

    public function storeMessage(Request $request)
    {
        // dd($request);
        $this->validate($request, [
            'uname'         =>  'required',
            'email'         =>  'required | email',
            'message'       =>  'required',
            'telno'         =>  'required',
        ]);

        $feedback = Feedback::create([
            'name'      => $request->get('uname'),
            'email'     => $request->get('email'),
            'telno'     => $request->get('telno'),
            'message'   => $request->get('message'),
        ]);

        return redirect()->back()->with('alert', 'Terima kasih');
    }

    //edit by wan
    // public function organizationList()
    public function indexOrganizationList()
    {
        // $organization = DB::table('organization_url')
        //     ->join('organizations', 'organization_url.organization_id', '=', 'organizations.id')
        //     ->get();

        // return view('landing-page.organization_list', ['organizations' => $organization]);
        return view('landing-page.organization_list');
    }
    //end edit by wan

    public function activitylist()
    {
        return view('landing-page.listactivity');
    }

    public function activitydetails()
    {
        return view('landing-page.activitydetails');
    }

    // public function getDonationDatatable()
    // {
    //     $data = DB::table('donations')
    //         ->join('donation_organization', 'donation_organization.donation_id', '=', 'donations.id')
    //         ->join('organizations', 'organizations.id', '=', 'donation_organization.organization_id')
    //         ->select('donations.id', 'donations.nama as nama_derma', 'donations.description', 'donations.date_started', 'donations.date_end', 'donations.status', 'donations.url', 'organizations.nama as nama_organisasi', 'organizations.email', 'organizations.address')
    //         ->where('donations.status', 1)
    //         ->orderBy('donations.nama')
    //         ->get();

    //     $table = Datatables::of($data);

    //     $table->addColumn('action', function ($row) {
    //         $btn = '<div class="d-flex justify-content-center">';
    //         $btn = $btn . '<a href="sumbangan/' . $row->url . ' " class="btn btn-success m-1">Bayar</a></div>';
    //         return $btn;
    //     });
    //     $table->rawColumns(['action']);
    //     return $table->make(true);
    // }

    // ********************************Landing page Donation**********************************

    public function indexDonation()
    {
        $organization = Organization::all()->count();
        $curYear = date("Y") . "-01-01";

        $transactions = Transaction::where('nama', 'LIKE', 'Donation%')
            ->where('status', 'Success')
            ->where('datetime_created', '>', $curYear)
            ->get()->count();

        // retrieve daily transactions
        $dailyTransactions = DB::table('transactions')
            ->where('status', 'success')
            ->where('nama', 'LIKE', 'donation%')
            ->where('datetime_created', '>', date('Y-m-d'))
            ->get()->count();

        $totalAmount = DB::table('transactions')
            ->where('status', 'success')
            ->where('nama', 'LIKE', 'donation%')
            ->where('datetime_created', '>', $curYear)
            ->select(DB::table('transactions')->raw('sum(amount) as total_amount'))
            ->first();

        $dailyGain = DB::table('transactions')
            ->where('status', 'success')
            ->where('nama', 'LIKE', 'donation%')
            ->where('datetime_created', '>', date('Y-m-d'))
            ->select(DB::table('transactions')->raw('sum(amount) as total_amount'))
            ->first();

        $dailyGain = $dailyGain->total_amount;

        $totalAmount = (int) $totalAmount->total_amount;

        // dd($totalAmount);

        /*
            SELECT SUM(amount) AS "Total Amount"
            FROM transactions
            WHERE datetime_created > CURDATE()
            AND `nama` LIKE "Donation%"
            AND `status` = "success";
        */

        $donation = DB::table('donations')
            ->where('status', 1)
            ->get()
            ->count();

        $oneWeekBeforeToday = date_create(date('Y-m-d'));
        date_sub($oneWeekBeforeToday,date_interval_create_from_date_string('7 days'));
        $donors = Transaction::where('nama','LIKE' , 'Donation%')
        ->where('status','Success')
        ->where(function($query) use ($oneWeekBeforeToday){
            $query->whereDate('datetime_created', '<=', date('Y-m-d'));
            // ->where('datetime_created' , '>' , $oneWeekBeforeToday);

        })
        ->orderBy('datetime_created' ,'desc')
        ->orderBy('amount' ,'desc')
        ->take(20)
        ->get();

        $leaders = DB::table('referral_code as rc')
        ->join('referral_code_member as rcm', 'rc.id', '=', 'rcm.leader_referral_code_id')
        ->join('users as u', 'u.id', '=', 'rc.user_id')
        ->where('u.id','<>',17151)
        ->groupBy('rc.id', 'u.name') // Also grouping by 'u.name'
        ->select('u.name', DB::raw('COUNT(rcm.id) as member_count'))
        ->orderBy('member_count','desc')
        ->limit(15)
        ->get();


        session()->forget('intendedUrl');//reset intended url for point system
        return view('landing-page.donation.index', compact('organization', 'transactions', 'donation', 'dailyGain', 'dailyTransactions', 'totalAmount' ,'donors','leaders'));
    }

    public function organizationListDonation()
    {
        return view('landing-page.donation.organization_list');
    }

    public function activitylistDonation()
    {
        return view('landing-page.donation.listactivity');
    }

    public function activitydetailsDonation()
    {
        return view('landing-page.donation.activitydetails');
    }

    public function getOrganizationByType($type)
    {
        try {
            $organizations = $this->organization->getOrganizationByType($type);
            return $organizations;
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), 500);
        }
    }

    public function getDonationByOrganizationId($id)
    {
        try {
            $donations = $this->donation->getDonationByOrganizationId($id);
            return $donations;
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), 500);
        }
    }

    public function getOrganizationByDonationId($id)
    {
        try {
            $organizations = $this->organization->getOrganizationByDonationId($id);
            return $organizations;
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), 500);
        }
    }

    public function getOrganizationDatatable(Request $request)
    {
        $data = $this->getOrganizationByType($request->type);

        $table = Datatables::of($data);

        $table->addColumn('action', function ($row) {
            $btn = '<div class="d-flex justify-content-center">';
            // $btn = $btn . '<a class="btn btn-outline-primary waves-effect waves-light btn-sm btn-donation" data-toggle="modal" data-target=".modal-derma" id="'. $row->id . '">Derma</a></div>';
            $btn = $btn . '<a href="#" class="boxed-btn btn-rounded btn-donation" data-toggle="modal" data-target=".modal-derma" id="' . $row->id . '" style="color: white;">Derma</a></div>';

            return $btn;
        });
        $table->rawColumns(['action']);
        return $table->make(true);
    }

    public function getDonationDatatable(Request $request)
    {
        $data = $this->getDonationByOrganizationId($request->id);

        $table = Datatables::of($data);

        $table->addColumn('action', function ($row) {
            $btn = '<div class="d-flex justify-content-center">';
            //$btn = $btn . '<a href="sumbangan/' . $row->url . ' " class="boxed-btn btn-rounded btn-donation">Bayar</a></div>';
            $btn = $btn . '<a href="#" class="boxed-btn btn-rounded btn-donation" data-toggle="modal" data-target=".modal-derma" id="' . $row->id . '" style="color: white;">Derma</a></div>';
            return $btn;
        });
        $table->rawColumns(['action']);
        return $table->make(true);
    }

    public function customOrganizationTabbing(Request $request)
    {
        // dd($request->type);
        $data = Donation::where('donations.donation_type', $request->type)
            ->where('donations.status', 1)
            ->get();
        $table = Datatables::of($data);
        $table->addColumn('email', function ($row) {
            $data1 = $this->getOrganizationByDonationId($row->id);
            $data2 = $data1->email;
            return $data2;
        });
        $table->addColumn('telno', function ($row) {
            $data1 = $this->getOrganizationByDonationId($row->id);
            $data2 = $data1->telno;
            return $data2;
        });
        $table->addColumn('action', function ($row) {
            // dd($row->url);
            $btn = '<div class="d-flex justify-content-center">';
            // $btn = $btn . '<a href="sumbangan/' . $row->url . ' " class="boxed-btn btn-rounded btn-donation">Jom&nbsp;Derma</a></div>';
            $btn = $btn . '<a href="' . route('URLdonate', ['link' => $row->url]) . ' " class="boxed-btn btn-rounded btn-donation">Derma Dengan Nama</a></div>';
            $btn = $btn . '<div class="d-flex justify-content-center"><a href="' . route('ANONdonate', ['link' => $row->url]) . ' " class="boxed-btn btn-rounded btn-donation2">Derma Tanpa Nama</a></div>';
            return $btn;
        });
        $table->rawColumns(['action']);
        return $table->make(true);
    }

    public function getDonationByTabbing(Request $request)
    {
        if ($request->ajax()) {
            $posters = '';


            $donations = DB::table('donations')
                ->where('donations.donation_type', $request->type)
                ->where('donations.status', 1)
                ->inRandomOrder()
                ->get();

            $currentYear = date('Y');


            foreach ($donations as $donation) {
                // to get the total amount of donation for each donation posters
                $amountDonation = DB::table('donation_transaction as dt')->
                join('transactions as t', 't.id' , 'dt.transaction_id')
                ->where([
                    't.status' => 'Success',
                    'dt.donation_id' => $donation->id,
                ])
                ->whereYear('t.datetime_created', $currentYear)
                ->sum('t.amount');

                $previousDonation = DB::table('donation_transaction as dt')->
                join('transactions as t', 't.id' , 'dt.transaction_id')
                ->where([
                    't.status' => 'Success',
                    'dt.donation_id' => $donation->id,
                ])
                ->whereYear('t.datetime_created', $currentYear -1)
                ->sum('t.amount');

                $posters = $posters . '<div class="card"> <div class="donation-amount">Tahun '.$currentYear.':<b> RM'.number_format($amountDonation,2).'</b></div>';
                $posters = $posters. '<div class="donation-amount">Tahun '.($currentYear -1).':<b> RM'.number_format($previousDonation,2).'</b></div><img class="card-img-top donation-poster" src="donation-poster/' . $donation->donation_poster . '" alt="Card image cap" loading="lazy">';
                $posters = $posters . '<div class="card-body"><div class="d-flex flex-column justify-content-center ">';
                $posters = $posters . '<a href="' . route('URLdonate', ['link' => $donation->url]) . ' " class="boxed-btn btn-rounded btn-donation">Derma Dengan Nama</a></div>';
                $posters = $posters . '<div class="d-flex justify-content-center"><a href="' . route('ANONdonate', ['link' => $donation->url]) . ' " class="boxed-btn btn-rounded btn-donation2">Derma Tanpa Nama</a></div></div>
                </div>';
            }

            if ($posters === '') {
                return '';
                // return '<div class="d-flex justify-content-center">Tiada Makulmat Dipaparkan</div>';
            }

            return $posters;
        }
    }

    public function getHeaderPoster()
    {
        $posters = '';

        $donations = DB::table('donations')
            ->where('donations.status', 1)
            ->inRandomOrder()
            ->limit(5)
            ->get();

            $currentYear = date('Y');

        foreach ($donations as $donation) {
            $amountDonation = DB::table('donation_transaction as dt')->
            join('transactions as t', 't.id' , 'dt.transaction_id')
            ->where([
                't.status' => 'Success',
                'dt.donation_id' => $donation->id,
            ])
            ->whereYear('t.datetime_created', $currentYear)
            ->sum('t.amount');

            $previousDonation = DB::table('donation_transaction as dt')->
            join('transactions as t', 't.id' , 'dt.transaction_id')
            ->where([
                't.status' => 'Success',
                'dt.donation_id' => $donation->id,
            ])
            ->whereYear('t.datetime_created', $currentYear -1)
            ->sum('t.amount');

            $posters = $posters . '<div class="card"> <div class="donation-amount">Tahun '.$currentYear.':<b> RM'.number_format($amountDonation,2).'</b></div> ';
            $posters = $posters .'<div class="donation-amount">Tahun '.($currentYear -1).':<b> RM'.number_format($previousDonation,2).'</b></div>';
            $posters = $posters. '<a href="' . route('ANONdonate', ['link' => $donation->url]) . '">';
            $posters = $posters . '<img class="card-img-top header-poster" src="donation-poster/' . $donation->donation_poster . '" alt="Card image cap" loading="lazy"></a></div>';
        }

        if ($posters === '') {
            return '';
        }

        return $posters;
    }
}
