<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\FcmHelper;

class SendFeeReminderNotification extends Command
{
    protected $signature = 'notification:send-fee-reminder';
    protected $description = 'Semak yuran yang hampir tamat tempoh setiap hari pada jam 8 pagi dan hantar peringatan';

    public function handle()
    {

        $today = now()->startOfDay();

        $users = DB::table('users as u')
            ->join('organization_user as ou', 'ou.user_id', '=', 'u.id')
            ->join('user_token as ut', 'ut.user_id', '=', 'u.id')
            ->where('ou.role_id', 6)
            ->where('ou.status', 1)
            ->select(
                'u.id as user_id',
                'u.name',
                'ut.notify_days_before'
            )
            ->groupBy('u.id', 'u.name', 'ut.notify_days_before')
            ->get();


        foreach ($users as $user) {
            $days = $user->notify_days_before;
            $deadline = $today->copy()->addDays($days);

            $orgIds = DB::table('organization_user_student as ous')
                ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
                ->where('ou.user_id', $user->user_id)
                ->pluck('ou.organization_id')->unique()->toArray();

            $studentIds = DB::table('organization_user_student as ous')
                ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
                ->where('ou.user_id', $user->user_id)
                ->pluck('ous.student_id')->unique()->toArray();

            if (empty($orgIds) && empty($studentIds)) {
                continue;
            }

            $fees = collect();

            if (!empty($orgIds)) {
                $categoryAFees = DB::table('fees_new as fn')
                    ->join('fees_new_organization_user as fno', 'fno.fees_new_id', '=', 'fn.id')
                    ->join('organization_user as ou', 'ou.id', '=', 'fno.organization_user_id')
                    ->select(
                        'fn.name as fee_name',
                        'fn.end_date',
                        'fn.price',
                        DB::raw("'Kategori A' as category"),
                        DB::raw("'' as student_name")
                    )
                    ->whereIn('ou.organization_id', $orgIds)
                    ->where('ou.user_id', $user->user_id)
                    ->where('ou.role_id', 6)
                    ->where('ou.status', 1)
                    ->where('fn.status', 1)
                    ->where('fno.status', 'Debt')
                    ->whereBetween('fn.end_date', [$today, $deadline])
                    ->get();

                $fees = $fees->merge($categoryAFees);
            }

            if (!empty($studentIds)) {
                $categoryBCFees = DB::table('fees_new as fn')
                    ->join('student_fees_new as sfn', 'sfn.fees_id', '=', 'fn.id')
                    ->join('class_student as cs', 'cs.id', '=', 'sfn.class_student_id')
                    ->join('students as s', 's.id', '=', 'cs.student_id')
                    ->select(
                        'fn.name as fee_name',
                        'fn.end_date',
                        'fn.price',
                        'fn.category',
                        's.nama as student_name'
                    )
                    ->whereIn('cs.student_id', $studentIds)
                    ->where('fn.status', 1)
                    ->where('sfn.status', 'Debt')
                    ->whereBetween('fn.end_date', [$today, $deadline])
                    ->get();

                $fees = $fees->merge($categoryBCFees);
            }

            if ($fees->isEmpty()) {
                continue;
            }

            $title = "🔔 Peringatan Bayaran Yuran";

            $body = "Hi {$user->name}, berikut adalah yuran yang akan tamat tempoh:\n\n";

            foreach ($fees as $fee) {
                $endDate = \Carbon\Carbon::parse($fee->end_date);
                $daysLeft = now()->startOfDay()->diffInDays($endDate, false);

                if ($daysLeft < 0) {
                    continue;
                }

                if ($daysLeft === 0) {
                    $when = "Tamat hari ini";
                } else {
                    $when = "$daysLeft hari lagi";
                }


                $studentPart = !empty($fee->student_name) ? "({$fee->student_name})" : '';
                $body .= "• {$fee->fee_name} {$studentPart}\n  (Tamat: {$fee->end_date}, $when)\n";
            }

            $body .= "\nSila buat pembayaran secepat mungkin.";

            $success = FcmHelper::sendToUser($user->user_id, $title, trim($body), [
                'type' => 'fee_reminder',
                'due_count' => (string)$fees->count(),
                'days_until_due' => (string)$days,
            ]);
        }
    }
}
