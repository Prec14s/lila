<?php $__env->startSection('content'); ?>
<?php $title = 'Dashboard Owner'; ?>


<div class="mb-6 bg-white rounded-2xl border border-coffee-100 p-4 lg:p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
        <h2 class="font-bold text-coffee-800 text-lg">Ringkasan Operasional Warkop</h2>
        <p class="text-xs text-coffee-500 mt-0.5">Waktu Server Real-time (WIB Jakarta)</p>
    </div>
    <div class="flex items-center gap-2.5 bg-coffee-50 border border-coffee-200/80 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-coffee-800 shrink-0 shadow-xs">
        <span>📅 <?php echo e(now()->translatedFormat('l, d F Y')); ?></span>
        <span class="text-coffee-300">|</span>
        <span class="flex items-center gap-1">⏰ <span id="ownerClock"><?php echo e(now()->format('H:i')); ?></span> WIB</span>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <?php
        $cards = [
            ['label' => 'Menunggu Verifikasi', 'value' => $stats['waiting_verification'], 'icon' => '⏳', 'color' => 'from-amber-400 to-amber-500'],
            ['label' => 'Disetujui Hari Ini', 'value' => $stats['approved_today'], 'icon' => '✅', 'color' => 'from-emerald-400 to-emerald-500'],
            ['label' => 'Sedang Diproses Dapur', 'value' => $stats['processing'], 'icon' => '🍳', 'color' => 'from-blue-400 to-blue-500'],
            ['label' => 'Omzet Hari Ini', 'value' => 'Rp '.number_format($stats['revenue_today'], 0, ',', '.'), 'icon' => '💰', 'color' => 'from-coffee-500 to-coffee-700'],
        ];
    ?>
    <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card-pop bg-white rounded-2xl border border-coffee-100 p-4 lg:p-5 shadow-sm" style="animation-delay: <?php echo e($i * 60); ?>ms">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br <?php echo e($card['color']); ?> flex items-center justify-center text-lg mb-3">
                <?php echo e($card['icon']); ?>

            </div>
            <p class="text-xl lg:text-2xl font-extrabold text-coffee-800"><?php echo e($card['value']); ?></p>
            <p class="text-coffee-500 text-xs mt-1"><?php echo e($card['label']); ?></p>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="bg-white rounded-2xl border border-coffee-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-coffee-100 flex items-center justify-between">
        <h2 class="font-bold text-coffee-800">Pesanan Terbaru</h2>
        <a href="<?php echo e(route('owner.orders.index')); ?>" class="text-xs font-semibold text-coffee-500 hover:text-coffee-700">Lihat Semua →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-coffee-50 text-coffee-500 text-xs uppercase font-bold">
                <tr>
                    <th class="px-5 py-3.5 text-left">No. Order</th>
                    <th class="px-5 py-3.5 text-left">Waktu Order</th>
                    <th class="px-5 py-3.5 text-left">Pelanggan</th>
                    <th class="px-5 py-3.5 text-left">Cara Bayar</th>
                    <th class="px-5 py-3.5 text-left">Status Bayar</th>
                    <th class="px-5 py-3.5 text-left">Status Pesanan</th>
                    <th class="px-5 py-3.5 text-right">Total</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-coffee-50">
                <?php $__empty_1 = true; $__currentLoopData = $latestOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-coffee-50/60 transition">
                        <td class="px-5 py-3 font-bold text-coffee-800"><?php echo e($order->order_number); ?></td>
                        <td class="px-5 py-3 text-xs text-coffee-600 whitespace-nowrap">
                            <?php echo e($order->created_at->translatedFormat('d M Y')); ?>, <span class="font-bold text-coffee-800"><?php echo e($order->created_at->format('H:i')); ?> WIB</span>
                        </td>
                        <td class="px-5 py-3 text-coffee-600"><?php echo e($order->customer_name); ?> <span class="text-xs font-bold text-coffee-800 bg-coffee-100 px-1.5 py-0.5 rounded">🪑 <?php echo e($order->table_number ?? '-'); ?></span></td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo e($order->paymentCategoryColor()); ?>">
                                <?php echo e($order->paymentCategoryIcon()); ?> <?php echo e($order->paymentCategoryLabel()); ?>

                            </span>
                        </td>
                        <td class="px-5 py-3"><span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo e($order->paymentStatusColor()); ?>"><?php echo e($order->paymentStatusLabel()); ?></span></td>
                        <td class="px-5 py-3"><span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo e($order->orderStatusColor()); ?>"><?php echo e($order->orderStatusLabel()); ?></span></td>
                        <td class="px-5 py-3 text-right font-bold text-coffee-800">Rp <?php echo e(number_format($order->total, 0, ',', '.')); ?></td>
                        <td class="px-5 py-3 text-right">
                            <?php if($order->payment_status === 'rejected'): ?>
                                <form action="<?php echo e(route('owner.orders.destroy', $order)); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ditolak ini?')" class="inline-block">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn-press text-xs font-semibold px-2.5 py-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition border border-red-200">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="<?php echo e(route('owner.orders.receipt', $order)); ?>" target="_blank" class="btn-press text-xs font-semibold px-2.5 py-1 rounded-lg border border-coffee-200 text-coffee-600 hover:bg-coffee-50">
                                    🧾 Struk
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="px-5 py-10 text-center text-coffee-400">Belum ada pesanan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    setInterval(() => {
        const d = new Date();
        const h = String(d.getHours()).padStart(2, '0');
        const m = String(d.getMinutes()).padStart(2, '0');
        const el = document.getElementById('ownerClock');
        if (el) el.textContent = `${h}:${m}`;
    }, 5000);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\lila\resources\views/owner/dashboard.blade.php ENDPATH**/ ?>