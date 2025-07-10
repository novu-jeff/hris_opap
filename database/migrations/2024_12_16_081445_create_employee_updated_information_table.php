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

        Schema::create('employee_update_personal', function(Blueprint $table) {
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

        Schema::create('employee_update_parents', function(Blueprint $table) {
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

        Schema::create('employee_update_children', function(Blueprint $table) {
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

        Schema::create('employee_update_education', function(Blueprint $table) {
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
            $table->string('highest_level')
                ->nullable();
            $table->string('year_graduated')
                ->nullable();
            $table->string('scholarship_honors')
                ->nullable();
            $table->string('documents')
                ->nullable();
        });

        Schema::create('employee_update_employment_history', function(Blueprint $table) {
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
            $table->string('salary_pay_grade')
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
        });

        Schema::create('employee_update_civil_service', function(Blueprint $table) {
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
        });

        Schema::create('employee_update_trainings', function(Blueprint $table) {
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
        });

        Schema::create('employee_update_other_works', function(Blueprint $table) {
            $table->id();
            $table->string('employee_no');
            $table->string('organization')
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
        });

        Schema::create('employee_update_skills_hobbies', function(Blueprint $table) {
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_update_skills_hobbies');
        Schema::dropIfExists('employee_update_other_works');
        Schema::dropIfExists('employee_update_trainings');
        Schema::dropIfExists('employee_update_civil_service');

        Schema::dropIfExists('employee_update_employment_history');
        Schema::dropIfExists('employee_update_education');
        Schema::dropIfExists('employee_update_children');
        Schema::dropIfExists('employee_update_parents');
        Schema::dropIfExists('employee_update_personal');
    }
};
