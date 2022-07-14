<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserBorrowRequest;
use App\UserBorrowRequestItem;
use App\UserReturnRequest;
use App\UserReturnRequestItem;
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
                    $message = "Hi ".$borrow_request->approved_by_it_head_info->name.", sending this request for your approval. Please check details below. Thank you.";
                    $link = "https://10.96.4.168:8676/borrow-request-for-approval?id=".$borrow_request->id;              
                    $send = $this->sendWebexMessageApprover($borrow_request->approved_by_it_head_info->email,'For Approval',$message,$borrow_request,$link);              
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
                // $message = "<p>Hi ".$borrow_request->employee->first_name.", this is to inform you that your IT asset request (<b>".$borrow_request->request_number."</b>) has been disapproved due to; Remarks : ".$borrow_request->acknowledge_by_it_support_remarks.". Thank you. </p>
                //             <p>Link : https://10.96.4.168:8676/home-borrow-requests</p>
                //             <small><i>This is an auto generated message. Please do not reply.</i></small>";
                $message = "Hi ".$borrow_request->employee->first_name.", this is to inform you that your IT asset request has been disapproved due to; Remarks : ".$borrow_request->acknowledge_by_it_support_remarks.". Thank you.";
                $link = "https://10.96.4.168:8676/home-borrow-requests?request_number=".$borrow_request->request_number;        
                $send = $this->sendWebexMessage($borrow_request->user->email,'Disapproved',$message,$borrow_request,$link);        

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
                                'borrow_location' => $borrow_request->location,
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

                    //Notify Group
                    $message = 'Borrow Request has been Approved. Please check details below. Thank you.';
                    $name = $borrow_request->employee->first_name . ' ' . $borrow_request->employee->last_name;
                    $link = 'https://10.96.4.168:8676/borrow-requests?request_number='. $borrow_request->request_number;
                    $send_webex = $this->sendGroupWebexMessage('Approved',$message,$borrow_request,$name,$link);

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
                    $message = "Hi ".$borrow_request->employee->first_name.", this is to inform you that your IT asset request has been disapproved due to; Remarks : ".$borrow_request->approved_by_it_head_remarks.". Thank you.";
                    $link = "https://10.96.4.168:8676/home-borrow-requests?request_number=".$borrow_request->request_number;        
                    $send = $this->sendWebexMessage($borrow_request->user->email,'Disapproved',$message,$borrow_request,$link);    
                    
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

    public function letterOfUndertaking(Request $request){
        session([
            'title' => 'Letter of Undertaking',
        ]);
        return view('pages.requests.letter_of_undertaking');
    }

    public function letterOfUndertakingData(Request $request){
        return $borrow_requests = UserBorrowRequest::with('employee.companies',
                                                          'employee.locations.address',
                                                          'user','approved_by_it_head_info',
                                                          'acknowledge_by_it_support_info',
                                                          'borrow_request_items.inventory_info.setting_location',
                                                          'employee.borrowed_items.inventory_info'
                                                    )
                                                    ->where('request_number',$request->request_number)
                                                    ->orderBy('created_at','DESC')
                                                    ->first();
    }

    public function letterOfUndertakingAccept(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            $borrow_request = UserBorrowRequest::with('employee','user','acknowledge_by_it_support_info','approved_by_it_head_info','borrow_request_items')->where('id',$request->id)->first();
            if($borrow_request){
                $data['acceptance'] = '1';
                $data['acceptance_date'] = date('Y-m-d');
                if($borrow_request->update($data)){
                    DB::commit();
                    $borrow_requests = UserBorrowRequest::with('employee.companies','employee.locations.address','user','approved_by_it_head_info','acknowledge_by_it_support_info','borrow_request_items.inventory_info.setting_location')
                                                    ->where('id',$request->id)
                                                    ->orderBy('created_at','DESC')
                                                    ->first();
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

    public function letterOfUndertakingNotification(Request $request){
        $borrow_request = UserBorrowRequest::with('employee','user','acknowledge_by_it_support_info','approved_by_it_head_info','borrow_request_items')->where('id',$request->id)->first();

        if($borrow_request){
            //Notify User
            $message = "Hi ".$borrow_request->employee->first_name.", this is to inform you that your IT asset request is ready for pickup. Just accept Letter of Undertaking. Thank you.";
            $link = "https://10.96.4.168:8676/letter-of-undertaking?request_number=".$borrow_request->request_number;        
            $send = $this->sendWebexMessage($borrow_request->user->email,'Approved',$message,$borrow_request,$link); 
            
            if($send == 'sent'){
                $borrow_request->update(['notify'=>1]);
                return $response = [
                    'status' => 'sent'
                ];
            }else{
                return $response = [
                    'status' => 'not'
                ];
            }
        }
    }

    public function returnRequests(){
        session([
            'title' => 'Return Requests',
        ]);
        return view('pages.requests.return_requests');
    }

    public function returnRequestsData(Request $request){
        return $return_requests = UserReturnRequest::with('user','employee','return_request_items.user_inventory.inventory_info','return_request_items.check_by_info')
                                                    ->orderBy('created_at','DESC')
                                                    ->get();
    }

    public function returnRequestsAccept(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try {
            $return_requests = UserReturnRequest::where('id',$request->id)->first();
            if($return_requests){
                $return_items = json_decode($data['return_items'], true);
                if($return_items){
                    foreach($return_items as $item){

                        UserReturnRequestItem::where('id',$item['id'])
                                                ->update([
                                                    'check_status'=>$item['check_status'],
                                                    'check_remarks'=>$item['check_remarks'],
                                                    'check_by'=>Auth::user()->id,
                                                    'check_acceptance'=>1,
                                                    'defect_value_deduction'=>$item['defect_value_deduction'],
                                                    'status'=>'Checked',
                                                ]);

                        UserInventory::where('id',$item['user_inventory_id'])
                                                ->update([
                                                    'return_date'=>$return_requests->created_at,
                                                    'status'=>'Returned',
                                                    'check_status'=>$item['check_status'],
                                                    'check_remarks'=>$item['check_remarks'],
                                                    'check_by'=>Auth::user()->id,
                                                    'check_acceptance'=>1,
                                                    'defect_value_deduction'=>$item['defect_value_deduction'],
                                                    'return_location'=>$return_requests->location,
                                                ]);
                    }
                }

                $return_requests->update([
                    'status'=>'Checked'
                ]);
                
                DB::commit();

                return $response = [
                    'status'=>'success'
                ];

            }


        }catch (Exception $e) {
            DB::rollBack();
            return $data = [
                'status'=>'error'
            ];
        }    
    }

    public function sendWebexMessageApprover($email,$title,$message,$details=array(),$link){

        $httpClient = new Client(); 

        if($email && $message){

            $cards = [
                array(
                    "contentType" => "application/vnd.microsoft.card.adaptive",
                    "content" => array(
                        "type" => "AdaptiveCard",
                        "body" => [
                            array(
                            "type" => "ColumnSet",
                            "columns" => [
                                    array( 
                                    "type" => "Column",
                                    "items" => [
                                        array(
                                            "type" => "TextBlock",
                                            "text" =>  $message,
                                            "wrap" => true,
                                            "spacing" => "Small",
                                            "color" => "Light"
                                        ),
                                        array(
                                            "type" => "TextBlock",
                                            "weight" => "Bolder",
                                            "text" => $title,
                                            "wrap" => true,
                                            "color" => "Warning",
                                            "size" => "Large",
                                            "spacing" => "Small"
                                        )
                                    ],
                                    "width" => "stretch"
                                    )
                                ]
                            ),
                            array( 
                            "type" => "ColumnSet",
                            "columns" => [
                                array(
                                "type" => "Column",
                                "width" => 50,
                                "items" => [
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Request No. : " . $details->request_number,
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Ticket No. : " . $details->ticket_number,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Details : " . $details->details,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Location : " . $details->location ,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Date : " . date('Y-m-d'),
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Acknowledge By : " . $details->acknowledge_by_it_support_info->name,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Remarks : " . $details->acknowledge_by_it_support_remarks,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    
                                ]
                                ),
                            ],
                            "spacing" => "Padding",
                            "horizontalAlignment" => "Center"
                            )
                        ],
                        "schema" => "http://adaptivecards.io/schemas/adaptive-card.json",
                        "version" => "1.2",
                        "actions" => [
                            array(
                                "type" => "Action.OpenUrl",
                                "title" => "More info.",
                                "url" => $link,
                            )
                            
                        ]
                    )
                )
            ];

            $body = [
                'toPersonEmail' => $email,
                'attachments' => $cards,
                "text" => "IT Asset and Inventory"    
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

    public function sendWebexMessage($email,$title,$message,$details=array(),$link){

        $httpClient = new Client(); 

        if($email && $message){

            $cards = [
                array(
                    "contentType" => "application/vnd.microsoft.card.adaptive",
                    "content" => array(
                        "type" => "AdaptiveCard",
                        "body" => [
                            array(
                            "type" => "ColumnSet",
                            "columns" => [
                                    array( 
                                    "type" => "Column",
                                    "items" => [
                                        array(
                                            "type" => "TextBlock",
                                            "text" =>  $message,
                                            "wrap" => true,
                                            "spacing" => "Small",
                                            "color" => "Light"
                                        ),
                                        array(
                                            "type" => "TextBlock",
                                            "weight" => "Bolder",
                                            "text" => $title,
                                            "wrap" => true,
                                            "color" => $title == 'Approved' ? "Good" : "Attention",
                                            "size" => "Large",
                                            "spacing" => "Small"
                                        )
                                    ],
                                    "width" => "stretch"
                                    )
                                ]
                            ),
                            array( 
                            "type" => "ColumnSet",
                            "columns" => [
                                array(
                                "type" => "Column",
                                "width" => 50,
                                "items" => [
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Request No. : " . $details->request_number,
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Ticket No. : " . $details->ticket_number,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Details : " . $details->details,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Location : " . $details->location ,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Date : " . date('Y-m-d'),
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                ]
                                ),
                            ],
                            "spacing" => "Padding",
                            "horizontalAlignment" => "Center"
                            )
                        ],
                        "schema" => "http://adaptivecards.io/schemas/adaptive-card.json",
                        "version" => "1.2",
                        "actions" => [
                            array(
                                "type" => "Action.OpenUrl",
                                "title" => $title == 'Approved' ? "For Acceptance" : "More info.",
                                "url" => $link,
                            )
                            
                        ]
                    )
                )
            ];

            $body = [
                'toPersonEmail' => $email,
                'attachments' => $cards,
                "text" => "IT Asset and Inventory"
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

    public function sendGroupWebexMessage($title,$message,$details=array(),$name,$link){

        $httpClient = new Client(); 

        if($message){

            $cards = [
                array(
                    "contentType" => "application/vnd.microsoft.card.adaptive",
                    "content" => array(
                        "type" => "AdaptiveCard",
                        "body" => [
                            array(
                            "type" => "ColumnSet",
                            "columns" => [
                                    array( 
                                    "type" => "Column",
                                    "items" => [
                                        array(
                                            "type" => "TextBlock",
                                            "text" =>  $message,
                                            "wrap" => true,
                                            "spacing" => "Small",
                                            "color" => "Light"
                                        ),
                                        array(
                                            "type" => "TextBlock",
                                            "weight" => "Bolder",
                                            "text" => $title,
                                            "wrap" => true,
                                            "color" => "Good",
                                            "size" => "Large",
                                            "spacing" => "Small"
                                        )
                                    ],
                                    "width" => "stretch"
                                    )
                                ]
                            ),
                            array( 
                            "type" => "ColumnSet",
                            "columns" => [
                                array(
                                "type" => "Column",
                                "width" => 50,
                                "items" => [
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Name : " . $name,
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Request No. : " . $details->request_number,
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Ticket No. : " . $details->ticket_number,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Details : " . $details->details,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Location : " . $details->location ,
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Date : " . date('Y-m-d'),
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                ]
                                ),
                            ],
                            "spacing" => "Padding",
                            "horizontalAlignment" => "Center"
                            )
                        ],
                        "schema" => "http://adaptivecards.io/schemas/adaptive-card.json",
                        "version" => "1.2",
                        "actions" => [
                            array(
                                "type" => "Action.OpenUrl",
                                "title" => "View Request",
                                "url" => $link,
                            )
                            
                        ]
                    )
                )
            ];
            $body = [
                //prod
                'roomId' => 'Y2lzY29zcGFyazovL3VzL1JPT00vZWVhMjQ2NDAtZWI4Ni0xMWVjLTg1OTMtODdlYjc0NGY4OWQ5',
                "markdown" => "IT Asset and Inventory",
                'attachments' => $cards,
                "text" => "IT Asset and Inventory"    
            ];

            try{
                $response = $httpClient->post(
                    'https://api.ciscospark.com/v1/messages',
                    [
                        RequestOptions::BODY => json_encode($body),
                        RequestOptions::HEADERS => [
                            'Content-Type' => 'application/json',
                            //prod
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
