<?php

namespace App\Http\Controllers;

use App\Inventory;
use App\InventoryTransfer;
use App\InventoryTransferItem;
use App\UserInventory;

use App\Imports\InventoriesImport;

use Illuminate\Http\Request;
use DB;
use Excel;
use PDF;
use Auth;
use QRCode;

use setasign\Fpdi\Fpdi;

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
        return view('pages.inventories.inventories');
    }

    public function indexData(Request $request){
        $data = $request->all();
        if($data['type'] || $data['location'] || $data['category'] || $data['status']){
            return Inventory::with('is_borrowed.employee_info','is_borrowed.letter_of_undertaking','is_transfer')
                                ->when(!empty($data['type']),function($q) use($data){
                                    $q->where('type','LIKE','%'.$data['type'].'%');
                                })
                                ->when(!empty($data['location']),function($q) use($data){
                                    $q->where('location','LIKE','%'.$data['location'].'%');
                                })
                                ->when(!empty($data['category']),function($q) use($data){
                                    $q->where('category','LIKE','%'.$data['category'].'%');
                                })
                                ->when(!empty($data['status']),function($q) use($data){
                                    $q->where('status','LIKE','%'.$data['status'].'%');
                                })
                                ->orderBy('type','ASC')
                                ->get();
        }else{
            return Inventory::with('is_borrowed.employee_info','is_borrowed.letter_of_undertaking','is_transfer')
                                // ->where('status','!=','Disposed')
                                ->orderBy('type','ASC')
                                ->get();
        }
       
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
            'model' => 'required',
            'type' => 'required',
            'status' => 'required',
            'serial_number' => 'required|unique:inventories,serial_number,',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            if($data['status'] == 'Disposed'){
                $data['disposed_by'] = Auth::user()->id;
            }else{
                $data['disposal_date'] = null;
            }
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
            'serial_number' => 'required',
            'model' => 'required',
            'type' => 'required',
            'status' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            $inventory = Inventory::where('id',$data['id'])->first();
            if($inventory){
                unset($data['id']);
                if($data['status'] == 'Disposed'){
                    $data['disposed_by'] = Auth::user()->id;
                }else{
                    $data['disposal_date'] = null;
                }
                $inventory->update($data);
                DB::commit();

                $inventory = Inventory::with('is_borrowed.employee_info','is_borrowed.letter_of_undertaking','is_transfer')->where('id',$request->id)->first();
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
                    $check_inventory_file = Inventory::where('serial_number',$item['serial_number'])
                                                        ->where('old_inventory_number',$item['old_inventory_number'])
                                                        ->where('type',$item['type'])
                                                        ->first();
                    $save_item = [
                        'type'=> isset($item['type']) ? $item['type'] : "",
                        'old_inventory_number'=> isset($item['old_inventory_number']) ? $item['old_inventory_number'] : "",
                        'new_it_tag_qr_code_bar_code'=> isset($item['new_it_tag_qr_code_bar_code']) ? $item['new_it_tag_qr_code_bar_code'] : "",
                        'serial_number'=> isset($item['serial_number']) ? $item['serial_number'] : "",
                        'model'=> isset($item['model']) ? $item['model'] : "",
                        'processor'=> isset($item['processor']) ? $item['processor'] : "",
                        'manufacturer'=> isset($item['manufacturer']) ? $item['manufacturer'] : "",
                        'supplier'=> isset($item['supplier']) ? $item['supplier'] : "",
                        'purchase_company'=> isset($item['purchase_company']) ? $item['purchase_company'] : "",
                        'delivery_date'=> isset($item['delivery_date']) ? $item['delivery_date'] : null,
                        'order_number'=> isset($item['order_number']) ? $item['order_number'] : "",
                        'retired_date'=> isset($item['retired_date']) ? $item['retired_date'] : "",
                        'estimated_retirement_date'=> isset($item['estimated_retirement_date']) ? $item['estimated_retirement_date'] : "",
                        'warranty_period'=> isset($item['warranty_period']) ? $item['warranty_period'] : "",
                        'asset_code'=> isset($item['asset_code']) ? $item['asset_code'] : "",
                        'purchase_cost'=> isset($item['purchase_cost']) ? $item['purchase_cost'] : "",
                        'insurance_date'=> isset($item['insurance_date']) ? $item['insurance_date'] : null,
                        'os_name_and_version'=> isset($item['os_name_and_version']) ? $item['os_name_and_version'] : "",
                        'tab_name'=>  isset($item['tab_name']) ? $item['tab_name'] : "",
                        'area'=> isset($item['area']) ? $item['area'] : "",
                        'location'=> isset($item['location']) ? $item['location'] : "",
                        'building'=> isset($item['building']) ? $item['building'] : "",
                        'category'=> isset($item['category']) ? $item['category'] : "",
                        'status'=> isset($item['status']) ? $item['status'] : "",
                        'disposal_date'=> isset($item['disposal_date']) ? $item['disposal_date'] : null,
                        'remarks'=> isset($item['remarks']) ? $item['remarks'] : null,
                    ];

                    if(empty($check_inventory_file)){
                        //Add
                        Inventory::create($save_item);
                        $save_count++;
                    }else{
                        //Update
                        $check_inventory_file->update($save_item);
                        $save_count++;
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
        return view('pages.inventories.inventory_transfer');
    }

    public function transferData(Request $request){
        return InventoryTransfer::with('inventory_transfer_items.inventory_info','requested_by_info')
                                ->orderBy('created_at','DESC')
                                // ->take(50)
                                ->get();
    }

    public function saveTransfer(Request $request){

        $this->validate($request, [
            'requested_by' => 'required',
            'transfer_department' => 'required',
            'transfer_company' => 'required',
            'local_number' => 'required',
            'date_requested' => 'required',
            'date_of_transfer' => 'required',
            'transfer_inventories' => 'required',
        ]);

        $data = $request->all();

        DB::beginTransaction();
        try {
            if($data['transfer_inventories']){

                //Save Transfer Header
                $transfer_data = [
                    'requested_by' => $data['requested_by'],
                    'transfer_department' => $data['transfer_department'],
                    'transfer_company' => $data['transfer_company'],
                    'local_number' => $data['local_number'],
                    'date_requested' => $data['date_requested'],
                    'date_of_transfer' => $data['date_of_transfer'],
                    'transfer_location' => $data['transfer_location'],
                    'status' => 'Pending',
                ];

                if($save_transfer = InventoryTransfer::create($transfer_data)){
                    //Generate Transfer Code
                    $date = date('Ymd');
                    $transfer_code = $date .'-'. str_pad($save_transfer->id, 4, '0', STR_PAD_LEFT); ;
                    $saveTransferCode = ['transfer_code'=>$transfer_code];
                    $checkTransfer = InventoryTransfer::where('id',$save_transfer->id)->update($saveTransferCode);
                    //Save Transfer Inventories
                    $transfer_inventories = json_decode($data['transfer_inventories'], true);
                    $save_count = 0;
                    foreach($transfer_inventories as $item){
                        $newData = [
                            'inventory_transfer_id' => $save_transfer->id,
                            'inventory_id' => $item['id'],
                            'location_from' => $item['location'],
                            'location_to' => $data['transfer_location'],
                            'status' => 'Pending'
                        ];
                        InventoryTransferItem::create($newData);
                        $save_count++;
                    }
                    if($save_count > 0){
                        DB::commit();
                        return $data = [
                            'status'=>'success',
                            'save_count'=>$save_count,
                        ];
                    }else{
                        return $data = [
                            'status'=>'error'
                        ];     
                    }
                }
            }else{
                return $data = [
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

    public function updateTransfer(Request $request){

        $this->validate($request, [
            'requested_by' => 'required',
            'transfer_department' => 'required',
            'transfer_company' => 'required',
            'local_number' => 'required',
            'date_requested' => 'required',
            'date_of_transfer' => 'required',
            'transfer_inventories' => 'required',
        ]);

        $data = $request->all();

        DB::beginTransaction();
        try {
            if($data['transfer_inventories']){

                $check_transfer = InventoryTransfer::where('id',$data['id'])->first();
                if($check_transfer){
                    //Save Transfer Header
                    $transfer_data = [
                        'requested_by' => $data['requested_by'],
                        'transfer_department' => $data['transfer_department'],
                        'transfer_company' => $data['transfer_company'],
                        'local_number' => $data['local_number'],
                        'date_requested' => $data['date_requested'],
                        'date_of_transfer' => $data['date_of_transfer'],
                        'transfer_location' => $data['transfer_location'],
                        'remarks' => $data['remarks'],
                        'effective_date' => $data['effective_date'],
                    ];

                    if($save_transfer = $check_transfer->update($transfer_data)){
                        //Save Transfer Inventories
                        $transfer_inventories = json_decode($data['transfer_inventories'], true);
                        $save_count = 0;
                        foreach($transfer_inventories as $item){
                            
                            $newData = [
                                'inventory_transfer_id' => $check_transfer->id,
                                'inventory_id' => $item['id'],
                                'location_from' => $item['location'],
                                'location_to' => $data['transfer_location']
                            ];

                            $validate_if_exist = InventoryTransferItem::where('inventory_transfer_id',$check_transfer['id'])
                                                                        ->where('inventory_id',$item['id'])
                                                                        ->first();
                            if(empty($validate_if_exist)){
                                $newData['status'] = 'Pending';
                                InventoryTransferItem::create($newData);
                                $save_count++;
                            }else{
                                $validate_if_exist->update($newData);
                                $save_count++;
                            }
                        }
                        
                        DB::commit();
                        return $data = [
                            'status'=>'success',
                            'save_count'=>$save_count,
                        ];
                       
                    }
                }
                
            }else{
                return $data = [
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

    public function removeTransferItem(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            if(InventoryTransferItem::where('inventory_id',$data['inventory_id'])->where('inventory_transfer_id',$data['inventory_transfer_id'])->delete()){
                DB::commit();
                return $data = [
                    'status'=>'success'
                ];
            }else{
                return $data = [
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

    public function printTransfer(Request $request){
        $data = $request->all();
        $transfer_data = InventoryTransfer::with('inventory_transfer_items.inventory_info','requested_by_info','received_by_info')->where('transfer_code',$data['transfer_code'])->first();
        $pdf = PDF::loadView('pages.inventories.print_transfer',array(
            'transfer_data' => $transfer_data,
        ))->setPaper('a4','portrait');

        return $pdf->stream($transfer_data['transfer_code'] . '_' . date('Ymd') . '.pdf');
    }

    public function searchTransferCode(Request $request){
        $data = $request->all();
        $inventory_transfer = InventoryTransfer::with('inventory_transfer_items.inventory_info')->where('transfer_code',$data['transfer_code'])->first();
        return $inventory_transfer;
    }

    public function receive(){
        session([
            'title' => 'Receive'
        ]);
        return view('pages.inventories.inventory_receive');
    }

    public function receiveData(Request $request){
        $inventory_transfer = InventoryTransfer::with('inventory_transfer_items.inventory_info','requested_by_info')
                                                ->whereHas('inventory_transfer_items',function ($q){
                                                    $q->where('status', 'Received');
                                                })
                                                ->orderBy('updated_at','DESC')
                                                ->get();
        return $inventory_transfer;
    }

    public function saveReceiveItem(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            $inventory_transfer_item = InventoryTransferItem::where('id',$data['inventory_transfer_item_id'])->first();
            if($inventory_transfer_item){
                //Update Transfer to Received
                $updateInventoryTransferItem = [
                    'receive_date'=> date('Y-m-d h:i:s'),
                    'received_by'=> Auth::user()->id,
                    'status'=>'Received'
                ];
                $inventory_transfer_item->update($updateInventoryTransferItem);

                //Update Location
                $inventory_transfer = InventoryTransfer::where('id',$inventory_transfer_item['inventory_transfer_id'])->first();
                $inventoryTransferData = [
                    'received_by'=> Auth::user()->id
                ];
                $inventory_transfer->update($inventoryTransferData);

                //Update Location
                $inventory = Inventory::where('id',$inventory_transfer_item['inventory_id'])->first();
                $inventoryData = [
                    'location'=> $inventory_transfer_item['location_to']
                ];
                $inventory->update($inventoryData);

                DB::commit();
                $item = InventoryTransferItem::with('inventory_info')->where('id',$data['inventory_transfer_item_id'])->first();
                return $data = [
                    'status'=>'success',
                    'item'=>$item
                ];
            }else{
                return $data = [
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

    public function assignEmployeeItem(Request $request){
        $this->validate($request, [
            'employee_id' => 'required',
            'inventory_id' => 'required',
            'borrow_date' => 'required',
        ]);

        $data = $request->all();
        DB::beginTransaction();
        try {
            $user_inventory = UserInventory::where('employee_id',$data['employee_id'])
                                                    ->where('inventory_id',$data['inventory_id'])
                                                    ->where('status','Borrowed')
                                                    ->first();
            if(empty($user_inventory)){
                $newData = [
                    'employee_id' => $data['employee_id'],
                    'inventory_id' => $data['inventory_id'],
                    'borrow_date' => $data['borrow_date'],
                    'is_assigned' => $data['is_assigned'],
                    'status' => 'Borrowed',
                    // 'ticket_number' => $data['ticket_number'],
                ];
                UserInventory::create($newData);
                DB::commit();

                $inventory = Inventory::with('is_borrowed.employee_info','is_transfer')->where('id',$request->inventory_id)->first();

                return $response = [
                    'status'=>'saved',
                    'inventory' => $inventory
                ];
            }
        }catch (Exception $e) {
            DB::rollBack();
            return $response = [
                'status'=>'error'
            ];
        }

    }

    public function generateQrCode(Request $request){
        if($request->id){
            QRCode::text($request->id)->setSize(25)->setMargin(2)->setOutfile(base_path().'/public/inventory_qr/'. $request->id.'.png')->png();


            $pdf = new Fpdi();
            $pdf->AddPage('L',[30,20]);

            $pdf->SetMargins(0, 0, 0, 0);
            $pdf->SetAutoPageBreak(0, 0);
            $pdf->Image(base_path().'/public/inventory_qr/'. $request->id.'.png', 7, 0, 17, 17,'PNG');

            $pdf->SetFont('Arial','B','8');
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY(0, 16);
            $pdf->Cell(30,4,$request->id,0,1,'C');


            $pdf->Output('I', $request->id . '.pdf');
            exit();

            // return redirect('inventory_qr/'. $request->id.'.png');
        }
    }

    public function updateUserInventoryIsAssigned(Request $request){
        $user_inventory = UserInventory::where('id',$request->id)->first();

        if($user_inventory){
            $user_inventory->update(['is_assigned'=>$request->is_assigned]);
            $inventory = Inventory::with('is_borrowed.employee_info','is_transfer')->where('id',$user_inventory->inventory_id)->first();
            return $response = [
                'status'=>'saved',
                'inventory' => $inventory,
            ];
        }
    }
}
