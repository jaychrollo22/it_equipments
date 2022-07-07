<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Inventory;

use QRCode;

class QrScannerController extends Controller
{

    public function qrScanner(){
        return view('pages.qr_scanner.index');
    }

    public function qrScannerData(Request $request){

        $inventory = Inventory::with('is_borrowed.employee_info','is_borrowed.letter_of_undertaking','is_transfer')
                                ->where('id',$request->id)->first();

        if($inventory){
            $response = [
                'status'=>'success',
                'inventory'=>$inventory
            ];
            return $response;
        }
    }

    public function qrGenerator(Request $request){
        $qr_name = date('YmdHis');
        QRCode::text($request->qr)->setSize(25)->setMargin(2)->setOutfile(base_path().'/public/qr_generator/'. $qr_name.'.png')->png();
        return redirect('/qr_generator/'. $qr_name.'.png');
    }
}
