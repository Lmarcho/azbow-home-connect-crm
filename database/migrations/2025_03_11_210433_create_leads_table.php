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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_info');
            $table->enum('source', ['Zillow', 'Realtor.com', 'Google Ads', 'Facebook Ads', 'Landing Page']);
            $table->timestamp('inquiry_date')->useCurrent();
            $table->enum('status', ['Unassigned', 'Assigned', 'Reserved', 'Financial Approved', 'Legal Finalized', 'Sold'])->default('Unassigned');
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
