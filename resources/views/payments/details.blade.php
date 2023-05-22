<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment reciept</title>
    <style>
        body{
            background-color: #F6F6F6; 
            margin: 0;
            padding: 0;
        }
        h1,h2,h3,h4,h5,h6{
            margin: 0;
            padding: 0;
        }
        p{
            margin: 0;
            padding: 0;
        }
        .container{
            width: 80%;
            margin-right: auto;
            margin-left: auto;
        }
        .brand-section{
           background-color: #0d1033;
           padding: 10px 40px;
        }
        .logo{
            width: 50%;
        }

        .row{
            display: flex;
            flex-wrap: wrap;
        }
        .col-6{
            width: 50%;
            flex: 0 0 auto;
        }
        .text-white{
            color: #fff;
        }
        .company-details{
            float: right;
            text-align: right;
        }
        .body-section{
            padding: 16px;
            border: 1px solid gray;
        }
        .heading{
            font-size: 20px;
            margin-bottom: 08px;
        }
        .sub-heading{
            color: #262626;
            margin-bottom: 05px;
        }
        table{
            background-color: #fff;
            width: 100%;
            border-collapse: collapse;
        }
        table thead tr{
            border: 1px solid #111;
            background-color: #f2f2f2;
        }
        table td {
            vertical-align: middle !important;
            text-align: center;
        }
        table th, table td {
            padding-top: 08px;
            padding-bottom: 08px;
        }
        .table-bordered{
            box-shadow: 0px 0px 5px 0.5px gray;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #dee2e6;
        }
        .text-right{
            text-align: end;
        }
        .w-20{
            width: 20%;
        }
        .float-right{
            float: right;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="brand-section">
            <div class="row">
                <div class="col-6">
                    <h1 class="text-white">CAMPUS</h1>
                    <h3 class="text-white">{{$payment->collegeName}}</h3>
                </div>
                <div class="col-6">
                    <div class="company-details">
                        <p class="text-white">{{$payment->collegeName}}</p>
                        <p class="text-white">{{$payment->courseName}}</p>
                        <p class="text-white">{{$payment->clgEmail}}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="body-section">
            <div class="row">
                <div class="col-6">
                    <p class="sub-heading">Reciept Id. : {{$payment->id}} </p>
                    <h2 class="heading">Payment Id.: {{$payment->payment_id}}</h2>
                    <p class="sub-heading">Payment Date:  {{$payment->created_at}} </p>
                    <p class="sub-heading">Email Address:{{$payment->userEmail}} </p>
                </div>
                <div class="col-6">
                    <p class="sub-heading">Full Name: {{$payment->name}}  </p>
                    <!-- <p class="sub-heading">Address: {{$payment->address}} </p> -->
                    <p class="sub-heading">Phone Number: {{$payment->phon_no}}  </p>
                    <!-- <p class="sub-heading">City,State,Pincode:  </p> -->
                </div>
            </div>
        </div>

        <div class="body-section">
            <!-- <h3 class="heading">Ordered Items</h3> -->
            <br>
            <table class="table-bordered">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th class="w-20">Fees Type</th>
                        <th class="w-20">Amount</th>
                        <!-- <th class="w-20">Grandtotal</th> -->
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{$payment->courseName}} </td>
                        <td>{{$payment->fees_type}}</td>
                        <td>{{$payment->amount}}</td>
                        <!-- <td>10</td> -->
                    </tr>
                    <!-- <tr>
                        <td colspan="3" class="text-right">Sub Total</td>
                        <td> 10.XX</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right">Tax Total %1X</td>
                        <td> 2</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right">Grand Total</td>
                        <td> 12.XX</td>
                    </tr> -->
                </tbody>
            </table>
            <br>
            <h3 class="heading">Payment Status: {{$payment->payment_status}}</h3>
            <!-- <h3 class="heading">Payment Mode: Cash on Delivery</h3> -->
        </div>

        <div class="body-section">
            <p>&copy; Copyright 2021 - NIC All rights reserved. 
              
            </p>
        </div>      
    </div>      

</body>
</html>