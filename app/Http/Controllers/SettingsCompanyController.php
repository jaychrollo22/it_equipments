<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SettingCompany;
use App\Company;
use DB;

class SettingsCompanyController extends Controller
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
        return view('pages.settings.companies');
    }

    public function indexData()
    {
        return Company::orderBy('name','ASC')->get();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            if($company = SettingCompany::create($data)){
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'company'=>$company,
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
            $company = SettingCompany::where('id',$data['id'])->first();
            if($company){
                unset($data['id']);
                $company->update($data);
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'company'=>$company,
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
