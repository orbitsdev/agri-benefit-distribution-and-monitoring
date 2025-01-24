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
            $table->foreignId('distribution_item_id')->nullable()->constrained('distribution_items')->onDelete('cascade'); // Foreign key constraint
            $table->string('name')->nullable(); 
            $table->string('contact')->nullable(); 
            $table->enum('status', ['Claimed', 'Unclaimed'])->default('Unclaimed'); // Claim status
            $table->timestamps(); // Timestamps for record tracking

            // Foreign key constraint
          
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
