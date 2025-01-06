<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Estado de cuenta</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.png') }}"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{{asset('css/pdf.css')}}">
    <style>
        table {
            border-collapse: separate;
            border-spacing: 1mm;
        }
        .borderless {
        /* table, tr, td, th { */
            border-width: 0px!important;
            border-style: hidden; 
            border-color: transparent;
            /* border: none!important; */
        }
        th, td, tr {
            /* padding: 0!important;
            margin: 0!important; */
            vertical-align: middle!important;
        }

        .black-bg {
            /* margin: 20mm!important; */
            color: white!important;
            /* border-width: 3px!important;
            border-style: solid!important; 
            border-color: white!important; */
            background-color: black;
        }

        .gray-bg {
            /* margin: 20mm!important; */
            /* border-width: 3px!important;
            border-style: solid!important; 
            border-color: white!important; */
            background-color: #D8D8D8!important;
        }

        .white-bg {
            /* margin: 20mm!important; */
            border-width: 1px!important;
            border-style: solid!important; 
            border-color: black!important;
            background-color: white!important;
        }
        
        .danger-text {
            color: red!important;
        }
    </style>
	{{-- <link rel="stylesheet" type="text/css" href="{{asset('css/atmos.css')}}"> --}}
</head>
<div class="fixed-top" style="text-align: left;">
	<img class="logo" src="{{asset('img/logo_completo.png')}}">
