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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['monetary', 'physical']);
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('currency')->nullable();
            $table->text('description');
            $table->integer('quantity')->nullable();
            $table->enum('category', ['food', 'essentials', 'clothing', 'other'])->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->foreignId('donor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->string('payment_method')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
