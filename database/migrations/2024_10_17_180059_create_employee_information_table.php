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
            $table->string('employee_no')
                ->nullable();
            $table->string('bsd_no')
                ->nullable();
            $table->foreignId('section_id')
                ->nullable()
                ->constrained('sections');
            $table->foreignId('position_id')
                ->nullable()
                ->constrained('positions');
            $table->string('job_completion')
                ->nullable()
                ->constrained('positions');
            $table->string('company_name')
                ->nullable();
            $table->string('date_hired')
                ->nullable();
            $table->foreignId('shift_id')
                ->nullable()
                ->constrained('shift_schedule')
                ->onDelete('set null');
            $table->foreignId('schedule_id')
                ->nullable()
                ->constrained('employee_schedules')
                ->onDelete('set null');
            $table->string('date_resignation')
                ->nullable();
            $table->foreignId('employment_type_id')
                ->nullable()
                ->constrained('employment_types')
                ->onDelete('set null');
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
            $table->boolean('isDeleted')
                ->default(false);
            $table->timestamps();
        });

        Schema::create('employee_account', function(Blueprint $table) {
            $table->id();
            $table->string('employee_no');
            $table->foreignId('applicant_id')
                ->nullable()
                ->constrained('applicant_users');
            $table->string('email_id')
                ->nullable();
            $table->string('email')
                ->nullable();
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
            $table->boolean('solo_parent')
                ->default(false)
                ->nullable();
            $table->string('country')
                ->nullable();
            $table->string('birth_certificate')
                ->nullable();
            $table->string('marriage_certificate')
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
            $table->index(['firstname', 'lastname', 'employee_no']);
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
            $table->string('documents')
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
            $table->string('documents')
                ->nullable();
            $table->timestamps();
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
            $table->string('monthly_salary')
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
            $table->string('documents')
                ->nullable();
            $table->timestamps();
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
            $table->string('documents')
                ->nullable();
            $table->timestamps();
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
            $table->string('documents')
                ->nullable();
            $table->timestamps();

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
            $table->string('documents')
                ->nullable();
            $table->timestamps();
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
            $table->string('documents')
                ->nullable();
            $table->timestamps();
        });

        Schema::create('employee_leave', function(Blueprint $table) {
            $table->id();
            $table->string('employee_no');
            $table->foreignId('leave_id')
                ->nullable()
                ->constrained('leave_types')
                ->onDelete('set null');
            $table->string('location')
                ->nullable();
            $table->string('location_specific')
                ->nullable();
            $table->string('confinement')
                ->nullable();
            $table->string('illness')
                ->nullable();
            $table->string('study')
                ->nullable();
            $table->string('study_other_purpose')
                ->nullable();
            $table->string('commutation')
                ->nullable();
            $table->enum('status', [
                    'approved',
                    'disapproved',
                    'pending'
                ])->default('pending');
            $table->longText('remarks')
                ->nullable();
            $table->foreignId('action_by_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->boolean('isDeleted')
                ->default(false);
            $table->timestamps();
        });

        Schema::create('employee_deduction', function(Blueprint $table) {
            $table->id();
            $table->string('employee_no')
                ->constrained('employee_information')
                ->onDelete('cascade');
            $table->foreignId('deduction_id')
                ->constrained('other_deductions')
                ->onDelete('cascade');
            $table->float('amount');
            $table->string('as_of')
                ->nullable();
            $table->timestamps();
        });

        Schema::create('employee_earnings', function(Blueprint $table) {
            $table->id();
            $table->string('employee_no')
                ->constrained('employee_information')
                ->onDelete('cascade');
            $table->foreignId('earning_id')
                ->constrained('other_earnings')
                ->onDelete('cascade');
            $table->float('amount');
            $table->string('as_of')
                ->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('employee_earnings');
        Schema::dropIfExists('employee_deduction');
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
