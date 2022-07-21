<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\GatePassLog;
use App\UserBorrowRequest;
use App\InventoryTransfer;

use QRCode;
use PDF;
use DB;
use Redirect;


use setasign\Fpdi\Fpdi;

class GatePassController extends Controller
{

    public function generateGatePass(Request $request){
        DB::beginTransaction();
        try {
            if($request->type == 'Borrow'){
                $user_borrow_request = UserBorrowRequest::where('id',$request->id)->first();
                if($user_borrow_request){
                    $data = [
                        'borrow_request_id' => $user_borrow_request->id,
                        'type' => 'Borrow',
                        'received_by' => $user_borrow_request->user_id,
                        'status' => 'Pending',
                    ];
                    $gate_pass = GatePassLog::where('borrow_request_id',$user_borrow_request->id)->first();
                    if(empty($gate_pass)){
                        if($save_gate_pass = GatePassLog::create($data)){
                            QRCode::text($save_gate_pass->id)->setSize(25)->setMargin(2)->setOutfile(base_path().'/public/gate_pass/'. $save_gate_pass->id.'.png')->png();
                            DB::commit();
                            return redirect('/print-gate-pass/' . $save_gate_pass->id);
                        }
                    }else{
                        return redirect('/print-gate-pass/' . $gate_pass->id);
                    }
                }else{
                    return Redirect::back();
                }
            }
            elseif($request->type == 'Transfer'){
                $inventory_transfer = InventoryTransfer::where('id',$request->id)->first();
                if($inventory_transfer){
                    $data = [
                        'transfer_id' => $inventory_transfer->id,
                        'type' => 'Transfer',
                        'received_by' => $inventory_transfer->requested_by,
                        'status' => 'Pending',
                    ];
                    $gate_pass = GatePassLog::where('transfer_id',$inventory_transfer->id)->first();
                    if(empty($gate_pass)){
                        if($save_gate_pass = GatePassLog::create($data)){
                            QRCode::text($save_gate_pass->id)->setSize(25)->setMargin(2)->setOutfile(base_path().'/public/gate_pass/'. $save_gate_pass->id.'.png')->png();

                            DB::commit();
                            return redirect('/print-gate-pass/' . $save_gate_pass->id);
                        }
                    }else{
                        return redirect('/print-gate-pass/' . $gate_pass->id);
                    }
                }else{
                    return Redirect::back();
                }
            }else{
                return Redirect::back();
            }

        }
        catch (Exception $e) {
            DB::rollBack();
            return 'error';
        } 
    }


