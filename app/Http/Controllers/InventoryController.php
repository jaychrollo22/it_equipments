<?php

namespace App\Http\Controllers;

use App\Inventory;
use Illuminate\Http\Request;
use DB;

class InventoryController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session([
            'title' => 'Inventories'
        ]);
        return view('pages.inventories');
    }

    public function indexData(){
        return Inventory::with('is_borrowed.employee_info')->orderBy('type','ASC')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            if($inventory = Inventory::create($data)){
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'inventory'=>$inventory,
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'type' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            $inventory = Inventory::where('id',$data['id'])->first();
            if($inventory){
                unset($data['id']);
                $inventory->update($data);
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'inventory'=>$inventory,
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        //
    }
}
