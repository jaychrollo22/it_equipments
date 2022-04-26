<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Employee;
use DB;
use Excel;
use PDF;
use Auth;

class EmployeeController extends Controller
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

    public function allEmployees(Request $request){
        return Employee::select('id','user_id','first_name','last_name')
                            ->where('status','Active')
                            ->orderBy('first_name','ASC')
                            ->get();
    }
}
