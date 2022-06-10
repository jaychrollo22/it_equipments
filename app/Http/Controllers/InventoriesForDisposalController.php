<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Inventory;
use App\InventoriesForDisposal;
use App\InventoriesForDisposalItem;

use Storage;
use Auth;
use DB;

class InventoriesForDisposalController extends Controller
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

    public function index(){

        session([
            'title' => 'For Disposal'
        ]);
        return view('pages.for_disposal.for_disposal');
    }

    public function indexData(){
        return InventoriesForDisposal::with('items.inventory','requested_by_info')
                                        ->orderBy('created_at','DESC')
                                        ->get();
    }

    public function store(Request $request){
        $data = $request->all();

        DB::beginTransaction();
        try {
            $data_items = json_decode($data['items']);

            if(count($data_items) > 0){

                $data_for_disposal_header = [
                    'requested_by'=>Auth::user()->id,
                    'requested_date'=>date('Y-m-d h:i:s'),
                ];

                if($save = InventoriesForDisposal::create($data_for_disposal_header)){
                    DB::commit();
                    $success = 0;
                    $is_exists = 0;
                    foreach($data_items as $item){

                        $inventory = Inventory::where('id',$item)->first();
                        if($inventory->status != 'For Disposal' || $inventory->status != 'Disposed'){

                            $validate = InventoriesForDisposalItem::where('inventory_id',$item)
                                                                    ->where('inventory_for_disposal_id',$save->id)
                                                                    ->first();
                            if(empty($validate)){
                                $data_for_disposal_items = [
                                    'inventory_for_disposal_id' => $save->id,
                                    'inventory_id' => $item,
                                    'old_status' =>  $inventory->status,
                                ];
                                InventoriesForDisposalItem::create($data_for_disposal_items);
                                $success++;
                            }else{
                                $is_exists++;
                            }
                            $inventory->update(['status'=>'For Disposal']);
                        }
                    }

                    return $response = [
                        'status'=>'saved',
                        'success'=>$success,
                        'is_exists'=>$is_exists,
                        'for_disposal_id'=>$save->id
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

    public function forDisposalItems(Request $request){
        session([
            'title'=>'For Disposal Items',
        ]);
        return view('pages.for_disposal.for_disposal_items');
    }

    public function forDisposalItemsData(Request $request){
        return InventoriesForDisposal::with('items.inventory','requested_by_info','approved_by_it_head_info','approved_by_finance_info')
                                        ->where('id',$request->id)
                                        ->first();

    }

    public function placeRequestForDisposal(Request $request){

        $data = $request->all();

        $this->validate($request, [
            'approved_by_it_head' => 'required',
            'approved_by_finance' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $validate = InventoriesForDisposal::where('id',$data['id'])->first();
            if($validate){
                $data['approved_by_it_head_status'] = 'Pending';
                $data['approved_by_finance_status'] = 'Pending';
                $data['status'] = 'For Approval';
                unset($data['id']);
                if($validate->update($data)){
                    DB::commit();
                    return $response = [
                        'status'=>'saved'
                    ];
                }
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

    public function deleteRequestForDisposal(Request $request){

        $data = $request->all();

        DB::beginTransaction();
        try {
            $for_disposal = InventoriesForDisposal::where('id',$data['id'])->first();
            $for_disposal_items = InventoriesForDisposalItem::where('inventory_for_disposal_id',$data['id'])->get();
            //Delete Header
            if($for_disposal){
                $for_disposal->delete();
                //Delete Items
                if(count($for_disposal_items) > 0){
                    foreach($for_disposal_items as $item){
                        $inventory = Inventory::where('id',$item['inventory_id'])->first();
                        if($inventory){
                            $inventory->update(['status'=>$item['old_status']]);
                        }
                        InventoriesForDisposalItem::where('id',$item->id)->delete();
                    }
                }
                DB::commit();
                return $response = [
                    'status'=>'removed'
                ];
            }
        }catch (Exception $e) {
            DB::rollBack();
            return $data = [
                'status'=>'error'
            ];
        }
    }

    public function deleteRequestForDisposalItem(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            $for_disposal_item = InventoriesForDisposalItem::where('id',$data['id'])->first();
            if($for_disposal_item){
                $inventory = Inventory::where('id',$for_disposal_item['inventory_id'])->first();
                if($inventory){
                    $inventory->update(['status'=>$for_disposal_item['old_status']]);
                }
                $for_disposal_item->delete();
                DB::commit();
                return $response = [
                    'status'=>'removed'
                ];
            }
        }catch (Exception $e) {
            DB::rollBack();
            return $data = [
                'status'=>'error'
            ];
        }
    }

    public function approveRequestForDisposalItem(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            $for_disposal = InventoriesForDisposal::with('items.inventory')->where('id',$data['id'])->first();
            if($for_disposal){
                if($data['approval_type'] == 'IT'){
                    $data['approved_by_it_head_status'] = 'Approved';
                    $data['approved_by_finance_status'] = 'Pending';
                    $data['approved_by_it_head_date'] = date('Y-m-d');
                    $data['status'] = 'Pre-approved';
                }
                if($data['approval_type'] == 'Finance'){
                    $data['approved_by_finance_status'] = 'Approved';
                    $data['approved_by_finance_date'] = date('Y-m-d');
                    $data['status'] = 'Approved';

                    if(count($for_disposal->items) > 0){
                        foreach($for_disposal->items as $item){
                            $inventory = Inventory::where('id',$item['inventory_id'])->first();
                            if($inventory){
                                $inventory->update(['status'=>'Disposed']);
                            }
                        }
                    }
                }
                unset($data['approval_type']);
                if($for_disposal->update($data)){
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

    public function disapproveRequestForDisposalItem(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            $for_disposal = InventoriesForDisposal::with('items.inventory')->where('id',$data['id'])->first();
            if($for_disposal){
                if($data['approval_type'] == 'IT'){
                    $data['approved_by_it_head_status'] = 'Disapproved';
                    $data['approved_by_it_head_date'] = date('Y-m-d');
                    $data['status'] = 'Disapproved';
                }
                if($data['approval_type'] == 'Finance'){
                    $data['approved_by_finance_status'] = 'Disapproved';
                    $data['approved_by_finance_date'] = date('Y-m-d');
                    $data['status'] = 'Disapproved';
                }
                if(count($for_disposal->items) > 0){
                    foreach($for_disposal->items as $item){
                        $inventory = Inventory::where('id',$item['inventory_id'])->first();
                        if($inventory){
                            $inventory->update(['status'=>$item['old_status']]);
                        }
                    }
                }
                unset($data['approval_type']);
                if($for_disposal->update($data)){
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

    public function saveRdfForDisposal(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            $for_disposal = InventoriesForDisposal::where('id',$data['id'])->first();
            if($for_disposal){
                if($request->file('attachment')){
                    $attachment = $request->file('attachment');   
                    $filename = $for_disposal->id . '_' . date('Ymd') . '_' . $attachment->getClientOriginalName();
                    $path = Storage::disk('public')->putFileAs('for_disposals/rdf_file/', $attachment , $filename);
                    $data['attachment'] =  $filename;
                    unset($data['id']);
                    if($for_disposal->update($data)){
                        DB::commit();
                        return $response = [
                            'status'=>'saved'
                        ];
                    }

                }
            }
        }catch (Exception $e) {
            DB::rollBack();
            return $data = [
                'status'=>'error'
            ];
        }
    }

    public function deleteRdfForDisposal(Request $request){
        $data = $request->all();

        DB::beginTransaction();
        try {
            $for_disposal = InventoriesForDisposal::where('id',$data['id'])->first();
            if($for_disposal){
                $data = [
                    'attachment'=>null
                ];
                unset($data['id']);
                if($for_disposal->update($data)){
                    DB::commit();
                    return $response = [
                        'status'=>'removed'
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

    public function savePictureForDisposalItem(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            $for_disposal_item = InventoriesForDisposalItem::where('id',$data['id'])->first();
            if($for_disposal_item){
                if($request->file('attachment')){
                    $attachment = $request->file('attachment');   
                    $filename = $for_disposal_item->id . '_' . date('Ymd') . '_' . $attachment->getClientOriginalName();
                    $path = Storage::disk('public')->putFileAs('for_disposals/picture_file/', $attachment , $filename);
                    $data['attachment'] =  $filename;
                    unset($data['id']);
                    if($for_disposal_item->update($data)){
                        DB::commit();
                        return $response = [
                            'status'=>'saved'
                        ];
                    }

                }
            }
        }catch (Exception $e) {
            DB::rollBack();
            return $data = [
                'status'=>'error'
            ];
        }
    }

    public function deletePictureForDisposalItem(Request $request){
        $data = $request->all();

        DB::beginTransaction();
        try {
            $for_disposal_item = InventoriesForDisposalItem::where('id',$data['id'])->first();
            if($for_disposal_item){
                $data = [
                    'attachment'=>null
                ];
                unset($data['id']);
                if($for_disposal_item->update($data)){
                    DB::commit();
                    return $response = [
                        'status'=>'removed'
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

    public function forDisposalApproval(){
        session([
            'title'=>'For Disposal Approval',
        ]);
        return view('pages.for_disposal.for_disposal_approval');
    }

}
