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
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade'); // Link to barangay
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Optional link to user accounts
            
            $table->string('position')->nullable(); // Position or designation (optional)
            $table->string('contact_number')->nullable(); // Contact information (optional)
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnels');
    }
};
