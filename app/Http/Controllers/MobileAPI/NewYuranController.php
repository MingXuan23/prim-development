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

class NewYuranController extends Controller
{
    public function loginAndGetYuran(Request $request)
    {
        try {
            $loginId = $request->input('email') ?? $request->input('login_id');
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

            if ($students->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'icno' => $user->icno,
                    'telno' => $user->telno,
                    'address' => $user->address,
                    'postcode' => $user->postcode,
                    'state' => $user->state,
                    'data' => []
                ], 200);
            }

            $orgIds = $students->pluck('organization_id')->unique()->toArray();
            $studentIds = $students->pluck('student_id')->toArray();

            $categoryAFees = DB::table('fees_new as fn')
                ->join('fees_new_organization_user as fno', 'fno.fees_new_id', '=', 'fn.id')
                ->join('organization_user as ou', 'ou.id', '=', 'fno.organization_user_id')
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

            $receipts = DB::table('transactions as t')
                ->join('fees_transactions_new as ftn', 'ftn.transactions_id', '=', 't.id')
                ->join('student_fees_new as sfn', 'sfn.id', '=', 'ftn.student_fees_id')
                ->join('class_student as cs', 'cs.id', '=', 'sfn.class_student_id')
                ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                ->select('t.id', 't.nama', 't.description', 't.amount', 't.datetime_created as date', 'co.organization_id')
                ->whereIn('co.organization_id', $orgIds)
                ->where('t.user_id', $user->id)
                ->where('t.description', 'like', 'YS%')
                ->where('t.status', 'success')
                ->distinct('t.id')
                ->get();

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

                $orgReceipts = $receipts->where('organization_id', $student->organization_id);

