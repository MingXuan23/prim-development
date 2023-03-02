<?php

namespace App\Http\Controllers\MobileAPI;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;

class YuranController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $username = $request->get('username');

        if(is_numeric($username)){
            
            if(!$this->startsWith((string)$username,"+60") && !$this->startsWith((string)$username,"60")){
                if(strlen((string)$username) == 10)
                {
                    $username = str_pad($username, 12, "+60", STR_PAD_LEFT);
                } 
                elseif(strlen((string)$username) == 11)
                {
                    $username = str_pad($username, 13, "+60", STR_PAD_LEFT);
                }   
            } else if($this->startsWith((string)$username,"60")){
                if(strlen((string)$username) == 11)
                {
                    $username = str_pad($username, 12, "+", STR_PAD_LEFT);
                } 
                elseif(strlen((string)$username) == 12)
                {
                    $username = str_pad($username, 13, "+", STR_PAD_LEFT);
                }   
            }
            
            $credentials = ['telno'=>$username, 'password' => $request->get('password')];

            if (Auth::attempt($credentials)) {
                $user = User::where('telno', $username)->first();

                if ($user->hasRole('Penjaga'))
                {
                    return response($user->id, 200);
                }
            }
        }
        else
        { 
            $credentials = ['email'=> $username, 'password' => $request->get('password')];

            if (Auth::attempt($credentials)) {
                $user = User::where('email', $username)->first();
                if ($user->hasRole('Penjaga'))
                {
                    dd($user->id);
                    return response($user->id, 200);
                }
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    private function startsWith($string, $startString) {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    public function getYuranByParentIdAndOrganId(Request $request)
    {
        $user_id = $request->user_id;
        $oid = $request->oid;

        $getfees_categoryA  = DB::table('fees_new')
            ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
            ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
            ->select('fees_new.*')
            ->orderBy('fees_new.name')
            ->where('fees_new.status', 1)
            ->where('organization_user.organization_id', $oid)
            ->where('organization_user.user_id', $user_id)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('fees_new_organization_user.status', 'Debt')
            ->get();

        foreach ($getfees_categoryA as $key => $value) {
            $value->student_id = null;
        }
        
        $getfees_categoryBC = DB::table('fees_new as fn')
            ->join('student_fees_new as sfn', 'sfn.fees_id', '=', 'fn.id')
            ->join('class_student as cs', 'sfn.class_student_id', '=', 'cs.id')
            ->join('students as s', 's.id', '=', 'cs.student_id')
            ->join('organization_user_student as ous', 'ous.student_id', '=', 's.id')
            ->join('organization_user as ou', 'ou.id', '=', 'ous.organization_user_id')
            ->select('fn.*', 'cs.student_id')
            ->orderBy('fn.category')
            ->orderBy('fn.name')
            ->where('ou.user_id', $user_id)
            ->where('ou.organization_id', $oid)
            ->where('fn.status', 1)
            ->where('sfn.status', 'Debt')
            ->get();

        $lists = $getfees_categoryA->merge($getfees_categoryBC);


        foreach ($lists as $key => $value) {

            if ($value->category != 'Kategory A')
            {
                $student = DB::table('students as s')
                    ->join('class_student as cs', 'cs.student_id', '=', 's.id')
                    ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
                    ->join('classes as c', 'c.id', '=', 'co.class_id')
                    ->select('s.nama as student_name', 'c.nama as class_name')
                    ->where('s.id', $value->student_id)    
                    ->first();
    
                $value->category = $value->category . ' - ' . $student->student_name . ' (' . $student->class_name . ')';
            }
        }

        return response($lists, 200);
    }

    public function getOrganizationByUserId(Request $request)
    {
        $organizations = DB::table('organization_user as ou')
            ->leftJoin('organizations as o', 'ou.organization_id', '=', 'o.id')
            ->select('o.*')
            ->distinct()
            ->where('ou.user_id', '=', $request->user_id)
            ->whereBetween('o.type_org', [1, 3])
            ->get();

        return response($organizations, 200);
    }

    public function getReceiptByOid(Request $request)
    {
        $listHisotry = DB::table('transactions as t')
                    ->join('fees_transactions_new as ftn', 'ftn.transactions_id', 't.id')
                    ->join('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                    ->join('class_student as cs', 'cs.id', 'sfn.class_student_id')
                    ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->where('t.user_id',$request->user_id)
                    ->where('t.description', "like", 'YS%')
                    ->where('t.status', 'success')
                    ->where('co.organization_id', $request->oid)
                    ->select('t.id as id', 't.nama as name', 't.description as desc', 't.amount as amount', 't.datetime_created as date')
                    ->distinct('name')
                    ->get();

        return response($listHisotry, 200);
    }

    public function getUserInfo(Request $request)
    {
        $user = User::find($request->user_id);

        return response($user, 200);
    }
}
