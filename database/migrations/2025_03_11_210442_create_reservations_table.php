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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->timestamp('reservation_date')->useCurrent();
            $table->decimal('reservation_fee', 10, 2)->nullable();
            $table->enum('financial_status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->enum('legal_status', ['Pending', 'Finalized'])->default('Pending');
            $table->enum('sale_status', ['Pending', 'Reserved', 'Sold'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
