<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserBorrowRequest;
use App\UserBorrowRequestItem;
use App\UserInventory;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;


use DB;
use Auth;
class RequestsController extends Controller
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


    public function borrowRequests(){
        session([
            'title' => 'Borrow Requests',
        ]);
        return view('pages.requests.borrow_requests');
    }

    public function borrowRequestsData(){
        return $borrow_requests = UserBorrowRequest::with('employee','user','approved_by_it_head_info','acknowledge_by_it_support_info')
                                                    ->orderBy('created_at','DESC')
                                                    ->get();
    }

    public function borrowRequestsUpdateApprover(Request $request){

        $this->validate($request, [
            'approved_by_it_head' => 'required',
            'acknowledge_by_it_support_remarks' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['acknowledge_by_it_support'] = Auth::user()->id;
            $data['acknowledge_by_it_support_date'] = date('Y-m-d h:i:s');
            $data['acknowledge_by_it_support_status'] = 'Approved';
            $data['status'] = 'Pre-approved';
        
            $borrow_request = UserBorrowRequest::where('id',$request->id)->first();

            if($borrow_request){

                //Save Borrow Request Items
                $borrow_request_items = json_decode($data['borrow_request_items'], true);
                if(count($borrow_request_items) > 0){
                    foreach($borrow_request_items as $item){
                        $validate_item = UserBorrowRequestItem::where('inventory_id',$item['id'])
                                                            ->where('status','For Approval')
                                                            ->first();
                        if(empty($validate_item)){
                            $is_assigned = $item['is_assigned'] == '1' ? 'true' : 'false';
                            $newData = [
                                'user_borrow_request_id' => $request->id,
                                'inventory_id' => $item['id'],
                                'is_assigned' => $is_assigned,
                                'validity_end_date' => $item['validity_end_date'] ? $item['validity_end_date'] : null,
                                'status' => 'For Approval'
                            ];
                            UserBorrowRequestItem::create($newData);
                        }
                    }
                }

                unset($data['notify_approver']);
                unset($data['borrow_request_items']);

                $borrow_request->update($data);
                DB::commit();
                $borrow_request = UserBorrowRequest::with('employee','user','acknowledge_by_it_support_info','approved_by_it_head_info','borrow_request_items')->where('id',$request->id)->first();

                //Notify Approver
                if($request->notify_approver == 'true'){
                    //Send Webex Notification
                    $message = "<span>Hi ".$borrow_request->approved_by_it_head_info->name.", sending this request for your approval. Please check details below. Thank you. </span>
                                <ul>
                                    <li>Request No: ". $borrow_request->request_number."</li>
                                    <li>Employee: ". $borrow_request->employee->first_name . ' ' . $borrow_request->employee->last_name ."</li>
                                    <li>Details: ".$borrow_request->details."</li>
                                    <li>Location: ".$borrow_request->location."</li>
                                    <li>Date: ".$borrow_request->created_at."</li>
                                </ul>
                                <small>Acknowledge By : ".$borrow_request->acknowledge_by_it_support_info->name."</small>
                                <small>Remarks : ".$borrow_request->acknowledge_by_it_support_remarks."</small>
                                <p>Link : http://10.96.4.168:8676/borrow-request-for-approval?id=".$borrow_request->id."</p>
                                <hr>";
                    $send = $this->sendWebexMessage($borrow_request->approved_by_it_head_info->email,$message);              
                }

                return $status_data = [
                    'status'=>'success',
                    'borrow_request'=>$borrow_request,
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

    public function borrowRequestsDisapproveByItSupport(Request $request){
        $this->validate($request, [
            'acknowledge_by_it_support_remarks' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['acknowledge_by_it_support_date'] = date('Y-m-d h:i:s');
            $data['acknowledge_by_it_support_status'] = 'Disapproved';
            $data['status'] = 'Disapproved';

            $borrow_request = UserBorrowRequest::where('id',$request->id)->first();

            if($borrow_request){
                $borrow_request->update($data);
                DB::commit();

                $borrow_request = UserBorrowRequest::with('employee','user','acknowledge_by_it_support_info','approved_by_it_head_info','borrow_request_items')->where('id',$request->id)->first();
                
                //Notify User
                $message = "<p>Hi ".$borrow_request->employee->first_name.", this is to inform you that your IT asset request (<b>".$borrow_request->request_number."</b>) has been disapproved due to; Remarks : ".$borrow_request->acknowledge_by_it_support_remarks.". Thank you. </p>
                            <p>Link : http://10.96.4.168:8676/home-borrow-requests</p>
                            <small><i>This is an auto generated message. Please do not reply.</i></small>";

                $send = $this->sendWebexMessage($borrow_request->user->email,$message);        

                return $status_data = [
                    'status'=>'success',
                    'borrow_request'=>$borrow_request,
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

    public function borrowRequestForApproval(Request $request){
        return view('pages.requests.borrow_request_for_approval');
    }

    public function borrowRequestApprove(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            $borrow_request = UserBorrowRequest::with('employee','user','acknowledge_by_it_support_info','approved_by_it_head_info','borrow_request_items')->where('id',$request->id)->first();

            if($borrow_request){
              
                $data['approved_by_it_head_status'] = 'Approved';
                $data['approved_by_it_head_date'] = date('Y-m-d');
                $data['status'] = 'Approved';
                
                if(count($borrow_request->borrow_request_items) > 0){
                    $save_count = 0;
                    foreach($borrow_request->borrow_request_items as $item){

                        $borrow_request_item = UserBorrowRequestItem::where('id',$item['id'])->first();

                        $user_inventory = UserInventory::where('employee_id',$borrow_request->employee->id)
                                                    ->where('inventory_id',$item->inventory_id)
                                                    ->where('status','Borrowed')
                                                    ->first();
                        if(empty($user_inventory)){
                            $newData = [
                                'employee_id' => $borrow_request->employee->id,
                                'inventory_id' => $item->inventory_id,
                                'borrow_date' => date('Y-m-d h:i:s'),
                                'status' => 'Borrowed',
                                'is_assigned' => $item->is_assigned,
                                'validity_end_date' => $item->validity_end_date ? $item->validity_end_date : null,
                                'ticket_number' => $borrow_request->ticket_number,
                            ];
                            UserInventory::create($newData);  
                            $borrow_request_item->update(['status'=>'Approved']);
                            $save_count++;
                        }
                    }
                }
                if($borrow_request->update($data)){
                    DB::commit();

                    $borrow_request = UserBorrowRequest::with('employee','user','acknowledge_by_it_support_info','approved_by_it_head_info','borrow_request_items')->where('id',$request->id)->first();

                    return $response = [
                        'borrowed_request' => $borrow_request,
                        'status'=>'saved'
                    ];
                }
               
            }
        }catch (Exception $e) {
            DB::rollBack();
            return $data = [
                'status'=>'error'
            ];
        }
    }

    public function borrowRequestDisapprove(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            $borrow_request = UserBorrowRequest::with('employee','user','acknowledge_by_it_support_info','approved_by_it_head_info','borrow_request_items')->where('id',$request->id)->first();
            if($borrow_request){

                $data['approved_by_it_head_status'] = 'Disapproved';
                $data['approved_by_it_head_date'] = date('Y-m-d');
                $data['status'] = 'Disapproved';

                if(count($borrow_request->borrow_request_items) > 0){
                    $save_count = 0;
                    foreach($borrow_request->borrow_request_items as $item){
                        $borrow_request_item = UserBorrowRequestItem::where('id',$item['id'])->first();
                        $borrow_request_item->update(['status'=>'Disapproved']);
                    }
                }
                if($borrow_request->update($data)){
                    DB::commit();

                    $borrow_request = UserBorrowRequest::with('employee','user','acknowledge_by_it_support_info','approved_by_it_head_info','borrow_request_items')->where('id',$request->id)->first();
                    
                    //Notify User
                    $message = "<p>Hi ".$borrow_request->employee->first_name.", this is to inform you that your IT asset request (<b>".$borrow_request->request_number."</b>) has been disapproved due to; Remarks : ".$borrow_request->approved_by_it_head_remarks.". Thank you. </p>
                                <p>Link : http://10.96.4.168:8676/home-borrow-requests</p>
                                <small><i>This is an auto generated message. Please do not reply.</i></small>";
                    $send = $this->sendWebexMessage($borrow_request->user->email,$message);   
                    
                    return $response = [
                        'borrowed_request' => $borrow_request,
                        'status'=>'saved'
                    ];
                }
            }

        }catch (Exception $e) {
            DB::rollBack();
            return $data = [
                'status'=>'error'
            ];
        }  
    }

    public function borrowRequestForApprovalData(Request $request){
        return UserBorrowRequest::with('employee','user','approved_by_it_head_info','acknowledge_by_it_support_info','borrow_request_items.inventory_info')
                                                    ->where('id',$request->id)
                                                    ->first();
    }   

    public function sendWebexMessage($email,$message){

        $httpClient = new Client(); 

        if($email && $message){
            $body = [
                'toPersonEmail' => $email,
                'html' => $message
            ];

            try{
                $response = $httpClient->post(
                    'https://api.ciscospark.com/v1/messages',
                    [
                        RequestOptions::BODY => json_encode($body),
                        RequestOptions::HEADERS => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer YjdhZjU5YmItYzRmOC00NzJkLWIyZjgtYWFkOWJhYWY4MDMzMmE2YWRmYTItN2I4_PF84_72c16376-f5a4-4a5c-ad51-a60a7b78a790',
                        ],
                    ]
                );

            return 'sent';

            }catch(ServerException $e){
                return 'not';
            }
            catch(RequestException $e){
                return 'not';
            }
            catch(ConnectException $e){
                return 'not';
            }
            catch(ClientException $e){
                return 'not';
            }

        }
    }
}
