<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Audit;
use App\Employee;

use Auth;
class ActivityLogsController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

     public function index(){
        $employee = Employee::select('id','user_id','id_number','first_name','last_name','middle_name','position','level')
                            ->with('user.user_role')
                            ->where('user_id',Auth::user()->id)
                            ->first();

        //Validate Role
        $role = 'User';
        if(isset($employee->level)){
            if($employee->user->user_role){
                if($employee->user->user_role->role == "Administrator"){
                    $role = $employee->user->user_role->role;
                }
            }
        }
     
        session([
            'title' => 'Activity Logs',
            'user' => $employee,
            'role' => $role
        ]);

        return view('pages.activity_logs');
     }

     public function activityLogs(Request $request){
        
        $data = $request->all();
        if($data){
            $start_date = isset($data['startDate']) ? $data['startDate'] : date('Y-m-d');
            $end_date = isset($data['endDate']) ? $data['endDate'] : date('Y-m-d');

            $audits = Audit::with('user')
                            ->whereDate('created_at', '>=', $start_date)
                            ->whereDate('created_at', '<=', $end_date)
                            ->orderBy('created_at','DESC')
                            ->get();
        }else{
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
            $audits = Audit::with('user')
                            ->whereDate('created_at', '=', $start_date)
                            ->orderBy('created_at','DESC')
                            ->get();
        }
        
        

        return $audits;

     }
}
