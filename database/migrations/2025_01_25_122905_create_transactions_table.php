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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->onDelete('cascade'); // Link to barangays
            $table->string('barangay_name')->nullable();
            $table->string('barangay_location')->nullable();

            $table->foreignId('distribution_id')->nullable()->constrained('distributions')->onDelete('cascade');
            $table->string('distribution_title')->nullable();
            $table->string('distribution_location')->nullable();
            $table->string('distribution_date')->nullable();
            $table->string('distribution_code')->nullable();

            $table->foreignId('distribution_item_id')->nullable()->constrained('distribution_items')->onDelete('cascade');
            $table->string('distribution_item_name')->nullable();


            // Foreign Keys
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade');
            $table->string('beneficiary_name')->nullable();
            $table->string('beneficiary_contact')->nullable();
            $table->string('beneficiary_email')->nullable();
            $table->string('beneficiary_code')->nullable();



            $table->foreignId('support_id')->nullable()->constrained('supports')->onDelete('set null');
            $table->string('support_name')->nullable();
            $table->string('support_type')->nullable();
            $table->string('support_unique_code')->nullable();

            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null'); // Link to admins

            // Snapshot Fields

            // Action Data
            $table->enum('action', ['Claimed', 'Unclaimed'])->nullable();
            $table->string('role')->nullable();
            $table->timestamp('performed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
