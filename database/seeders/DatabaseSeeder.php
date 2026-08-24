<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Menu;
use App\Models\PaymentSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Akun default ----------
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@warkopsamalila.test',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]);

        User::create([
            'name' => 'Owner Samalila',
            'email' => 'owner@warkopsamalila.test',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'phone' => '6281234567890',
        ]);

        User::create([
            'name' => 'Petugas Dapur',
            'email' => 'dapur@warkopsamalila.test',
            'password' => Hash::make('password'),
            'role' => 'dapur',
        ]);

        // ---------- Pengaturan usaha ----------
        BusinessSetting::create([
            'business_name' => 'Warkop Samalila',
            'address' => 'Jl. Contoh No. 1, Pekanbaru',
            'wa_owner_number' => '6281234567890',
            'wa_dapur_number' => '6281234567891',
        ]);

        // ---------- Metode pembayaran ----------
        PaymentSetting::create([
            'type' => 'qris',
            'label' => 'QRIS Warkop Samalila',
            'is_active' => true,
        ]);

        PaymentSetting::create([
            'type' => 'bank_transfer',
            'label' => 'Transfer BCA',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Warkop Samalila',
            'is_active' => true,
        ]);

        PaymentSetting::create([
            'type' => 'cash',
            'label' => 'Tunai / Bayar di Kasir',
            'instruction' => 'Silakan bayar langsung ke kasir saat mengambil atau menerima pesanan.',
            'is_active' => true,
        ]);

        // ---------- Kategori & menu contoh ----------
        $makanan = Category::create(['name' => 'Makanan', 'slug' => Str::slug('Makanan').'-'.Str::random(4), 'icon' => '🍛', 'sort_order' => 1]);
        $minuman = Category::create(['name' => 'Minuman', 'slug' => Str::slug('Minuman').'-'.Str::random(4), 'icon' => '☕', 'sort_order' => 2]);
        $snack = Category::create(['name' => 'Snack', 'slug' => Str::slug('Snack').'-'.Str::random(4), 'icon' => '🍟', 'sort_order' => 3]);

        Menu::insert([
            ['category_id' => $makanan->id, 'name' => 'Mie Goreng Spesial', 'description' => 'Mie goreng telur & sosis', 'price' => 18000, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $makanan->id, 'name' => 'Nasi Goreng Samalila', 'description' => 'Nasi goreng khas rumahan', 'price' => 20000, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $makanan->id, 'name' => 'Roti Bakar Coklat Keju', 'description' => null, 'price' => 15000, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],

            ['category_id' => $minuman->id, 'name' => 'Kopi Susu Gula Aren', 'description' => null, 'price' => 12000, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $minuman->id, 'name' => 'Es Teh Manis', 'description' => null, 'price' => 5000, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $minuman->id, 'name' => 'Americano', 'description' => null, 'price' => 14000, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],

            ['category_id' => $snack->id, 'name' => 'Pisang Goreng', 'description' => null, 'price' => 10000, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $snack->id, 'name' => 'Kentang Goreng', 'description' => null, 'price' => 13000, 'is_available' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
