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

            // Foreign Keys
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade'); // Link to beneficiaries
            $table->foreignId('distribution_item_id')->nullable()->constrained('distribution_items')->onDelete('cascade'); // Link to distribution items
            $table->foreignId('distribution_id')->nullable()->constrained('distributions')->onDelete('cascade'); // Link to distributions
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->onDelete('cascade'); // Link to barangays
            $table->foreignId('support_id')->nullable()->constrained('support')->onDelete('set null'); // Link to support personnel
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null'); // Link to admins

            // Snapshot Fields
            $table->string('beneficiary_name'); // Snapshot of the beneficiary's name
            $table->string('support_name'); // Snapshot of the support's name (or admin)
            $table->string('support_contact')->nullable(); // Snapshot of the support's contact
            $table->string('distribution_title')->nullable(); // Snapshot of the distribution title
            $table->string('barangay_name')->nullable(); // Snapshot of the barangay name
            $table->string('item_name')->nullable(); // Snapshot of the distribution item name

            // Action Data
            $table->enum('action', ['Claimed', 'Unclaimed']); // Action performed
            $table->string('role')->nullable(); // Role of the person performing the action (e.g., Scanner, Admin)
            $table->timestamp('performed_at')->nullable(); // Exact time the action was performed
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
