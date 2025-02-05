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
            $table->foreignId('personnel_id')->constrained('personnels')->onDelete('cascade'); 
            $table->foreignId('distribution_id')->constrained('distributions')->onDelete('cascade');
            $table->string('type');
            $table->string('unique_code')->nullable()->unique();
            $table->boolean('enable_item_scanning')->default(false);
            $table->boolean('enable_beneficiary_management')->default(false);
            $table->boolean('enable_list_access')->default(false);
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
