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

        Schema::create('gsis_billing', function (Blueprint $table) {
            $table->id();
            $table->string('remitting_agency');
            $table->string('office_code');
            $table->string('billing_month');
            $table->string('date_uploaded');
            $table->timestamps();
        });

        Schema::create('gsis_billing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gsis_billing_id')
                ->constrained('gsis_billing')
                ->onDelete('cascade');
            $table->string('bp_no');
            $table->string('crn_no')
                ->nullable();
            $table->string('effectivity_date');
            $table->float('ps');
            $table->float('gs');
            $table->float('ec');
            $table->float('consoloan');
            $table->float('ecardplus');
            $table->float('salary_loan');
            $table->float('cash_adv');
            $table->float('emrgy_loan');
            $table->float('educ_loan');
            $table->float('ela');
            $table->float('sos');
            $table->float('plreg');
            $table->float('plopt');
            $table->float('rel');
            $table->float('lch_dcs');
            $table->float('stock_purchase');
            $table->float('opt_life');
            $table->float('ceap');
            $table->float('edu_child');
            $table->float('genesis');
            $table->float('genplus');
            $table->float('genflexi');
            $table->float('genspcl');
            $table->float('help');
            $table->float('gfal');
            $table->float('mpl');
            $table->float('cpl');
            $table->float('gel');
            $table->float('mpl_lite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gsis_billing_items');
        Schema::dropIfExists('gsis_billing');
    }
};
