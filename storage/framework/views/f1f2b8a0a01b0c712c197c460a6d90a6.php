<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto px-4 py-10 sm:py-14">
    <div class="text-center mb-6">
        <span class="text-4xl">🔎</span>
        <h1 class="text-xl font-extrabold text-coffee-800 mt-2">Cek Status Pesanan</h1>
        <p class="text-coffee-500 text-sm">Masukkan Nomor Order yang kamu terima setelah checkout.</p>
    </div>

    <form action="<?php echo e(route('order.status')); ?>" method="GET" class="flex gap-2 mb-6">
        <input name="order_number" value="<?php echo e(request('order_number')); ?>" type="text" placeholder="WS-20260824-0001" required
               class="flex-1 border border-coffee-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
        <button class="btn-press bg-coffee-800 hover:bg-coffee-900 text-white font-bold px-5 rounded-xl transition">Cari</button>
    </form>

    <?php if(session('error')): ?>
        <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs p-3.5 text-center font-medium">
            ⚠️ <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($notFound): ?>
        <div class="text-center py-10 animate-popin">
            <span class="text-4xl">🙁</span>
            <p class="text-coffee-500 mt-2 text-sm">Nomor order tidak ditemukan. Periksa kembali penulisannya.</p>
        </div>
    <?php endif; ?>

    <?php if($order): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-coffee-100 p-5 card-hover animate-popin">
            <div class="flex justify-between items-center mb-3">
                <span class="text-coffee-500 text-xs uppercase">No. Order</span>
                <span class="font-bold text-coffee-800"><?php echo e($order->order_number); ?></span>
            </div>

            <div class="flex gap-2 mb-4 flex-wrap">
                <span class="text-xs font-semibold px-3 py-1 rounded-full <?php echo e($order->paymentStatusColor()); ?>">
                    💳 <?php echo e($order->paymentStatusLabel()); ?>

                </span>
                <span class="text-xs font-semibold px-3 py-1 rounded-full <?php echo e($order->orderStatusColor()); ?>">
                    🍽️ <?php echo e($order->orderStatusLabel()); ?>

                </span>
                <span class="text-xs font-semibold px-3 py-1 rounded-full <?php echo e($order->paymentCategoryColor()); ?>">
                    <?php echo e($order->paymentCategoryIcon()); ?> <?php echo e($order->paymentCategoryLabel()); ?>

                </span>
            </div>

            <p class="text-sm text-coffee-600 mb-1">Atas nama: <span class="font-semibold text-coffee-800"><?php echo e($order->customer_name); ?></span> · No. Meja: <span class="font-bold text-coffee-800 bg-coffee-100 px-2 py-0.5 rounded">🪑 <?php echo e($order->table_number ?? '-'); ?></span></p>

            <div class="space-y-1.5 mt-3">
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex justify-between text-sm">
                        <span class="text-coffee-700"><?php echo e($item->menu_name); ?> <span class="text-coffee-400">x<?php echo e($item->qty); ?></span></span>
                        <span class="font-medium text-coffee-800">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="border-t border-dashed border-coffee-200 mt-3 pt-3 flex justify-between items-center">
                <span class="font-semibold text-coffee-700">Total</span>
                <span class="font-extrabold text-coffee-800">Rp <?php echo e(number_format($order->total, 0, ',', '.')); ?></span>
            </div>

            <?php if($order->payment_status === 'rejected'): ?>
                <div class="mt-4 rounded-xl bg-red-50 border border-red-200 p-3.5 text-red-800">
                    <p class="text-xs font-bold uppercase tracking-wider text-red-700">❌ Pesan / Alasan Penolakan dari Owner:</p>
                    <p class="text-sm font-semibold mt-1.5 bg-white p-3 rounded-lg border border-red-200 shadow-xs"><?php echo e($order->rejection_reason ?? 'Bukti pembayaran tidak sesuai atau belum valid.'); ?></p>
                    <p class="text-xs text-red-600 mt-2">Silakan hubungi kasir/staf warkop untuk mengonfirmasi ulang.</p>
                </div>
            <?php endif; ?>

            <?php if($order->payment_status === 'approved'): ?>
                <div class="mt-4 pt-3 border-t border-coffee-100 flex justify-end">
                    <a href="<?php echo e(route('order.receipt', $order->order_number)); ?>" target="_blank"
                       class="btn-press text-xs font-semibold px-4 py-2.5 rounded-xl bg-coffee-800 hover:bg-coffee-900 text-white shadow transition">
                        🧾 Lihat & Download Struk (PDF)
                    </a>
                </div>
            <?php else: ?>
                <div class="mt-4 pt-3 border-t border-coffee-100 text-center">
                    <span class="text-[11px] text-coffee-500 font-medium italic">⏳ Struk PDF dapat diunduh setelah pembayaran disetujui (ACC) Owner.</span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <a href="<?php echo e(route('menu.index')); ?>" class="block mt-6 text-center text-coffee-400 text-sm underline">← Kembali ke menu</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\lila\resources\views/customer/status.blade.php ENDPATH**/ ?>