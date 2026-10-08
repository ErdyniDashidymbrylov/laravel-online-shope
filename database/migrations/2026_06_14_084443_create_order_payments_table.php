<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->index();
            $table->string('status')->index();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('RUB');
            $table->string('external_payment_id')->nullable()->index();
            $table->string('idempotence_key')->nullable()->unique();
            $table->text('confirmation_url')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'external_payment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
