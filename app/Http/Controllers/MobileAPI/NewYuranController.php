<?php

namespace App\Http\Controllers\MobileAPI;

use App\Models\Organization;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;


class NewYuranController extends Controller
{
    const APP_NAME = 'prim_bayarYuran_app';

    public function loginAndGetYuran(Request $request)
    {
        try {
            $loginId = $request->input('email') ?? $request->input('login_id');
            $rememberMe = $request->boolean('remember_me', false);
            if (!$loginId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required parameter: login_id (email, phone, or icno)',
                ], 400);
            }

            $user = null;

            if (is_numeric($loginId)) {
                $user = DB::table('users')->where('icno', $loginId)->first();
                if (!$user) {
                    $candidates = [];
                    $clean = preg_replace('/\D/', '', $loginId);

                    $candidates[] = $loginId;
                    if ($clean !== '' && strlen($clean) >= 9) {
                        $candidates[] = $clean;
                    }
                    if (strlen($clean) >= 10 && $clean[0] === '0') {
                        $candidates[] = substr($clean, 1);
                    }
                    $formatted = $this->formatMalaysianPhone($loginId);
                    $candidates[] = $formatted;

                    $candidates = array_unique($candidates);
                    $user = DB::table('users')->whereIn('telno', $candidates)->first();
                }
            } elseif (strpos($loginId, '@') !== false) {
                $user = DB::table('users')->where('email', $loginId)->first();
            } else {
                $candidates = [];
                $clean = preg_replace('/\D/', '', $loginId);
                $candidates[] = $loginId;
                if ($clean !== '' && strlen($clean) >= 9) {
                    $candidates[] = $clean;
                }
                if (strlen($clean) >= 10 && $clean[0] === '0') {
                    $candidates[] = substr($clean, 1);
                }
                $formatted = $this->formatMalaysianPhone($loginId);
                $candidates[] = $formatted;
                $candidates = array_unique($candidates);
                $user = DB::table('users')->whereIn('telno', $candidates)->first();
            }

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }

            $existingToken = DB::table('user_token')->where('user_id', $user->id)->first();
            $deviceToken = $request->input('device_token');
            $fcmToken = $request->input('fcm_token');

            $isFirstTimeBind = false;

            if (!$existingToken) {
                $isFirstTimeBind = true;
            } elseif ($existingToken->device_token !== $deviceToken) {
                $isFirstTimeBind = true;
            }

            $apiToken = bin2hex(random_bytes(32));

            if (!$deviceToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing device_token'
                ], 400);
            }

            if ($existingToken && $existingToken->device_token !== null) {
                if ($existingToken->device_token !== $deviceToken) {
                    $boundDeviceToken = $existingToken->device_token;
                    $deviceName = preg_replace('/^DEV_[^_]+_/', '', $boundDeviceToken);
                    $deviceName = str_replace('_', '', $deviceName);

                    return response()->json([
                        'success' => false,
                        'message' => 'This account is ready bound to another device.',
                        'error_code' => 'DEVICE_MISMATCH',
                        'hint' => 'Akaun ini telah diikat kepada peranti: ' . $deviceName .
                            '. Log masuk menggunakan peranti berdaftar, atau pilih "Peranti Hilang" untuk tukar peranti.'
                    ], 409);
                }
            }

            $tokenData = [
                'api_token' => $apiToken,
                'fcm_token' => $fcmToken,
                'updated_at' => now(),
                'expired_at' => now()->addYear(),
                'device_token' => $deviceToken
            ];

            $rememberToken = null;
            if ($rememberMe) {
                $rememberToken = bin2hex(random_bytes(32));
                $tokenData['remember_token'] = $rememberToken;
            } else {
                if ($existingToken) {
                    $tokenData['remember_token'] = $existingToken->remember_token;
                    $rememberToken = $existingToken->remember_token;
                }
            }

            if ($existingToken) {
                DB::table('user_token')
                    ->where('user_id', $user->id)
                    ->update($tokenData);
            } else {
                $tokenData['user_id'] = $user->id;
                $tokenData['application_id'] = DB::table('applications')->where('application_name', self::APP_NAME)->value('id');
                DB::table('user_token')->insert($tokenData);
            }

            $students = DB::table('students as s')
                ->join('organization_user_student as ous', 'ous.student_id', '=', 's.id')
                ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
                ->join('organizations as o', 'o.id', '=', 'ou.organization_id')
                ->select(
                    's.id as student_id',
                    's.nama as student_name',
                    'ou.organization_id',
                    'o.nama as organization_name',
                    'ou.user_id'
                )
                ->where('ou.user_id', $user->id)
                ->where('ou.role_id', 6)
                ->where('ou.status', 1)
                ->get();

            $profileImageUrl = $user->profile_image ? asset($user->profile_image) : null;

            if ($students->isEmpty()) {
                return response()->json([
                    'id' => $user->id,
                    'success' => true,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'icno' => $user->icno,
                    'telno' => $user->telno,
                    'address' => $user->address,
                    'postcode' => $user->postcode,
                    'state' => $user->state,
                    'api_token' => $apiToken,
                    'remember_token' => $rememberToken,
                    'is_first_time_device_bind' => $isFirstTimeBind,
                    'profile_image' => $profileImageUrl,
                    'data' => []
                ], 200);
            }

            $orgIds = $students->pluck('organization_id')->unique()->toArray();
            $studentIds = $students->pluck('student_id')->toArray();

            $categoryAFees = DB::table('fees_new as fn')
                ->join('fees_new_organization_user as fno', 'fno.fees_new_id', '=', 'fn.id')
                ->join('organization_user as ou', 'ou.id', '=', 'fno.organization_user_id')
                ->join('organizations as o', 'fn.organization_id', '=', 'o.id')
                ->select(
                    'fn.id',
                    'fn.name',
                    'fn.desc',
                    'fn.category',
                    'fn.quantity',
                    'fn.price',
                    'fn.totalamount',
                    'fn.status',
                    'fn.organization_id',
                    'o.nama as organization_name',
                    'fno.status as fno_status',
                    'fno.transaction_id',
                    'ou.organization_id as ou_org_id',
                    'fn.start_date',
                    'fn.end_date'
                )
                ->whereIn('ou.organization_id', $orgIds)
                ->where('ou.user_id', $user->id)
                ->where('ou.role_id', 6)
                ->where('ou.status', 1)
                ->where('fn.status', 1)
                ->get();

            $categoryBCFees = DB::table('fees_new as fn')
                ->join('student_fees_new as sfn', 'sfn.fees_id', '=', 'fn.id')
                ->join('class_student as cs', 'cs.id', '=', 'sfn.class_student_id')
                ->join('organizations as o', 'fn.organization_id', '=', 'o.id')
                ->select(
                    'fn.id',
                    'fn.name',
                    'fn.desc',
                    'fn.category',
                    'fn.quantity',
                    'fn.price',
                    'fn.totalamount',
                    'fn.status',
                    'fn.organization_id',
                    'o.nama as organization_name',
                    'sfn.id as student_fees_new_id',
                    'sfn.status as sfn_status',
                    'cs.student_id',
                    'fn.start_date',
                    'fn.end_date'
                )
                ->whereIn('cs.student_id', $studentIds)
                ->where('fn.status', 1)
                ->where('sfn.status', 'Debt')
                ->get();

            $receiptsFromStudent = DB::table('transactions as t')
                ->join('fees_transactions_new as ftn', 'ftn.transactions_id', '=', 't.id')
                ->join('student_fees_new as sfn', 'sfn.id', '=', 'ftn.student_fees_id')
                ->join('class_student as cs', 'cs.id', '=', 'sfn.class_student_id')
                ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                ->select('t.id', 't.nama', 't.description', 't.amount', 't.datetime_created as date', 'co.organization_id')
                ->whereIn('co.organization_id', $orgIds)
                ->where('t.user_id', $user->id)
                ->where('t.status', 'Success');

            $receiptsFromParent = DB::table('transactions as t')
                ->join('fees_new_organization_user as fno', 'fno.transaction_id', '=', 't.id')
                ->join('organization_user as ou', 'ou.id', '=', 'fno.organization_user_id')
                ->select('t.id', 't.nama', 't.description', 't.amount', 't.datetime_created as date', 'ou.organization_id')
                ->whereIn('ou.organization_id', $orgIds)
                ->where('t.user_id', $user->id)
                ->where('t.status', 'Success');

            $receipts = $receiptsFromParent->union($receiptsFromStudent)->distinct()->get();

            $studentClassMap = DB::table('students as s')
                ->join('class_student as cs', 'cs.student_id', '=', 's.id')
                ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                ->join('classes as c', 'c.id', '=', 'co.class_id')
                ->select('s.id as student_id', 's.nama as student_name', 'c.nama as class_name')
                ->whereIn('s.id', $studentIds)
                ->get()
                ->keyBy('student_id');

            $studentsData = [];

            foreach ($students as $student) {
                $feesA = $categoryAFees
                    ->where('ou_org_id', $student->organization_id)
                    ->where('fno_status', 'Debt')
                    ->map(function ($fee) {
                        $fee->student_id = null;
                        return $fee;
                    });

                $feesBC = $categoryBCFees
                    ->where('student_id', $student->student_id)
                    ->where('sfn_status', 'Debt');

                $allFees = $feesA->merge($feesBC);

                foreach ($allFees as $fee) {
                    if ($fee->category !== 'Kategory A' && !empty($fee->student_id)) {
                        $info = $studentClassMap[$fee->student_id] ?? null;
                        if ($info) {
                            $fee->category = $fee->category . ' - ' . $info->student_name . ' (' . $info->class_name . ')';
                        }
                    }
                }


                $studentsData[] = [
                    'student' => $student,
                    'fees' => $allFees
                ];
            }



            return response()->json([
                'success' => true,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'icno' => $user->icno,
                'telno' => $user->telno,
                'address' => $user->address,
                'postcode' => $user->postcode,
                'state' => $user->state,
                'api_token' => $apiToken,
                'remember_token' => $rememberToken,
                'data' => $studentsData,
                'receipts' => $receipts,
                'is_first_time_device_bind' => $isFirstTimeBind,
                'profile_image' => $profileImageUrl,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    private function formatMalaysianPhone($number)
    {
        $number = preg_replace('/\D/', '', $number);

        if (strlen($number) < 9) {
            return $number;
        }

        if (strpos($number, '60') === 0) {
            return '+' . $number;
        } elseif ($number[0] === '0') {
            return '+60' . substr($number, 1);
        } else {
            return '+60' . $number;
        }
    }

    public function pay(Request $request)
    {
        try {
            $sessionKey = $request->query('session_key');
            $mobileUserId = $request->query('user_id');

            if (!$sessionKey || !$mobileUserId) {
                abort(400, 'Invalid mobile payment request.');
            }

            $rawStudent = $request->input('student_fees_id', []);
            $rawParent  = $request->input('parent_fees_id', []);

            $studentFeesNewIds = is_array($rawStudent)
                ? array_map('intval', $rawStudent)
                : (strlen($rawStudent) > 0 ? [(int)$rawStudent] : []);

            $parentFeesNewIds = is_array($rawParent)
                ? array_map('intval', $rawParent)
                : (strlen($rawParent) > 0 ? [(int)$rawParent] : []);

            $studentFeesNewIds = array_filter($studentFeesNewIds, fn($id) => is_int($id) && $id > 0);
            $parentFeesNewIds = array_filter($parentFeesNewIds, fn($id) => is_int($id) && $id > 0);

            if (empty($studentFeesNewIds) && empty($parentFeesNewIds)) {
                abort(400, 'Tiada yuran dipilih.');
            }


            $orgUser = DB::table('organization_user')
                ->where('user_id', $mobileUserId)
                ->where('role_id', 6)
                ->where('status', 1)
                ->first();

            if (!$orgUser) {
                abort(400, 'Organization user not found for this guardian.');
            }

            $getorganization = null;
            $getstudent = collect([]);
            $getstudentfees = collect([]);
            $getfees_category_A_byparent = collect([]);

            if (!empty($studentFeesNewIds)) {
                $orgId = DB::table('student_fees_new as sfn')
                    ->join('class_student as cs', 'cs.id', '=', 'sfn.class_student_id')
                    ->join('organization_user_student as ous', 'ous.student_id', '=', 'cs.student_id')
                    ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
                    ->whereIn('sfn.id', $studentFeesNewIds)
                    ->value('ou.organization_id');

                if ($orgId) {
                    $getorganization = Organization::find($orgId);
                }
            }

            if (!$getorganization && !empty($parentFeesNewIds)) {
                $orgId = DB::table('fees_new as fn')
                    ->join('fees_new_organization_user as fno', 'fno.fees_new_id', '=', 'fn.id')
                    ->join('organization_user as ou', 'ou.id', '=', 'fno.organization_user_id')
                    ->where('ou.user_id', $mobileUserId)
                    ->where('ou.role_id', 6)
                    ->where('ou.status', 1)
                    ->whereIn('fn.id', $parentFeesNewIds)
                    ->value('ou.organization_id');

                if ($orgId) {
                    $getorganization = Organization::find($orgId);
                }
            }

            if (!$getorganization) {
                abort(400, 'Organization not found for the selected fees. Please contact support.');
            }

            $totalBC = 0.0;

            if (!empty($studentFeesNewIds)) {
                $getstudentfees = DB::table('student_fees_new as sfn')
                    ->join('fees_new as fn', 'fn.id', '=', 'sfn.fees_id')
                    ->join('class_student as cs', 'cs.id', '=', 'sfn.class_student_id')
                    ->join('students as s', 's.id', '=', 'cs.student_id')
                    ->select(
                        'sfn.id as student_fees_id',
                        'sfn.fees_id',
                        'sfn.status',
                        'fn.name',
                        'fn.desc',
                        'fn.category',
                        'fn.quantity',
                        'fn.price',
                        'fn.totalamount',
                        's.id as studentid',
                        's.nama as studentname'
                    )
                    ->whereIn('sfn.id', $studentFeesNewIds)
                    ->where('sfn.status', 'Debt')
                    ->get();

                foreach ($getstudentfees as $fee) {
                    $totalBC += $fee->quantity * $fee->price;
                }

                $studentIds = $getstudentfees->pluck('studentid')->unique()->toArray();
                $getstudent = DB::table('students')
                    ->select('id as studentid', 'nama as studentname')
                    ->whereIn('id', $studentIds)
                    ->get();
            }

            $totalA = 0.0;
            $fno_ids = [];

            if (!empty($parentFeesNewIds)) {
                $getfees_category_A_byparent = DB::table('fees_new as fn')
                    ->join('fees_new_organization_user as fno', 'fno.fees_new_id', '=', 'fn.id')
                    ->join('organization_user as ou', 'ou.id', '=', 'fno.organization_user_id')
                    ->select('fn.*', 'fno.id as fno_id')
                    ->where('ou.user_id', $mobileUserId)
                    ->where('ou.role_id', 6)
                    ->where('ou.status', 1)
                    ->whereIn('fn.id', $parentFeesNewIds)
                    ->get();

                foreach ($getfees_category_A_byparent as $fee) {
                    $totalA += $fee->quantity * $fee->price;
                }

                $fno_ids = $getfees_category_A_byparent->pluck('fno_id')->unique()->toArray();
            }

            $fixedCharges = $getorganization && isset($getorganization->fixed_charges)
                ? (float)$getorganization->fixed_charges
                : 0.0;

            $grandTotal = $totalA + $totalBC + $fixedCharges;

            return view('fee.pay.newmobilepay', compact(
                'getstudent',
                'getorganization',
                'getstudentfees',
                'getfees_category_A_byparent'
            ) + [
                'user_id' => $mobileUserId,
                'totalA' => $totalA,
                'totalBC' => $totalBC,
                'fixedCharges' => $fixedCharges,
                'grandTotal' => $grandTotal,
                'original_student_fees_ids' => $studentFeesNewIds,
                'fno_ids' => $fno_ids,
            ]);
        } catch (\Exception $e) {
            abort(500, 'Failed to load payment page. Please try again later.');
        }
    }

    public function receipt(Request $request, $transactionId)
    {
        try {
            $token = $request->query('token');
            $deviceToken = $request->query('device_token');
            $forceLogoutUrl = 'https://prim.my/mobile/force-logout';

            if (!$token) {
                return redirect()->away($forceLogoutUrl);
            }

            $session = DB::table('user_token')
                ->where('api_token', $token)
                ->where('expired_at', '>', now())
                ->first();

            if (!$session) {
                return redirect()->away($forceLogoutUrl);
            }

            if ($session->device_token && $session->device_token !== $deviceToken) {
                return redirect()->away($forceLogoutUrl);
            }

            $transaction = Transaction::find($transactionId);
            if (!$transaction) {
                abort(404, 'Transaction not found.');
            }

            if ($transaction->user_id != $session->user_id) {
                abort(403, 'Unauthorized access to this receipt.');
            }

            $getparent = null;
            $get_transaction = $transaction;
            $get_student = collect([]);
            $get_category = collect([]);
            $get_fees = collect([]);
            $getfees_categoryA = collect([]);
            $get_organization = null;

            $getparent = DB::table('users')->where('id', $transaction->user_id)->first();

            $receiptData = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
                ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
                ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
                ->leftJoin('fees_recurring as fr', 'fr.student_fees_new_id', '=', 'student_fees_new.id')
                ->select(
                    'students.id as studentid',
                    'students.nama as studentname',
                    'classes.nama as classname',
                    'fees_new.category',
                    'fees_new.name',
                    'fees_new.quantity',
                    'fees_new.price',
                    'fees_new.totalAmount',
                    'fr.finalAmount as fr_finalamount',
                    'fees_new.organization_id'
                )
                ->where('fees_transactions_new.transactions_id', $transactionId)
                ->get();

            $orgId = $receiptData->isNotEmpty() ? $receiptData->first()->organization_id : null;

            if (!$orgId) {
                $orgId = DB::table('fees_new_organization_user')
                    ->join('fees_new', 'fees_new.id', '=', 'fees_new_organization_user.fees_new_id')
                    ->where('fees_new_organization_user.transaction_id', $transactionId)
                    ->value('fees_new.organization_id');
            }

            if (!$orgId && $getparent && $getparent->organization_id) {
                $orgId = $getparent->organization_id;
            }

            if (!$orgId) {
                abort(400, 'Organization not found for the transaction.');
            }

            $get_organization = Organization::find($orgId);

            $get_student = $receiptData->unique('studentid')->map(function ($item) {
                return (object) [
                    'id' => $item->studentid,
                    'nama' => $item->studentname,
                    'classname' => $item->classname
                ];
            });

            $get_fees = $receiptData->map(function ($item) {
                if ($item->category === 'Kategori Berulang' && $item->fr_finalamount !== null) {
                    $item->totalAmount = $item->fr_finalamount;
                }
                return (object) $item;
            });

            $get_category = $get_fees->unique(function ($item) {
                return $item->category . '_' . $item->studentid;
            })->map(function ($item) {
                return (object) [
                    'category' => $item->category,
                    'studentid' => $item->studentid
                ];
            });

            $getfees_categoryA = DB::table('fees_new')
                ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
                ->select('fees_new.*')
                ->where('fees_new_organization_user.transaction_id', $transactionId)
                ->get();

            return view('fee.pay.newreceipt2', compact(
                'getparent',
                'get_transaction',
                'get_student',
                'get_category',
                'get_fees',
                'getfees_categoryA',
                'get_organization'
            ));
        } catch (\Exception $e) {
            abort(500, 'Failed to load receipt page.');
        }
    }

    public function downloadReceipt(Request $request, $transactionId)
    {
        try {
            set_time_limit(300);
            ini_set('memory_limit', '512M');

            $transaction = Transaction::find($transactionId);
            if (!$transaction) {
                abort(404, 'Transaction not found.');
            }

            $get_transaction = $transaction;

            $getparent = DB::table('users')->where('id', $transaction->user_id)->first();
            if (!$getparent) {
                abort(404, 'Parent not found for this transaction.');
            }

            $receiptData = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
                ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
                ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
                ->select(
                    'students.id as studentid',
                    'students.nama as studentname',
                    'classes.nama as classname',
                    'fees_new.category',
                    'fees_new.name',
                    'fees_new.quantity',
                    'fees_new.totalAmount',
                    'fees_new.organization_id'
                )
                ->where('fees_transactions_new.transactions_id', $transactionId)
                ->get();

            $orgId = $receiptData->isNotEmpty() ? $receiptData->first()->organization_id : null;

            if (!$orgId) {
                $orgId = DB::table('fees_new_organization_user')
                    ->join('fees_new', 'fees_new.id', '=', 'fees_new_organization_user.fees_new_id')
                    ->where('fees_new_organization_user.transaction_id', $transactionId)
                    ->value('fees_new.organization_id');
            }

            if (!$orgId && $getparent && $getparent->organization_id) {
                $orgId = $getparent->organization_id;
            }

            if (!$orgId) {
                abort(400, 'Organization not found for the transaction.');
            }

            $get_organization = Organization::find($orgId);

            $get_student = $receiptData->unique('studentid')->map(function ($item) {
                return (object) [
                    'id' => $item->studentid,
                    'nama' => $item->studentname,
                    'classname' => $item->classname
                ];
            });

            $get_fees = $receiptData->map(function ($item) {
                return (object) $item;
            });

            $get_category = $get_fees->unique(function ($item) {
                return $item->category . '_' . $item->studentid;
            })->map(function ($item) {
                return (object) [
                    'category' => $item->category,
                    'studentid' => $item->studentid
                ];
            });

            $getfees_categoryA = DB::table('fees_new')
                ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
                ->select('fees_new.*')
                ->where('fees_new_organization_user.transaction_id', $transactionId)
                ->get();

            $data = compact(
                'getparent',
                'get_transaction',
                'get_student',
                'get_category',
                'get_fees',
                'getfees_categoryA',
                'get_organization'
            );

            $pdf = Pdf::loadView('fee.pay.receipt2_pdf', $data)
                ->setPaper('A4', 'portrait')
                ->setOption('isRemoteEnabled', true)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('defaultFont', 'Arial');

            return $pdf->download("resit-{$transactionId}.pdf");
        } catch (\Exception $e) {
            abort(500, 'Failed to generate receipt PDF.');
        }
    }


    public function updateProfile(Request $request)
    {
        try {
            $userId = $request->input('auth_user_id');

            $currentUser = DB::table('users')->where('id', $userId)->first();

            if (!$currentUser) {
                return response()->json(['success' => false, 'message' => 'Pengguna tidak dijumpai.'], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:users,email,' . $userId,
                'username' => 'nullable|string|max:255|unique:users,username,' . $userId,
                'icno' => 'nullable|string|max:20|unique:users,icno,' . $userId,
                'telno' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'postcode' => 'nullable|string|max:10',
                'state' => 'nullable|string|max:50',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $newData = [];
            $hasChanges = false;

            $standardFields = ['username', 'address', 'postcode', 'state'];
            foreach ($standardFields as $field) {
                if ($request->has($field)) {
                    $newData[$field] = $request->input($field);
                }
            }

            $protectedFields = ['email', 'name', 'icno', 'telno'];

            foreach ($protectedFields as $field) {
                if ($request->has($field)) {
                    $inputValue = trim($request->input($field));

                    if ($inputValue !== '') {
                        $newData[$field] = $inputValue;
                    } else {
                        if (!empty($currentUser->$field)) {
                        } else {
                            $newData[$field] = null;
                        }
                    }
                }
            }

            if ($request->input('remove_image') === 'true') {
                if ($currentUser->profile_image) {
                    $oldPath = public_path($currentUser->profile_image);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }
                $newData['profile_image'] = null;
                $hasChanges = true;
            } else if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');

                if ($currentUser->profile_image) {
                    $oldPath = public_path($currentUser->profile_image);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('profile_images'), $filename);

                $newData['profile_image'] = 'profile_images/' . $filename;
                $hasChanges = true;
            }

            foreach ($newData as $key => $newValue) {
                $currentValue = $currentUser->$key ?? null;

                $strCurrent = (string)$currentValue;
                $strNew = (string)$newValue;

                if ($strCurrent !== $strNew) {
                    $hasChanges = true;
                }
            }

            if (!$hasChanges) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiada perubahan dibuat.'
                ], 200);
            }

            $newData['updated_at'] = now();

            DB::table('users')->where('id', $userId)->update($newData);

            $updatedUser = DB::table('users')->where('id', $userId)->first();
            $profileImageUrl = $updatedUser->profile_image ? asset($updatedUser->profile_image) : null;

            return response()->json([
                'success' => true,
                'message' => 'Profil berjaya dikemaskini.',
                'data' => [
                    'id' => $updatedUser->id,
                    'name' => $updatedUser->name,
                    'email' => $updatedUser->email,
                    'username' => $updatedUser->username,
                    'icno' => $updatedUser->icno,
                    'telno' => $updatedUser->telno,
                    'address' => $updatedUser->address,
                    'postcode' => $updatedUser->postcode,
                    'state' => $updatedUser->state,
                    'profile_image' => $profileImageUrl,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ralat pelayan dalaman: ' . $e->getMessage()
            ], 500);
        }
    }

    public function refreshSession(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $rememberToken = $request->input('remember_token');
            $deviceToken = $request->input('device_token');

            if (!$userId || !$rememberToken || !$deviceToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required parameter'
                ], 400);
            }

            $userTokenRecord = DB::table('user_token')
                ->where('user_id', $userId)
                ->where('remember_token', $rememberToken)
                ->where('expired_at', '>', now())
                ->first();

            if (!$userTokenRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired session.'
                ], 401);
            }

            if ($userTokenRecord->device_token !== $deviceToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login failed.',
                    'error_code' => 'DEVICE_MISMATCH',
                    'hint' => 'Please log in using your registered device.'
                ], 403);
            }

            $user = DB::table('users')->where('id', $userId)->first();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }

            $rememberToken = $userTokenRecord->remember_token;
            $newApiToken = bin2hex(random_bytes(32));

            DB::table('user_token')
                ->where('user_id', $userId)
                ->update([
                    'api_token' => $newApiToken,
                    'updated_at' => now(),
                    'expired_at' => now()->addYear(),
                ]);

            $students = DB::table('students as s')
                ->join('organization_user_student as ous', 'ous.student_id', '=', 's.id')
                ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
                ->join('organizations as o', 'o.id', '=', 'ou.organization_id')
                ->select(
                    's.id as student_id',
                    's.nama as student_name',
                    'ou.organization_id',
                    'o.nama as organization_name',
                    'ou.user_id'
                )
                ->where('ou.user_id', $userId)
                ->where('ou.role_id', 6)
                ->where('ou.status', 1)
                ->get();

            $profileImageUrl = $user->profile_image ? asset($user->profile_image) : null;

            if ($students->isEmpty()) {
                return response()->json([
                    'id' => $user->id,
                    'success' => true,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'icno' => $user->icno,
                    'telno' => $user->telno,
                    'address' => $user->address,
                    'postcode' => $user->postcode,
                    'state' => $user->state,
                    'api_token' => $newApiToken,
                    'profile_image' => $profileImageUrl,
                    'data' => []
                ], 200);
            }

            $orgIds = $students->pluck('organization_id')->unique()->toArray();
            $studentIds = $students->pluck('student_id')->toArray();

            $categoryAFees = DB::table('fees_new as fn')
                ->join('fees_new_organization_user as fno', 'fno.fees_new_id', '=', 'fn.id')
                ->join('organization_user as ou', 'ou.id', '=', 'fno.organization_user_id')
                ->join('organizations as o', 'fn.organization_id', '=', 'o.id')
                ->select(
                    'fn.id',
                    'fn.name',
                    'fn.desc',
                    'fn.category',
                    'fn.quantity',
                    'fn.price',
                    'fn.totalamount',
                    'fn.status',
                    'fn.organization_id',
                    'o.nama as organization_name',
                    'fno.status as fno_status',
                    'fno.transaction_id',
                    'ou.organization_id as ou_org_id',
                    'fn.start_date',
                    'fn.end_date'
                )
                ->whereIn('ou.organization_id', $orgIds)
                ->where('ou.user_id', $userId)
                ->where('ou.role_id', 6)
                ->where('ou.status', 1)
                ->where('fn.status', 1)
                ->get();

            $categoryBCFees = DB::table('fees_new as fn')
                ->join('student_fees_new as sfn', 'sfn.fees_id', '=', 'fn.id')
                ->join('class_student as cs', 'cs.id', '=', 'sfn.class_student_id')
                ->join('organizations as o', 'fn.organization_id', '=', 'o.id')
                ->select(
                    'fn.id',
                    'fn.name',
                    'fn.desc',
                    'fn.category',
                    'fn.quantity',
                    'fn.price',
                    'fn.totalamount',
                    'fn.status',
                    'fn.organization_id',
                    'o.nama as organization_name',
                    'sfn.id as student_fees_new_id',
                    'sfn.status as sfn_status',
                    'cs.student_id',
                    'fn.start_date',
                    'fn.end_date'
                )
                ->whereIn('cs.student_id', $studentIds)
                ->where('fn.status', 1)
                ->where('sfn.status', 'Debt')
                ->get();

            $receiptsFromStudent = DB::table('transactions as t')
                ->join('fees_transactions_new as ftn', 'ftn.transactions_id', '=', 't.id')
                ->join('student_fees_new as sfn', 'sfn.id', '=', 'ftn.student_fees_id')
                ->join('class_student as cs', 'cs.id', '=', 'sfn.class_student_id')
                ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                ->select('t.id', 't.nama', 't.description', 't.amount', 't.datetime_created as date', 'co.organization_id')
                ->whereIn('co.organization_id', $orgIds)
                ->where('t.user_id', $user->id)
                ->where('t.status', 'Success');

            $receiptsFromParent = DB::table('transactions as t')
                ->join('fees_new_organization_user as fno', 'fno.transaction_id', '=', 't.id')
                ->join('organization_user as ou', 'ou.id', '=', 'fno.organization_user_id')
                ->select('t.id', 't.nama', 't.description', 't.amount', 't.datetime_created as date', 'ou.organization_id')
                ->whereIn('ou.organization_id', $orgIds)
                ->where('t.user_id', $user->id)
                ->where('t.status', 'Success');

            $receipts = $receiptsFromParent->union($receiptsFromStudent)->distinct()->get();

            $studentClassMap = DB::table('students as s')
                ->join('class_student as cs', 'cs.student_id', '=', 's.id')
                ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                ->join('classes as c', 'c.id', '=', 'co.class_id')
                ->select('s.id as student_id', 's.nama as student_name', 'c.nama as class_name')
                ->whereIn('s.id', $studentIds)
                ->get()
                ->keyBy('student_id');

            $studentsData = [];

            foreach ($students as $student) {
                $feesA = $categoryAFees
                    ->where('ou_org_id', $student->organization_id)
                    ->where('fno_status', 'Debt')
                    ->map(function ($fee) {
                        $fee->student_id = null;
                        return $fee;
                    });

                $feesBC = $categoryBCFees
                    ->where('student_id', $student->student_id)
                    ->where('sfn_status', 'Debt');

                $allFees = $feesA->merge($feesBC);

                foreach ($allFees as $fee) {
                    if ($fee->category !== 'Kategory A' && !empty($fee->student_id)) {
                        $info = $studentClassMap[$fee->student_id] ?? null;
                        if ($info) {
                            $fee->category = $fee->category . ' - ' . $info->student_name . ' (' . $info->class_name . ')';
                        }
                    }
                }


                $studentsData[] = [
                    'student' => $student,
                    'fees' => $allFees
                ];
            }

            $updatedToken = DB::table('user_token')->where('user_id', $userId)->first();

            return response()->json([
                'success' => true,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'icno' => $user->icno,
                'telno' => $user->telno,
                'address' => $user->address,
                'postcode' => $user->postcode,
                'state' => $user->state,
                'api_token' => $newApiToken,
                'remember_token' =>  $updatedToken->remember_token,
                'data' => $studentsData,
                'receipts' => $receipts,
                'profile_image' => $profileImageUrl,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function unbindDevice(Request $request)
    {
        $userId = $request->input('auth_user_id') ?? $request->input('user_id');
        $currentDeviceToken = $request->input('device_token');

        if (!$userId || !$currentDeviceToken) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required parameter: user_id or device_token'
            ], 400);
        }

        $userTokenRecord = DB::table('user_token')
            ->where('user_id', $userId)
            ->where('device_token', $currentDeviceToken)
            ->first();

        if (!$userTokenRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No binding found for this device.'
            ], 404);
        }

        DB::table('user_token')
            ->where('user_id', $userId)
            ->update([
                'device_token' => null,
                'remember_token' => null,
                'fcm_token' => null,
                'updated_at' => now(),
                'expired_at' => null
            ]);


        return response()->json([
            'success' => true,
            'message' => 'Device unbound successfully.'
        ], 200);
    }

    public function getUserByEmailOrPhone(Request $request)
    {
        $loginId = $request->input('login_id');
        if (!$loginId) {
            return response()->json(['success' => false, 'message' => 'Missing login_id'], 400);
        }

        $user = $this->findUserByAnyLoginId($loginId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }


        $students = DB::table('students as s')
            ->join('organization_user_student as ous', 'ous.student_id', '=', 's.id')
            ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
            ->join('organizations as o', 'o.id', '=', 'ou.organization_id')
            ->join('class_student as cs', 'cs.student_id', '=', 's.id')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->join('classes as c', 'c.id', '=', 'co.class_id')
            ->where('ou.user_id', $user->id)
            ->select(
                's.nama as student_name',
                'c.nama as class_name',
                'o.nama as organization_name'
            )
            ->get()
            ->map(function ($item) {
                return [
                    'student_name' => $item->student_name,
                    'class_name' => $item->class_name,
                    'organization_name' => $item->organization_name,
                ];
            })
            ->toArray();

        $deviceData = DB::table('user_token')->where('user_id', $user->id)->first();

        return response()->json([
            'success' => true,
            'email' => $user->email,
            'has_icno' => !empty($user->icno),
            'students' => $students,
            'device_token' => $deviceData ? $deviceData->device_token : null,
        ], 200);
    }

    public function sendOtp(Request $request)
    {
        $email = $request->input('email');
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put("otp_$email", $otp, now()->addMinutes(10));


        try {
            Mail::raw("Kod OTP anda: $otp\nSah selama 10 minit.", function ($message) use ($email) {
                $message->to($email)->subject('Kod OTP untuk Gantian Peranti');
            });

            return response()->json(['success' => true, 'message' => 'OTP sent']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send email'], 500);
        }
    }

    public function forceBindDevice(Request $request)
    {
        $email = $request->input('email');
        $newDeviceToken = $request->input('new_device_token');
        $rememberMe = $request->boolean('remember_me', false);
        $fcmToken = $request->input('fcm_token');
        $isFirstTimeBind = true;
        $newEmail = $request->input('new_email');

        $user = null;
        if ($request->has('login_id')) {
            $user = $this->findUserByAnyLoginId($request->input('login_id'));
        } else {
            $user = DB::table('users')->where('email', $email)->first();
        }

        if (!$user) {
            return response()->json(['message' => 'Pengguna tidak wujud'], 404);
        }

        if (!empty($newEmail) && $newEmail !== $user->email) {
            $exists = DB::table('users')->where('email', $newEmail)->where('id', '!=', $user->id)->exists();
            if (!$exists) {
                DB::table('users')->where('id', $user->id)->update(['email' => $newEmail]);
                $user->email = $newEmail;
            }
        }

        $newApiToken = bin2hex(random_bytes(32));
        $rememberToken = null;

        if ($rememberMe) {
            $rememberToken = bin2hex(random_bytes(32));
        }

        DB::table('user_token')
            ->where('user_id', $user->id)
            ->update([
                'device_token' => $newDeviceToken,
                'api_token' => $newApiToken,
                'remember_token' => $rememberToken,
                'application_id' => DB::table('applications')->where('application_name', self::APP_NAME)->value('id'),
                'fcm_token' => $fcmToken,
                'updated_at' => now(),
                'expired_at' => now()->addYear(),
            ]);


        $students = DB::table('students as s')
            ->join('organization_user_student as ous', 'ous.student_id', '=', 's.id')
            ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
            ->join('organizations as o', 'o.id', '=', 'ou.organization_id')
            ->select(
                's.id as student_id',
                's.nama as student_name',
                'ou.organization_id',
                'o.nama as organization_name',
                'ou.user_id'
            )
            ->where('ou.user_id', $user->id)
            ->where('ou.role_id', 6)
            ->where('ou.status', 1)
            ->get();

        $profileImageUrl = $user->profile_image ? asset($user->profile_image) : null;

        if ($students->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Peranti berjaya digantikan',
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'icno' => $user->icno,
                'telno' => $user->telno,
                'address' => $user->address,
                'postcode' => $user->postcode,
                'state' => $user->state,
                'api_token' => $newApiToken,
                'remember_token' => $rememberToken,
                'data' => [],
                'receipts' => [],
                'is_first_time_device_bind' => $isFirstTimeBind ?? false,
                'profile_image' => $profileImageUrl,
            ]);
        }

        $orgIds = $students->pluck('organization_id')->unique()->toArray();
        $studentIds = $students->pluck('student_id')->toArray();

        $categoryAFees = DB::table('fees_new as fn')
            ->join('fees_new_organization_user as fno', 'fno.fees_new_id', '=', 'fn.id')
            ->join('organization_user as ou', 'ou.id', '=', 'fno.organization_user_id')
            ->join('organizations as o', 'fn.organization_id', '=', 'o.id')
            ->select(
                'fn.id',
                'fn.name',
                'fn.desc',
                'fn.category',
                'fn.quantity',
                'fn.price',
                'fn.totalamount',
                'fn.status',
                'fn.organization_id',
                'o.nama as organization_name',
                'fno.status as fno_status',
                'fno.transaction_id',
                'ou.organization_id as ou_org_id',
                'fn.start_date',
                'fn.end_date'
            )
            ->whereIn('ou.organization_id', $orgIds)
            ->where('ou.user_id', $user->id)
            ->where('ou.role_id', 6)
            ->where('ou.status', 1)
            ->where('fn.status', 1)
            ->get();

        $categoryBCFees = DB::table('fees_new as fn')
            ->join('student_fees_new as sfn', 'sfn.fees_id', '=', 'fn.id')
            ->join('class_student as cs', 'cs.id', '=', 'sfn.class_student_id')
            ->join('organizations as o', 'fn.organization_id', '=', 'o.id')
            ->select(
                'fn.id',
                'fn.name',
                'fn.desc',
                'fn.category',
                'fn.quantity',
                'fn.price',
                'fn.totalamount',
                'fn.status',
                'fn.organization_id',
                'o.nama as organization_name',
                'sfn.id as student_fees_new_id',
                'sfn.status as sfn_status',
                'cs.student_id',
                'fn.start_date',
                'fn.end_date'
            )
            ->whereIn('cs.student_id', $studentIds)
            ->where('fn.status', 1)
            ->where('sfn.status', 'Debt')
            ->get();

        $receiptsFromStudent = DB::table('transactions as t')
            ->join('fees_transactions_new as ftn', 'ftn.transactions_id', '=', 't.id')
            ->join('student_fees_new as sfn', 'sfn.id', '=', 'ftn.student_fees_id')
            ->join('class_student as cs', 'cs.id', '=', 'sfn.class_student_id')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->select('t.id', 't.nama', 't.description', 't.amount', 't.datetime_created as date', 'co.organization_id')
            ->whereIn('co.organization_id', $orgIds)
            ->where('t.user_id', $user->id)
            ->where('t.status', 'Success');

        $receiptsFromParent = DB::table('transactions as t')
            ->join('fees_new_organization_user as fno', 'fno.transaction_id', '=', 't.id')
            ->join('organization_user as ou', 'ou.id', '=', 'fno.organization_user_id')
            ->select('t.id', 't.nama', 't.description', 't.amount', 't.datetime_created as date', 'ou.organization_id')
            ->whereIn('ou.organization_id', $orgIds)
            ->where('t.user_id', $user->id)
            ->where('t.status', 'Success');

        $receipts = $receiptsFromParent->union($receiptsFromStudent)->distinct()->get();

        $studentClassMap = DB::table('students as s')
            ->join('class_student as cs', 'cs.student_id', '=', 's.id')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->join('classes as c', 'c.id', '=', 'co.class_id')
            ->select('s.id as student_id', 's.nama as student_name', 'c.nama as class_name')
            ->whereIn('s.id', $studentIds)
            ->get()
            ->keyBy('student_id');

        $studentsData = [];

        foreach ($students as $student) {
            $feesA = $categoryAFees
                ->where('ou_org_id', $student->organization_id)
                ->where('fno_status', 'Debt')
                ->map(function ($fee) {
                    $fee->student_id = null;
                    return $fee;
                });

            $feesBC = $categoryBCFees
                ->where('student_id', $student->student_id)
                ->where('sfn_status', 'Debt');

            $allFees = $feesA->merge($feesBC);

            foreach ($allFees as $fee) {
                if ($fee->category !== 'Kategory A' && !empty($fee->student_id)) {
                    $info = $studentClassMap[$fee->student_id] ?? null;
                    if ($info) {
                        $fee->category = $fee->category . ' - ' . $info->student_name . ' (' . $info->class_name . ')';
                    }
                }
            }

            $studentsData[] = [
                'student' => $student,
                'fees' => $allFees
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Peranti berjaya digantikan',
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'icno' => $user->icno,
            'telno' => $user->telno,
            'address' => $user->address,
            'postcode' => $user->postcode,
            'state' => $user->state,
            'api_token' => $newApiToken,
            'remember_token' => $rememberToken,
            'data' => $studentsData,
            'receipts' => $receipts,
            'is_first_time_device_bind' => $isFirstTimeBind,
            'profile_image' => $profileImageUrl,
        ]);
    }

    public function updateUserEmail(Request $request)
    {
        $loginId = $request->input('login_id');
        $newEmail = $request->input('email');

        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => false, 'message' => 'Emel tidak sah'], 400);
        }

        $user = $this->findUserByAnyLoginId($loginId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Pengguna tidak dijumpai'], 404);
        }

        DB::table('users')->where('id', $user->id)->update(['email' => $newEmail]);

        return response()->json([
            'success' => true,
            'message' => 'Emel berjaya dikemaskini'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $email = $request->input('email');
        $otp = $request->input('otp');
        $loginId = $request->input('login_id');

        if (!$email || !$otp || !$loginId) {
            return response()->json(['success' => false, 'message' => 'Missing required parameters'], 400);
        }

        $originalUser = $this->findUserByAnyLoginId($loginId);
        if (!$originalUser) {
            return response()->json(['success' => false, 'message' => 'Pengguna tidak dijumpai'], 404);
        }

        $cachedOtp = Cache::get("otp_$email");
        if ($cachedOtp !== $otp) {
            return response()->json(['success' => false, 'message' => 'OTP salah'], 400);
        }

        Cache::forget("otp_$email");

        if (!$cachedOtp) {
            return response()->json(['success' => false, 'message' => 'OTP tamat tempoh atau tidak sah'], 400);
        }

        if ($cachedOtp !== $otp) {
            return response()->json(['success' => false, 'message' => 'OTP salah'], 400);
        }

        Cache::forget("otp_$email");

        return response()->json([
            'success' => true,
            'message' => 'OTP berjaya disahkan'
        ]);
    }

    protected function normalizePhoneNumber($phone)
    {
        $clean = preg_replace('/[^\d]/', '', $phone);
        if (strlen($clean) < 9) return $clean;

        $candidates = [];

        if (substr($clean, 0, 2) === '60') {
            $candidates[] = '+60' . substr($clean, 2);
            $candidates[] = '0' . substr($clean, 2);
        } elseif ($clean[0] === '0') {
            $candidates[] = '+60' . substr($clean, 1);
            $candidates[] = $clean;
        } else {
            $candidates[] = '+60' . $clean;
            $candidates[] = '0' . $clean;
        }

        return array_unique($candidates);
    }

    private function findUserByAnyLoginId($loginId)
    {
        if (!$loginId) return null;

        if (filter_var($loginId, FILTER_VALIDATE_EMAIL)) {
            return DB::table('users')->where('email', $loginId)->first();
        }

        if (is_numeric($loginId)) {
            $user = DB::table('users')->where('icno', $loginId)->first();
            if ($user) return $user;

            $clean = preg_replace('/\D/', '', $loginId);
            $phoneCandidates = $this->normalizePhoneNumber($loginId);

            return DB::table('users')
                ->whereIn('telno', $phoneCandidates)
                ->first();
        }

        $phoneCandidates = $this->normalizePhoneNumber($loginId);

        return DB::table('users')
            ->whereIn('telno', $phoneCandidates)
            ->first();
    }

    public function getNotifyDays(Request $request)
    {
        try {
            $userId = $request->input('auth_user_id') ?? $request->input('user_id');

            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'User ID not found'], 400);
            }

            $record = DB::table('user_token')->where('user_id', $userId)->first();

            if (!$record) {
                return response()->json(['success' => false, 'message' => 'Settings not found'], 404);
            }

            return response()->json([
                'success' => true,
                'notify_days_before' => $record->notify_days_before
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateNotifyDays(Request $request)
    {
        try {
            $userId = $request->input('auth_user_id') ?? $request->input('user_id');
            $days = $request->input('notify_days_before');

            if ($days === null || $days < 1 || $days > 30) {
                return response()->json([
                    'success' => false,
                    'message' => 'notify_days_before must be between 1 and 30'
                ], 400);
            }

            $updated = DB::table('user_token')
                ->where('user_id', $userId)
                ->update(['notify_days_before' => $days]);

            return response()->json([
                'success' => true,
                'message' => 'Notification days updated successfully',
                'notify_days_before' => $days
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOrganizations(Request $request)
    {
        try {
            $organizations = DB::table("organizations as o")
                ->join("type_organizations as to", "to.id", "=", "o.type_org")
                ->whereIn("to.nama", ["SK /SJK", "SRA /SRAI", "SMK /SMJK", "Sekolah Swasta /Tadika"])
                ->select("o.*")
                ->get();

            return response()->json([
                'success' => true,
                'data' => $organizations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ralat pelayan dalaman.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getClasses(Request $request)
    {
        try {
            $organizationId = $request->input('organization_id');

            if (!$organizationId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter organization_id diperlukan.'
                ], 400);
            }


            $classes = DB::table('class_organization as co')
                ->join('classes as c', 'c.id', '=', 'co.class_id')
                ->where('co.organization_id', $organizationId)
                ->select('c.id as cid', 'c.nama as cname')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $classes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ralat pelayan dalaman.'
            ], 500);
        }
    }



    public function registerStudents(Request $request)
    {
        try {
            $userId = $request->input('auth_user_id') ?? $request->input('user_id');
            $apiToken = $request->input('user_token');

            if (!$userId || !$apiToken) {
                Log::warning('registerStudents - Missing user_id or user_token', ['user_id' => $userId, 'api_token_present' => !empty($apiToken)]);
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required parameter: user_id or user_token'
                ], 400);
            }

            Log::info('registerStudents - Authenticating user', ['user_id' => $userId]);

            $parentToken = DB::table('user_token')
                ->where('user_id', $userId)
                ->where('api_token', $apiToken)
                ->first();

            if (!$parentToken) {
                Log::warning('registerStudents - Invalid token for user', ['user_id' => $userId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'students' => 'required|array|min:1',
                'students.*.name' => 'required|string|max:255',
                'students.*.icno' => 'required|string|size:14|unique:students,icno',
                'students.*.email' => 'nullable|email|max:255|unique:students,email',
                'students.*.gender' => 'required|in:L,P',
                'students.*.class_id' => 'required|exists:classes,id',
            ]);

            if ($validator->fails()) {
                Log::warning('registerStudents - Validation failed', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Pengesahan gagal.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $studentsData = $request->input('students');
            $failedRegistrations = [];

            DB::beginTransaction();
            Log::info('registerStudents - Database transaction started');

            foreach ($studentsData as $index => $studentInfo) {
                try {
                    $organizationId = $this->getOrganizationIdForClass($studentInfo['class_id']);

                    $icExists = DB::table('students')->where('icno', $studentInfo['icno'])->exists();
                    $emailExists = !empty($studentInfo['email']) && DB::table('students')->where('email', $studentInfo['email'])->exists();

                    if ($icExists) {
                        throw new \Exception("No. kad pengenalan tersebut sudah digunakan.");
                    }
                    if ($emailExists) {
                        throw new \Exception("Emel tersebut sudah digunakan.");
                    }

                    $studentJson = json_encode([
                        'name' => $studentInfo['name'],
                        'icno' => $studentInfo['icno'],
                        'gender' => $studentInfo['gender'],
                        'email' => $studentInfo['email'] ?? null,
                        'class_id' => $studentInfo['class_id'],
                    ]);

                    DB::table('registration_requests')->insert([
                        'student_info' => $studentJson,
                        'status' => 'Pending',
                        'parent_id' => $userId,
                        'organization_id' => $organizationId
                    ]);
                } catch (\Exception $e) {
                    Log::error("registerStudents - Failed to process student {$index}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'student_info' => $studentInfo
                    ]);
                    $failedRegistrations[] = [
                        'index' => $index,
                        'name' => $studentInfo['name'],
                        'error' => $e->getMessage()
                    ];
                }
            }

            if (count($failedRegistrations) === count($studentsData)) {
                DB::rollBack();
                Log::warning('registerStudents - All registrations failed, transaction rolled back', ['failed_registrations' => $failedRegistrations]);
                return response()->json([
                    'success' => false,
                    'message' => 'Semua pendaftaran gagal.',
                    'details' => $failedRegistrations
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permohonan pendaftaran pelajar telah dihantar kepada pentadbir untuk disemak dan diterima.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::critical('registerStudents - Uncaught Exception caused 500 error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ralat semasa pendaftaran.'
            ], 500);
        }
    }

    private function getOrganizationIdForClass($classId)
    {
        $org = DB::table('class_organization')
            ->where('class_id', $classId)
            ->first(['organization_id']);

        if (!$org) {
            throw new \Exception("Kelas tidak dikaitkan dengan sebarang organisasi.");
        }

        return $org->organization_id;
    }

    public function registerParent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'icno'      => ['required', 'string', 'min:12', 'max:14'],
            'telno'     => ['required', 'numeric', 'min:10', 'unique:users,telno'],
        ], [
            'icno.min' => 'IC No. mesti sekurang-kurangnya 12 digit',
            'telno.unique' => 'Nombor telefon telah didaftarkan.',
            'email.unique' => 'Emel telah didaftarkan.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Sila semak maklumat anda.',
                'errors' => $validator->errors()
            ], 422);
        }

        $cleanIc = str_replace("-", "", $request->input("icno"));
        $icExists = DB::table("users")->where("icno", $cleanIc)->exists();

        if ($icExists) {
            return response()->json([
                'success' => false,
                'message' => 'No. Kad Pengenalan telah wujud.',
                'errors' => ['icno' => ['No. Kad Pengenalan telah didaftarkan.']]
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                "name" => $request->input("name"),
                "email" => $request->input("email"),
                "password" => Hash::make("abc123"),
                "telno" => $request->input("telno"),
                "purpose" => "bayar_yuran",
            ]);

            DB::table("users")->where("id", "=", $user->id)->update([
                "icno" => $cleanIc,
                "email_verified_at" => now()
            ]);

            $role = DB::table("roles")->where("name", "Penjaga")->first();
            if ($role) {
                DB::table("model_has_roles")->insert([
                    "role_id" => $role->id,
                    "model_id" => $user->id,
                    "model_type" => "App\User"
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tahniah! Pendaftaran berjaya! Sila log masuk.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Register Parent Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat semasa pendaftaran. Sila cuba lagi.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function getAnnouncements(Request $request)
    {
        try {
            $userId = $request->input('auth_user_id') ?? $request->input('user_id');
            $requestedOrgId = $request->input('organization_id');

            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'User ID is required'], 400);
            }

            $query = DB::table('organization_user_student as ous')
                ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
                ->join('class_student as cs', 'cs.student_id', '=', 'ous.student_id')
                ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                ->join('classes as c', 'c.id', '=', 'co.class_id')
                ->where('ou.user_id', $userId)
                ->where('ou.role_id', 6)
                ->where('ou.status', 1);

            if ($requestedOrgId) {
                $query->where('ou.organization_id', $requestedOrgId);
            }

            $studentsData = $query->select(
                'ou.organization_id',
                'co.class_id as real_class_id',
                'c.levelid'
            )->get();

            if ($studentsData->isEmpty()) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $parentOrgIds = $studentsData->pluck('organization_id')->unique()->toArray();
            $classIds = $studentsData->pluck('real_class_id')->unique()->toArray();

            $allowedTahaps = ['both'];
            foreach ($studentsData as $data) {
                if ($data->levelid) {
                    $level = (int)$data->levelid;
                    if ($level >= 1 && $level <= 3) {
                        if (!in_array('1', $allowedTahaps)) $allowedTahaps[] = '1';
                    } elseif ($level >= 4 && $level <= 6) {
                        if (!in_array('2', $allowedTahaps)) $allowedTahaps[] = '2';
                    }
                }
            }

            $orgAnnouncements = DB::table('organization_announcements as oa')
                ->join('organizations as o', 'o.id', '=', 'oa.organization_id')
                ->whereIn('oa.organization_id', $parentOrgIds)
                ->where('oa.status', 'published')
                ->whereIn('oa.tahap', $allowedTahaps)
                ->select(
                    'oa.id',
                    'oa.title',
                    'oa.content',
                    'oa.created_at',
                    'oa.tahap',
                    'o.nama as source_name',
                    DB::raw('"organization" as type'),
                    DB::raw('NULL as student_name')
                );

            $classAnnouncements = DB::table('class_announcements as ca')
                ->join('classes as c', 'c.id', '=', 'ca.class_id')
                ->join('class_organization as co', 'co.class_id', '=', 'c.id')
                ->join('organizations as o', 'o.id', '=', 'co.organization_id')
                ->whereIn('ca.class_id', $classIds)
                ->whereIn('o.id', $parentOrgIds)
                ->where('ca.status', 'published')
                ->distinct()
                ->select(
                    'ca.id',
                    'ca.title',
                    'ca.content',
                    'ca.created_at',
                    DB::raw('NULL as tahap'),
                    DB::raw("CONCAT(o.nama, ' - ', c.nama) as source_name"),
                    DB::raw('"class" as type'),
                    DB::raw('NULL as student_name')
                );

            $allAnnouncements = $orgAnnouncements
                ->union($classAnnouncements)
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedData = $allAnnouncements->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'source_name' => $item->source_name,
                    'tahap' => $item->tahap,
                    'type' => $item->type,
                    'date' => \Carbon\Carbon::parse($item->created_at)->format('d M Y, h:i A'),
                    'raw_date' => $item->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching announcements',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSocialFeeds(Request $request)
    {
        try {
            $userId = $request->input('auth_user_id') ?? $request->input('user_id');

            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'User ID is required'], 400);
            }

            \Carbon\Carbon::setLocale('ms');

            $classIds = DB::table('organization_user_student as ous')
                ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
                ->join('class_student as cs', 'cs.student_id', '=', 'ous.student_id')
                ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                ->where('ou.user_id', $userId)
                ->where('ou.status', 1)
                ->pluck('co.class_id')
                ->unique()
                ->toArray();

            if (empty($classIds)) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $query = DB::table('class_posts as p')
                ->join('users as u', 'u.id', '=', 'p.user_id')
                ->join('classes as c', 'c.id', '=', 'p.class_id')
                ->whereIn('p.class_id', $classIds)
                ->select(
                    'p.id',
                    'p.content',
                    'p.media_url',
                    'p.media_type',
                    'p.created_at',
                    'u.name as author_name',
                    'u.profile_image as author_image',
                    'c.nama as class_name',
                    DB::raw('(SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = p.id) as like_count'),
                    DB::raw('(SELECT COUNT(*) FROM post_comments WHERE post_comments.post_id = p.id) as comment_count'),
                    DB::raw('(SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = p.id AND post_likes.user_id = ?) as is_liked')
                )
                ->setBindings([$userId], 'select')
                ->orderBy('p.created_at', 'desc');

            $posts = $query->paginate(10);

            foreach ($posts as $post) {
                $post->media_full_url = $post->media_url ? asset($post->media_url) : null;
                $post->author_image_url = $post->author_image ? asset($post->author_image) : null;
                $post->is_liked = $post->is_liked > 0;
                $post->time_ago = \Carbon\Carbon::parse($post->created_at)->diffForHumans();
            }

            return response()->json([
                'success' => true,
                'data' => $posts
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleLikePost(Request $request)
    {
        $userId = $request->input('auth_user_id') ?? $request->input('user_id');
        $postId = $request->input('post_id');

        if (!$userId || !$postId) return response()->json(['success' => false], 400);

        $exists = DB::table('post_likes')
            ->where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();

        $action = '';
        if ($exists) {
            DB::table('post_likes')->where('id', $exists->id)->delete();
            $action = 'unliked';
        } else {
            DB::table('post_likes')->insert([
                'user_id' => $userId,
                'post_id' => $postId,
                'created_at' => now(),
            ]);
            $action = 'liked';
        }

        $newLikeCount = DB::table('post_likes')->where('post_id', $postId)->count();

        try {
            $options = array(
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            );

            if (config('app.env') === 'local') {
                $options['curl_options'] = [
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                ];
            }

            $pusher = new \Pusher\Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );

            $data = [
                'post_id' => (int)$postId,
                'new_like_count' => $newLikeCount
            ];

            $pusher->trigger('social-feed', 'post-liked', $data);
        } catch (\Exception $e) {
            Log::error("Pusher Like Error: " . $e->getMessage());
        }

        return response()->json(['success' => true, 'action' => $action, 'new_count' => $newLikeCount]);
    }

    public function getPostComments(Request $request)
    {
        $postId = $request->input('post_id');

        if (!$postId) {
            return response()->json(['success' => false, 'message' => 'Post ID is required'], 400);
        }

        \Carbon\Carbon::setLocale('ms');

        $comments = DB::table('post_comments as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->where('c.post_id', $postId)
            ->select('c.id', 'c.user_id', 'c.content', 'c.created_at', 'u.name as user_name', 'u.profile_image')
            ->orderBy('c.created_at', 'asc')
            ->get();

        $comments->transform(function ($comment) {
            $comment->profile_image_url = $comment->profile_image ? asset($comment->profile_image) : null;
            $comment->time_ago = \Carbon\Carbon::parse($comment->created_at)->diffForHumans();
            return $comment;
        });

        return response()->json(['success' => true, 'data' => $comments]);
    }

    public function addPostComment(Request $request)
    {
        $userId = $request->input('auth_user_id') ?? $request->input('user_id');
        $postId = $request->input('post_id');
        $content = $request->input('content');

        if (!$userId || !$postId || !$content) {
            return response()->json(['success' => false, 'message' => 'Missing parameters'], 400);
        }

        $id = DB::table('post_comments')->insertGetId([
            'user_id' => $userId,
            'post_id' => $postId,
            'content' => $content,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $newCommentCount = DB::table('post_comments')->where('post_id', $postId)->count();

        try {
            $options = array(
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            );

            if (config('app.env') === 'local') {
                $options['curl_options'] = [
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                ];
            }

            $pusher = new \Pusher\Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );

            $data = [
                'post_id' => (int)$postId,
                'new_comment_count' => $newCommentCount
            ];

            $pusher->trigger('social-feed', 'comment-added', $data);
        } catch (\Exception $e) {
            Log::error("Pusher Comment Error: " . $e->getMessage());
        }

        return response()->json(['success' => true, 'comment_id' => $id]);
    }

    public function deletePostComment(Request $request)
    {
        $userId = $request->input('auth_user_id') ?? $request->input('user_id');
        $commentId = $request->input('comment_id');

        if (!$userId || !$commentId) {
            return response()->json(['success' => false, 'message' => 'Missing parameters'], 400);
        }

        $comment = DB::table('post_comments')->where('id', $commentId)->first();

        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Comment not found'], 404);
        }

        if ($comment->user_id != $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action'], 403);
        }

        DB::table('post_comments')->where('id', $commentId)->delete();


        if (isset($comment->post_id)) {
            $postId = $comment->post_id;
            $newCommentCount = DB::table('post_comments')->where('post_id', $postId)->count();

            try {
                $options = array('cluster' => env('PUSHER_APP_CLUSTER'), 'useTLS' => true);
                if (config('app.env') === 'local') {
                    $options['curl_options'] = [CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0];
                }
                $pusher = new \Pusher\Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), $options);

                $pusher->trigger('social-feed', 'comment-added', [
                    'post_id' => (int)$postId,
                    'new_comment_count' => $newCommentCount
                ]);
            } catch (\Exception $e) {
                Log::error("Pusher Delete Error: " . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => 'Comment deleted successfully']);
    }

    public function updatePostComment(Request $request)
    {
        $userId = $request->input('auth_user_id') ?? $request->input('user_id');
        $commentId = $request->input('comment_id');
        $content = $request->input('content');

        if (!$userId || !$commentId || empty($content)) {
            return response()->json(['success' => false, 'message' => 'Missing parameters'], 400);
        }

        $comment = DB::table('post_comments')->where('id', $commentId)->first();

        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Comment not found'], 404);
        }

        if ($comment->user_id != $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action'], 403);
        }

        DB::table('post_comments')
            ->where('id', $commentId)
            ->update([
                'content' => $content,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Comment updated successfully']);
    }

    public function checkEmailAvailability(Request $request)
    {
        $email = $request->input('email');
        $exists = DB::table('users')->where('email', $email)->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Emel ini telah digunakan oleh pengguna lain.']);
        }
        return response()->json(['success' => true]);
    }

    public function verifyIdentity(Request $request)
    {
        $loginId = $request->input('login_id');
        $type = $request->input('type');
        $value = $request->input('value');

        $user = $this->findUserByAnyLoginId($loginId);
        if (!$user) return response()->json(['success' => false, 'message' => 'User not found'], 404);

        if ($type === 'icno') {
            $inputIc = str_replace(['-', ' '], '', $value);
            $dbIc = str_replace(['-', ' '], '', $user->icno);

            if ($inputIc === $dbIc) {
                return response()->json(['success' => true]);
            }
        } elseif ($type === 'name') {
            if (strtolower(trim($value)) === strtolower(trim($user->name))) {
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Maklumat tidak tepat.']);
    }

    public function getPostLikesUser(Request $request)
    {
        try {
            $postId = $request->input('post_id');

            $likes = DB::table('post_likes')
                ->join('users', 'post_likes.user_id', '=', 'users.id')
                ->where('post_likes.post_id', $postId)
                ->select('users.name', 'users.profile_image')
                ->orderBy('post_likes.created_at', 'desc')
                ->get();

            $formattedLikes = $likes->map(function ($user) {
                return [
                    'name' => $user->name,
                    'profile_image' => $user->profile_image ? asset($user->profile_image) : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedLikes
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function checkAuthStatus(Request $request)
    {
        return response()->json(
            [
                'success' => true,
                'message' => 'Session and device are valid.'
            ]

        );
    }

    // admin test FCM cron notification
    public function testfcm()
    {
        $exitCode = Artisan::call('notification:send-fee-reminder');

        $output = Artisan::output();

        return response()->json([
            'message' => 'Command executed',
            'exitCode' => $exitCode,
            'output' => $output
        ]);
    }
}
