<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\Inventory;
use App\UserInventory;

use App\SettingLocation;
use App\SettingType;

use Auth;
use DB;

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

    public function currentUser(){
        return session('user');
    }

    public function dashboard(){
        $employee = Employee::select('id','user_id','id_number','first_name','last_name','middle_name','position','level')
                            ->with('user.user_role')
                            ->where('user_id',Auth::user()->id)
                            ->first();
        if($employee->user->user_role){
            if($employee->user->user_role->role == 'Administrator' || $employee->user->user_role->role == 'IT Support'){
                session([
                    'title' => 'Dashboard',
                    'user' => $employee,
                    'user_role' => $employee->user->user_role->role,
                ]);
                return view('pages.dashboard');    
            }else{
                return redirect('/home-user');
            }
        }else{
            return redirect('/home-user');
        }
       
    }

    public function dashboardData(){
        $dateToday = date('Y-m-d');
        $overall_total_inventory = Inventory::get();
        $total_borrowed_items_today = UserInventory::whereDate('borrow_date', '=' , $dateToday)->get();
        $total_returned_items_today = UserInventory::whereDate('return_date','=',$dateToday)->get();

        $locations = SettingLocation::get();
        $per_location = [];
        if($locations){
            foreach($locations as $k=> $item_location){
                $count_per_location = Inventory::where('location','like' , '%'.$item_location['name'].'%')->count();
                $per_location[$k]['name'] = $item_location['name'];
                $per_location[$k]['color'] = $item_location['color'];
                $per_location[$k]['count'] = $count_per_location;
            }
        }

        $types = SettingType::get();
        $per_type = [];
        if($types){
            foreach($types as $k=> $item_type){
                $count_per_type = Inventory::where('type','like' , '%'.$item_type['name'].'%')->count();
                $per_type[$k]['name'] = $item_type['name'];
                $per_type[$k]['color'] = $item_type['color'];
                $per_type[$k]['count'] = $count_per_type;
            }
        }
        return $dashboard_data = [
            'overall_total_inventory' =>  $overall_total_inventory,
            'total_borrowed_items_today' => $total_borrowed_items_today,
            'total_returned_items_today' => $total_returned_items_today,
            'per_location' => $per_location,
            'per_type' => $per_type,
        ];
    }

    public function inventoriesActiveData(){
        return $inventories = Inventory::where('status','Active')->get();
    }
    public function inventoriesSpareData(){
        return $inventories = Inventory::where('status','Spare')->get();
    }
    public function inventoriesLoanItemsData(){
        return $inventories = Inventory::where('status','Loan Item')->get();
    }
    public function inventoriesMaintenanceData(){
        return $inventories = Inventory::where('status','For Repair')->get();
    }

    public function inventoriesStatusCountData(){
        return $inventories = Inventory::select('status', DB::raw('count(*) as total'))->groupBy('status')->orderBy('status','ASC')->get();
    }
}
