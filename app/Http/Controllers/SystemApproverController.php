<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SystemApprover;
class SystemApproverController extends Controller
{
    public function systemApproverITData(){
        return SystemApprover::with('user')
                                    ->where('approver_type','IT')
                                    ->get();
    }
    public function systemApproverFinanceData(){
        return SystemApprover::with('user')
                                    ->where('approver_type','Finance')
                                    ->get();
    }
}
