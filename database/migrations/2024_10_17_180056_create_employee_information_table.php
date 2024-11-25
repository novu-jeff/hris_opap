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
            $table->foreignId('section_id')
                ->nullable()
                ->constrained('sections');
            $table->foreignId('position_id')
                ->nullable()
                ->constrained('positions');
            $table->string('date_hired')
                ->nullable();
            $table->string('date_resignation')
                ->nullable();
            $table->foreignId('job_category_id')
                ->nullable()
                ->constrained('job_categories');
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
            $table->boolean('hasDBP')
                ->default(false)
                ->nullable();
            $table->boolean('hasUK')
                ->default(false)
                ->nullable();
            $table->boolean('hasPagibigLoan')
                ->default(false)
                ->nullable();
            $table->timestamps();
        });

        Schema::create('employee_account', function(Blueprint $table) {
            $table->id();
            $table->string('employee_no');
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
            $table->string('employee_no');
            $table->string('profile')
                ->nullable();
            $table->string('firstname');
            $table->string('middlename')
                ->nullable();
            $table->string('lastname');
            $table->string('suffix')
                ->nullable();
            $table->string('birthday')
                ->nullable();
            $table->integer('age')
                ->nullable();
            $table->string('civil_status')
                ->nullable();
            $table->string('sex')
                ->nullable();
            $table->string('citizenship')
                ->nullable();
            $table->string('citizenship_type')
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
            $table->string('employee_no');
            $table->string('spouse_surname')
                ->nullable();
            $table->string('spouse_firstname')
                ->nullable();
            $table->string('spouse_middlename')
                ->nullable();
            $table->string('spouse_suffix')
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
            $table->string('father_suffix')
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
            $table->string('employee_no');
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
            $table->string('employee_no');
            $table->string('level')
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
            $table->string('employee_no');
            $table->string('position')
                ->nullable();
            $table->string('department')
                ->nullable();
            $table->string('company_name')
                ->nullable();
            $table->float('monthly_salary')
                ->nullable();
            $table->string('employment_status')
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

        Schema::create('employee_civil_service', function(Blueprint $table) {

            $table->id();
            $table->string('employee_no');
            $table->string('certification')
                ->nullable();
            $table->string('rating')
                ->nullable();
            $table->string('date_exam')
                ->nullable();
            $table->string('place_exam')
                ->nullable();
            $table->string('license_no')
                ->nullable();
            $table->string('date_validity')
                ->nullable();
        });

        Schema::create('employee_trainings', function(Blueprint $table) {

            $table->id();
            $table->string('employee_no');
            $table->string('type')
                ->nullable();
            $table->string('name')
                ->nullable();
            $table->string('date_from')
                ->nullable();
            $table->string('date_to')
                ->nullable();
            $table->integer('consumed_hours')
                ->nullable();
            $table->string('sponsored_by')
                ->nullable();
        });

        Schema::create('employee_other_works', function(Blueprint $table) {

            $table->id();
            $table->string('employee_no');
            $table->string('organization')
                ->nullable();
            $table->string('address')
                ->nullable();
            $table->string('date_from')
                ->nullable();
            $table->string('date_to')
                ->nullable();
            $table->integer('consumed_hours')
                ->nullable();
            $table->string('position')
                ->nullable();
        });

        Schema::create('employee_skills_hobbies', function(Blueprint $table) {

            $table->id();
            $table->string('employee_no');
            $table->string('name')
                ->nullable();
            $table->string('recognition')
                ->nullable();
            $table->string('organization')
                ->nullable();
        });

        Schema::create('employee_leave', function(Blueprint $table) {
            $table->id();
            $table->string('employee_no');
            $table->string('status')
                ->default('pending');
            $table->string('type');
            $table->longtext('reason');
            $table->string('from');
            $table->string('to');
            $table->string('measurement');
            $table->string('consumed_hours')
                ->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('employee_leave');
        Schema::dropIfExists('employee_skills_hobbies');
        Schema::dropIfExists('employee_other_works');
        Schema::dropIfExists('employee_trainings');
        Schema::dropIfExists('employee_civil_service');

        Schema::dropIfExists('employee_employment_history');
        Schema::dropIfExists('employee_education');
        Schema::dropIfExists('employee_children');
        Schema::dropIfExists('employee_parents');
        Schema::dropIfExists('employee_personal');
        Schema::dropIfExists('employee_account');
        Schema::dropIfExists('employee_information');
    }
};
