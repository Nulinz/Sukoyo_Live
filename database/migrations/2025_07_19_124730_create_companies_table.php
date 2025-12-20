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
        Schema::create('companies', function (Blueprint $table) {
        $table->id();

        // Company Details
        $table->string('business_type');
        $table->string('company_name');
        $table->string('owner_name');
        $table->string('company_logo');

        // Contact Information
        $table->string('contact_number');
        $table->string('alternate_contact_number');
        $table->string('email');
        $table->string('website_url');

        // Address
        $table->string('address');
        $table->string('city');
        $table->string('state');
        $table->string('pincode');

        // Tax Information
        $table->string('gst_number');
        $table->string('pan_card_number');
        $table->string('cin_llp_number');
        $table->string('trade_license_number');

        // Bank Details
        $table->string('bank_name');
        $table->string('account_holder_name');
        $table->string('account_number');
        $table->string('ifsc_code');
        $table->string('branch_name');

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
