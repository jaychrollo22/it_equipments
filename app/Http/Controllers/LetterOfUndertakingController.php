<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\UserInventory;
use App\LetterOfUndertaking;
use Auth;
use DB;

use setasign\Fpdi\Fpdi;

class LetterOfUndertakingController extends Controller
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

    public function generateLetterOfUndertaking(Request $request){

        session([
            'title' => 'Letter of Undertaking'
        ]);

        $user_inventory = UserInventory::with('inventory_info','employee_info','letter_of_undertaking')
                                        ->where('id',$request->id)
                                        ->first();
        if($user_inventory){
            session([
                'generate_lou_user_inventory'=>$user_inventory
            ]);
            return view('pages.letter_of_undertaking.generate');
        }else{
            return redirect('/inventories');
        }
    }

    public function getLouUserInventory(){
        return session('generate_lou_user_inventory');
    }

    public function saveGenerateLetterOfUndertaking(Request $request){

        DB::beginTransaction();
        try {
            $data = $request->all();
        
            $letter_of_undertaking = LetterOfUndertaking::where('user_inventory_id',$request->user_inventory_id)->first();

            $letter_of_undertaking_data = [
                'user_inventory_id'=>$data['user_inventory_id'],
                'os_installations'=>$data['os_installations'],
                'inclusions'=>$data['inclusions'],
                'generated_by'=>Auth::user()->id
            ];

            

            if($letter_of_undertaking){
                $letter_of_undertaking->update($letter_of_undertaking_data);
                DB::commit();
                return $response = [
                    'status'=>'saved'
                ];
            }else{
                LetterOfUndertaking::create($letter_of_undertaking_data);
                DB::commit();
                return $response = [
                    'status'=>'saved'
                ];
            }



        }
        catch (Exception $e) {
            DB::rollBack();
            return 'error';
        } 

    }


    // Print Letter of Undertaking
    public function printGenerateLetterOfUndertakingLaptop(Request $request){

        $letter_of_undertaking = LetterOfUndertaking::where('id',$request->id)
                                                        ->with('user_inventory')
                                                        ->first();
        if($letter_of_undertaking){
            $pdf = new Fpdi();
            $pdf->AddPage('P','A4');
            $pdf->setSourceFile("letter_of_undertaking_template/Laptop.pdf");
            $tplIdx = $pdf->importPage(1);
            $pdf->useTemplate($tplIdx, 0, 0, 210);

            $pdf->SetMargins(0, 0, 0, 0);
            $pdf->SetAutoPageBreak(0, 0);

            $pdf->Output('I', $request->id . '.pdf');
            exit();
            
        }else{
            return redirect('/inventories');
        }

    }



}
