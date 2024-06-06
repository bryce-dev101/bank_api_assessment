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
            $table->bigInteger('pf_payment_id');
            $table->enum('payment_status', ['cancelled','complete']);
            $table->string('item_name');
            $table->text('item_description');
            $table->decimal('amount_gross');
            $table->decimal('amount_fee');
            $table->decimal('amount_net');
            $table->bigInteger('merchant_id');
            $table->string('name_first');
            $table->string('name_last');
            $table->string('email_address');
            $table->string('signature', 32);
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
