<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SettingType;
use DB;

class SettingsTypeController extends Controller
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
        return view('pages.settings.types');
    }

    public function indexData()
    {
        return SettingType::orderBy('name','ASC')->get();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            if($type = SettingType::create($data)){
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'type'=>$type,
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
            $type = SettingType::where('id',$data['id'])->first();
            if($type){
                unset($data['id']);
                $type->update($data);
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'type'=>$type,
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
