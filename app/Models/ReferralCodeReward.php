<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class ReferralCodeReward extends Model
{
    //
    protected $table = 'referral_code_reward';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'desc',
        'status',
        'quantity',
        'condition',
        'requireAsset',
        'additionInfo',
        'external_link',
        'payment',
        'paymentAmount',
    ];
//{"medal": {"num": 1, "quality": 2}, "follower": {"num": 1, "level": 1}, "follower_medal": {"num": 2, "quality": 40}}
    public function validateCondition($user_id){
        $condition = json_decode($this->condition);
       
        if(isset($condition->medal)){
            $results = DB::table('donation_streak as ds')
                        ->join('donation_streak_transaction as dst', 'dst.donation_streak_id', 'ds.id')
                        ->where('ds.status', 1)
                        ->where('dst.status', 1)
                        ->where('ds.prim_medal', '>', 0)
                        ->where('ds.user_id', $user_id)
                        ->groupBy('ds.id')
                        ->select('ds.id', 
                        DB::raw('COUNT(CASE WHEN dst.quality_donation = 1 THEN 1 END) as quality_donation_count'),
                        DB::raw('COUNT(CASE WHEN dst.quality_donation = 0 THEN 1 END) as normal_donation_count'))
                        ->get();

            if(isset($condition->medal->quality)){
                $quality_condition =$condition->medal->quality;
                $results = $results->filter(function($result) use ($quality_condition) {
                    return $result->quality_donation_count >= $quality_condition;
                });
            }
                        //dd($condition->medal->num);
            //dd($medal_count ,$condition);
            //dd( count($results),$results);
            if($condition->medal->num >  count($results)){
                return false;
            }
        }

        if(isset($condition->follower)){
            $follower = DB::table('referral_code as rc')
                        ->join('referral_code_member as rcm','rcm.leader_referral_code_id','rc.id')
                        ->where('rc.status',1)
                        ->where('rcm.status',1)
                        ->where('rc.user_id',$user_id)
                        ->where('rcm.level','>=',$condition->follower->level)
                        ->groupBy('rc.id')
                        ->count('rcm.id');
            if($condition->follower->num > $follower){
                return false;
            }
           
        }

        if(isset($condition->follower_medal)){
            $follower =  DB::table('referral_code as rc')
                        ->join('referral_code_member as rcm','rcm.leader_referral_code_id','rc.id')
                        
                        ->where('rc.status',1)
                        ->where('rcm.status',1)
                        ->where('rc.user_id',$user_id)
                        ->select('rcm.member_user_id as follower_id')
                       ->get()
                       ->pluck('follower_id')
                       ->toArray();

             $results = DB::table('donation_streak as ds')
                        ->join('donation_streak_transaction as dst', 'dst.donation_streak_id', 'ds.id')
                        ->where('ds.status', 1)
                        ->where('dst.status', 1)
                        ->where('ds.prim_medal', '>', 0)
                        ->whereIn('ds.user_id', $follower)
                        ->groupBy('ds.id')
                        ->select('ds.id', 
                        DB::raw('COUNT(CASE WHEN dst.quality_donation = 1 THEN 1 END) as quality_donation_count'),
                        DB::raw('COUNT(CASE WHEN dst.quality_donation = 0 THEN 1 END) as normal_donation_count'))
                        ->get();

            if(isset($condition->follower_medal->quality)){
                $quality_condition =$condition->follower_medal->quality;
                $results = $results->filter(function($result) use ($quality_condition) {
                    return $result->quality_donation_count >= $quality_condition;
                });
            }
                        //dd($condition->medal->num);
            //dd($medal_count ,$condition);
            //dd( count($results),$results);
            if($condition->follower_medal->num >  count($results)){
                return false;
            }
           //dd($medal_count ,$condition);
         
           
        }

        return true;
    }

    public function getConditionText()
    {
        if (!$this->condition) {
            return 'No conditions specified.';
        }

        $conditionArray = json_decode($this->condition, true);
        $conditionText = [];

        $textArray = [
            "medal" => "Anda perlu mempunyai {num} PRiM Medal",
            "follower" => "Anda perlu mempunyai {num} Ahli level {level}",
            "follower_medal" => "Ahli anda perlu mempunyai total {num} PRiM Medal",
            "quality"       =>" dengan {quality} hari SedekahSubuh"
        ];

    

        //dd($conditionArray);
        foreach ($conditionArray as $key => $value) {

          if(isset($textArray[$key])){
                $text = $textArray[$key]; 
                foreach($value as $attr => $v){
                    $text =str_replace('{'.$attr.'}', $v, $text);
                    if($attr == "quality"){
                        $text = $text . str_replace('{'.$attr.'}', $v, $textArray[$attr]);
                    }
                }   
                $conditionText[] = $text;
            }else{
                $readableKey = str_replace('_', ' ', $key);
                $conditionText[] = "You need to have $value " . ucfirst($readableKey);
            }
            
        }

        return implode("\n", $conditionText);
    }
}