    public function printGatePass(GatePassLog $gate_pass){

        if($gate_pass){
            $details = [];
            if($gate_pass->type == 'Borrow'){
                $user_borrow_request = UserBorrowRequest::with('approved_by_it_head_info.employee','employee.departments','borrow_request_items.inventory_info')->where('id',$gate_pass->borrow_request_id)->first();
                if($user_borrow_request){
                    $details['requester'] = $user_borrow_request->employee->first_name . ' ' . $user_borrow_request->employee->last_name;
                    $details['department'] = $user_borrow_request->employee->departments[0]->name;
                    $details['items'] = $user_borrow_request->borrow_request_items;
                    $details['approved_by'] = $user_borrow_request->approved_by_it_head_info->name;
                    $details['effective_date'] = $user_borrow_request->approved_by_it_head_date ? date('Y-m-d',strtotime($user_borrow_request->approved_by_it_head_date)) : "";
                    $details['acceptance_date'] = $user_borrow_request->acceptance_date ? date('Y-m-d',strtotime($user_borrow_request->acceptance_date)) : "";
                    $details['remarks'] = 'Borrowed/Assigned Request';
                }
            }
            if($gate_pass->type == 'Transfer'){
                $transfer = InventoryTransfer::with('approved_by_it_head_info.employee','requested_by_info','inventory_transfer_items.inventory_info')->where('id',$gate_pass->transfer_id)->first();
                if($transfer){
                    $details['requester'] = $transfer->requested_by_info->name;
                    $details['department'] = 'ITD';
                    $details['items'] = $transfer->inventory_transfer_items;
                    $details['approved_by'] = $transfer->approved_by_it_head_info->name;
                    $details['effective_date'] = $transfer->date_of_transfer ? date('Y-m-d',strtotime($transfer->date_of_transfer)) : "";
                    $details['acceptance_date'] = $transfer->date_of_transfer ? date('Y-m-d',strtotime($transfer->date_of_transfer)) : "";
                    $details['remarks'] = 'For Transfer';
                }
            }


            if($details){
                $pdf = new Fpdi();
                $pdf->AddPage('P','Letter');

                $pdf->SetMargins(5,5,5,5);
                $pdf->SetAutoPageBreak(false);

                //Header-----------------------------------------------------------------------
                $pdf->Image(base_path().'/public/img/pfmc.png', 7, 5, 30, 30,'PNG');
                $pdf->SetXY(7,5);
                $pdf->SetFont('Arial', '', 5);
                $pdf->MultiCell(30,30, '' ,1,'C');

                $pdf->SetXY(37,5);
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->MultiCell(170,10, 'Philippine Foremost Milling Corporation' ,1,'L');

                $pdf->SetXY(37,$pdf->getY());
                $pdf->SetFont('Arial', '', 9);
                $pdf->MultiCell(80,10, 'Doc. No. MAD - F - 016 Rev. No. 01' ,1,'L');

                $pdf->SetXY(117,$pdf->getY() - 10);
                $pdf->SetFont('Arial', '', 9);
                $pdf->MultiCell(40,10, 'Effective date' ,1,'L');

                $pdf->SetXY(157,$pdf->getY() - 10);
                $pdf->SetFont('Arial', '', 9);
                $pdf->MultiCell(50,10, $details['effective_date'] ,1,'L');

                $pdf->SetXY(37,$pdf->getY());
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->MultiCell(170,10, 'GATE PASS' ,1,'L');

                //------------------------------------------------------------------------------
                
                //Requester---------------------------------------------------------------------
                $pdf->SetXY(7,$pdf->getY());
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(80,20, '' ,1,'L');

                $boxY = $pdf->getY();

                $pdf->SetXY(8,$boxY - 19);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(25,5, 'REQUESTER:' ,0,'L');

                $pdf->SetXY(8,$boxY - 10);
                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(78,5, $details['requester'] ,0,'L');

                $pdf->SetXY(87,$boxY - 20);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(80,20, '' ,1,'L');

                $pdf->SetXY(88,$boxY - 19);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(50,5, 'DEPARTMENT / SECTION:' ,0,'L');

                $pdf->SetXY(88,$boxY - 10);
                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(78,5, $details['department'] ,0,'L');

                $pdf->SetXY(167,$boxY - 20);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(40,20, '' ,1,'L');

                $pdf->SetXY(168,$boxY - 19);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(15,5, 'DATE:' ,0,'L');

                $pdf->SetXY(168,$boxY - 10);
                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(30,5, $details['effective_date'] ,0,'L');


                //Head ------------------------------------------------------------------------------
                $headY = $pdf->getY();
                $pdf->SetXY(7,$headY+5);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(15,8, 'QTY' ,1,'C');

                $pdf->SetXY(22,$headY+5);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(130,8, 'COMMODITY/ITEM DESCRIPTION ' ,1,'C');

                $pdf->SetXY(152,$headY+5);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(55,8, 'REMARKS' ,1,'C');

                //Body ------------------------------------------------------------------------------
                $bodyY = $pdf->getY();
                $pdf->SetXY(7,$bodyY);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(15,130, '' ,1,'C');

                $pdf->SetXY(22,$bodyY);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(130,130, '' ,1,'C');

                $pdf->SetXY(152,$bodyY);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(55,7, $details['remarks'] ,0,'C');

                $pdf->Image(base_path().'/public/gate_pass/'.$gate_pass->id.'.png', 165, 162, 30, 30,'PNG');

                $pdf->SetXY(152,190);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(55,3, $gate_pass->id ,0,'C');

                $pdf->SetXY(152,$bodyY);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(55,130, '' ,1,'C');

                $bodycontentY = $pdf->getY();

                if($details['items']){
                    foreach($details['items'] as $item){
                        $inventory = $item['inventory_info'];
                        $pdf->SetXY(7,$bodyY);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->MultiCell(15,7, '1' ,0,'C');

                        $pdf->SetXY(22,$bodyY);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->MultiCell(130,7, $inventory['id'].' | ' .  'S/N '.$inventory['serial_number'].' | '  .  ' '.$inventory['model'].' | ' . $inventory['type'] ,0,'C');

                        $bodyY+=5;
                    }
                }
                

                //Footer ------------------------------------------------------------------------------
                $footerY=$bodycontentY;

                $pdf->SetXY(7,$footerY);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(70,5, 'APPROVED BY:' ,0,'L');

                $pdf->SetXY(7,$footerY + 9);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(70,5, $details['approved_by'] ,0,'C');

                $pdf->SetXY(7,$footerY + 10);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(70,5, '________________________________________' ,0,'C');

                $pdf->SetXY(7,$footerY + 15);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(70,3, 'DEPARTMENT HEAD / SECTION SUPERVISOR' ,0,'C');

                $pdf->SetXY(7,$footerY + 19);
                $pdf->SetFont('Arial', '', 7);
                $pdf->MultiCell(70,3, '(Printed Name and Signature)' ,0,'C');

                $pdf->SetXY(7,$footerY);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(70,25, '' ,1,'C');

                $box2Y = $pdf->getY();

                $pdf->SetXY(77,$footerY);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(130,5, 'CHECKED BY:' ,0,'L');

                $pdf->SetXY(77,$footerY + 9);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(70,5, $gate_pass->guard_on_duty ,0,'C');

                $pdf->SetXY(77,$footerY + 10);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(70,5, '________________________________________' ,0,'C');


                $pdf->SetXY(77,$footerY + 15);
                $pdf->SetFont('Arial', '', 7);
                $pdf->MultiCell(70,3, 'GUARD ON DUTY' ,0,'C');

                $pdf->SetXY(77,$footerY + 18);
                $pdf->SetFont('Arial', '', 7);
                $pdf->MultiCell(70,3, 'Printed Name and Signature' ,0,'C');

                $pdf->SetXY(147,$footerY + 9);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(35,5, $gate_pass->date_time_released ? date('Y-m-d',strtotime($gate_pass->date_time_released)) : ""  ,0,'C');

                $pdf->SetXY(147,$footerY + 10);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(35,5, '___________________' ,0,'C');

                $pdf->SetXY(147,$footerY + 15);
                $pdf->SetFont('Arial', '', 7);
                $pdf->MultiCell(35,3, 'Date Released' ,0,'C');

                $pdf->SetXY(182,$footerY + 9);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(25,5, $gate_pass->date_time_released ? date('H:i A',strtotime($gate_pass->date_time_released)) : "" ,0,'C');

                $pdf->SetXY(182,$footerY + 10);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(25,5, '_____________' ,0,'C');

                $pdf->SetXY(182,$footerY + 15);
                $pdf->SetFont('Arial', '', 7);
                $pdf->MultiCell(25,3, 'Time Released' ,0,'C');

                $pdf->SetXY(77,$footerY);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(130,25, '' ,1,'C');

                //CARRIER/HAULER:-----------------------------------------------------------

                $pdf->SetXY(7,$box2Y + 9);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(70,5, $gate_pass->carrier_by ,0,'C');

                $pdf->SetXY(7,$box2Y + 10);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(70,5, '________________________________________' ,0,'C');

                $pdf->SetXY(7,$box2Y + 15);
                $pdf->SetFont('Arial', '', 7);
                $pdf->MultiCell(70,3, '(Printed Name and Signature) ' ,0,'C');

                $pdf->SetXY(7,$box2Y);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(70,5, 'CARRIER/HAULER:' ,0,'L');

                $pdf->SetXY(7,$box2Y);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(70,25, "" ,1,'C');


                $pdf->SetXY(77,$box2Y + 9);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(70,5, "" ,0,'C');

                $pdf->SetXY(77,$box2Y + 10);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(70,5, '________________________________________' ,0,'C');


                $pdf->SetXY(77,$box2Y + 15);
                $pdf->SetFont('Arial', '', 7);
                $pdf->MultiCell(70,3, 'Printed Name and Signature' ,0,'C');


                $pdf->SetXY(147,$box2Y + 9);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(35,5, "" ,0,'C');

                $pdf->SetXY(147,$box2Y + 10);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(35,5, '___________________' ,0,'C');

                $pdf->SetXY(147,$box2Y + 15);
                $pdf->SetFont('Arial', '', 7);
                $pdf->MultiCell(35,3, 'Date Released' ,0,'C');


                $pdf->SetXY(182,$box2Y + 9);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(25,5, "" ,0,'C');

                $pdf->SetXY(182,$box2Y + 10);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(25,5, '_____________' ,0,'C');

                $pdf->SetXY(182,$box2Y + 15);
                $pdf->SetFont('Arial', '', 7);
                $pdf->MultiCell(25,3, 'Time Released' ,0,'C');

                $pdf->SetXY(77,$box2Y);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(130,5, 'RECEIVED BY:' ,0,'L');

                $pdf->SetXY(77,$box2Y);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(130,25, '' ,1,'C');


                $pdf->Output('I', $gate_pass->id . '.pdf');
                exit();


            }
           
        }
        

    }

