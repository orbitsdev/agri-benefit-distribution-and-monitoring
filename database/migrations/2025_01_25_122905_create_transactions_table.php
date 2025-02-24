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
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->onDelete('cascade');
            $table->foreignId('distribution_id')->nullable()->constrained('distributions')->onDelete('cascade');
            $table->foreignId('distribution_item_id')->nullable()->constrained('distribution_items')->onDelete('cascade');
            $table->foreignId('beneficiary_id')->nullable()->constrained('beneficiaries')->onDelete('cascade');
            $table->foreignId('support_id')->nullable()->constrained('supports')->onDelete('set null');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null'); // Link to admins

            // JSON Snapshots (to store full details at the time of transaction)
            $table->json('barangay_details')->nullable(); // Stores barangay details
            $table->json('distribution_details')->nullable(); // Stores distribution details
            $table->json('distribution_item_details')->nullable(); // Stores item details
            $table->json('beneficiary_details')->nullable(); // Stores beneficiary details
            $table->json('support_details')->nullable(); // Stores support details
            $table->json('recorder_details')->nullable(); // Stores support details

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
