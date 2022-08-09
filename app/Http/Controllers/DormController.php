<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dorm;
use App\Models\Outing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeacherExport;
use App\Imports\TeacherImport;
use App\Models\Organization;
use App\Models\OrganizationRole;
use App\User;
use Illuminate\Validation\Rule;
use App\Models\TypeOrganization;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class DormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        
    }

    public function indexOuting()
    {
        // 
        $organization = $this->getOrganizationByUserId();

        return view('dorm.outing.index', compact('organization'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        
    }

    public function createOuting()
    {
        //
        $organization = $this->getOrganizationByUserId();
        return view('dorm.outing.add', compact('organization'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 
        
    }

    public function storeOuting(Request $request)
    {
        // 
        $this->validate($request, [
            'start_date'    =>  'required',
            'end_date'      =>  'required'
        ]);

        DB::table('outings')->insert([
            'start_date_time' => $request->get('start_date'),
            'end_date_time'   => $request->get('end_date')
        ]);

        return redirect('/dorm/dorm/indexOuting')->with('success', 'New outing date and time has been added successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function addOutingTime()
    {
        //
    }

    public function updateOutingTime($id)
    {
        $outing = Outing::findOrFail($id);
        $name = $request->input('stud_name');
        DB::update('update student set name = ? where id = ?',[$name,$id]);
        echo "Record updated successfully.<br/>";
        echo '<a href = "/edit-records">Click Here</a> to go back.';

<<<<<<< HEAD
        $outing->update(array('start_date_time' => new DateTime()));
        return redirect('/asrama')->with('success', 'Data is successfully updated');
=======
        // return redirect('/asrama')->with('success', 'Data is successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        // DB::table('asramas')
        //     ->where('id', $id)
        //     ->delete();
        // return redirect('/asrama')->with('success', 'Application Data is successfully deleted');
>>>>>>> dcd430e3153f1eff1e14d496929b2172b132e7e2
    }

    public function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {

            return Organization::all();
        } else {
            // user role pentadbir 
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->Where(function ($query) {
                    $query->where('organization_user.role_id', '=', 4)
                        ->Orwhere('organization_user.role_id', '=', 5);
                });
            })->get();
        }
    }

    public function updateOutTime($id)
    {
        // $asrama = Asrama::findOrFail($id);
        // $asrama->update(array('outing_time' => new DateTime()));
        // return redirect('/asrama')->with('success', 'Data is successfully updated');
    }

    public function updateInTime($id)
    {
        // $asrama = Asrama::findOrFail($id);
        // $asrama->update(array('in_time' => new DateTime()));
        // return redirect('/asrama')->with('success', 'Data is successfully updated');
    }

    public function updateOutArriveTime($id)
    {
        // $asrama = Asrama::findOrFail($id);
        // $asrama->update(array('out_arrive_time' => new DateTime()));
        // return redirect('/asrama')->with('success', 'Data is successfully updated');
    }

    public function updateInArriveTime($id)
    {
        // $asrama = Asrama::findOrFail($id);
        // $asrama->update(array('in_arrive_time' => new DateTime()));
        // return redirect('/asrama')->with('success', 'Data is successfully updated');
    }

    public function addOutingTime()
    {
        $users = DB::table('students')
            ->where('id', 1)
            ->first();

        // if (is_null($users)) {
        // } else {
        return view('dorm.create');
        // }
    }

    public function updateOutingTime($id)
    {
        $outing = Outing::findOrFail($id);
        $name = $request->input('stud_name');
        DB::update('update student set name = ? where id = ?',[$name,$id]);
        echo "Record updated successfully.<br/>";
        echo '<a href = "/edit-records">Click Here</a> to go back.';

        $outing->update(array('start_date_time' => new DateTime()));
        return redirect('/asrama')->with('success', 'Data is successfully updated');
    }
}
