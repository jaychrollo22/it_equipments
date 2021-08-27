<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\Inventory;
use App\UserInventory;

use Auth;

class HomeController extends Controller
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

    public function dashboard(){
        $employee = Employee::select('id','user_id','id_number','first_name','last_name','middle_name','position','level')
                            ->with('user.user_role')
                            ->where('user_id',Auth::user()->id)
                            ->first();

        session([
            'title' => 'Dashboard',
            'user' => $employee,
        ]);
        return view('pages.dashboard');
    }
}
