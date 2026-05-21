<?php

use App\Enums\BillStatus;
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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tenant::class)->constrained()->onDelete('cascade');
            $table->decimal('amount_due', 10, 2);
            $table->timestamp('due_date');
            $table->enum('status', array_column(BillStatus::cases(), 'value'))
                ->default(BillStatus::UNPAID->value);
            $table->timestamps();

            $table->unique(['tenant_id', 'created_at'], 'unique_tenant_monthly_bill');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
