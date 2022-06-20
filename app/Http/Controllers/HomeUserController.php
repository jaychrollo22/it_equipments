<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserInventory;
use App\UserBorrowRequest;
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

                //Generate Request No. (IT + User ID + YearMonth + Primary ID)
                $request_number = "IT" . Auth::user()->id . '' . date('Ym') . str_pad($borrow_request->id, 4, '0', STR_PAD_LEFT);
                UserBorrowRequest::where('id',$borrow_request->id)->update(['request_number'=>$request_number]);

                //Send Notification
                $borrow_request = UserBorrowRequest::where('id',$borrow_request->id)->first();
                $message = "<span><strong>New</strong> borrow request has been received. Please check details below. Thank you. </span>
                            <ul>
                                <li>Request No: ". $borrow_request->request_number."</li>
                                <li>Ticket No: ". $borrow_request->ticket_number."</li>
                                <li>Employee: ". $user->employee->first_name . ' ' . $user->employee->last_name ."</li>
                                <li>Details: ".$borrow_request->details."</li>
                                <li>Location: ".$borrow_request->location."</li>
                                <li>Date: ".date('Y-m-d')."</li>
                            </ul>
                            <p>Link : http://10.96.4.168:8676/borrow-requests</p>
                            <hr>";
                $send_webex = $this->sendGroupWebexMessage($message);

                $message_to_user = "<span>Hi ".$user->employee->first_name.", your IT asset request has been received. We will check and validate this request. Thank you.</span>
                            <ul>
                                <li>Request No: ". $borrow_request->request_number."</li>
                                <li>Ticket No: ". $borrow_request->ticket_number."</li>
                                <li>Details: ".$borrow_request->details."</li>
                                <li>Location: ".$borrow_request->location."</li>
                                <li>Date: ".date('Y-m-d')."</li>
                            </ul>
                            <p>Link : http://10.96.4.168:8676/login</p>
                            <small><i>This is an auto generated message. Please do not reply.</i></small>
                            <hr>";

                //Send Notification to User
                $send_webex_to_user = $this->sendWebexMessage($user->email,$message_to_user);
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

    public function sendGroupWebexMessage($message){

        $httpClient = new Client(); 

        if($message){
            $body = [
                //prod
                'roomId' => 'Y2lzY29zcGFyazovL3VzL1JPT00vZWVhMjQ2NDAtZWI4Ni0xMWVjLTg1OTMtODdlYjc0NGY4OWQ5',
                'html' => $message
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
