<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SettingCategory;
use DB;
class SettingsCategoryController extends Controller
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
        session([
            'title' => 'Categories'
        ]);

        return view('pages.settings.categories');
    }

    public function indexData()
    {
        return SettingCategory::orderBy('name','ASC')->get();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            if($category = SettingCategory::create($data)){
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'category'=>$category,
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
            $category = SettingCategory::where('id',$data['id'])->first();
            if($category){
                unset($data['id']);
                $category->update($data);
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'category'=>$category,
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
