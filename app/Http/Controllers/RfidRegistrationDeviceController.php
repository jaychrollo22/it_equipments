<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RfidRegistrationDevice;
use DB;
class RfidRegistrationDeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session([
            'title' => 'RFID Registration Device'
        ]);

        return view('pages.rfid.rfid_registration_device');
    }

    public function indexData()
    {
        return RfidRegistrationDevice::orderBy('reader_name','ASC')->get();
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
            'reader_name' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            if($device = RfidRegistrationDevice::create($data)){
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'device'=>$device,
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'reader_name' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            $device = RfidRegistrationDevice::where('id',$data['id'])->first();
            if($device){
                unset($data['id']);
                $device->update($data);
                DB::commit();
                return $status_data = [
                    'status'=>'success',
                    'device'=>$device,
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

    public function activateImpinjReader(Request $request){
        session([
            'activate_impinj_device' => $request->all()
        ]);
        return 'saved';
    }
    public function activatedImpinjReaderData(){
       return session('activate_impinj_device');
    }


}
