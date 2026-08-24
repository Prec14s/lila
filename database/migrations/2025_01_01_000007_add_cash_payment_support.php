<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah opsi 'cash' (tunai) pada enum type di payment_settings
        DB::statement("ALTER TABLE payment_settings MODIFY type ENUM('qris','bank_transfer','cash') NOT NULL");

        // Tandai kolom yang tidak wajib untuk tipe cash & tambahkan info instruksi tunai
        Schema::table('payment_settings', function ($table) {
            $table->text('instruction')->nullable()->after('account_holder');
        });

        // Tandai kategori pembayaran pada order: tunai / non tunai (memudahkan filter & laporan)
        Schema::table('orders', function ($table) {
            $table->enum('payment_category', ['cash', 'non_cash'])->default('non_cash')->after('payment_method');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payment_settings MODIFY type ENUM('qris','bank_transfer') NOT NULL");

        Schema::table('payment_settings', function ($table) {
            $table->dropColumn('instruction');
        });

        Schema::table('orders', function ($table) {
            $table->dropColumn('payment_category');
        });
    }
};
