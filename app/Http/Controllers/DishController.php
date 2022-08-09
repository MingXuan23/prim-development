<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DishController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dish  $dish
     * @return \Illuminate\Http\Response
     */
    public function show(Dish $dish)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dish  $dish
     * @return \Illuminate\Http\Response
     */
    public function edit(Dish $dish)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dish  $dish
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dish $dish)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dish  $dish
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dish $dish)
    {
        //
    }

    
     // to get all organization with type_org = 8
     public function getOrganizationWithDish()
     {
        return DB::table('organizations')
            ->where('type_org', 8)
            ->get();
     }

    //to get all dishes
    public function getAllDishes()
    {
        $dishes = DB::table('dishes')
            ->get();

        foreach ($dishes as $dish) {
            $dish_available = DB::table('dish_available')
            ->where('dish_id', $dish->id)
            ->get();

            $dish->dish_available = $dish_available;
        }

        return $dishes;
    }

    //to get date available based on dish id
    public function getAllAvailableDates()
    {
        $AllAvailableDates =  DB::table('dish_available')
            ->where('date', '>=', DB::raw('curdate()'))
            ->get();

        foreach ($AllAvailableDates as $AllAvailableDate) {
            $dish = DB::table('dishes')
                ->where('id', $AllAvailableDate->dish_id)
                ->first();

            $AllAvailableDate->dish = $dish;
        }
            
        return $AllAvailableDates;
    }

    //to get all data in dish_available table
    public function getAllDishAvailable()
    {
        return DB::table('dish_available')
            ->select()
            ->get();
    }
}
