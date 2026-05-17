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
        Schema::create('kurirs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('plate_number')->unique();
            $table->string('photo')->nullable();
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->softDeletes();
            $table->timestamps();
        });

        // Add kurir_id to orders table if it doesn't exist
        if (!Schema::hasColumn('orders', 'kurir_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('kurir_id')->nullable()->after('customer_id');
                $table->foreign('kurir_id')->references('id')->on('kurirs')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key first
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeignIfExists('orders_kurir_id_foreign');
            $table->dropColumn('kurir_id');
        });

        Schema::dropIfExists('kurirs');
    }
};
