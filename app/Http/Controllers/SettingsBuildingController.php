<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SettingBuilding;
use DB;

class SettingsBuildingController extends Controller
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
        return view('pages.settings.buildings');
    }

    public function indexData()
    {
        return SettingBuilding::orderBy('name','ASC')->get();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            if($building = SettingBuilding::create($data)){
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'building'=>$building,
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
            $building = SettingBuilding::where('id',$data['id'])->first();
            if($building){
                unset($data['id']);
                $building->update($data);
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'building'=>$building,
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
