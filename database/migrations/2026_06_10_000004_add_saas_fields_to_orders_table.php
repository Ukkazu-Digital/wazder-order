<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('orders', 'payment_term_id')) {
                $table->foreignId('payment_term_id')->nullable()->constrained()->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'tenant_id')) {
                $table->dropForeign(['tenant_id']);
                $table->dropColumn('tenant_id');
            }
            if (Schema::hasColumn('orders', 'payment_term_id')) {
                $table->dropForeign(['payment_term_id']);
                $table->dropColumn('payment_term_id');
            }
        });
    }
};
