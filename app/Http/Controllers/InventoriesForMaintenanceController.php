<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Inventory;
use App\InventoriesForMaintenance;

use Storage;
use Auth;
use DB;

class InventoriesForMaintenanceController extends Controller


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
            'title' => 'For Maintenance'
        ]);
        return view('pages.for_maintenance.for_maintenance');
    }

    public function indexData(){
        return InventoriesForMaintenance::with('inventory','prepared_by_info')
                                        ->orderBy('created_at','DESC')
                                        ->get();
    }

    public function setSchedule(Request $request){
        $request->all();

        DB::beginTransaction();
        try {
            $for_maintence = InventoriesForMaintenance::where('id',$request->id)->first();
            if($for_maintence){
                $for_maintence->update([
                    'maintenance_date'=>$request->maintenance_date
                ]);
                DB::commit();
                $for_maintenance_updated = InventoriesForMaintenance::with('inventory','prepared_by_info')
                                                            ->where('id',$request->id)
                                                            ->first();
                return $response = [
                    'status'=>'success',
                    'for_maintenance'=> $for_maintenance_updated
                ];
            }else{
                return $response = [
                    'status'=>'error'
                ];
            }
        }catch (Exception $e) {
            DB::rollBack();
            return $data = [
                'status'=>'error'
            ];
        }
    }
    public function changeStatus(Request $request){
        $request->all();

        DB::beginTransaction();
        try {
            $for_maintence = InventoriesForMaintenance::where('id',$request->id)->first();
            if($for_maintence){
                $for_maintence->update([
                    'status'=>$request->status
                ]);
                Inventory::where('id',$for_maintence->inventory_id)->update([
                    'status'=>'Active'
                ]);
                DB::commit();
                $for_maintenance_updated = InventoriesForMaintenance::with('inventory','prepared_by_info')
                                                            ->where('id',$request->id)
                                                            ->first();
                return $response = [
                    'status'=>'success',
                    'for_maintenance'=> $for_maintenance_updated
                ];
            }else{
                return $response = [
                    'status'=>'error'
                ];
            }
        }catch (Exception $e) {
            DB::rollBack();
            return $data = [
                'status'=>'error'
            ];
        }
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
        $data = $request->all();

        DB::beginTransaction();
        try {
            $data_items = json_decode($data['items']);

            if(count($data_items) > 0){

                $data_for_disposal_header = [
                    'prepared_by'=>Auth::user()->id,
                ];

                if($save = InventoriesForMaintenance::create($data_for_disposal_header)){
                    DB::commit();
                    $success = 0;
                    $is_exists = 0;
                    foreach($data_items as $item){

                        $inventory = Inventory::where('id',$item)->first();
                        if($inventory->status != 'For Maintenance'){

                            $validate = InventoriesForMaintenance::where('inventory_id',$item)
                                                                    ->where('status','For Maintenance')
                                                                    ->first();
                            if(empty($validate)){
                                $data_for_disposal_items = [
                                    'inventory_id' => $item,
                                    'status' =>  'For Maintenance',
                                    'prepared_by' => Auth::user()->id
                                ];
                                InventoriesForMaintenance::create($data_for_disposal_items);
                                $success++;
                            }else{
                                $is_exists++;
                            }
                            $inventory->update(['status'=>'For Maintenance']);
                        }
                    }

                    DB::commit();

                    return $response = [
                        'status'=>'saved',
                        'success'=>$success,
                        'is_exists'=>$is_exists,

                    ];
                }
            }

        }catch (Exception $e) {
            DB::rollBack();
            return $data = [
                'status'=>'error'
            ];
        }
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
}