    public function gatePassData(Request $request){
        $gate_pass = GatePassLog::where('id',$request->id)->first();
        if($gate_pass){
            $details = [];
            if($gate_pass->type == 'Borrow'){
                $user_borrow_request = UserBorrowRequest::with('approved_by_it_head_info.employee','employee.departments','borrow_request_items.inventory_info')->where('id',$gate_pass->borrow_request_id)->first();
                if($user_borrow_request){
                    
                    $details['requester'] = $user_borrow_request->employee->first_name . ' ' . $user_borrow_request->employee->last_name;
                    $details['department'] = $user_borrow_request->employee->departments[0]->name;
                    $details['items'] = $user_borrow_request->borrow_request_items;
                    $details['approved_by'] = $user_borrow_request->approved_by_it_head_info->name;
                    $details['effective_date'] = date('Y-m-d',strtotime($user_borrow_request->approved_by_it_head_date));
                    $details['remarks'] = 'Borrow/Assigned Request';
                }
            }
            if($gate_pass->type == 'Transfer'){
                $transfer = InventoryTransfer::with('approved_by_it_head_info.employee','requested_by_info','inventory_transfer_items.inventory_info')->where('id',$gate_pass->transfer_id)->first();
                if($transfer){
                    $details['requester'] = $transfer->requested_by_info->name;
                    $details['department'] = 'ITD';
                    $details['items'] = $transfer->inventory_transfer_items;
                    $details['approved_by'] = $transfer->approved_by_it_head_info->name;
                    $details['effective_date'] = date('Y-m-d',strtotime($transfer->date_of_transfer));
                    $details['remarks'] = 'For Transfer';
                }
            }

            return $response = [
                'gate_pass' => $gate_pass,
                'details' => $details,
                'status' => 'success'
            ];
        }else{
            return $response = [
                'status' => 'error',
            ];
        }
    }

    public function saveCheckGatePass(Request $request){
        DB::beginTransaction();
        try {
            $gate_pass = GatePassLog::where('id',$request->id)->first();
            if($gate_pass){
                $gate_pass->update([
                    'guard_on_duty'=> $request->guard_on_duty,
                    'carrier_by'=> $request->carrier_by,
                    'date_time_released'=> date('Y-m-d H:i:s')
                ]);
                DB::commit();
                return $response = [
                    'status' => 'success'
                ];
            }
        }catch (Exception $e) {
            DB::rollBack();
            return 'error';
        } 
    }
}
