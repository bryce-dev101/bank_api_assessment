<?php

use App\Models\Payment;
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
        Schema::create('payment_notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('pf_payment_id');
            $table->enum('payment_status', ['cancelled','complete']);
            $table->string('item_name');
            $table->text('item_description');
            $table->decimal('amount');
            $table->integer('merchant_id');
            $table->string('token', 36);
            $table->string('signature', 32);
            $table->date('billing_date')->nullable();
            $table->foreignIdFor(Payment::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_notifications');
    }
};
