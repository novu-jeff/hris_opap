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

        $product = config('app.product');

        if($product == 'government') {
            Schema::create('payroll_salary_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_id')
                    ->constrained('payroll_salary')
                    ->onDelete('cascade');
                $table->string('employee_no');
                $table->string('name');
                $table->string('position');
                $table->decimal('basic_salary', 12, 2);

                $table->decimal('pera', 12, 2)->default(0);
                $table->decimal('gross_amount_earned', 12, 2)->default(0);

                $table->decimal('rlip', 12, 2)->default(0);
                $table->decimal('hdmf', 12, 2)->default(0);
                $table->decimal('philhealth', 12, 2)->default(0);
                $table->decimal('consoloan', 12, 2)->default(0);
                $table->decimal('emergency_loan', 12, 2)->default(0);
                $table->decimal('plreg', 12, 2)->default(0);
                $table->decimal('mpl', 12, 2)->default(0);
                $table->decimal('cpl', 12, 2)->default(0);
                $table->decimal('mp2', 12, 2)->default(0);
                $table->decimal('mplstlms', 12, 2)->default(0);
                $table->decimal('cir375_cir449', 12, 2)->default(0);
                $table->decimal('w_tax', 12, 2)->default(0);
                $table->decimal('uca', 12, 2)->default(0);
                $table->decimal('aut', 12, 2)->default(0);
                $table->decimal('total_deductions', 12, 2)->default(0);

                $table->decimal('net_amount', 12, 2)->default(0);

                $table->decimal('dbp', 12, 2)->default(0);
                $table->decimal('kawani', 12, 2)->default(0);
                $table->decimal('lbp_payroll_account', 12, 2)->default(0);

                $table->decimal('salary', 12, 2)->default(0);

                $table->timestamps();

            });
        }

        if($product == 'private') {
            Schema::create('payroll_salary_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_id')
                    ->constrained('payroll_salary')
                    ->onDelete('cascade');
                $table->string('employee_no');
                $table->string('name');
                $table->string('position');
                $table->decimal('basic_salary', 12, 2);

                $table->decimal('overtime_pay', 12, 2)->default(0);
                $table->decimal('holiday_pay', 12, 2)->default(0);
                $table->decimal('allowances', 12, 2)->default(0);
                $table->decimal('gross_amount_earned', 12, 2)->default(0);

                $table->decimal('sss', 12, 2)->default(0);
                $table->decimal('pagibig', 12, 2)->default(0);
                $table->decimal('philhealth', 12, 2)->default(0);
                $table->decimal('w_tax', 12, 2)->default(0);
                $table->decimal('other_loans', 12, 2)->default(0);
                $table->decimal('total_deductions', 12, 2)->default(0);

                $table->decimal('net_amount', 12, 2)->default(0);

                $table->string('bank_account')->nullable();
                $table->string('bank_name')->nullable();

                $table->timestamps();
            });
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_salary_items'); 
    }
};
