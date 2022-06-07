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
                $letter_of_undertaking = LetterOfUndertaking::where('user_inventory_id',$request->user_inventory_id)->first();
                return $response = [
                    'status'=>'saved',
                    'letter_of_undertaking'=>$letter_of_undertaking
                ];
            }else{
                LetterOfUndertaking::create($letter_of_undertaking_data);
                DB::commit();

                $letter_of_undertaking = LetterOfUndertaking::where('user_inventory_id',$request->user_inventory_id)->first();
                return $response = [
                    'status'=>'saved',
                    'letter_of_undertaking'=>$letter_of_undertaking
                ];
            }



        }
        catch (Exception $e) {
            DB::rollBack();
            return 'error';
        } 

    }


    // Print Letter of Undertaking
    public function printGenerateLetterOfUndertaking(Request $request){

        $letter_of_undertaking = LetterOfUndertaking::where('id',$request->id)
                                                        ->with('user_inventory.inventory_info.setting_location','user_inventory.employee_info.companies')
                                                        ->first();
        if($letter_of_undertaking){
            $pdf = new Fpdi();
            $pdf->AddPage('P','A4');

            if($letter_of_undertaking->user_inventory){

                $inventory_info = $letter_of_undertaking->user_inventory->inventory_info;
                $employee_info = $letter_of_undertaking->user_inventory->employee_info;
                //Laptop
                if($inventory_info->type == 'Laptop'){
                    $pdf->setSourceFile("letter_of_undertaking_template/Laptop.pdf");
                    $tplIdx = $pdf->importPage(1);
                    $pdf->useTemplate($tplIdx, 0, 0, 210);

                    $pdf->SetMargins(0, 0, 0, 0);
                    $pdf->SetAutoPageBreak(0, 0);

                    $full_name = ucfirst($employee_info->first_name) . ' ' . ucfirst($employee_info->last_name);
                    $pdf->SetXY(74,39);
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->MultiCell(59,3, ucwords($full_name) ,0,'C');

                    $company = $employee_info->companies[0]->name;
                    $pdf->SetXY(12,46);
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->MultiCell(95,3, ucwords($company) ,0,'C');

                    $address = $inventory_info->setting_location->address;
                    $pdf->SetXY(47,53);
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->MultiCell(150,3, ucwords($address) ,0,'C');

                    //Computer Identification
                        $pdf->SetXY(24.7,172.5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(30.8,5, "Laptop" ,1,'C');
                        
                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,172.5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(47.9,5, $inventory_info->model . '' ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,172.5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(46.4,5, $inventory_info->serial_number ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,172.5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(35.5,5, $inventory_info->id ,1,'C');

                        $pdf->SetXY(24.7,172.5 + 5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(30.8,5, "Processor" ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,172.5 + 5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(47.9,5, $inventory_info->processor ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,172.5 + 5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(46.4,5, "" ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,172.5 + 5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(35.5,5, "" ,1,'C');
                   
                    //Operating System Installation:
                        $pdf->SetXY(24.7,200);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(160.5,5, $inventory_info->os_name_and_version ,1,'C');

                    //CONFORME
                        $pdf->SetXY(24.7,235);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->MultiCell(50.5,5, $full_name ,0,'C');

                        $pdf->SetXY(140,235);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->MultiCell(50.5,5, date('Y-m-d') ,0,'C');
                }else{
                    $pdf->SetXY(10,100);
                    $pdf->SetFont('Arial', '', 50);
                    $pdf->MultiCell(190,3, "Not Available" ,0,'C');
                }


                $pdf->Output('I', $request->id . '.pdf');
                exit();
            }
            
            
        }else{
            return redirect('/inventories');
        }

    }



}
