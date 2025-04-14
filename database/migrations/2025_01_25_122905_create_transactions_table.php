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
                $table->foreignId('barangay_distribution_id')->nullable()->constrained('barangay_distributions')->onDelete('cascade');
                $table->foreignId('beneficiary_id')->nullable()->constrained('beneficiaries')->onDelete('cascade');
                $table->foreignId('crops_to_received_id')->nullable()->constrained('crops_to_receiveds')->onDelete('cascade');

                // JSON Snapshots (to store full details at the time of transaction)
                $table->json('barangay_details')->nullable(); // Stores barangay details
                $table->json('distribution_details')->nullable(); // Stores distribution details
                $table->json('barangay_distribution_details')->nullable(); // Stores barangay distribution details
                $table->json('beneficiary_details')->nullable(); // Stores beneficiary details
                $table->json('crops_details')->nullable(); // Stores crops details
                $table->json('recorder_details')->nullable(); // Stores user/recorder details including role and action details

                // Timestamp of when the action was performed
                $table->string('action')->nullable();
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
