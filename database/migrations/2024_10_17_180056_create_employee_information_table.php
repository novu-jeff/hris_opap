<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_information', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')
                ->nullable();
            $table->string('employee_no')
                ->nullable();
            $table->string('biometrics_id')
                ->nullable();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches');
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('department_centers');
            $table->foreignId('position_id')
                ->nullable()
                ->constrained('positions');
            $table->string('date_hired');
            $table->string('date_resignation')
                ->nullable();
            $table->enum('type', [
                    'freelance',
                    'part time',
                    'contractual',
                    'project based',
                    'regular',
                    'probationary'
                ])
                ->nullable();
            $table->enum('status', [
                    'active',
                    'inactive'
                ])
                ->nullable()
                ->default('active');
            $table->enum('salary_method', [
                    'cash',
                    'bank transfer',
                    'paycheck',
                    'e-wallet'
                ])
                ->nullable();
            $table->integer('leave_credits')
                ->default(0)
                ->nullable();;
            $table->float('monthly_rate')
                ->default(0)
                ->nullable();
            $table->string('payroll_account_number')
                ->nullable();
            $table->string('bank_account_no')
                ->nullable();
            $table->timestamps();
        });

        Schema::create('employee_account', function(Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->constrained('employee_information')
                ->onCascade('delete');
            $table->foreignId('applicant_id')
                ->nullable()
                ->constrained('applicant_users');
            $table->string('email')
                ->unique();
            $table->string('password')
                ->nullable();
            $table->boolean('isLoggedIn')
                ->default(false);
            $table->string('token')
                ->nullable();
        });

        Schema::create('employee_personal', function(Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->constrained('employee_information')
                ->onCascade('delete');
            $table->string('profile')
                ->nullable();
            $table->string('firstname');
            $table->string('middlename')
                ->nullable();
            $table->string('lastname');
            $table->enum('suffix', [
                    'jr',
                    'sr',
                    'I',
                    'II',
                    'III',
                    'IIII',
                    'IIIII'
                ])
                ->nullable();
            $table->string('birthday')
                ->nullable();
            $table->integer('age')
                ->nullable();
            $table->enum('civil_status', [
                    'single',
                    'married',
                    'divorced',
                    'seperated',
                    'widowed',
                    'anulled'
                ])
                ->nullable();
            $table->enum('sex', [
                    'male',
                    'female',
                    'not to say'
                ])
                ->nullable();
            $table->enum('citizenship', 
                ['filipino', 'dual_citizenship'])
                ->nullable();
            $table->enum('citizenship_type', 
                ['by_birth', 'by_naturalization'])
                ->nullable();
            $table->string('country')
                ->nullable();

            $table->string('present_address')
                ->nullable(); 
            $table->string('present_province')
                ->nullable(); 
            $table->string('present_city')
                ->nullable(); 
            $table->string('permanent_address')
                ->nullable(); 
            $table->string('permanent_province')
                ->nullable(); 
            $table->string('permanent_city')
                ->nullable();
            $table->string('mobile_number')
                ->nullable();
            $table->string('tel_no')
                ->nullable();
            $table->string('email')
                ->nullable(); 
            $table->string('height')
                ->nullable(); 
            $table->string('weight')
                ->nullable(); 
            $table->string('blood_type')
                ->nullable();

            $table->string('gsis_no')
                ->nullable(); 
            $table->string('pagibig_no')
                ->nullable(); 
            $table->string('philhealth_no')
                ->nullable(); 
            $table->string('sss_no')
                ->nullable(); 
            $table->string('tin_no')
                ->nullable();
        });

        Schema::create('employee_parents', function(Blueprint $table) {

            $table->id();
            $table->foreignId('employee_id')
                ->constrained('employee_information')
                ->onCascade('delete');
            
            $table->string('spouse_surname')
                ->nullable();
            $table->string('spouse_firstname')
                ->nullable();
            $table->string('spouse_middlename')
                ->nullable();
            $table->enum('spouse_suffix', [
                    'jr',
                    'sr',
                    'I',
                    'II',
                    'III',
                    'IIII',
                    'IIIII'
                ])
                ->nullable();
            $table->string('spouse_occupation')
                ->nullable();
            $table->string('spouse_business_name_employer')
                ->nullable();
            $table->string('spouse_business_address')
                ->nullable();
            $table->string('spouse_contact_no')
                ->nullable();  


            $table->string('father_surname')
                ->nullable();
            $table->string('father_firstname')
                ->nullable();
            $table->string('father_middlename')
                ->nullable();
            $table->enum('father_suffix', [
                    'jr',
                    'sr',
                    'I',
                    'II',
                    'III',
                    'IIII',
                    'IIIII'
                ])
                ->nullable();

            $table->string('mother_surname')
                ->nullable();
            $table->string('mother_firstname')
                ->nullable();
            $table->string('mother_middlename')
                ->nullable();
        });

        Schema::create('employee_children', function(Blueprint $table) {

            $table->id();
            $table->foreignId('employee_id')
                ->constrained('employee_information')
                ->onCascade('delete');
            $table->string('firstname')
                ->nullable();
            $table->string('middlename')
                ->nullable();
            $table->string('lastname')
                ->nullable();
            $table->string('birthdate')
                ->nullable();
        });

        Schema::create('employee_education', function(Blueprint $table) {

            $table->id();
            $table->foreignId('employee_id')
                ->constrained('employee_information')
                ->onCascade('delete');
            $table->enum('level', [
                    'elementary',
                    'secondary',
                    'vocational',
                    'highschool',
                    'senior_highschool',
                    'college',
                    'masters',
                    'doctoral'
                ])
                ->nullable();
            $table->string('school_name')
                ->nullable();
            $table->string('course')
                ->nullable();
            $table->string('from_year')
                ->nullable();
            $table->string('to_year')
                ->nullable();
        });

        Schema::create('employee_employment_history', function(Blueprint $table) {

            $table->id();
            $table->foreignId('employee_id')
                ->constrained('employee_information')
                ->onCascade('delete');

            $table->string('position')
                ->nullable();
            $table->string('department')
                ->nullable();
            $table->string('company_name')
                ->nullable();
            $table->float('monthly_salary')
                ->nullable();
            $table->enum('employment_status', [
                    'regular',
                    'part time',
                    'freelance',
                    'project base'
                ])
                ->nullable();
            $table->enum('isGovernment', [
                    'yes',
                    'no'
                ])
                ->nullable();
            $table->string('from_year')
                ->nullable();
            $table->string('to_year')
                ->nullable();
        });

        Schema::create('employee_leave', function(Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->constrained('employee_information')
                ->onCascade('delete');
            $table->string('status')
                ->default('pending');
            $table->enum('type', [
                'casual',
                'medical',
                'unpaid',
                'emergency',
                'sick'
            ]);
            $table->longtext('reason');
            $table->string('from');
            $table->string('to');
            $table->enum('measurement', [
                'full day',
                'half day',
                'range'
            ]);
            $table->string('consumed_hours')
                ->nullable();
            $table->timestamps();
        });


        Schema::create('employee_time_in_out', function(Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->constrained('employee_information')
                ->onCascade('delete');
            $table->string('time_in');
            $table->string('time_out');
            $table->string('total_hours');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('employee_leave');
        Schema::dropIfExists('employee_time_in_out');
        Schema::dropIfExists('employee_employment_history');
        Schema::dropIfExists('employee_education');
        Schema::dropIfExists('employee_children');
        Schema::dropIfExists('employee_parents');
        Schema::dropIfExists('employee_personal');
        Schema::dropIfExists('employee_account');
        Schema::dropIfExists('employee_information');
    }
};
