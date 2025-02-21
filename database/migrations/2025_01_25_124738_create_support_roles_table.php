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
        Schema::create('support_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the support role (e.g., Scanner, Checker)
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_roles');
    }
};
