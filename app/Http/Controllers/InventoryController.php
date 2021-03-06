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

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;


use App\Helpers\RotateTextHelper;
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
            return Inventory::with('is_borrowed.employee_info','is_borrowed.letter_of_undertaking','is_transfer','for_maintenance_logs.prepared_by_info')
                                ->when(!empty($data['type']),function($q) use($data){
                                    $q->where('type', '=', $data['type']);
                                })
                                ->when(!empty($data['location']),function($q) use($data){
                                    $q->where('location', '=', $data['location']);
                                })
                                ->when(!empty($data['category']),function($q) use($data){
                                    $q->where('category','=',$data['category']);
                                })
                                ->when(!empty($data['status']) && $data['status'] != 'No Status',function($q) use($data){
                                    $q->where('status','=',$data['status']);
                                })
                                ->when($data['status'] == 'No Status',function($q) use($data){
                                    $q->whereNull('status');
                                    $q->OrWhere('status','=','');
                                })
                                ->orderBy('type','ASC')
                                ->get();
        }else{
            return Inventory::with('is_borrowed.employee_info','is_borrowed.letter_of_undertaking','is_transfer','for_maintenance_logs.prepared_by_info')
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
                $inventory = Inventory::with('is_borrowed.employee_info','is_borrowed.letter_of_undertaking','is_transfer','for_maintenance_logs.prepared_by_info')->where('id',$inventory->id)->first();
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
                $inventory->update($data);
                DB::commit();
                $inventory = Inventory::with('is_borrowed.employee_info','is_borrowed.letter_of_undertaking','is_transfer','for_maintenance_logs.prepared_by_info')->where('id',$request->id)->first();
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
                                                        // ->where('old_inventory_number',$item['old_inventory_number'])
                                                        // ->where('type',$item['type'])
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
                        'company'=> isset($item['company']) ? $item['company'] : "",
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

    public function uploadCompanyInventories(Request $request){

        $this->validate($request, [
            'upload_inventory_file' => 'required|mimes:xls,xlsx'
        ]);

        $data = $request->all();
        $upload_inventory_file = Excel::toArray(new InventoriesImport, $request->file('upload_inventory_file'));
        $save_count = 0;
        if($upload_inventory_file){
            foreach($upload_inventory_file[0] as $k => $item){
                if($item['serial_number']){
                    if($item['serial_number'] != 'N/R' || $item['serial_number'] != 'N/A'){

                        $check_inventory_file = Inventory::where('serial_number',$item['serial_number'])->first();

                        $save_item = [
                            'company'=> isset($item['company']) ? strtoupper($item['company']) : "",
                        ];

                        if($check_inventory_file){
                            //Update
                            $check_inventory_file->update($save_item);
                            $save_count++;
                        }
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
            // 'requested_by' => 'required',
            'transfer_department' => 'required',
            'transfer_company' => 'required',
            'transfer_location' => 'required',
            'local_number' => 'required',
            'date_requested' => 'required',
            'date_of_transfer' => 'required',
            'transfer_inventories' => 'required',
            'approved_by_it_head' => 'required',
            'approved_by_finance' => 'required',
        ],
        [
            'approved_by_it_head.required'=> 'IT Head Approver is Required',
            'approved_by_finance.required'=> 'Finance Approver is Required',
        ]);

        $data = $request->all();

        DB::beginTransaction();
        try {
            if($data['transfer_inventories']){

                //Save Transfer Header
                $transfer_data = [
                    'requested_by' => Auth::user()->id,
                    'transfer_department' => $data['transfer_department'],
                    'transfer_company' => $data['transfer_company'],
                    'local_number' => $data['local_number'],
                    'date_requested' => $data['date_requested'],
                    'date_of_transfer' => $data['date_of_transfer'],
                    'transfer_location' => $data['transfer_location'],
                    'approved_by_it_head' => $data['approved_by_it_head'],
                    'approved_by_it_head_status' => 'Pending',
                    'approved_by_finance' => $data['approved_by_finance'],
                    'approved_by_finance_status' => 'Pending',
                    'remarks' => $data['remarks'],
                    'status' => 'For Approval',
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
                            'company_from' => $item['company'],
                            'company_to' => $data['transfer_company'],
                            'status' => 'Pending'
                        ];
                        InventoryTransferItem::create($newData);
                        $save_count++;
                    }
                    if($save_count > 0){
                        DB::commit();
                        $inventory_transfer = InventoryTransfer::with('inventory_transfer_items.inventory_info','requested_by_info','approved_by_it_head_info')
                                                                ->where('id',$save_transfer->id)
                                                                ->first();

                        //Send Notification
                        $message = "Hi ".$inventory_transfer->approved_by_it_head_info->name.", sending this request for approval. Thank you.";
                        $link = "https://10.96.4.168:8676/transfer-approval?transfer_code=".$inventory_transfer->transfer_code;        
                        $send = $this->sendWebexMessageTransfer($inventory_transfer->approved_by_it_head_info->email,'For Transfer',$message,$inventory_transfer,$link);     

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
            // 'requested_by' => 'required',
            'transfer_department' => 'required',
            'transfer_company' => 'required',
            'transfer_location' => 'required',
            'local_number' => 'required',
            'date_requested' => 'required',
            'date_of_transfer' => 'required',
            'transfer_inventories' => 'required',
            'approved_by_it_head' => 'required',
            'approved_by_finance' => 'required',
        ],
        [
            'approved_by_it_head.required'=> 'IT Head Approver is Required',
            'approved_by_finance.required'=> 'Finance Approver is Required',
        ]);

        $data = $request->all();

        DB::beginTransaction();
        try {
            if($data['transfer_inventories']){

                $check_transfer = InventoryTransfer::where('id',$data['id'])->first();
                if($check_transfer){
                    //Save Transfer Header
                    $transfer_data = [
                        // 'requested_by' => $data['requested_by'],
                        'transfer_department' => $data['transfer_department'],
                        'transfer_company' => $data['transfer_company'],
                        'local_number' => $data['local_number'],
                        'date_requested' => $data['date_requested'],
                        'date_of_transfer' => $data['date_of_transfer'],
                        'transfer_location' => $data['transfer_location'],
                        'remarks' => $data['remarks'],
                        'effective_date' => $data['effective_date'],
                        'approved_by_it_head' => $data['approved_by_it_head'],
                        'approved_by_it_head_status' => 'Pending',
                        'approved_by_finance' => $data['approved_by_finance'],
                        'approved_by_finance_status' => 'Pending',
                        'status' => 'For Approval',
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

    public function transferApproval(){
        session([
            'title' => 'Transfer Approval'
        ]);
        return view('pages.inventories.transfer_approval');
    }

    public function transferApprovalData(Request $request){
        return $inventory_transfer = InventoryTransfer::with('inventory_transfer_items.inventory_info',
                                                                'requested_by_info',
                                                                'received_by_info',
                                                                'approved_by_it_head_info',
                                                                'approved_by_finance_info'
                                                        )
                                                        ->where('transfer_code',$request->transfer_code)
                                                        ->first();
    }

    public function approveRequestForTransfer(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            $inventory_transfer = InventoryTransfer::where('id',$data['id'])->first();
            if($inventory_transfer){
                if($data['approval_type'] == 'IT'){
                    $data['approved_by_it_head_status'] = 'Approved';
                    $data['approved_by_it_head_date'] = date('Y-m-d');
                    $data['approved_by_it_head_remarks'] = $request->approved_by_it_head_remarks;
                    $data['status'] = 'Pre-approved';
                }
                if($data['approval_type'] == 'Finance'){
                    $data['approved_by_finance_status'] = 'Approved';
                    $data['approved_by_finance_date'] = date('Y-m-d');
                    $data['approved_by_finance_remarks'] = $request->approved_by_finance_remarks;
                    $data['status'] = 'Approved';
                }
                unset($data['approval_type']);
                if($inventory_transfer->update($data)){
                    DB::commit();
                    $inventory_transfer = InventoryTransfer::with('inventory_transfer_items.inventory_info','requested_by_info','approved_by_it_head_info','approved_by_finance_info')
                                                                ->where('id',$inventory_transfer->id)
                                                                ->first();
                    //Send Notification
                    if($request->approval_type == 'IT'){
                        $message = "Hi ".$inventory_transfer->approved_by_finance_info->name.", sending this request for approval. Thank you.";
                        $link = "https://10.96.4.168:8676/transfer-approval?transfer_code=".$inventory_transfer->transfer_code;        
                        $send = $this->sendWebexMessageTransfer($inventory_transfer->approved_by_finance_info->email,'For Transfer',$message,$inventory_transfer,$link);     
                    }

                    return $response = [
                        'status'=>'saved'
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
    public function disapproveRequestForTransfer(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            $inventory_transfer = InventoryTransfer::where('id',$data['id'])->first();
            if($inventory_transfer){
                if($data['approval_type'] == 'IT'){
                    $data['approved_by_it_head_status'] = 'Disapproved';
                    $data['approved_by_it_head_date'] = date('Y-m-d');
                    $data['approved_by_it_head_remarks'] = $request->approved_by_it_head_remarks;
                    $data['status'] = 'Disapproved';
                }
                if($data['approval_type'] == 'Finance'){
                    $data['approved_by_finance_status'] = 'Disapproved';
                    $data['approved_by_finance_date'] = date('Y-m-d');
                    $data['approved_by_finance_remarks'] = $request->approved_by_finance_remarks;
                    $data['status'] = 'Disapproved';
                }
                unset($data['approval_type']);
                if($inventory_transfer->update($data)){
                    DB::commit();
                    return $response = [
                        'status'=>'saved'
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
        $inventory_transfer = InventoryTransfer::with('inventory_transfer_items.inventory_info')
                                                ->where('transfer_code',$data['transfer_code'])
                                                ->where('status','Approved')
                                                ->first();
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
                    'location'=> $inventory_transfer_item['location_to'],
                    'company'=> $inventory_transfer_item['company_to']
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

        $inventory = Inventory::select('id','company')->where('id',$request->id)->first();
        if($inventory->id){
            QRCode::text($request->id)->setSize(25)->setMargin(2)->setOutfile(base_path().'/public/inventory_qr/'. $request->id.'.png')->png();

            $pdf = new RotateTextHelper();
            $pdf->AddPage('L',[30,20]);

            if($inventory->company == 'GLOBAL' || $inventory->company == 'GENTLE EARTH'){
                $pdf->SetFont('Arial','B','4');
            }else{
                $pdf->SetFont('Arial','B','5');
            }
            
            
            if($inventory->company){
                $pdf->TextWithDirection(6.5,17.5,$inventory->company . ' PROPERTY' ,'U');
            }
           
            $pdf->SetMargins(0, 0, 0, 0);
            $pdf->SetAutoPageBreak(0, 0);
            $pdf->Image(base_path().'/public/inventory_qr/'. $request->id.'.png', 7, 1.5, 17, 17,'PNG');

            $pdf->SetFont('Arial','B','8');
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY(0, 16);

            $pdf->TextWithDirection(26,13,$request->id,'U');
         

            $pdf->Output('I', $request->id . '.pdf');
            exit();

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



    //
    public function sendWebexMessageTransfer($email,$title,$message,$details=array(),$link){

        $httpClient = new Client(); 

        if($email && $message){

            $cards = [
                array(
                    "contentType" => "application/vnd.microsoft.card.adaptive",
                    "content" => array(
                        "type" => "AdaptiveCard",
                        "body" => [
                            array(
                            "type" => "ColumnSet",
                            "columns" => [
                                    array( 
                                    "type" => "Column",
                                    "items" => [
                                        array(
                                            "type" => "TextBlock",
                                            "text" =>  $message,
                                            "wrap" => true,
                                            "spacing" => "Small",
                                            "color" => "Light"
                                        ),
                                        array(
                                            "type" => "TextBlock",
                                            "weight" => "Bolder",
                                            "text" => $title,
                                            "wrap" => true,
                                            "color" => "Attention",
                                            "size" => "Large",
                                            "spacing" => "Small"
                                        )
                                    ],
                                    "width" => "stretch"
                                    )
                                ]
                            ),
                            array( 
                            "type" => "ColumnSet",
                            "columns" => [
                                array(
                                "type" => "Column",
                                "width" => 50,
                                "items" => [
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Transfer Code : " . $details->transfer_code,
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Requested By : " . $details->requested_by_info->name,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Total Items : " . count($details->inventory_transfer_items),
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Date of Transfer : " . $details->date_of_transfer,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Transfer Location : " . $details->transfer_location ,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Remarks : " . $details->remarks,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                ]
                                ),
                            ],
                            "spacing" => "Padding",
                            "horizontalAlignment" => "Center"
                            )
                        ],
                        "schema" => "http://adaptivecards.io/schemas/adaptive-card.json",
                        "version" => "1.2",
                        "actions" => [
                            array(
                                "type" => "Action.OpenUrl",
                                "title" => $title == 'Approved' ? "For Acceptance" : "More info.",
                                "url" => $link,
                            )
                            
                        ]
                    )
                )
            ];

            $body = [
                'toPersonEmail' => $email,
                'attachments' => $cards,
                "text" => "IT Asset and Inventory"
            ];

            try{
                $response = $httpClient->post(
                    'https://api.ciscospark.com/v1/messages',
                    [
                        RequestOptions::BODY => json_encode($body),
                        RequestOptions::HEADERS => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer YjdhZjU5YmItYzRmOC00NzJkLWIyZjgtYWFkOWJhYWY4MDMzMmE2YWRmYTItN2I4_PF84_72c16376-f5a4-4a5c-ad51-a60a7b78a790',
                        ],
                    ]
                );

            return 'sent';

            }catch(ServerException $e){
                return 'not';
            }
            catch(RequestException $e){
                return 'not';
            }
            catch(ConnectException $e){
                return 'not';
            }
            catch(ClientException $e){
                return 'not';
            }

        }
    }
}
