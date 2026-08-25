<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:12', 'regex:/^[0-9]+$/'],
            'table_number' => ['required', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:255'],
            'cart' => ['required'],
        ], [
            'customer_phone.max' => 'Nomor WhatsApp maksimal 12 digit.',
            'customer_phone.regex' => 'Nomor WhatsApp hanya boleh berupa angka.',
        ]);

        $cart = is_string($validated['cart']) ? json_decode($validated['cart'], true) : $validated['cart'];

        if (! is_array($cart) || count($cart) === 0) {
            return back()->withErrors(['cart' => 'Keranjang pesanan masih kosong.'])->withInput();
        }

        $order = DB::transaction(function () use ($validated, $cart) {
            $menuIds = collect($cart)->pluck('id');
            $menus = Menu::query()->with('category')->whereIn('id', $menuIds)->get()->keyBy('id');

            $total = 0;
            $items = [];

            foreach ($cart as $row) {
                $menu = $menus->get($row['id'] ?? null);
                $qty = max(1, (int) ($row['qty'] ?? 1));

                if (! $menu || ! $menu->is_available) {
                    continue;
                }

                $subtotal = $menu->price * $qty;
                $total += $subtotal;

                $items[] = [
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'category_name' => $menu->category?->name,
                    'price' => $menu->price,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ];
            }

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'table_number' => $validated['table_number'],
                'total' => $total,
                'note' => $validated['note'] ?? null,
                'payment_status' => 'waiting_payment',
                'order_status' => 'waiting',
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }

            \App\Models\ActivityLog::record(
                'Buat Pesanan',
                "Pelanggan {$order->customer_name} membuat pesanan {$order->order_number} (Meja: {$order->table_number}) senilai Rp ".number_format($order->total, 0, ',', '.'),
                null,
                $order->customer_name
            );

            return $order;
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['redirect' => route('order.pay', $order->order_number)]);
        }

        return redirect()->route('order.pay', $order->order_number);
    }

    public function pay(string $orderNumber): View|RedirectResponse
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        if ($order->payment_status !== 'waiting_payment') {
            return redirect()->route('order.success', $order->order_number);
        }

        $paymentSettings = PaymentSetting::query()->where('is_active', true)->get();

        return view('customer.payment', compact('order', 'paymentSettings'));
    }

    public function uploadProof(Request $request, string $orderNumber): RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $validated = $request->validate([
            'payment_method' => ['required', 'in:qris,bank_transfer,cash'],
            'payment_proof' => ['required_unless:payment_method,cash', 'nullable', 'image', 'max:10240'],
        ], [
            'payment_proof.required_unless' => 'Foto bukti pembayaran wajib diunggah untuk metode Non-Tunai.',
            'payment_proof.image' => 'File bukti pembayaran harus berupa gambar (JPG, PNG, WEBP).',
            'payment_proof.max' => 'Ukuran foto bukti pembayaran maksimal 10MB.',
        ]);

        $isCash = $validated['payment_method'] === 'cash';

        $path = $order->payment_proof;
        if (! $isCash) {
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                if ($file && $file->isValid()) {
                    $ext = $file->getClientOriginalExtension() ?: 'jpg';
                    $filename = Str::random(40) . '.' . $ext;
                    $tempPath = $file->getPathname();

                    if ($tempPath && file_exists($tempPath)) {
                        Storage::disk('public')->put('proofs/' . $filename, file_get_contents($tempPath));
                        $path = 'proofs/' . $filename;
                    } else {
                        return back()->withErrors(['payment_proof' => 'Gagal membaca file gambar sementara. Silakan coba pilih ulang foto.'])->withInput();
                    }
                } else {
                    $errCode = $file ? $file->getError() : 0;
                    $msg = ($errCode === 1 || $errCode === 2)
                        ? 'Ukuran foto terlalu besar (melebihi batas upload PHP). Silakan gunakan screenshot / foto yang lebih kecil.'
                        : 'File bukti pembayaran tidak valid atau gagal diunggah. Silakan pilih ulang foto.';
                    return back()->withErrors(['payment_proof' => $msg])->withInput();
                }
            } elseif (! $path) {
                return back()->withErrors(['payment_proof' => 'Foto bukti pembayaran wajib diunggah untuk metode Non-Tunai.'])->withInput();
            }
        }

        $order->update([
            'payment_method' => $validated['payment_method'],
            'payment_category' => $isCash ? 'cash' : 'non_cash',
            'payment_proof' => $path,
            'payment_status' => 'waiting_verification',
        ]);

        \App\Models\ActivityLog::record(
            'Konfirmasi Pembayaran',
            "Pelanggan {$order->customer_name} mengonfirmasi pembayaran ({$order->paymentMethodLabel()}) untuk order {$order->order_number}.",
            null,
            $order->customer_name
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['redirect' => route('order.success', $order->order_number)]);
        }

        return redirect()->route('order.success', $order->order_number);
    }

    public function success(string $orderNumber): View
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        $business = BusinessSetting::instance();

        $lines = [];
        $lines[] = "Halo Owner {$business->business_name}, saya ingin memesan.";
        $lines[] = "No. Order: {$order->order_number}";
        $lines[] = "Nama: {$order->customer_name}";
        $lines[] = "No. WA: {$order->customer_phone}";
        $lines[] = "No. Meja: {$order->table_number}";
        $lines[] = '';
        $lines[] = 'Pesanan:';
        foreach ($order->items as $i => $item) {
            $lines[] = ($i + 1).". {$item->menu_name} x{$item->qty} = Rp ".number_format($item->subtotal, 0, ',', '.');
        }
        $lines[] = 'Total: Rp '.number_format($order->total, 0, ',', '.');
        $lines[] = '';
        if ($order->isCash()) {
            $lines[] = 'Metode: Bayar TUNAI di kasir. Saya akan membayar saat mengambil/menerima pesanan. Mohon dikonfirmasi. Terima kasih.';
        } else {
            $lines[] = 'Bukti pembayaran sudah saya unggah di sistem. Mohon diverifikasi. Terima kasih.';
        }

        $waText = rawurlencode(implode("\n", $lines));
        $waOwnerLink = 'https://wa.me/'.preg_replace('/[^0-9]/', '', (string) $business->wa_owner_number).'?text='.$waText;

        return view('customer.success', compact('order', 'waOwnerLink'));
    }

    public function receipt(string $orderNumber): View|RedirectResponse
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        if ($order->payment_status !== 'approved' && ! auth()->check()) {
            return redirect()->route('order.status', ['order_number' => $order->order_number])
                ->with('error', 'Struk hanya dapat diunduh setelah pembayaran disetujui (ACC) oleh Owner.');
        }

        $business = BusinessSetting::instance();

        return view('owner.orders.receipt', compact('order', 'business'));
    }
}
