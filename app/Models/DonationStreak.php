<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DonationStreak extends Model
{


    protected $table = 'donation_streak';

    protected $fillable = [
        'user_id',
        'startdate',
        'enddate',
        'prim_medal',
        'status',
        'desc',
    ];


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
    static public function getStreakData(){
        $user_id = Auth::id();
        
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
            ->first();

        $prim_medal = DonationStreak::where('user_id', $user_id)
                    ->where('status', 1)
                    ->where('prim_medal', 1) 
                    ->count();


        if($streak == null){
            return ['current_streak'=>0, 'streak_today'=>0, 'prim_medal'=>$prim_medal];
        }
        
       
        $streak_startdate = $streak->startdate;
        $streak_today = Carbon::today()->diffInDays(DonationStreak::getDateWithoutTime($streak->updated_at)) == 0;
        $current_streak = Carbon::today()->diffInDays($streak->startdate) + ($streak_today?1:0);
       // dd($streak_today,$streak->updated_at->format('Y-m-d'),Carbon::today());

       //dd($current_streak);
        return ['current_streak'=>$current_streak, 'streak_today'=>$streak_today , 'prim_medal' => $prim_medal ,'streak_startdate'=>$streak_startdate];
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
    
    static public function updateStreak($user_id, $transaction_id)
    {
        $transaction = DB::table('transactions')->where('id', $transaction_id)->first();
    
        if (!$transaction) {
            return; // Or handle the error as appropriate
        }
        else if($transaction->status != "Success"){
            return;
        }
        else if(DB::table('donation_streak_transaction')->where('status',1)->where('transaction_id',$transaction->id)->exists()){
            return;
        }
    
        $transactionDate = Carbon::parse($transaction->datetime_created);
        $quality_transaction = $transactionDate->hour >= 3 && $transactionDate->hour < 7.5;
    
        $streaks = DonationStreak::where('user_id', $user_id)
            ->where('status', 1)
            ->whereNull('enddate')
            ->where('prim_medal', -1)
            ->get();
        foreach($streaks as $s){
            $s->validateCurrentStreak($transactionDate);
        }
            
       
        $streak =  DonationStreak::where('user_id', $user_id)
            ->where('status', 1)
            ->whereNull('enddate')
            ->where('prim_medal', -1) 
            ->first();
        //dd($transactionDate);
        $today = Carbon::today();
    
        if ($streak === null) {
            $streak = new DonationStreak();
            $streak->user_id = $user_id;
            $streak->startdate = $transactionDate;
            $streak->status = 1;
            $streak->prim_medal = -1;
           // $streak->desc = 'Anda telah derma 1 hari';
          
          
            $streak->save();
        } 
        //dd($streak->updated_at);
    
        $current_streak =DonationStreak::getDateWithoutTime($transactionDate)->diffInDays($streak->startdate) +1;
        if($current_streak >=40){
            $streak->prim_medal = 1;
            $streak->enddate = $transactionDate;
        }


        $streak->desc = 'Anda telah derma ' . $current_streak . ' hari';
        $streak->updated_at = $transactionDate;
        $streak->save();
    
        DB::table('donation_streak_transaction')->insert([
            'donation_streak_id' => $streak->id,
            'transaction_id' => $transaction->id,
            'day' => $current_streak,
            'quality_donation' => $quality_transaction,
            'created_at' => $transactionDate,
            'updated_at' => $transactionDate,
            'status'     => 1
        ]);

        DonationStreak::updateFollowerStatus($user_id);

    }

}
