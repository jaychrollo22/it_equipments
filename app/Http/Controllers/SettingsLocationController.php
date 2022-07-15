<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SettingLocation;
use App\Inventory;
use DB;

class SettingsLocationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('pages.settings.locations');
    }

    public function indexData()
    {
        return SettingLocation::orderBy('name','ASC')->get();
    }
    public function locationOptions()
    {
        return $locations = Inventory::select('location')
                                        ->where(function($q){
                                            $q->whereNotNull('location');
                                            $q->orWhere('location','!=','');
                                        })
                                        ->groupBy('location')
                                        ->orderBy('location','ASC')
                                        ->get();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            if($location = SettingLocation::create($data)){
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'location'=>$location,
                ];
            }else{
                return $status_data = [
                    'status'=>'error'
                ];
            }
        }
        catch (Exception $e) {
            DB::rollBack();
            return 'error';
        } 
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            $location = SettingLocation::where('id',$data['id'])->first();
            if($location){
                unset($data['id']);
                $location->update($data);
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'location'=>$location,
                ];
            }else{
                return $data = [
                    'status'=>'error'
                ];
            }
        }
        catch (Exception $e) {
            DB::rollBack();
            return 'error';
        } 
    }
}
