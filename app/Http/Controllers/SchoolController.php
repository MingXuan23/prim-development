<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    public function index()
    {
        //
        // return view("pentadbir.school.index");
        $school = Organization::all()->toArray();
        return view('pentadbir.school.index', compact('school'));
    }

    public function indexLogin($name)//use to customise login page of each school
    {
        $title="";
        $placeholder="Email/Nombor Telefon/Nombor IC";
        $loginText="Log Masuk ke PRIM";
        switch($name)
        {
            case "lmm":
                $oid=137;
                $placeholder="Email/Nombor IC";
                $title="Lembaga Maktab Mahmud";
                $loginText="Laman Web Untuk Bayar Yuran LMM";
                break;
            case "polimas":
                $oid =107;
                $placeholder="Email/Nombor IC";
                $title="Polimas";
                $loginText="Laman Web Untuk Bayar Yuran Polimas";
                break;
            case "samura":
                $oid=141;
                $placeholder="Email/Nombor IC";
                $title="Sains Muar";
                $loginText="Laman Web Untuk Bayar Yuran SAMURA";
                break;
            case "srab":
                $oid=160;
                $placeholder="Email/Nombor IC";
                $title="SRAB";
                $loginText="Laman Web Untuk Bayar Yuran SRAB MUAR";
                break;
             default:
                return redirect('/login');

        }

        $org=DB::table('organizations')->where('id',$oid)->first()->organization_picture;
        //dd($org);
        return view('polimas.index',compact('org','placeholder','title','loginText'));
    }

    public function create()
    {
        //
        return view('pentadbir.school.add');
    }

    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name'         =>  'required',
            'code'         =>  'required',
            'telno'        =>  'required|numeric',
            'email'        =>  'required',
            'address'      =>  'required',
            'postcode'     =>  'required',
            'state'        =>  'required',
        ]);

        $school = new Organization([
            'nama'         =>  $request->get('name'),
            'code'         =>  $request->get('code'),
            'telno'        =>  $request->get('telno'),
            'email'        =>  $request->get('email'),
            'address'      =>  $request->get('address'),
            'postcode'     =>  $request->get('postcode'),
            'state'        =>  $request->get('state'),
        ]);

        $school->save();
        return redirect('/school')->with('success', 'New school has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
        $school = DB::table('organizations')->where('id', $id)->first();

        //$userinfo = User_info::find($id);
        //dd($userinfo);
        return view('pentadbir.school.update', compact('school'));
    }

    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'name'         =>  'required',
            'code'         =>  'required',
            'telno'        =>  'required|numeric',
            'email'        =>  'required',
            'address'      =>  'required',
            'postcode'     =>  'required',
            'state'        =>  'required',
        ]);

        $sekolahupdate    = DB::table('organizations')
            ->where('id', $id)
            ->update(
                [
                    'nama'      => $request->get('name'),
                    'code'      => $request->get('code'),
                    'email'     => $request->get('email'),
                    'telno'     => $request->get('telno'),
                    'address'   => $request->get('address'),
                    'state'     => $request->get('state'),
                    'postcode'  => $request->get('postcode')
                ]
            );

        return redirect('/school')->with('success', 'The data has been updated!');
    }

    public function destroy($id)
    {
        //
    }
}
