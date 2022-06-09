<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\UserInventory;
use App\LetterOfUndertaking;
use Auth;
use DB;
use Storage;

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

    public function saveLetterofUndertakingAttachment(Request $request){

        $data = $request->all();
        DB::beginTransaction();
        try{
            $letter_of_undertaking = LetterOfUndertaking::where('id',$data['letter_of_undertaking_id'])->first();
            if($letter_of_undertaking){
                if(isset($request->uploaded_scanned_file)){
                    if($request->file('uploaded_scanned_file')){
                        $uploaded_scanned_file = $request->file('uploaded_scanned_file');   
                        $file_name = $letter_of_undertaking->id . '.' . $uploaded_scanned_file->getClientOriginalExtension();
                        $path = Storage::disk('public')->putFileAs('letter_of_undertaking_attachments', $uploaded_scanned_file , $file_name);
                        $data['uploaded_scanned_file'] = $file_name;
                        unset($data['letter_of_undertaking_id']);
                        $letter_of_undertaking->update($data);
                        DB::commit();
                        return $response = [
                            'status'=>'saved'
                        ];
                    }    
                }
            }else{
                return $response = [
                    'status'=>'error'
                ];
            }
        }catch (Exception $e) {
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
                    $pdf->MultiCell(59,3, utf8_decode($full_name) ,0,'C');

                    $company = $employee_info->companies[0]->name;
                    $pdf->SetXY(12,46);
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->MultiCell(95,3, ucwords($company) ,0,'C');

                    $address = $inventory_info->setting_location ? $inventory_info->setting_location->address : "";
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
                        $pdf->Cell(47.9,5, utf8_decode($inventory_info->model) . '' ,1,'C');

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
                        $pdf->Cell(47.9 + 46.4 + 35.5,5, utf8_decode($inventory_info->processor) ,1,'C');
                   
                    //Operating System Installation:
                        $pdf->SetXY(24.7,200);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(160.7,5, utf8_decode($inventory_info->os_name_and_version) ,1,'C');

                    //CONFORME
                        $pdf->SetXY(24.7,235);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->MultiCell(50.5,5, $full_name ,0,'C');

                        $pdf->SetXY(140,235);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->MultiCell(50.5,5, date('Y-m-d') ,0,'C');

                    //Inclusions
                    if($letter_of_undertaking->inclusions){
                        $pdf->SetXY(10,270);
                        $pdf->SetFont('Arial', 'I', 8);
                        $pdf->MultiCell(50.5,5, 'INCLUSIONS: ' . $letter_of_undertaking->inclusions ,0,'C');
                    }
                }
                //Desktop NUC
                else if($inventory_info->type == 'Desktop'){
                    $pdf->setSourceFile("letter_of_undertaking_template/Desktop_NUC.pdf");
                    $tplIdx = $pdf->importPage(1);
                    $pdf->useTemplate($tplIdx, 0, 0, 210);

                    $pdf->SetMargins(0, 0, 0, 0);
                    $pdf->SetAutoPageBreak(0, 0);

                    $full_name = ucfirst($employee_info->first_name) . ' ' . ucfirst($employee_info->last_name);
                    $pdf->SetXY(74,39);
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->MultiCell(59,3, utf8_decode($full_name) ,0,'C');

                    $company = $employee_info->companies[0]->name;
                    $pdf->SetXY(12,46);
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->MultiCell(95,3, ucwords($company) ,0,'C');

                    $address = $inventory_info->setting_location ? $inventory_info->setting_location->address : "";
                    $pdf->SetXY(47,53);
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->MultiCell(150,3, ucwords($address) ,0,'C');

                    //Computer Identification
                        $pdf->SetXY(24.7,168);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(30.8,5, "Desktop" ,1,'C');
                        
                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,168);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(47.9,5, utf8_decode($inventory_info->model) . '' ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,168);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(46.4,5, $inventory_info->serial_number ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,168);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(35.5,5, $inventory_info->id ,1,'C');

                        $pdf->SetXY(24.7,168 + 5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(30.8,5, "Processor" ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,168 + 5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(47.9 + 46.4 + 35.5,5, utf8_decode($inventory_info->processor) ,1,'C');
                   
                    //Operating System Installation:
                        $pdf->SetXY(24.7,195.6);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(160.7,5, utf8_decode($inventory_info->os_name_and_version) ,1,'C');

                    //CONFORME
                        $pdf->SetXY(24.7,225);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->MultiCell(50.5,5, $full_name ,0,'C');

                        $pdf->SetXY(140,225);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->MultiCell(50.5,5, date('Y-m-d') ,0,'C');

                        //Inclusions
                        if($letter_of_undertaking->inclusions){
                            $pdf->SetXY(1,260);
                            $pdf->SetFont('Arial', 'I', 8);
                            $pdf->MultiCell(50.5,5, 'INCLUSIONS: ' . $letter_of_undertaking->inclusions ,0,'C');
                        }
                }
                //Headset
                else if($inventory_info->type == 'Headset'){
                    $pdf->setSourceFile("letter_of_undertaking_template/Headset.pdf");
                    $tplIdx = $pdf->importPage(1);
                    $pdf->useTemplate($tplIdx, 0, 0, 210);

                    $pdf->SetMargins(0, 0, 0, 0);
                    $pdf->SetAutoPageBreak(0, 0);

                    $full_name = ucfirst($employee_info->first_name) . ' ' . ucfirst($employee_info->last_name);
                    $pdf->SetXY(77.5,19.5);
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->MultiCell(59,3, utf8_decode($full_name) ,0,'C');

                    $address = $inventory_info->setting_location ? $inventory_info->setting_location->address : "";
                    $pdf->SetXY(32,24);
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->MultiCell(166,3, ucwords($address) ,0,'C');

                    //Computer Identification
                        $pdf->SetXY(24.7,97);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(30.8,5, "Headset" ,1,'C');
                        
                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,97);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(47.9,5, utf8_decode($inventory_info->model) . '' ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,97);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(46.4,5, $inventory_info->serial_number ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,97);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(35.5,5, $inventory_info->id ,1,'C');

                        $pdf->SetXY(24.7,97 + 5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(30.8,5, "Processor" ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,97 + 5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(47.9 + 46.4 + 35.5,5, utf8_decode($inventory_info->processor) ,1,'C');
                   
                    //Operating System Installation:
                        $pdf->SetXY(25.4,120.6);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(160.3,5, utf8_decode($inventory_info->os_name_and_version) ,1,'C');

                    //CONFORME
                        $pdf->SetXY(24.7,151);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->MultiCell(50.5,5, $full_name ,0,'C');

                        $pdf->SetXY(140,151);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->MultiCell(50.5,5, date('Y-m-d') ,0,'C');
                    
                    //Inclusions
                    if($letter_of_undertaking->inclusions){
                        $pdf->SetXY(1,171);
                        $pdf->SetFont('Arial', 'I', 8);
                        $pdf->MultiCell(50.5,5, 'INCLUSIONS: ' . $letter_of_undertaking->inclusions ,0,'C');
                    }
                    
                }
                //Portable Printer
                if($inventory_info->type == 'Portable Printer'){
                    $pdf->setSourceFile("letter_of_undertaking_template/Portable_Printer.pdf");
                    $tplIdx = $pdf->importPage(1);
                    $pdf->useTemplate($tplIdx, 0, 0, 210);

                    $pdf->SetMargins(0, 0, 0, 0);
                    $pdf->SetAutoPageBreak(0, 0);

                    $full_name = ucfirst($employee_info->first_name) . ' ' . ucfirst($employee_info->last_name);
                    $pdf->SetXY(73,33);
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->MultiCell(59,3, utf8_decode($full_name) ,0,'C');

                    $company = $employee_info->companies[0]->name;
                    $pdf->SetXY(12,40);
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->MultiCell(76,3, ucwords($company) ,0,'C');

                    $address = $inventory_info->setting_location ? $inventory_info->setting_location->address : "";
                    $pdf->SetXY(33,47);
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->MultiCell(160,3, ucwords($address) ,0,'C');

                    //Computer Identification
                        $pdf->SetXY(24.7,157);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(30.8,5, "Portable Printer" ,1,'C');
                        
                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,157);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(47.9,5, utf8_decode($inventory_info->model) . '' ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,157);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(46.4,5, $inventory_info->serial_number ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,157);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(35.5,5, $inventory_info->id ,1,'C');

                        $pdf->SetXY(24.7,157 + 5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(30.8,5, "Processor" ,1,'C');

                        $current_x = $pdf->getx();
                        $pdf->SetXY($current_x,157 + 5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(47.9 + 46.4 + 35.5,5, utf8_decode($inventory_info->processor) ,1,'C');
                   
                    //Operating System Installation:
                        $pdf->SetXY(25.4,185.6);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(158.8,5, utf8_decode($inventory_info->os_name_and_version) ,1,'C');

                    //CONFORME
                        $pdf->SetXY(23,220);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->MultiCell(50.5,5, $full_name ,0,'C');

                        $pdf->SetXY(140,220);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->MultiCell(50.5,5, date('Y-m-d') ,0,'C');

                    //Inclusions
                    if($letter_of_undertaking->inclusions){
                        $pdf->SetXY(5,260);
                        $pdf->SetFont('Arial', 'I', 8);
                        $pdf->MultiCell(50.5,5, 'INCLUSIONS: ' . $letter_of_undertaking->inclusions ,0,'C');
                    }
                }
                else
                {
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