</div>
{{-- <div class="fixed-middle">
	<img class="water-mark" src="{{asset('img/fa_icon.png')}}">
</div> --}}
<div class="start-page av-font">
    <br>
    <table id="" class="borderless uppercase" style="width: 100%;">
        <thead>
            <tr class="">
                <th class="borderless" style="width:70%; text-align: left;">ESTADO DE CUENTA</th>
                <th class="borderless" style="width:30%; text-align: right;">{{
                    strftime('%d', strtotime( date('Y-m-d H:i:s') )).' de '.
                    strftime('%B', strtotime( date('Y-m-d H:i:s') )). ' del '.
                    strftime('%Y', strtotime( date('Y-m-d H:i:s') ))
                }}</th>
            </tr>
        </thead>
    </table>

    {{-- <br> --}}
    <table id="" class="" style="width: 100%;">
        <tbody class="">
            <tr>
                <td scope="col" class="black-bg" style="width:20%; text-align: center;">Cliente</td>
                <td scope="col" class="gray-bg uppercase" style="width:30%; text-align: center;">{{$property->owner->fullname}}</td>
                <td scope="col" class="black-bg" style="width:20%; text-align: center;">Precio lista</td>
                <td scope="col" class="gray-bg uppercase" style="width:30%; text-align: center;">${{number_format($property->price, 2)}}</td>
            </tr>
            <tr>
                <td scope="col" class="white-bg" style="width:20%; text-align: center;">Unidad</td>
                <td scope="col" class="white-bg uppercase" style="width:30%; text-align: center;">L-065</td>
                <td scope="col" class="white-bg" style="width:20%; text-align: center;">Porcentaje descuento</td>
                <td scope="col" class="white-bg uppercase" style="width:30%; text-align: center;">{{number_format(0.00, 2)}}%</td>
            </tr>
            <tr>
                <td scope="col" class="black-bg" style="width:20%; text-align: center;">Desarrollo</td>
                <td scope="col" class="gray-bg uppercase" style="width:30%; text-align: center;">{{$property->project->name}}</td>
                <td scope="col" class="black-bg" style="width:20%; text-align: center;">Cantidad descuento</td>
                <td scope="col" class="gray-bg uppercase" style="width:30%; text-align: center;">${{number_format(0.00, 2)}}</td>
            </tr>
            <tr>
                <td scope="col" class="white-bg" style="width:20%; text-align: center;">Plazo</td>
                <td scope="col" class="white-bg uppercase" style="width:30%; text-align: center;">Regular a 48 meses</td>
                {{-- <td scope="col" class="white-bg uppercase" style="width:30%; text-align: center;">Ciudad deportiva SC 20,21 y 23 Act. Regular a 48 meses</td> --}}
                <td scope="col" class="white-bg" style="width:20%; text-align: center;">Total venta</td>
                <td scope="col" class="white-bg uppercase" style="width:30%; text-align: center;">${{number_format($property->price, 2)}}</td>
            </tr>
            <tr>
                {{-- <td scope="col" class="black-bg" style="width:20%; text-align: center;">MT2</td>
                <td scope="col" class="gray-bg uppercase" style="width:30%; text-align: center;">N/A</td> --}}
                <td scope="col" class="black-bg" style="width:20%; text-align: center;">Total pagado</td>
                <td scope="col" class="gray-bg uppercase" style="width:30%; text-align: center;">${{number_format($property->payments->sum('amount'), 2)}}</td>
                <td scope="col" class="black-bg" style="width:20%; text-align: center;">Total por pagar</td>
                <td scope="col" class="gray-bg uppercase" style="width:30%; text-align: center;">${{ number_format( ($property->price - $property->payments->sum('amount') ), 2) }}</td>
            </tr>
            <tr>
                {{-- <td scope="col" class="white-bg" style="width:20%; text-align: center;">Precio MT2</td>
                <td scope="col" class="white-bg uppercase" style="width:30%; text-align: center;">N/A</td> --}}
                {{-- <td scope="col" class="white-bg uppercase" style="width:30%; text-align: center;">Ciudad deportiva SC 20,21 y 23 Act. Regular a 48 meses</td> --}}
            </tr>
            <tr>
                <td scope="col" class="" style="width:20%; text-align: center;"></td>
                <td scope="col" class="" style="width:30%; text-align: center;"></td>
                <td scope="col" class="white-bg" style="width:20%; text-align: center;">Total vencido</td>
                <td scope="col" class="white-bg uppercase" style="width:30%; text-align: center;">${{number_format($property->payments->where('payment_status_id', 4)->sum('amount'), 2)}}</td>
            </tr>
            <tr>
                <td scope="col" class="black-bg" style="width:20%; text-align: center;">Cuenta transferencias STP</td>
                <td scope="col" class="gray-bg uppercase" style="text-align: center;" colspan="3">{{@$property->owner->clabe}}</td>
            </tr>
            {{-- <tr>
                <td scope="col" class="black-bg" style="width:20%; text-align: center;">Referencia para depósitos</td>
                <td scope="col" class="gray-bg uppercase" style="text-align: center;" colspan="3"></td>
            </tr> --}}
        </tbody>
    </table>

    <br>
    <h3 class="uppercase left">Tabla de amortizaciones</h3>
    <br>
    <table id="" class="center" style="width: 100%;">
        <thead>
            <th class="black-bg" style="width: 5%;">No.</th>
            <th class="black-bg" style="width: 19%;">Fecha</th>
            <th class="black-bg" style="width: 19%;">Pago</th>
            <th class="black-bg" style="width: 19%;">Saldo</th>
            <th class="black-bg" style="width: 19%;">Tipo</th>
            <th class="black-bg" style="width: 19%;">Estatus</th>
        </thead>
        <tbody class="">
            @if( $property->pay_in_advance )
                <tr>
                    <td scope="col" class="white-bg" style="width:5%; text-align: center;">1</td>
                    <td scope="col" class="white-bg" style="width:19%; text-align: center;">N/A</td>
                    <td scope="col" class="white-bg" style="width:19%; text-align: center;">${{number_format($property->pay_in_advance,2)}}</td>
                    <td scope="col" class="white-bg" style="width:19%; text-align: center;">${{number_format(0.00,2)}}</td>
                    <td scope="col" class="white-bg" style="width:19%; text-align: center;">Enganche</td>
                    <td scope="col" class="white-bg" style="width:19%; text-align: center;">Pagado</td>
                </tr>
            @endif
            @foreach($property->installments->whereIn('installment_status_id', [1,3]) as $key => $installment)
                <tr>
                    <td scope="col" class="white-bg" style="width:5%; text-align: center;">{{ $counter }}</td>
                    <td scope="col" class="white-bg" style="width:19%; text-align: center;">{{$installment->date}}</td>
                    <td scope="col" class="white-bg" style="width:19%; text-align: center;">${{number_format($installment->amount,2)}}</td>
                    <td scope="col" class="white-bg" style="width:19%; text-align: center;">${{number_format($installment->amount - $installment->amount_paid,2)}}</td>
                    <td scope="col" class="white-bg" style="width:19%; text-align: center;">Mensualidad</td>
                    <td scope="col" class="white-bg {{$installment->status->id == 4 ? 'danger-text' : ''}}" style="width:19%; text-align: center;">{{$installment->status->name}}</td>
                </tr>
                @php
                    $counter ++;
                @endphp
            @endforeach
        </tbody>
    </table>
</div>
</html>