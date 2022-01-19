<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserInventory;
use App\Inventory;
use App\AssetHandoverForm;


use App\Imports\UserInventoriesImport;

use Excel;
use PDF;
use Auth;
class ReportsController extends Controller
{
    public function borrowLogs(){
        session([
            'title' => 'Borrow Logs',
        ]);
        return view('pages.reports.borrow_logs');
    }

    public function borrowLogData(Request $request){
        $dateToday = date('Y-m-d');
        if($request->date_from && $request->date_to){
            return UserInventory::with('employee_info','inventory_info')
                                ->where('borrow_date','>=',$request->date_from ? $request->date_from : $dateToday)
                                ->whereDate('borrow_date','<=',$request->date_to ? $request->date_to : $dateToday)
                                ->orderBy('borrow_date','DESC')
                                ->get();
        } 
        else{
            return UserInventory::with('employee_info','inventory_info')->whereDate('borrow_date','=',$dateToday)->orderBy('borrow_date','DESC')->take(50)->get();
        }
    }

    public function returnLogs(){
        session([
            'title' => 'Return Logs',
        ]);
        return view('pages.reports.return_logs');
    }

    public function returnLogData(Request $request){
        $dateToday = date('Y-m-d');
        if($request->date_from && $request->date_to){
            return UserInventory::with('employee_info','inventory_info')
                                ->where('return_date','>=',$request->date_from ? $request->date_from : $dateToday)
                                ->whereDate('return_date','<=',$request->date_to ? $request->date_to : $dateToday)
                                ->orderBy('return_date','DESC')
                                ->get();
        }else{
            return UserInventory::with('employee_info','inventory_info')->whereDate('return_date','=',$dateToday)->orderBy('return_date','DESC')->take(50)->get();
        }
    }

    public function assetLogs(){
        session([
            'title' => 'Asset Logs',
        ]);
        return view('pages.reports.asset_logs');
    }

    public function assetLogData(Request $request){
        return UserInventory::with('employee_info','inventory_info')
                            ->where('status','Borrowed')
                            ->whereNull('return_date')
                            ->orderBy('employee_id','DESC')
                            ->orderBy('created_at','DESC')
                            ->get();
    }

    public function disposedLogs(){
        session([
            'title' => 'Diposed Logs',
        ]);
        return view('pages.reports.disposed_logs');
    }

    public function disposedLogData(Request $request){
        if($request->date_from && $request->date_to){
            return Inventory::with('disposed_by_info')->where('disposal_date','>=',$request->date_from ? $request->date_from : date('Y-m-d'))
                                ->whereDate('disposal_date','<=',$request->date_to ? $request->date_to : date('Y-m-d'))
                                ->orderBy('disposal_date','DESC')
                                ->get();
        }else{
            return Inventory::with('disposed_by_info')->where('disposal_date',date('Y-m-d'))->orderBy('disposal_date','DESC')->take(50)->get();
        }
    }


    public function assetHandoverForms(){
        session([
            'title' => 'Asset Handover Forms',
        ]);
        return view('pages.reports.asset_handover_forms');
    }

    public function assetHandoverFormsData(Request $request){
        if($request->date_from && $request->date_to){
            return AssetHandoverForm::with('employee_info')->where('hof_date','>=',$request->date_from ? $request->date_from : date('Y-m-d'))
                                ->whereDate('hof_date','<=',$request->date_to ? $request->date_to : date('Y-m-d'))
                                ->orderBy('hof_date','DESC')
                                ->get();
        }else{
            return AssetHandoverForm::with('employee_info')->where('hof_date',date('Y-m-d'))->orderBy('hof_date','DESC')->take(50)->get();
        }
    }

    public function printAssetHandoverForm(Request $request){

        $asset_handover_form_data = AssetHandoverForm::with('employee_info.companies','employee_info.departments')->where('id',$request->id)->first();

        $asset_handover_form = $asset_handover_form_data;
        $asset_handover_form_details = json_decode($asset_handover_form_data['details'],true);
        $details = [];
        if($asset_handover_form_details){
            foreach( $asset_handover_form_details as $k=>$item ){
                $inventory = Inventory::where('id',$item['id'])->first();
                $details[$k] = $inventory;
            }
        }
        $asset_handover_form['details'] = $details;
        // return $asset_handover_form;
        if($asset_handover_form){
            $pdf = PDF::loadView('pages.reports.print_asset_handover_form',array(
                'asset_handover_form' => $asset_handover_form,
                'requested_by' => Auth::user()->name,
            ))->setPaper('a4','portrait');
    
            return $pdf->stream($asset_handover_form['id'] . '_' . date('Ymd') . '.pdf');
        }else{
            return redirect('/reports-asset-handover-forms');
        }
    }

    public function uploadUserInventories(Request $request){

        $this->validate($request, [
            'upload_inventory_file' => 'required|mimes:xls,xlsx'
        ]);
        $data = $request->all();
        $upload_inventory_file = Excel::toArray(new UserInventoriesImport, $request->file('upload_inventory_file'));
        $save_count = 0;
        if($upload_inventory_file){
            foreach($upload_inventory_file[0] as $k => $item){
                if($item['employee_id']){
                    $user_inventory = UserInventory::where('employee_id',$item['employee_id'])
                                                    ->where('inventory_id',$item['id'])
                                                    ->where('status','Borrowed')
                                                    ->first();

                    $borrow_date = isset($item['borrow_date']) ? ($item['borrow_date'] - 25569) * 86400 : "";
                    $borrow_date = $item['borrow_date'] ? gmdate("Y-m-d", $borrow_date) : "";

                    $save_item = [
                        'employee_id' => $item['employee_id'],
                            'inventory_id' => $item['id'],
                            'borrow_date' => $borrow_date,
                            'status' => 'Borrowed',
                            'ticket_number' => $item['ticket_number'],
                    ];

                    if(empty($user_inventory)){
                        //Add
                        UserInventory::create($save_item);
                        $save_count++;
                    }else{
                        //Update
                        $user_inventory->update($save_item);
                        $save_count++;
                    }
                }
            }
        }

        return $save_count;

    }
}
