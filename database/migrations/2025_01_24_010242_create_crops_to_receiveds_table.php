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
        Schema::create('crops_to_receiveds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_id')->constrained('crops')->onDelete('cascade');
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade');


             $table->string('unique_code')->nullable()->unique();
            $table->boolean('is_claimed')->default(false);
            $table->dateTime('date_claimed')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crops_to_receiveds');
    }
};
