<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\RfidRegistrationDevice;
use App\RfidItemsDevice;
use DB;

use Log;
class RfidController extends Controller
{
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function impinj_rfid_log_registration_details(Request $request)
    {
        $reader_name = $request->activate_impinj_device;
        
        $rfid_details = RfidRegistrationDevice::where('reader_name',$reader_name)->first();
        if($rfid_details){
            $rfid_logs = json_decode($rfid_details->rfid_log,true);
            $x = count($rfid_logs) > 0 ? count($rfid_logs) - 1 :  "";
            if(count($rfid_logs) > 0){
                $last_scan_date = date('Y-m-d h',strtotime($rfid_details->updated_at));
                $current_date = date('Y-m-d h');
                if($last_scan_date == $current_date){
                    return $data = [
                        'reader_name' => $rfid_details->reader_name,
                        'epc' => $rfid_logs[$x]['epc'] ? $rfid_logs[$x]['epc'] : "",
                        'tid' => $rfid_logs[$x]['tid'] ? $rfid_logs[$x]['tid'] : "",
                        'last_scan_date' => $rfid_logs[$x]['firstSeenTimestamp'] ? date('Y-m-d h:i:s A',strtotime($rfid_details->updated_at)) : "",
                        'message'=> 'success',
                        'status'=> 'success',
                    ];
                }else{
                    return $data = [
                        'message'=>'Please tap the item to scan',
                        'status'=>'error'
                    ];
                }
            }else{
                return $data = [
                    'message'=>'Please tap the item to scan',
                    'status'=>'error'
                ];
            }
        }else{
            return $data = [
                'message'=>'Reader not found! Please try to activate rfid reader.',
                'status'=>'error'
            ];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeImpinjRfid(Request $request)
    {
        // // $tag_reads = $request->tag_reads;
        // // $stored_tags = array();
        // Log::info($request->all());
        // //
        // return $request->all();
        DB::beginTransaction();
        try {
            $data = $request->all();
            if($data['reader_name']){
                $reader = RfidRegistrationDevice::where('reader_name',$data['reader_name'])->first();
                $saveDetails = [
                    'reader_name' => $data['reader_name'],
                    'rfid_log' => json_encode($data['tag_reads'],true),
                    'mac_address' => $data['mac_address'],
                ];
                if(empty($reader)){
                    RfidRegistrationDevice::create($saveDetails);
                    DB::commit();
                }else{
                    $reader->update($saveDetails);
                    DB::commit();
                }
            }
        }
        catch (Exception $e) {
            DB::rollBack();
            return 'error';
        } 
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function impinj_rfid_log_item_details(Request $request)
    {
        $reader_name = $request->activate_impinj_device;
        
        $rfid_details = RfidItemsDevice::where('reader_name',$reader_name)->first();
        if($rfid_details){
            $rfid_logs = json_decode($rfid_details->rfid_log,true);
            $x = count($rfid_logs) > 0 ? count($rfid_logs) - 1 :  "";
            if(count($rfid_logs) > 0){
                $last_scan_date = date('Y-m-d h:i',strtotime($rfid_details->updated_at));
                $current_date = date('Y-m-d h:i');
                if($last_scan_date == $current_date){
                    return $data = [
                        'reader_name' => $rfid_details->reader_name,
                        'epc' => $rfid_logs[$x]['epc'] ? $rfid_logs[$x]['epc'] : "",
                        'tid' => $rfid_logs[$x]['tid'] ? $rfid_logs[$x]['tid'] : "",
                        'last_scan_date' => $rfid_logs[$x]['firstSeenTimestamp'] ? date('Y-m-d h:i:s A',strtotime($rfid_details->updated_at)) : "",
                        'message'=> 'success',
                        'status'=> 'success',
                    ];
                }else{
                    return $data = [
                        'message'=>'Please tap the item to scan',
                        'status'=>'error'
                    ];
                }
            }else{
                return $data = [
                    'message'=>'Please tap the item to scan',
                    'status'=>'error'
                ];
            }
        }else{
            return $data = [
                'message'=>'Reader not found! Please try to activate rfid reader.',
                'status'=>'error'
            ];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeImpinjRfidItem(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            if($data['reader_name']){
                $reader = RfidItemsDevice::where('reader_name',$data['reader_name'])->first();
                $saveDetails = [
                    'reader_name' => $data['reader_name'],
                    'rfid_log' => json_encode($data['tag_reads'],true),
                    'mac_address' => $data['mac_address'],
                ];
                if(empty($reader)){
                    RfidItemsDevice::create($saveDetails);
                    DB::commit();
                }else{
                    $reader->update($saveDetails);
                    DB::commit();
                }
            }
        }
        catch (Exception $e) {
            DB::rollBack();
            return 'error';
        } 
    }

}
