<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SettingDepartment;
use App\Department;
use DB;

class SettingsDepartmentController extends Controller
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
        return view('pages.settings.departments');
    }

    public function indexData()
    {
        return Department::orderBy('name','ASC')->get();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            if($department = SettingDepartment::create($data)){
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'department'=>$department,
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
            $department = SettingDepartment::where('id',$data['id'])->first();
            if($department){
                unset($data['id']);
                $department->update($data);
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'department'=>$department,
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
