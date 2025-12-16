@extends('admin.download.show', [
])

@section('content')
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 30px 10px 30px 10px;
        }
        table {
            width: 70% !important;
            border-collapse: collapse !important;
            margin: auto;
        }
        th, td {
            padding: 5px 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            text-transform: uppercase;
            padding: 12px 12px 12px 12px !important;
            background-color: #225f8b;
            color: white;
        }
        td {
            background-color: #f9f9f9;
            padding: 15px 15px 15px 15px !important;
        }
        .container {
            margin-top: 10px;
        }

        p {
            margin: 0;
        }
        .label {
            font-weight: 800;
            text-transform: uppercase;
            color: rgba(0, 0, 0, 0.6);
            font-size: 10px;
            white-space: nowrap;
        }
        .value {
            font-weight: 600;
            margin-top: 3px;
            white-space: inherit;
            text-transform: uppercase;
        }

        .btn-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 30px 20px 50px 0px;
        }

        .btn-actions .btn-custom {
            padding: 15px 30px 15px 30px;
            font-size: 15px;
            text-transform: uppercase;
            display: flex;
            text-decoration: none;
            border-radius: 8px;
        }

        .btn-back {
            border: 1px solid #225f8b;
            background-color: transparent;
            color: #225f8b;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-print, .btn-download {
            border: 1px solid #225f8b;
            background-color: #225f8b;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        @media print {
            .btn-actions {
                display: none; 
            }

            @page   { 
                size: auto;   
                margin: 25mm 25mm 25mm 25mm;  
            } 

        }

        
    </style>
    

    <div class="container">
        <div class="btn-actions">
            <!-- <a href="{{route('hris.index')}}" class="btn-custom btn-back"><i class="fa-solid fa-arrow-left-long"></i> Go Back</a> -->
            <button class="btn-custom btn-print"><i class="fa-solid fa-print"></i> Print Page</button>
            <a href="{{route('download.view', ['show' => 'employee', 'employee_no' => $data[0]['employee_no'], 'toPDS' => 'true'])}}" class="btn-custom btn-download"><i class="fa-solid fa-download"></i> Download PDS</a>
        </div>
        @forelse($data as $key => $data)
            @if($key > 0)
                <hr style="margin-bottom: 80px;">
            @endif
            <table style="margin-bottom: 80px; !important">
                <!-- Employee Information Section -->
                <tr>
                    <th colspan="12">Employee Information</th>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="label">Employee No</p>
                        <p class="value">{{$data['employee_no'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">BSD No</p>
                        <p class="value">{{$data['bsd_no'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Date Hired</p>
                        <p class="value">{{$data['date_hired'] ?? 'N/A'}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="label">Position</p>
                        <p class="value">{{$data['positions']['name'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Employment Type</p>
                        <p class="value">{{$data['employment_type']['name'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Status</p>
                        <p class="value">{{$data['status'] ?? 'N/A'}}</p>
                    </td>
                </tr>       
            
                <!-- Salary and Payment Information Section -->
                <tr>
                    <th colspan="12">Salary and Payment Information</th>
                </tr>
                <tr>
                    <td>
                        <p class="label">Salary Method</p>
                        <p class="value">{{$data['salary_method'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Monthly Rate</p>
                        <p class="value">₱{{number_format($data['salary'], 2) ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Payroll Account Number</p>
                        <p class="value">{{$data['payroll_account_number'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Bank Account No</p>
                        <p class="value">{{$data['bank_account_no'] ?? 'N/A'}}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="label">Has DBP Savings</p>
                        <p class="value">{{$data['hasDBP'] ? 'Yes' : 'No'}}</p>
                    </td>
                    <td>
                        <p class="label">Has Unlad Kawani</p>
                        <p class="value">{{$data['hasUK'] ? 'Yes' : 'No'}}</p>
                    </td>
                    <td>
                        <p class="label">Has Pagibig Loan</p>
                        <p class="value">{{$data['hasPagibigLoan'] ? 'Yes' : 'No'}}</p>
                    </td>
                    <td>
                    
                    </td>
                </tr>
                <!-- Personal Information Section -->
                <tr>
                    <th colspan="12">Personal Information</th>
                </tr>
                <tr>
                    <td>
                        <p class="label">First Name</p> 
                        <p class="value">{{$data['personal']['firstname'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Middle Name</p> 
                        <p class="value">{{$data['personal']['middlename'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Last Name</p> 
                        <p class="value">{{$data['personal']['lastname'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Suffix</p> 
                        <p class="value">{{$data['personal']['suffix'] ?? 'N/A'}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="12" style="padding: 0px !important">
                        <table style="border: 0px !important; width: 100% !important">
                            <tr>
                                <td>
                                    <p class="label">Birthday</p> 
                                    <p class="value">{{$data['personal']['birthday'] ?? 'N/A'}}</p>
                                </td>
                                <td>
                                    <p class="label">Age</p> 
                                    <p class="value">{{$data['personal']['age'] ?? 'N/A'}}</p>
                                </td>
                                <td>
                                    <p class="label">Sex</p> 
                                    <p class="value">{{$data['personal']['sex'] ?? 'N/A'}}</p>
                                </td>
                                <td>
                                    <p class="label">Civil Status</p>
                                    <p class="value">{{$data['personal']['civil_status'] ?? 'N/A'}}</p>
                                </td>
                                <td>
                                    <p class="label">Citizenship</p>
                                    <p class="value">{{$data['personal']['citizenship'] ?? 'N/A'}}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="label">Citizenship Type</p>
                        <p class="value">{{$data['personal']['citizenship_type'] ?? 'N/A'}}</p>
                    </td>
                    <td colspan="4">
                        <p class="label">Country</p>
                        <p class="value">{{$data['personal']['country'] ?? 'N/A'}}</p>
                    </td>
                </tr>
                
                <!-- Contact Information Section -->
                <tr>
                    <th colspan="12">Contact Information</th>
                </tr>
                <tr>
                    <td>
                        <p class="label">Mobile Number</p>
                        <p class="value">{{$data['personal']['mobile_number'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Telephone Number</p>
                        <p class="value">{{$data['personal']['tel_no'] ?? 'N/A'}}</p>
                    </td>
                    <td colspan="12">
                        <p class="label">Email</p>
                        <p class="value">{{$data['account']['email'] ?? 'N/A'}}</p>
                    </td>
                </tr>
                
                <!-- Address Information Section -->
                <tr>
                    <th colspan="12">Address Information</th>
                </tr>
                <tr>
                    <td colspan="12">
                        <p class="label">Present Address</p>
                        <p class="value">{{$data['personal']['present_address'] ?? 'N/A'}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="label">Province</p>
                        <p class="value">{{$data['personal']['present_province'] ?? 'N/A'}}</p>
                    </td>
                    <td colspan="2">
                        <p class="label">City</p>
                        <p class="value">{{$data['personal']['present_city'] ?? 'N/A'}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="12">
                        <p class="label">Permanent Address</p>
                        <p class="value">{{$data['personal']['permanent_address'] ?? 'N/A'}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="label">Province</p>
                        <p class="value">{{$data['personal']['permanent_province'] ?? 'N/A'}}</p>
                    </td>
                    <td colspan="2">
                        <p class="label">City</p>
                        <p class="value">{{$data['personal']['permanent_city'] ?? 'N/A'}}</p>
                    </td>
                </tr>

                <!-- Employee Education Information Section -->
                <tr>
                    <th colspan="12">Education Information</th>
                </tr>
                @forelse($data['education'] as $education)
                    <tr>
                        <td colspan="2">
                            <p class="label">Education Level</p>
                            <p class="value">{{$education['level'] ?? 'N/A'}}</p>
                        </td>
                        <td colspan="2">
                            <p class="label">School Name</p>
                            <p class="value">{{$education['school_name'] ?? 'N/A'}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p class="label">Course</p>
                            <p class="value">{{$education['course'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">From Year</p>
                            <p class="value">{{$education['from_year'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">To Year</p>
                            <p class="value">{{$education['to_year'] ?? 'N/A'}}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="text-align: center; text-transform: uppercase; font-weight: 500">
                            No Education Found
                        </td>
                    </tr>

                @endforelse
                
            
                <!-- Government IDs Section -->
                <tr>
                    <th colspan="12">Government IDs</th>
                </tr>
                <tr>
                    <td colspan="12" style="padding: 0px !important; width: 100%; ">
                        <table style="border: 0px !important; width: 100% !important">
                            <tr>
                                <td colspan="12">
                                    <p class="label">GSIS No</p>
                                    <p class="value">{{$data['personal']['gsis_no'] ?? 'N/A'}}</p>
                                </td>
                                <td colspan="12">
                                    <p class="label">PAGIBIG No</p>
                                    <p class="value">{{$data['personal']['pagibig_no'] ?? 'N/A'}}</p>
                                </td>
                                <td colspan="12">
                                    <p class="label">PhilHealth No</p>
                                    <p class="value">{{$data['personal']['philhealth_no'] ?? 'N/A'}}</p>
                                </td>
                                <td colspan="12">
                                    <p class="label">SSS No</p>
                                    <p class="value">{{$data['personal']['sss_no'] ?? 'N/A'}}</p>
                                </td>
                                <td colspan="12">
                                    <p class="label">TIN No</p>
                                    <p class="value">{{$data['personal']['tin_no'] ?? 'N/A'}}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- Spouse Information Section -->
                <tr>
                    <th colspan="12">Spouse Information</th>
                </tr>
                <tr>
                    <td>
                        <p class="label">Surname</p>
                        <p class="value">{{$data['parents']['spouse_surname'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">First Name</p>
                        <p class="value">{{$data['parents']['spouse_firstname'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Middle Name</p>
                        <p class="value">{{$data['parents']['spouse_middlename'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Suffix</p>
                        <p class="value">{{$data['parents']['spouse_suffix'] ?? 'N/A'}}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="label">Occupation</p>
                        <p class="value">{{$data['parents']['spouse_occupation'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Employer/Business Name</p>
                        <p class="value">{{$data['parents']['spouse_business_name_employer'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Business Address</p>
                        <p class="value">{{$data['parents']['spouse_business_address'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Contact No</p>
                        <p class="value">{{$data['parents']['spouse_contact_no'] ?? 'N/A'}}</p>
                    </td>
                </tr>
                
                <!-- Father's Information Section -->
                <tr>
                    <th colspan="12">Father's Information</th>
                </tr>
                <tr>
                    <td>
                        <p class="label">Surname</p>
                        <p class="value">{{$data['parents']['father_surname'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">First Name</p>
                        <p class="value">{{$data['parents']['father_firstname'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Middle Name</p>
                        <p class="value">{{$data['parents']['father_middlename'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Suffix</p>
                        <p class="value">{{$data['parents']['father_suffix'] ?? 'N/A'}}</p>
                    </td>
                </tr>

                <!-- Mother's Information Section -->
                <tr>
                    <th colspan="12">Mother's Information</th>
                </tr>
                <tr>
                    <td>
                        <p class="label">Surname</p>
                        <p class="value">{{$data['parents']['mother_surname'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">First Name</p>
                        <p class="value">{{$data['parents']['mother_fistname'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label">Middle Name</p>
                        <p class="value">{{$data['parents']['mother_middlename'] ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <p class="label"></p>
                        <p class="value"></p>
                    </td>
                </tr>
                <!-- Employee Children Information Section -->
                <tr>
                    <th colspan="12">Children Information</th>
                </tr>
                @forelse($data['children'] as $children)
                    <tr>
                        <td>
                            <p class="label">First Name</p>
                            <p class="value">{{$children['firstname'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Middle Name</p>
                            <p class="value">{{$children['middlename'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Last Name</p>
                            <p class="value">{{$children['lastname'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Birthdate</p>
                            <p class="value">{{$children['birthdate'] ?? 'N/A'}}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="text-align: center; text-transform: uppercase; font-weight: 500">
                            No Children Found
                        </td>
                    </tr>
                @endforelse
                <!-- Employee Employment History -->
                <tr>
                    <th colspan="12">Employment History</th>
                </tr>
                @forelse($data['employment_history'] as $employment_history)
                    <tr>
                        <td>
                            <p class="label">Position</p>
                            <p class="value">{{$employment_history['position'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Department</p>
                            <p class="value">{{$employment_history['department'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Company Name</p>
                            <p class="value">{{$employment_history['company_name'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Monthly Salary</p>
                            <p class="value">₱{{number_format($employment_history['monthly_salary'], 2) ?? 'N/A'}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="label">Employment Status</p>
                            <p class="value">{{$employment_history['employment_status'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Is Government</p>
                            <p class="value">{{$employment_history['isGovernment'] ? 'Yes' : 'No'}}</p>
                        </td>
                        <td>
                            <p class="label">From Year</p>
                            <p class="value">{{$employment_history['from_year'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">To Year</p>
                            <p class="value">{{$employment_history['to_year'] ?? 'N/A'}}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="text-align: center; text-transform: uppercase; font-weight: 500">
                            No Employment History Found
                        </td>
                    </tr>
                @endforelse

                <!-- Employee Civil Service -->
                <tr>
                    <th colspan="12">Civil Service</th>
                </tr>
                @forelse($data['civil_service'] as $civil_service)
                    <tr>
                        <td>
                            <p class="label">Certification</p>
                            <p class="value">{{$civil_service['certification'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Rating</p>
                            <p class="value">{{$civil_service['rating'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">License No</p>
                            <p class="value">{{$civil_service['license_no'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Date Validity</p>
                            <p class="value">{{$civil_service['date_validity'] ?? 'N/A'}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p class="label">Date Exam</p>
                            <p class="value">{{$civil_service['date_exam'] ?? 'N/A'}}</p>
                        </td>
                        <td colspan="2">
                            <p class="label">Place Exam</p>
                            <p class="value">{{$civil_service['place_exam'] ?? 'N/A'}}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="text-align: center; text-transform: uppercase; font-weight: 500">
                            No Civil Service Found
                        </td>
                    </tr>
                @endforelse
                <!-- Employee Trainings -->
                <tr>
                    <th colspan="12">Trainings</th>
                </tr>
                @forelse ($data['trainings'] as $training)
                    <tr>
                        <td>
                            <p class="label">Type</p>
                            <p class="value">{{$training['type'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Name</p>
                            <p class="value">{{$training['name'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Date From</p>
                            <p class="value">{{$training['date_from'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Date To</p>
                            <p class="value">{{$training['date_to'] ?? 'N/A'}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p class="label">Consumed Hours</p>
                            <p class="value">{{$training['consumed_hours'] ?? 'N/A'}}</p>
                        </td>
                        <td colspan="2">
                            <p class="label">Sponsored By</p>
                            <p class="value">{{$training['sponsored_by'] ?? 'N/A'}}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="text-align: center; text-transform: uppercase; font-weight: 500">
                            No Trainings Found
                        </td>
                    </tr>
                @endforelse
                
                <!-- Other Works -->
                <tr>
                    <th colspan="12">Others</th>
                </tr>
                @forelse($data['others'] as $other)
                    <tr>
                        <td colspan="2">
                            <p class="label">Organization</p>
                            <p class="value">{{$other['organization'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Consumed Hours</p>
                            <p class="value">{{$other['consumed_hours'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Position</p>
                            <p class="value">{{$other['position'] ?? 'N/A'}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="12">
                            <p class="label">Address</p>
                            <p class="value">{{$other['address'] ?? 'N/A'}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p class="label">Date From</p>
                            <p class="value">{{$other['date_from'] ?? 'N/A'}}</p>
                        </td>
                        <td colspan="2">
                            <p class="label">Date To</p>
                            <p class="value">{{$other['date_to'] ?? 'N/A'}}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="text-align: center; text-transform: uppercase; font-weight: 500">
                            No Other Works Found
                        </td>
                    </tr>
                @endforelse

                <!-- Hobbies / Skills -->
                <tr>
                    <th colspan="12">Hobbies / Skills</th>
                </tr>
                @forelse($data['skills'] as $skills)
                    <tr>
                        <td>
                            <p class="label">Name</p>
                            <p class="value">{{$skills['name'] ?? 'N/A'}}</p>
                        </td>
                        <td colspan="2">
                            <p class="label">Recognition</p>
                            <p class="value">{{$skills['recognition'] ?? 'N/A'}}</p>
                        </td>
                        <td>
                            <p class="label">Organization</p>
                            <p class="value">{{$skills['organization'] ?? 'N/A'}}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="text-align: center; text-transform: uppercase; font-weight: 500">
                            No Skills / Hobbies Found
                        </td>
                    </tr>
                @endforelse
            </table>
        @empty
            <table style="margin-bottom: 80px; !important;">
                <tr>
                    <th colspan="12" style="text-align: center; letter-spacing: 2px;">No Data Found</th>
                </tr>
            </table>
        @endforelse
    </div>
    
@endsection

@section('script')
    <script>
        $(function() {
            $('.btn-print').on('click', function() {
                window.print();
            });
        });
    </script>
@endsection