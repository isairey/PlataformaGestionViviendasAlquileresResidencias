<?php

use App\Enums\PaymentMethod;
use App\Enums\TransactionStatus;
use App\Models\Bill;
use App\Models\Tenant;
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
            $table->id();
            $table->foreignIdFor(Tenant::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Bill::class)->unique()->constrained()->onDelete('cascade');
            $table->enum('status', array_column(TransactionStatus::cases(), 'value'))
                ->default(TransactionStatus::PENDING->value);
            $table->enum('payment_method', array_column(PaymentMethod::cases(), 'value')); // values are lowercase!
            $table->decimal('amount', 12, 2)->nullable(); // amount should be decimal, not string
            $table->string('proof_photo')->nullable();
            $table->string('gcash_number', 20)->nullable();
            $table->string('maya_number', 20)->nullable();
            $table->string('reference_number', 64)->nullable();
            $table->date('payment_date');
            $table->timestamp('confirmed_at')->nullable();
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
