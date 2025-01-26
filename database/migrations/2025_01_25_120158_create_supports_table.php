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
            $table->boolean('enable_item_scanning')->default(false); // Indicates if they can register beneficiaries
            $table->boolean('enable_beneficiary_management')->default(false); // Indicates if they can register beneficiaries
            $table->boolean('enable_list_access')->default(false); // Indicates if they can confirm claims
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