                $studentsData[] = [
                    'student' => $student,
                    'fees' => $allFees,
                    'receipt' => $orgReceipts,
                ];
            }

            $apiToken = bin2hex(random_bytes(32));
            DB::table('users')->where('id', $user->id)->update(['api_token' => $apiToken]);

            return response()->json([
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
                'data' => $studentsData
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
            $mobileStudentFeesIdsStr = $request->query('student_fees_ids', '');
            $mobileParentFeesIdsStr = $request->query('parent_fees_ids', '');

            if (!$sessionKey || !$mobileUserId) {
                abort(400, 'Invalid mobile payment request.');
            }

            $studentFeesNewIds = array_unique(array_filter(explode(',', $mobileStudentFeesIdsStr)));
            $parentFeesIds = array_unique(array_filter(explode(',', $mobileParentFeesIdsStr)));

            $getorganization = null;
            $getstudent = collect([]);
            $getstudentfees = collect([]);
            $get_fees_by_parent = collect([]);
            $getfees_category_A_byparent = collect([]);

            $orgUser = DB::table('organization_user')
                ->join('organizations', 'organizations.id', '=', 'organization_user.organization_id')
                ->select('organizations.*')
                ->where('organization_user.user_id', $mobileUserId)
                ->where('organization_user.role_id', 6)
                ->where('organization_user.status', 1)
                ->first();

            if ($orgUser) {
                $getorganization = $orgUser;
            } else {
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
            }

            if (!$getorganization && !empty($parentFeesIds)) {
                $orgId = DB::table('fees_new as fn')
                    ->join('fees_new_organization_user as fno', 'fno.fees_new_id', '=', 'fn.id')
                    ->join('organization_user as ou', 'ou.id', '=', 'fno.organization_user_id')
                    ->where('ou.user_id', $mobileUserId)
                    ->where('ou.role_id', 6)
                    ->where('ou.status', 1)
                    ->whereIn('fn.id', $parentFeesIds)
                    ->value('ou.organization_id');

                if ($orgId) {
                    $getorganization = Organization::find($orgId);
                }
            }

            if (!$getorganization) {
                abort(400, 'Organization not found for the selected fees. Please contact support.');
            }

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

                $studentIds = $getstudentfees->pluck('studentid')->unique()->toArray();
                $getstudent = DB::table('students')
                    ->select('id as studentid', 'nama as studentname')
                    ->whereIn('id', $studentIds)
                    ->get();
            }

            if (!empty($parentFeesIds)) {
                $get_fees_by_parent = DB::table('fees_new')
                    ->whereIn('id', $parentFeesIds)
                    ->where('status', 1)
                    ->get();

                $getfees_category_A_byparent = DB::table('fees_new as fn')
                    ->join('fees_new_organization_user as fno', 'fno.fees_new_id', '=', 'fn.id')
                    ->join('organization_user as ou', 'ou.id', '=', 'fno.organization_user_id')
                    ->select('fn.*')
                    ->where('ou.user_id', $mobileUserId)
                    ->where('ou.role_id', 6)
                    ->where('ou.status', 1)
                    ->whereIn('fn.id', $parentFeesIds)
                    ->get();
            }

            return view('fee.pay.newmobilepay', compact(
                'getstudent',
                'getorganization',
                'getstudentfees',
                'get_fees_by_parent',
                'getfees_category_A_byparent',
            ) + ['user_id' => $mobileUserId]);
        } catch (\Exception $e) {
            Log::error('Error in mobile pay: ' . $e->getMessage());
            abort(500, 'Failed to load payment page. Please try again later.');
        }
    }

    public function receipt(Request $request, $transactionId)
    {
        try {
            $transaction = Transaction::find($transactionId);
            if (!$transaction) {
                abort(404, 'Transaction not found.');
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
            Log::error('Error in mobile receipt: ' . $e->getMessage());
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
                Log::error("Transaction not found for ID: {$transactionId}");
                abort(404, 'Transaction not found.');
            }

            $get_transaction = $transaction;

            $getparent = DB::table('users')->where('id', $transaction->user_id)->first();
            if (!$getparent) {
                Log::error("Parent not found for user ID: {$transaction->user_id}");
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
                Log::error("Organization not found for transaction ID: {$transactionId}");
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
            Log::critical('CRITICAL ERROR in mobile receipt download: ' . $e->getMessage());
            Log::critical('Stack trace: ' . $e->getTraceAsString());
            abort(500, 'Failed to generate receipt PDF.');
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $apiToken = $request->input('user_token');

            if (!$userId || !$apiToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required parameter: user_id or user_token'
                ], 400);
            }

            $currentUser = DB::table('users')
                ->where('id', $userId)
                ->where('api_token', $apiToken)
                ->first();

            if (!$currentUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 401);
            }


            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $userId,
                'username' => 'nullable|string|max:255|unique:users,username,' . $userId,
                'icno' => 'nullable|string|max:20|unique:users,icno,' . $userId,
                'telno' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'postcode' => 'nullable|string|max:10',
                'state' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $newData = array_filter([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'username' => $request->input('username'),
                'icno' => $request->input('icno'),
                'telno' => $request->input('telno'),
                'address' => $request->input('address'),
                'postcode' => $request->input('postcode'),
                'state' => $request->input('state'),
            ], fn($value) => $value !== '');

            $fieldsToCompare = ['name', 'email', 'username', 'icno', 'telno', 'address', 'postcode', 'state'];
            $hasChanges = false;

            foreach ($fieldsToCompare as $field) {
                $currentValue = $currentUser->$field ?? '';
                $newValue = $newData[$field] ?? '';
                if ($currentValue !== $newValue) {
                    $hasChanges = true;
                    break;
                }
            }

            if (!$hasChanges) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiada perubahan dibuat.'
                ], 200);
            }

            $newData['updated_at'] = now();
            $updated = DB::table('users')->where('id', $userId)->update($newData);

            if ($updated) {
                $updatedUser = DB::table('users')->where('id', $userId)->first();
                return response()->json([
                    'success' => true,
                    'message' => 'Profil berjaya dikemaskini.',
                    'data' => [
                        'name' => $updatedUser->name,
                        'email' => $updatedUser->email,
                        'username' => $updatedUser->username,
                        'icno' => $updatedUser->icno,
                        'telno' => $updatedUser->telno,
                        'address' => $updatedUser->address,
                        'postcode' => $updatedUser->postcode,
                        'state' => $updatedUser->state,
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Kemas kini gagal.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error in updateProfile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat pelayan dalaman.'
            ], 500);
        }
    }
}
