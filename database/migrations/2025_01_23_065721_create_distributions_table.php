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
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade'); // Foreign key to barangays
            $table->string('title'); // Title of the distribution
            $table->date('distribution_date'); // Distribution date
            $table->string('location')->nullable(); // Venue or location of the distribution
            $table->text('description')->nullable(); // Short description of the distribution
            $table->string('code')->unique()->nullable(); // Unique and nullable distribution code
            $table->enum('status', ['Planned', 'Ongoing', 'Completed', 'Canceled'])->default('Planned');
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
