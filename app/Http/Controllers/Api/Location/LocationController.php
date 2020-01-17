<?php

namespace App\Http\Controllers\Api\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Location;
use DB;
use App\GlobalSetting;

class LocationController extends Controller
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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



    // Custom functions

    public function getLocations(Request $request)
    {
        $this->authenticate($request);
        $location=Location::select('*')
        ->where('is_archive',0)
        ->get();
        
        if($request->has('latitude'))
        {
            $my_lat=$request->input('latitude');
        }
        else
        {
            $failure_arr = array(
                'status' => 0,
                'message' => 'Parameter missing - latitude filed should not be empty.'
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }
        if($request->has('longitude'))
        {
            $my_long=$request->input('longitude');
        }
        else
        {
            $failure_arr = array(
                'status' => 0,
                'message' => 'Parameter missing - longitude field should not be empty.'
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }
        $meters = GlobalSetting::select('value')
        ->where('setting_name','area_covered')
        ->first();
        // return $meters['area_covered'];
        $conn = new Db;
       
        $query=DB::select('
   
        SELECT location.*, ( 6371000 * acos( cos( radians('.$my_lat.') ) * cos( radians( latitude ) ) * 
        cos( radians( longitude ) - radians('.$my_long.') ) + sin( radians('.$my_lat.') ) * 
        sin( radians( latitude ) ) ) ) AS distance FROM location where is_archive = 0 and ( 6371000 * acos( cos( radians('.$my_lat.') ) * cos( radians( latitude ) ) * 
        cos( radians( longitude ) - radians('.$my_long.') ) + sin( radians('.$my_lat.') ) * 
        sin( radians( latitude ) ) ) ) <= '.$meters['value'].'
         order by distance limit 1');
        // return $query;
        if($query == false)
        {
            $failure_arr = array(
                'status' => 0,
                'message' => 'We are not operating in this area.'
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        }
        else
        {
            $success_arr = array(
                'status' => 1,
                'message' => 'Successfully retrieved nearby locations.',
                'nearby_locations' => $query[0]
            );
            return json_encode($success_arr, JSON_NUMERIC_CHECK);
        }

    


    }
}
