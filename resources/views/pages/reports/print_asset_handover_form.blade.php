<head>
    <title> Asset Handover Form | IT Equipments </title>
    <style>
        @page { 
            margin: 110px 40px;
        }
        header {
            position: fixed;
            top: -50px;
            left: 0px;
            right: 0px;
            height: 100px;
            text-align: center;
        }
        table { 
            border-spacing: 0;
            border-collapse: collapse;
        }
        
        body{
            font-family: "Calibri", Helvetica, sans-serif;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <header>
        <table width='100%' border='1'>
            <tr>
                <td rowspan='3' width='50px'>
                    <img src='http://10.96.4.126:8668/img/front-logo.jpg' width='30px' style='padding:10px;'>
                </td>
                <td colspan='4'>
                    <strong style='margin-left:10px'>La Filipina Uy Gongco Group of Companies</strong>
                </td>
            </tr>
            <tr>
                <td align="center">Doc. No. LFIT-F-009</td>
                <td align="center">Rev. No. 00</td>
                <td align="center">Effective Date: </td>
                <td align="center"></td>
            </tr>
            <tr>
                <td colspan='4' height="30px">
                    <strong style='margin-left:10px'>ASSET HANDOVER FORM</strong> 
                </td>
            </tr>
        </table>
    </header>
    <main>
        <table width='100%' border='1' style="margin-top:20px;">
            <tr>
                <td>Name of Employee : {{$asset_handover_form->employee_info->first_name . ' ' . $asset_handover_form->employee_info->last_name }}</td>
                <td>Handover Date : {{$asset_handover_form->hof_date}}</td>
            </tr>
            <tr>
                <td>Department : {{$asset_handover_form->employee_info->departments[0]->name}}</td>
                <td>Local No. : </td>
            </tr>
            <tr>
                <td>Company : {{$asset_handover_form->employee_info->companies[0]->company_abbreviation}}</td>
                <td> </td>
            </tr>
            <tr>
                <td colspan="2" height="15px"><i>Please find below as the assets handed over to you as requested in carrying assigned work.</i> </td>
            </tr>
        </main>
    </table>

    <table width='100%' border='1' style="margin-top:10px;">
        <tr>
            <td align="center"><b>QTY</b></td>
            <td align="center"><b>Asset Description</b></td>
            <td align="center"><b>Serial No.</b></td>
            <td align="center"><b>Location</b></td>
            <td align="center"><b>Remarks</b></td>
        </tr>

        @foreach($asset_handover_form->details as $item)
            <tr>
                <td align="center">1</td>
                <td>{{$item['type'] . ' (' . $item['model'] . ')' }}</td>
                <td align="center">{{$item['serial_number'] }}</td>
                <td align="center">{{$item['location']}}</td>
                <td align="center">{{$item['remarks']}}</td>
            </tr>
        @endforeach
    </table>

    <table width='100%' border='1' style="margin-top:10px;">
        <tr>
            <td align="center">
                <br>
                <u>{{$requested_by}}</u> <br>
                <b>Requested By</b>
            </td>
            <td align="center">
                <br>
                <u>DONNABELLE PASQUIN</u> <br>
                <b>Approved By</b><br>
            </td>
            <td align="center">
                <u>_____________</u> <br>
                <b>Carried By</b>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <strong>ACKNOWLEDGEMENT AND DECLARATION BY HANDLER</strong> <br> <br>
                <i>I hereby acknowledged that I have received the above mentioned assets. I understant that this asset bellongs to LFUGGOC and is under my possession as requesting the unit for requirements. I hereby assure that I will take responsibility of the assets of the company to the best possible extend.</i>
                <br><br>
                <div align="center">
                    <b>Received by:</b><br>
                    <u>{{$asset_handover_form->employee_info->first_name . ' ' . $asset_handover_form->employee_info->last_name }}</u><br>
                    <p>(Employee Signature)</p>
                </div>
                
            </td>
        </tr>
    </table>
</body>
