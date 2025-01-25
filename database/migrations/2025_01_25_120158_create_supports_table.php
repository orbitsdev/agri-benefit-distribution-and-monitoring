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
        Schema::create('supports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personnel_id')->constrained('personnels')->onDelete('cascade'); // Link to personnel table
            $table->foreignId('distribution_id')->constrained('distributions')->onDelete('cascade'); // Link to distribution table
            $table->string('type'); // Support role type (e.g., Scanner, Checker, Registrar)
            $table->string('unique_code')->nullable()->unique(); //specific roles
            $table->boolean('can_scan_qr')->default(false); // Indicates if they can register beneficiaries
            $table->boolean('can_register')->default(false); // Indicates if they can register beneficiaries
            $table->boolean('can_confirm_claims')->default(false); // Indicates if they can confirm claims
            $table->boolean('can_view_list')->default(false); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supports');
    }
};
