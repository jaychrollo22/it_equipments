<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SystemApprover;
use DB;
class SystemApproverController extends Controller
{
    public function systemApproverITData(){
        return SystemApprover::with('user')
                                    ->where('approver_type','IT')
                                    ->get();
    }
    public function systemApproverFinanceData(){
        return SystemApprover::with('user')
                                    ->where('approver_type','Finance')
                                    ->get();
    }

    public function systemApproversData(){
        return SystemApprover::with('user')->get();
    }

    public function systemApprovers(){
        session([
            'title' => 'System Approvers'
        ]);

        return view('pages.settings.system_approvers');
    }

    public function store(Request $request){
        $data = $request->all();

        $this->validate($request, [
            'user_id' => 'required',
            'approver_type' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            if($system_approver = SystemApprover::create($data)){
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'system_approver'=>$system_approver,
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
            'user_id' => 'required',
            'approver_type' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            $system_approver = SystemApprover::where('id',$data['id'])->first();
            if($system_approver){
                unset($data['id']);
                $system_approver->update($data);
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'system_approver'=>$system_approver,
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
