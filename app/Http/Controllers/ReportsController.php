<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserInventory;
class ReportsController extends Controller
{
    public function borrowLogs(){
        session([
            'title' => 'Borrow Logs',
        ]);
        return view('pages.reports.borrow_logs');
    }

    public function borrowLogData(Request $request){
        if($request->date_from && $request->date_to){
            return UserInventory::with('employee_info','inventory_info')
                                ->where('borrow_date','>=',$request->date_from)
                                ->whereDate('borrow_date','<=',$request->date_to)
                                ->orderBy('borrow_date','DESC')
                                ->get();
        }else{
            return UserInventory::with('employee_info','inventory_info')->whereDate('borrow_date', date('Y-m-d'))->orderBy('borrow_date','DESC')->get();
        }
    }

    public function returnLogs(){
        session([
            'title' => 'Return Logs',
        ]);
        return view('pages.reports.return_logs');
    }

    public function returnLogData(Request $request){
        if($request->date_from && $request->date_to){
            return UserInventory::with('employee_info','inventory_info')
                                ->where('return_date','>=',$request->date_from)
                                ->whereDate('return_date','<=',$request->date_to)
                                ->orderBy('return_date','DESC')
                                ->get();
        }else{
            return UserInventory::with('employee_info','inventory_info')->whereDate('return_date', date('Y-m-d'))->orderBy('return_date','DESC')->get();
        }
    }
}
