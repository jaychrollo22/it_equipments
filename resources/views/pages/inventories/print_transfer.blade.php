<head>
    <title> {{ $transfer_data ? $transfer_data->transfer_code : "Print Transfer" }} | IT ASSET & INVENTORY </title>
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
                <td align="center">LFIT – F – 012</td>
                <td align="center">Rev. No. 00</td>
                <td align="center">Effective Date: </td>
                <td align="center">{{ $transfer_data ? $transfer_data->effective_date : ""}}</td>
            </tr>
            <tr>
                <td colspan='3'>
                    <strong style='margin-left:10px'>TRANSFER ASSET</strong> 
                </td>
                <td align="center">
                    Transfer No.:<strong>{{ $transfer_data ? $transfer_data->transfer_code : ""}}</strong> 
                </td>
            </tr>
        </table>
    </header>
    <main>
        <table width='100%' border='1' style="margin-top:20px;">
            <tr>
                <td>Requested Name : {{ $transfer_data ? $transfer_data->requested_by_info->name : "" }}</td>
                <td>Date Requested : {{ $transfer_data ? $transfer_data->date_requested : ""}}</td>
            </tr>
            <tr>
                <td>Department : {{ $transfer_data ? $transfer_data->transfer_department : ""}}</td>
                <td>Local No. : {{ $transfer_data ? $transfer_data->local_number : ""}}</td>
            </tr>
            <tr>
                <td>Company : {{ $transfer_data ? $transfer_data->transfer_company : ""}}</td>
                <td> Date of Transfer : {{ $transfer_data ? $transfer_data->date_of_transfer : ""}}</td>
            </tr>
        </table>

        <table width='100%' border='1' style="margin-top:10px;">
            <tr>
                <td align="center"><b>QTY</b></td>
                <td align="center"><b>Asset Description</b></td>
                <td align="center"><b>S/N</b></td>
                <td align="center"><b>(From) Location</b></td>
                <td align="center"><b>(To) New Location</b></td>
            </tr>
            
            @if($transfer_data)
                @foreach($transfer_data->inventory_transfer_items as $item)
                    <tr>
                        <td align="center">1</td>
                        <td>{{$item['inventory_info']['type'] . ' (' . $item['inventory_info']['model'] . ')' }}</td>
                        <td align="center">{{$item['inventory_info']['serial_number'] }}</td>
                        <td align="center">{{$item['location_from']}}</td>
                        <td align="center">{{$item['location_to']}}</td>
                    </tr>
                @endforeach
            
                <tr>
                    <td colspan="5">
                        <h3 style="margin-left:5px;">REMARKS</h3>
                        <p style="margin-left:10px;"> {{$transfer_data->remarks}}</p>
                    </td>
                </tr>
           
                <tr>
                    <td align="center">
                        @if($transfer_data->requested_by_info)
                            <u>{{ $transfer_data->requested_by_info->name }}</u> 
                        @else 
                            _____________
                        @endif
                        <br>
                        <b>Requested By</b>
                    </td>
                    <td align="center">
                        <br>
                        <u>_____________</u> <br>
                        <b>Approved By</b><br>
                        <b>(Dept. Head)</b>
                    </td>
                    <td align="center">
                        <u>_____________</u> <br>
                        <b>Carried By</b>
                    </td>
                    <td align="center">
                        <br>
                        <u>DONNABELLE PASQUIN</u> <br>
                        <b>Approved By</b><br>
                        <b>(IT Head)</b>
                    </td>
                    <td align="center">
                        @if($transfer_data->received_by_info)
                            <u>{{ $transfer_data->received_by_info->name }}</u> 
                        @else 
                            _____________
                        @endif
                        <br>
                        <b>Received By</b>
                    </td>
                </tr>
            @endif
        </table>

    </main>

</body>