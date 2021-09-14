<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\RfidRegistrationDevice;
use DB;

use Log;
class RfidController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return 'x';
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
