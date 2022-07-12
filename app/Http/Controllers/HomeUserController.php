<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserInventory;
use App\UserBorrowRequest;
use App\UserBorrowRequestItem;
use App\UserReturnRequest;
use App\UserReturnRequestItem;
use Auth;
use DB;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;

class HomeUserController extends Controller
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

    public function index(){
        session([
            'title' => 'Assign/Borrowed',
        ]);
        return view('pages.home_user.index');
    }

    public function indexData(Request $request){
        $user = User::select('id','name','email')->with('employee')->where('id',Auth::user()->id)->first();
        if($user){
            return $user_inventories = UserInventory::with('inventory_info')
                                                        ->where('employee_id',$user->employee->id)
                                                        ->orderBy('created_at','DESC')
                                                        ->get();
        }

    }
    //Borrow Request
    public function homeBorrowRequest(){
        session([
            'title' => 'Borrowed Requests',
        ]);
        return view('pages.home_user.borrowed_request');
    }
    
    public function homeBorrowRequestData(){
        return $borrow_requests = UserBorrowRequest::where('user_id',Auth::user()->id)->orderBy('created_at','DESC')->get();
    }

    public function borrowedRequestStore(Request $request){

        $user = User::select('id','name','email')->with('employee')->where('id',Auth::user()->id)->first();

        $this->validate($request, [
            'ticket_number' => 'required',
            'details' => 'required',
            'location' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['user_id'] = $user->id;
            $data['employee_id'] = $user->employee->id;
            $data['status'] = "For Approval";
            if($borrow_request = UserBorrowRequest::create($data)){
                DB::commit();

                //Generate Request No. (ITB + User ID + YearMonth + Primary ID)
                $request_number = "ITB" . Auth::user()->id . '' . date('Ym') . str_pad($borrow_request->id, 4, '0', STR_PAD_LEFT);
                UserBorrowRequest::where('id',$borrow_request->id)->update(['request_number'=>$request_number]);

                //Send Notification
                $borrow_request = UserBorrowRequest::where('id',$borrow_request->id)->first();
                $message = 'New borrow request has been received. Please check details below. Thank you.';
                $name = $user->employee->first_name . ' ' . $user->employee->last_name;
                $link = 'https://10.96.4.168:8676/borrow-requests?request_number='. $borrow_request->request_number;
                $send_webex = $this->sendGroupWebexMessageBorrow('Borrow Request',$message,$borrow_request,$name,$link);

                //Send Notification to User
                $message_to_user = 'Hi! '.$user->employee->first_name. ' your IT asset request has been received. We will check and validate this request. Thank you' ;
                $link_user = 'https://10.96.4.168:8676/home-borrow-requests?request_number=' . $borrow_request->request_number;
                $send_webex_to_user = $this->sendWebexMessage($user->email,'Borrow Request',$message_to_user,$borrow_request,$link_user);
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

    public function borrowedRequestUpdate(Request $request){

        $this->validate($request, [
            'ticket_number' => 'required',
            'details' => 'required',
            'location' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            
            $borrow_request = UserBorrowRequest::where('id',$request->id)->first();

            if($borrow_request){
                $borrow_request->update($data);
                DB::commit();
                
                $borrow_request = UserBorrowRequest::where('id',$request->id)->first();
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

    public function borrowedRequestDelete(Request $request){

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            $borrow_request = UserBorrowRequest::where('id',$request->id)->first();

            if($borrow_request){
                $borrow_request->delete();
                DB::commit();
                return $status_data = [
                    'status'=>'success'
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

    public function homeReturnRequest(){
        session([
            'title' => 'Return Requests',
        ]);
        return view('pages.home_user.return_request');
    }

    public function homeReturnRequestData(Request $request){
        $user = User::select('id','name','email')->with('employee')->where('id',Auth::user()->id)->first();
        if($user){
            return $return_requests = UserReturnRequest::with('return_request_items.user_inventory.inventory_info')
                                                    ->where('user_id',Auth::user()->id)
                                                    ->orderBy('created_at','DESC')
                                                    ->get();
        }
    }

    public function homeReturnRequestAssignedItems(){
        $user = User::select('id','name','email')->with('employee')->where('id',Auth::user()->id)->first();
        if($user){
            return $user_inventories = UserInventory::with('inventory_info','is_return_for_checking')
                                                        ->doesnthave('is_return_for_checking')
                                                        ->where('employee_id',$user->employee->id)
                                                        ->where('status','Borrowed')
                                                        ->orderBy('created_at','DESC')
                                                        ->get();
        }
    }

    public function returnedRequestStore(Request $request){
        $user = User::select('id','name','email')->with('employee')->where('id',Auth::user()->id)->first();

        $this->validate($request, [
            'details' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['user_id'] = $user->id;
            $data['employee_id'] = $user->employee->id;
            $data['return_date'] = date('Y-m-d');
            $data['status'] = 'For Checking';
            unset($data['return_request_items']);

            if($return_request = UserReturnRequest::create($data)){

                $return_request_items = json_decode($request->return_request_items, true);

                if(count($return_request_items) > 0){
                    foreach($return_request_items as $item){
                        $newData = [
                            'user_return_request_id' => $return_request->id,
                            'user_inventory_id' => $item['id'],
                            'status' => 'For Checking',
                        ];
                        UserReturnRequestItem::create($newData);
                    }
                }

                DB::commit();

                //Generate Request No. (ITR + User ID + YearMonth + Primary ID)
                $request_number = "ITR" . Auth::user()->id . '' . date('Ym') . str_pad($return_request->id, 4, '0', STR_PAD_LEFT);
                UserReturnRequest::where('id',$return_request->id)->update(['request_number'=>$request_number]);

                //Send Notification
                $return_request = UserReturnRequest::with('return_request_items')->where('id',$return_request->id)->first();
                $message = 'New return request has been received. Please check details below. Thank you.';
                $name = $user->employee->first_name . ' ' . $user->employee->last_name;
                $link = 'https://10.96.4.168:8676/return-requests?request_number='. $return_request->request_number;
                $send_webex = $this->sendGroupWebexMessageReturn('Return Request',$message,$return_request,$name,$link);

                return $response = [
                    'status'=>'success'
                ];

            }
        }
        catch (Exception $e) {
            DB::rollBack();
            return 'error';
        } 
    }

    public function returnedRequestDelete(Request $request){

        DB::beginTransaction();
        try {
            $data = $request->all();
            $borrow_request = UserReturnRequest::where('id',$request->id)->first();
            if($borrow_request){
                $borrow_request_item = UserReturnRequestItem::where('user_return_request_id',$request->id)->delete();
                $borrow_request->delete();
                DB::commit();
                return $status_data = [
                    'status'=>'success'
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
    public function returnedRequestitemDelete(Request $request){

        DB::beginTransaction();
        try {
            $data = $request->all();
            $borrow_request_item = UserReturnRequestItem::where('id',$request->id)->first();
            if($borrow_request_item){
                $borrow_request_item->delete();

                DB::commit();
                
                $return_requests = UserReturnRequest::with('return_request_items.user_inventory.inventory_info')
                                                    ->where('id',$request->return_request_id)
                                                    ->orderBy('created_at','DESC')
                                                    ->get();

                return $status_data = [
                    'status'=>'success',
                    'return_requests'=>$return_requests,
                    
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

    public function sendGroupWebexMessageBorrow($title,$message,$details=array(),$name,$link){

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
                                            "color" => "Attention",
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

    public function sendWebexMessage($email,$title,$message,$details=array(),$link){

        $httpClient = new Client(); 
        $cards = $this->cards($title,$message,$details,$link);
        if($email && $message){
            $body = [
                "markdown" => "IT Asset and Inventory",
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

    public function cards($title,$message,$details=array(),$link){
        $data = [
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
                                    "text" => "This is an auto generated message. Please do not reply.",
                                    "weight" => "Lighter",
                                    "color" => "Light",
                                    "spacing" => "Medium",
                                    "wrap" => true,
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
        return $data;
 
    }

    public function sendGroupWebexMessageReturn($title,$message,$details=array(),$name,$link){

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
                                        "text" => "Items to return : " . count($details->return_request_items),
                                        "weight" => "Lighter",
                                        "color" => "Light",
                                        "spacing" => "Small"
                                    ),
                                    array(
                                        "type" => "TextBlock",
                                        "text" => "Details : " . $details->details,
                                        "color" => "Lighter",
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
