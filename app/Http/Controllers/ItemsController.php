<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\Inventory;
use App\UserInventory;
use App\AssetHandoverForm;

use DB;

class ItemsController extends Controller
{
    public function index()
    {
        session([
            'title' => 'Items',
        ]);
        return view('items');
    }

    public function itemSearchEmployee(Request $request){

        $search = $request->search;
        $employee = Employee::select('id','user_id','id_number','first_name','last_name')
                                ->with('borrowed_items.inventory_info')
                                ->where(function ($query) use ($search) {
                                    $query->where('first_name', 'like' , '%' .  $search . '%')
                                            ->orWhere('last_name', 'like' , '%' .  $search . '%')
                                            ->orWhere('id_number', 'like' , '%' .  $search . '%');
                                    $query->orWhereRaw("CONCAT(`first_name`, ' ', `last_name`) LIKE ?", ["%{$search}%"]);
                                    $query->orWhereRaw("CONCAT(`last_name`, ' ', `first_name`) LIKE ?", ["%{$search}%"]);
                                })
                                ->first();
        return $employee;
    }

    public function itemSearch(Request $request){
        $data = $request->all();
        
        $item = Inventory::select('id','model','serial_number','type','location')
                                ->doesnthave('is_borrowed')
                                ->doesnthave('is_transfer')
                                ->where(function($q) use($data){
                                        $q->where('model', 'like','%'.$data['item_name'].'%')
                                        ->orWhere('serial_number', 'like','%'.$data['item_name'].'%')
                                        ->orWhere('type', 'like','%'.$data['item_name'].'%');
                                })
                                ->orWhere('id', '=',$data['item_name'])
                                ->take(5)
                                ->get();

        return $item;
    }

    public function saveBorrowItem(Request $request){
        $data = $request->all();

        DB::beginTransaction();
        try {
            if($data['user_inventories']){
                $user_inventories = json_decode($data['user_inventories'], true);
                $save_count = 0;
                foreach($user_inventories as $item){
                    $user_inventory = UserInventory::where('employee_id',$data['employee_id'])
                                                    ->where('inventory_id',$item['id'])
                                                    ->where('status','Borrowed')
                                                    ->first();
                    if(empty($user_inventory)){
                        $newData = [
                            'employee_id' => $data['employee_id'],
                            'inventory_id' => $item['id'],
                            'borrow_date' => date('Y-m-d h:i:s'),
                            'status' => 'Borrowed',
                            'ticket_number' => $data['ticket_number'],
                        ];
                        UserInventory::create($newData);
                        $save_count++;
                    }
                }
                if($save_count > 0){
                    $asset_handover_form = [
                        'employee_id'=>$data['employee_id'],
                        'hof_date'=>date('Y-m-d'),
                        'details'=>$data['user_inventories'],
                    ];
                    //Save Handover Form
                    AssetHandoverForm::create($asset_handover_form);

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

    public function saveReturnItem(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            $user_inventory = UserInventory::where('id',$data['user_inventory_id'])->first();
            if($user_inventory){
                $updateUserInventory = [
                    'return_date'=> date('Y-m-d h:i:s'),
                    'status'=>'Returned'
                ];
                $user_inventory->update($updateUserInventory);
                DB::commit();
                $item = UserInventory::with('inventory_info')->where('id',$data['user_inventory_id'])->first();
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

}
