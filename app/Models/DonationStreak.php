<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DonationStreak extends Model
{


    protected $table = 'donation_streak_new';

    protected $fillable = [
        'user_id',
        'startdate',
        'enddate',
        'prim_medal',
        'status',
        'desc',
        'quality_donation',
        'donation_streak_record_ids',
        'total_day'
    ];

    static public function fetchLastTransactionStatus($days,$user_id){
        $transactions = DB::table('transactions')
        ->where('datetime_created', '>=', Carbon::today()->subDays($days))
        ->orderBy('datetime_created')
        ->where('nama','LIKE', "Donation%")
        ->where('status',"Success")
        ->where('user_id',$user_id)
        ->get();

        foreach($transactions as $t)
        {
            $record = DB::table('donation_streak_record')->where('transaction_id',$t->id)->where('status',1)->first();
            $transactionDate = Carbon::parse($t->datetime_created);
            $quality_transaction = $transactionDate->hour >= 3 && $transactionDate->hour < 7.5;

            
            if($record == null){
                $record_id = DB::table('donation_streak_record')->insertGetId([
            
                    'transaction_id' => $t->id,
                    'user_id'       =>$user_id,
                    'quality_donation' => $quality_transaction, //check this
                    'created_at' => $transactionDate,
                    'updated_at' => $transactionDate,
                    'status'     => 1
                ]);
                $record = DB::table('donation_streak_record')->where('transaction_id',$t->id)->where('status',1)->first();
                
            }

            $streak =  DonationStreak::where('user_id', $user_id)
                    ->where('status', 1)
                    ->whereNull('enddate')
                    ->where('prim_medal', -1) 
                    ->where('quality_donation',0)
                    ->first();

            if (!$transactionDate->gte(Carbon::parse($streak->startdate))) {
                continue;
            }
            if($streak !=null){
                $ids = json_decode($streak->donation_streak_record_ids);

                if (!in_array($record->id, $ids)) {
                    DonationStreak::saveStreak($user_id,$transactionDate,0,$record->id);
    
                }
            }
            

           
            if($record->quality_donation){
                $streak =  DonationStreak::where('user_id', $user_id)
                ->where('status', 1)
                ->whereNull('enddate')
                ->where('prim_medal', -1) 
                ->where('quality_donation',1)
                ->first();

                if($streak == null){
                    continue;
                }
                $ids = json_decode($streak->donation_streak_record_ids);

                if (!in_array($record->id, $ids)) {
                    DonationStreak::saveStreak($user_id,$transactionDate,1,$record->id);

                }
            }
        }


    }

    public function validateCurrentStreak(?Carbon $date = null)
    {
        $updatedAt = Carbon::parse($this->updated_at);
        $today = $date??Carbon::today();
    
       // dd($updatedAt->diffInDays($today));
       if($this->status == 0 || $this->enddate != null || $this->prim_medal >=0){
        
        return; 
       }

      
        if (DonationStreak::getDateWithoutTime($updatedAt)->diffInDays($today) > 1) {
            $this->enddate = $this->updated_at;
            $this->desc = "Derma anda henti pada " . $updatedAt->format('Y-m-d');
            $this->save();

        }
    
      
    }


    static private function getDateWithoutTime($date){
        return Carbon::parse($date->format('Y-m-d'));

    }

    static public function getStreakData($user_id = null){
        $user_id = $user_id??Auth::id();
        
        $streaks = DonationStreak::where('user_id', $user_id)
            ->where('status', 1)
            ->whereNull('enddate')
            ->where('prim_medal', -1)
            ->get();
        foreach($streaks as $s){
            $s->validateCurrentStreak();
        }

        $streak =  DonationStreak::where('user_id', $user_id)
            ->where('status', 1)
            ->whereNull('enddate')
            ->where('prim_medal', -1)
            ->where('quality_donation',0) 
            ->first();

        $prim_medal = DonationStreak::where('user_id', $user_id)
                    ->where('status', 1)
                    ->where('prim_medal', 1) 
                    ->where('quality_donation',0) 
                    ->count();


        if($streak == null){
            $donation_streak = ['current_streak'=>0, 'streak_today'=>0, 'prim_medal'=>$prim_medal];
        }else{
 
            $streak_startdate = $streak->startdate;
            $streak_today = Carbon::today()->diffInDays(DonationStreak::getDateWithoutTime($streak->updated_at)) == 0;
            $current_streak = $streak->total_day;
           // dd($streak_today,$streak->updated_at->format('Y-m-d'),Carbon::today());
    
           //dd($current_streak);
           $donation_streak = ['current_streak'=>$current_streak, 'streak_today'=>$streak_today , 'prim_medal' => $prim_medal ,'streak_startdate'=>$streak_startdate];
        }

        $q_streak =  DonationStreak::where('user_id', $user_id)
        ->where('status', 1)
        ->whereNull('enddate')
        ->where('prim_medal', -1)
        ->where('quality_donation',1) 
        ->first();

        $q_prim_medal = DonationStreak::where('user_id', $user_id)
                    ->where('status', 1)
                    ->where('prim_medal', 1) 
                    ->where('quality_donation',1) 
                    ->count();
      
        if($q_streak == null){
            $sedekah_subuh = ['current_streak'=>0, 'streak_today'=>0, 'prim_medal'=>$q_prim_medal];
        }else{
    
            $streak_startdate = $q_streak->startdate;
            $streak_today = Carbon::today()->diffInDays(DonationStreak::getDateWithoutTime($q_streak->updated_at)) == 0;
            $current_streak = $q_streak->total_day;
            // dd($streak_today,$streak->updated_at->format('Y-m-d'),Carbon::today());
    
            //dd($current_streak);
            $sedekah_subuh = ['current_streak'=>$current_streak, 'streak_today'=>$streak_today , 'prim_medal' => $q_prim_medal ,'streak_startdate'=>$streak_startdate];
        }
      
        return ['donation_streak'=>$donation_streak ,'sedekah_subuh'=>$sedekah_subuh];
    }

    static public function updateFollowerStatus($user_id){
        $referral_code_member = DB::table('referral_code_member')
            ->where('member_user_id', $user_id)
            ->where('status',1)
            ->first();

        if( $referral_code_member ==null){
            return;
        }        

        $point_history = DB::table('point_history')
                        ->where('member_id', $referral_code_member->id)
                        ->whereBetween('created_at', [
                            Carbon::today()->subDays(40)->startOfDay(),
                            Carbon::tomorrow()->endOfDay()
                        ])
                        ->select(DB::raw('DATE(created_at) as date'))
                        ->groupBy('date')
                        ->get()
                        ->count();
        
        $member_prim_medal = DB::table('donation_streak')
                            ->where('status',1)
                            ->where('prim_medal','>=',0)
                            ->where('user_id',$user_id)
                            ->count();

        if ($member_prim_medal >= 9 &&  $point_history>=20 ) {
            $level = 11; // Ahli Terbilang 9
        }
        else if ($member_prim_medal >= 3  && $point_history>=15) {
            $level = max(10, 2 +$member_prim_medal) ;// Ahli Cemerlang 3-8
        }
        else if ($member_prim_medal >= 1 && $point_history>=10) {
            $level = max(4, 2 +$member_prim_medal); // Ahli Medal 1-2
        }
        else if ($point_history >= 3) {
            $level = 2; // Ahli Aktif
        }
        else if ($point_history < 3) {
            $level = 1; // Ahli Biasa
        }

        if($level == $referral_code_member->level){
            return;
        }

        DB::table('referral_code_member')->where('id',$referral_code_member->id)->update([
            'level' => $level
        ]);

     
    }
    

    static public function saveStreak($user_id,$transactionDate,$quality_donation,$record_id){
        $streak =  DonationStreak::where('user_id', $user_id)
        ->where('status', 1)
        ->whereNull('enddate')
        ->where('prim_medal', -1) 
        ->where('quality_donation',$quality_donation)
        ->first();
        //dd($transactionDate);
        $today = Carbon::today();

        if ($streak === null) {
            $ids =[] ;
            $streak = new DonationStreak();
            $streak->user_id = $user_id;
            $streak->startdate = $transactionDate;
            $streak->status = 1;
            $streak->prim_medal = -1;
           // $streak->desc = 'Anda telah derma 1 hari';
            $streak->quality_donation = $quality_donation;
            $streak->donation_streak_record_ids = json_encode($ids);
          
            $streak->save();
        } 

        $current_streak =DonationStreak::getDateWithoutTime($transactionDate)->diffInDays($streak->startdate) +1;
        if($current_streak >=40){
            $streak->prim_medal = 1;
            $streak->enddate = $transactionDate;
        }

        $ids = json_decode($streak->donation_streak_record_ids);
        $ids[] = $record_id;

        $streak->donation_streak_record_ids = json_encode($ids);
        $streak->desc = 'Anda telah derma ' . $current_streak . ' hari';
        $streak->updated_at = $transactionDate;
        $streak->total_day = $current_streak;
        
        $streak->save();
    }

    static public function updateStreak($user_id, $transaction_id)
    {
        $transaction = DB::table('transactions')->where('id', $transaction_id)->first();
    
        if (!$transaction || !$user_id) {
            return; // Or handle the error as appropriate
        }
        else if($transaction->status != "Success"){
            return;
        }
        else if(DB::table('donation_streak_record')->where('status',1)->where('transaction_id',$transaction->id)->exists()){
            return;
        }

        $transactionDate = Carbon::parse($transaction->datetime_created);
        $hour = $transactionDate->hour; // Integer representing the hour
        $minute = $transactionDate->minute; // Integer representing the minute

        // Check if the time is between 3:00 AM and 7:30 AM
        $quality_transaction = ($hour >= 3 ) &&
                            ($hour < 7 || ($hour == 7 && $minute <= 30));


        $record_id = DB::table('donation_streak_record')->insertGetId([
            
            'transaction_id' => $transaction->id,
            'user_id'       =>$user_id,
            'quality_donation' => $quality_transaction,
            'created_at' => $transactionDate,
            'updated_at' => $transactionDate,
            'status'     => 1
        ]);
    
        if($quality_transaction){
            $streaks = DonationStreak::where('user_id', $user_id)
            ->where('status', 1)
            ->whereNull('enddate')
            ->where('prim_medal', -1)
            ->where('quality_donation',1)
            ->get();

            foreach($streaks as $s){
                $s->validateCurrentStreak($transactionDate);
            }

            DonationStreak::saveStreak($user_id,$transactionDate,1,$record_id);
           

        }
        
        $streaks = DonationStreak::where('user_id', $user_id)
        ->where('status', 1)
        ->whereNull('enddate')
        ->where('prim_medal', -1)
        ->where('quality_donation',0)
        ->get();

        foreach($streaks as $s){
            $s->validateCurrentStreak($transactionDate);
        }

        DonationStreak::saveStreak($user_id,$transactionDate,0,$record_id);
       

        DonationStreak::updateFollowerStatus($user_id);

    }

    // static public function updateStreak($user_id, $transaction_id)
    // {
    //     $transaction = DB::table('transactions')->where('id', $transaction_id)->first();
    
    //     if (!$transaction || !$user_id) {
    //         return; // Or handle the error as appropriate
    //     }
    //     else if($transaction->status != "Success"){
    //         return;
    //     }
    //     else if(DB::table('donation_streak_transaction')->where('status',1)->where('transaction_id',$transaction->id)->exists()){
    //         return;
    //     }
    
    //     $transactionDate = Carbon::parse($transaction->datetime_created);
    //     $quality_transaction = $transactionDate->hour >= 3 && $transactionDate->hour < 7.5;
    
    //     $streaks = DonationStreak::where('user_id', $user_id)
    //         ->where('status', 1)
    //         ->whereNull('enddate')
    //         ->where('prim_medal', -1)
    //         ->get();
    //     foreach($streaks as $s){
    //         $s->validateCurrentStreak($transactionDate);
    //     }
            
       
    //     $streak =  DonationStreak::where('user_id', $user_id)
    //         ->where('status', 1)
    //         ->whereNull('enddate')
    //         ->where('prim_medal', -1) 
    //         ->first();
    //     //dd($transactionDate);
    //     $today = Carbon::today();
    
    //     if ($streak === null) {
    //         $streak = new DonationStreak();
    //         $streak->user_id = $user_id;
    //         $streak->startdate = $transactionDate;
    //         $streak->status = 1;
    //         $streak->prim_medal = -1;
    //        // $streak->desc = 'Anda telah derma 1 hari';
          
          
    //         $streak->save();
    //     } 
    //     //dd($streak->updated_at);
    
    //     $current_streak =DonationStreak::getDateWithoutTime($transactionDate)->diffInDays($streak->startdate) +1;
    //     if($current_streak >=40){
    //         $streak->prim_medal = 1;
    //         $streak->enddate = $transactionDate;
    //     }


    //     $streak->desc = 'Anda telah derma ' . $current_streak . ' hari';
    //     $streak->updated_at = $transactionDate;
    //     $streak->save();
    
    //     DB::table('donation_streak_transaction')->insert([
    //         'donation_streak_id' => $streak->id,
    //         'transaction_id' => $transaction->id,
    //         'day' => $current_streak,
    //         'quality_donation' => $quality_transaction,
    //         'created_at' => $transactionDate,
    //         'updated_at' => $transactionDate,
    //         'status'     => 1
    //     ]);

    //     DonationStreak::updateFollowerStatus($user_id);

    // }

}
