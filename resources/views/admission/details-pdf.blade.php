<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Acknowledgement</title>

    <style>
        html,
        body {
            margin: 10px;
            padding: 10px;
            font-family: sans-serif;
        }
        h1,h2,h3,h4,h5,h6,p,span,label {
            font-family: sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0px !important;
        }
        table thead th {
            height: 28px;
            text-align: left;
            font-size: 16px;
            font-family: sans-serif;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 14px;
        }

        .heading {
            font-size: 24px;
            margin-top: 12px;
            margin-bottom: 12px;
            font-family: sans-serif;
        }
        .small-heading {
            font-size: 18px;
            font-family: sans-serif;
        }
        .total-heading {
            font-size: 18px;
            font-weight: 700;
            font-family: sans-serif;
        }
        .order-details tbody tr td:nth-child(1) {
            width: 20%;
        }
        .order-details tbody tr td:nth-child(3) {
            width: 20%;
        }

        .text-start {
            text-align: left;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .company-data span {
            margin-bottom: 4px;
            display: inline-block;
            font-family: sans-serif;
            font-size: 14px;
            font-weight: 400;
        }
        .no-border {
            border: 1px solid #fff !important;
        }
        .bg-blue {
            background-color: #414ab1;
            color: #fff;
        },
     
    </style>
</head>
<body>

    <table class="order-details">
        <thead>
            <tr>
                <th width="50%" colspan="2">
                    <h2 class="text-start">CAMPUS</h2>
                    <h4 class="text-start">{{$admission->collegeName}}</h4>
                </th>
                <th width="50%" colspan="2" class="text-end company-data">
                    <span>Application Id: #{{$admission->id}}</span> <br>
                    <!-- <span>Date: 24 / 09 / 2022</span> <br> -->
                    <span>Zip code : 785614</span> <br>
                    <span>college Address:  {{$admission->clgAdress}}</span> <br>
                </th>
            </tr>
            <tr class="bg-blue">
                <th width="50%" colspan="2">Application Details</th>
                <th width="50%" colspan="2">Student Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Application Id:</td>
                <td> #{{$admission->id}}</td>

                <td>Full Name:</td>
                <td>{{$admission->first_name}} {{$admission->middle_name}} {{$admission->last_name}} </td>
            </tr>
            <tr>
                <td>College name:</td>
                <td>{{$admission->collegeName}}</td>

                <td>Email Id:</td>
                <td>{{$admission->email}} </td>
            </tr>
            <tr>
                <td>apply Date:</td>
                <td>{{$admission->created_at}} </td>

                <td>Phone:</td>
                <td>{{$admission->phon_no}} </td>
            </tr>
            <tr>
                <td>Course Name</td>
                <td>{{$admission->courseName}} </td>

                <td>Address:</td>
                <td>{{$admission->state}}  {{$admission->district}}  {{$admission->sub_district}} </td>
            </tr>
            <tr>
                <td>Application Status:</td>
                <td>{{$admission->admission_status}} </td>

                <td>Pin code:</td>
                <td>{{$admission->pin_no}}</td>
            </tr>
            <tr>
                <td>Application fees state</td>
                <td>{{$admission->apply_payment_status}}</td>

                <td>Identification:</td>
                <td>{{$admission->identification}}</td>
            </tr>
            <tr>
                <td>Admission fees Status:</td>
                <td>{{$admission->admission_payment_status}}</td>

                <td>Identification no:</td>
                <td>{{$admission->identification_no}}</td>
            </tr>
        </tbody>
    </table>

    <table class='fileTable'>
        <thead>
            <tr>
                <th class="no-border text-start heading" colspan="5">
                  Educational Details
                </th>
            </tr>
            <tr class="bg-blue">
                <th>Exam name</th>
                <th>School/College</th>
                <th>Roll/no</th>
                <th>Passing year</th>
                <th>board</th>
                <th>Mark obtain</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="10%">HSSLC</td>
                <td>
                {{$admission->class12_college}}
                </td>
                <td width="10%"> {{$admission->class12_roll}}/ {{$admission->class12_no}}</td>
                <td width="10%"> {{$admission->class12_passingYear}}</td>
                <td width="10%"> {{$admission->class12_board}}</td>
                <td width="10%">{{$admission->class12_markObtain}}%</td>



                <!-- <td width="10%">1</td>
                <td width="15%" class="fw-bold">$14000</td> -->
            </tr>
            <tr>
            <td width="10%">HSLC</td>
            <td>
                {{$admission->class10_school}}
                </td>
                <td width="10%"> {{$admission->class10_roll}}/ {{$admission->class10_no}}</td>
                <td width="10%"> {{$admission->class10_passingYear}}</td>
                <td width="10%"> {{$admission->class10_board}}</td>
                <td width="10%">{{$admission->class10_markObtain}}%</td>
            <!-- <tr>
                <td colspan="4" class="total-heading">Total Amount - <small>Inc. all vat/tax</small> :</td>
                <td colspan="1" class="total-heading">$14699</td>
            </tr> -->
        </tbody>
    </table>


    
    <table>
        <thead>
            <tr>
                <th class="no-border text-start heading" colspan="5">
                 File details
                </th>
            </tr>
            <tr class="bg-blue">
                <th>File name</th>
                <th>upload Status</th>
              
            </tr>
        </thead>
        <tbody>
        <tr>
                <td  width="70%"> Profile</td>
                <td>
                {{$admission->profile=!null ? 'Uploaded' : 'Not Uploaded'}}
                </td>
               
            </tr>
            <tr>
                <td  width="70%"> Signature</td>
                <td>
                {{$admission->signature=!null ? 'Uploaded' : 'Not Uploaded'}}
                </td>
              
            </tr>
            <tr>
                <td  width="70%"> Aadhar</td>
                <td>
                {{$admission->aadhar=!null ? 'Uploaded' : 'Not Uploaded'}}
                </td>
              
            </tr>
            <tr>
                <td  width="70%">HSLC ADMIT</td>
                <td>
                {{$admission->hslc_admit=!null ? 'Uploaded' : 'Not Uploaded'}}
                </td>
               
            </tr>
            <tr>
                <td  width="70%">HSLC CERTIFICATE</td>
                <td>
                {{$admission->hslc_certificate=!null ? 'Uploaded' : 'Not Uploaded'}}
                </td>
              
            </tr>
            <tr>
                <td  width="70%">HSLC MARKSHEET</td>
                <td>
                {{$admission->hslc_marksheet=!null ? 'Uploaded' : 'Not Uploaded'}}
                </td>
              
            </tr>
            <tr>
                <td  width="70%">HSLC CERTIFICATE</td>
                <td>
                {{$admission->hslc_certificate=!null ? 'Uploaded' : 'Not Uploaded'}}
                </td>
              
            </tr>
            <tr>
                <td  width="70%">HSLC REGISTRATION</td>
                <td>
                {{$admission->hslc_registation=!null ? 'Uploaded' : 'Not Uploaded'}}
                </td>
              
            </tr>
            <tr>
                <td  width="70%">HSSLC ADMIT</td>
                <td>
                {{$admission->hsslc_admit=!null ? 'Uploaded' : 'Not Uploaded'}}
                </td>
               
            </tr>
            <tr>
                <td  width="70%">HSSLC CERTIFICATE</td>
                <td>
                {{$admission->hsslc_certificate=!null ? 'Uploaded' : 'Not Uploaded'}}
                </td>
              
            </tr>
            <tr>
                <td  width="70%">HSSLC MARKSHEET</td>
                <td>
                {{$admission->hsslc_marksheet=!null ? 'Uploaded' : 'Not Uploaded'}}
                </td>
              
            </tr>
            <tr>
                <td  width="70%">HSSLC CERTIFICATE</td>
                <td>
                {{$admission->hsslc_certificate=!null ? 'Uploaded' : 'Not Uploaded'}}
                </td>
              
            </tr>
            <tr>
                <td  width="70%">HSSLC REGISTRATION</td>
                <td>
                {{$admission->hsslc_registation=!null ? 'Uploaded' : 'Not Uploaded'}}
                </td>
              
            </tr>
        </tbody>
    </table>
    <br>
    <p class="text-center">
        Thank your for applying , 
    </p>

</body>
</html>