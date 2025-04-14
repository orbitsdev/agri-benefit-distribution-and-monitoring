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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('distribution_item_id')->nullable()->constrained('distribution_items')->onDelete('cascade'); // Foreign key constraint
            // $table->string('name')->nullable();
            // $table->string('contact')->nullable();
            // $table->string('email')->nullable();
            // $table->text('address')->nullable();
            // $table->enum('status', ['Claimed', 'Unclaimed'])->default('Unclaimed');
            // $table->string('code')->nullable()->unique();


            $table->string('rsbsa_no')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('ext_name')->nullable();

            // Address
            $table->string('farmer_address')->nullable();
            $table->string('farmer_address_mun')->nullable();
            $table->string('farmer_address_prv')->nullable();

            // Personal details
            $table->date('birthday')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('contact_num')->nullable();

         $table->string('email')->nullable();

            // Farm affiliation
            $table->string('agency')->nullable();

            // Relation to distribution
            $table->foreignId('barangay_distribution_id')->constrained('barangay_distributions')->onDelete('cascade');

            // Status
            $table->boolean('is_approved')->default(true);
            $table->timestamps(); // Timestamps for record tracking

            // Foreign key constraint11

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
