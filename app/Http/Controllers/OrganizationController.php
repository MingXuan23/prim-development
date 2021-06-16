<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use App\Models\TypeOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use View;

class OrganizationController extends Controller
{
    public function index()
    {
        return view('organization.index');
    }

    public function create()
    {
        // after launch remove where
        $type_org = TypeOrganization::where('id', 4)->orWhere('id', 5)->get();
        return view('organization.add', compact('type_org'));
    }

    public function store(OrganizationRequest $request)
    {
        $organization = Organization::create($request->validated());

        Organization::where('id', $organization->id)->update(['code' => $this->generateOrganizationCode($request->type_org, $organization->id)]);

        //attach foreign key to pivot table
        $organization->user()->attach(Auth::id(), ['role_id' => 2]);

        $user = Auth::user();
        $user->assignRole('Admin');

        if ($request->type_org == 1 || $request->type_org == 2 || $request->type_org == 3) {
            $organization->user()->attach(Auth::id(), ['start_date' => now(), 'status' => 1, 'role_id' => 4]);
            $user->assignRole('Pentadbir');
        }

        return redirect('/organization')->with('success', 'New organization has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        // after launch remove where
        $type_org = TypeOrganization::where('id', 4)->orWhere('id', 5)->get();
        $org = DB::table('organizations')->where('id', $id)->first();

        return view('organization.update', compact('org', 'type_org'));
    }

    public function update(OrganizationRequest $request, $id)
    {
        Organization::where('id', $id)->update($request->validated());

        return redirect('/organization')->with('success', 'Maklumat berjaya dikemaskini');
    }

    public function destroy($id)
    {
        $result = Organization::find($id)->delete();

        if ($result) {
            Session::flash('success', 'Organization Delete Successfully');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Organization Delete Failed');
            return View::make('layouts/flash-messages');
        }
    }

    public function getOrganizationDatatable()
    {
        $organizationList = $this->getOrganizationByUserId();

        if (request()->ajax()) {
            return datatables()->of($organizationList)
                ->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href="' . route('organization.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                })
                ->make(true);
        }
    }

    public static function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {
            return Organization::all();
        } else {
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->get();
        }
    }

    public function getAllOrganization()
    {
        return view('organization.index');
    }

    public function generateOrganizationCode($typeOrg, $id)
    {
        if ($typeOrg == 4) {
            $code = 'MS' . str_pad($id, 5, '0', STR_PAD_LEFT);
        } elseif ($typeOrg == 5) {
            $code = 'NGO' . str_pad($id, 5, '0', STR_PAD_LEFT);
        }

        return $code;
    }
}
