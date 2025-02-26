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
        Schema::create('certificate_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('template')->nullable();
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('certificate_type_id')->constrained()->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['issued', 'revoked', 'expired'])->default('issued');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('certificate_types');
    }
}; 