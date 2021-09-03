<?php

namespace App\Http\Controllers;

use App\Inventory;

use App\Imports\InventoriesImport;

use Illuminate\Http\Request;
use DB;
use Excel;

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

    public function uploadInventories(Request $request){

        $this->validate($request, [
            'upload_inventory_file' => 'required|mimes:xls,xlsx'
        ]);
        $data = $request->all();
        $upload_inventory_file = Excel::toArray(new InventoriesImport, $request->file('upload_inventory_file'));
        $save_count = 0;
        if($upload_inventory_file){
            // return count($upload_inventory_file[0]);
            foreach($upload_inventory_file[0] as $k => $item){
                if($item['serial_number']){
                    $check_inventory_file = Inventory::where('serial_number',$item['serial_number'])->where('old_inventory_number',$item['old_inventory_number'])->first();
                    $save_item = [
                        'type'=> $item['type'],
                        'old_inventory_number'=> $item['old_inventory_number'],
                        'new_it_tag_qr_code_bar_code'=> $item['new_it_tag_qr_codebar_code'],
                        'serial_number'=> $item['serial_number'],
                        'model'=> $item['model'],
                        'processor'=> $item['processor'],
                        'manufacturer'=> $item['manufacturer'],
                        'supplier'=> $item['supplier'],
                        'delivery_date'=> $item['delivery_date'],
                        'order_number'=> $item['order_number'],
                        'retired_date'=> $item['retired_date'],
                        'estimated_retirement_date'=> $item['estimated_retirement_date'],
                        'warranty_period'=> $item['warranty_period'],
                        'asset_code'=> $item['asset_code'],
                        'purchase_cost'=> $item['purchase_cost'],
                        'insurance_date'=> $item['insurance_date'],
                        'os_name_and_version'=> $item['os_name_and_version'],
                        'tab_name'=> $item['tab_name'],
                        'area'=> $item['area'],
                        'location'=> $item['location'],
                        'building'=> $item['building'],
                    ];

                    if(empty($check_inventory_file)){
                        //Add
                        Inventory::create($save_item);
                        $save_count++;
                    }else{
                        //Update
                        $check_inventory_file->update($save_item);
                    }
                }
            }
        }

        return $save_count;

    }

    public function transfer(){
        session([
            'title' => 'Transfer'
        ]);
        return view('pages.inventory_transfer');
    }
}
