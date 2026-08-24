<?php $__env->startSection('content'); ?>
<?php $title = 'Verifikasi Pembayaran'; ?>

<div class="mb-5 flex items-center gap-3 flex-wrap">
    <span class="text-sm text-coffee-500">Filter cepat:</span>
    <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-indigo-100 text-indigo-700">📲 Non-Tunai: cek bukti transfer/QRIS</span>
    <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-orange-100 text-orange-700">💵 Tunai: konfirmasi setelah bayar di kasir</span>
</div>

<div class="grid gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card-pop bg-white rounded-2xl border border-coffee-100 shadow-sm p-5">
            <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-bold text-coffee-800"><?php echo e($order->order_number); ?></p>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo e($order->paymentCategoryColor()); ?>">
                            <?php echo e($order->paymentCategoryIcon()); ?> <?php echo e($order->paymentCategoryLabel()); ?>

                        </span>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-coffee-100 text-coffee-700">
                            <?php echo e($order->paymentMethodLabel()); ?>

                        </span>
                    </div>
                    <p class="text-sm text-coffee-500 mt-1"><?php echo e($order->customer_name); ?> · <?php echo e($order->customer_phone); ?> · <span class="font-bold text-coffee-800 bg-coffee-100 px-2 py-0.5 rounded">🪑 <?php echo e($order->table_number ?? '-'); ?></span></p>
                    <p class="text-xs text-coffee-400 mt-0.5"><?php echo e($order->created_at->translatedFormat('d M Y, H:i')); ?></p>
                </div>
                <p class="font-extrabold text-lg text-coffee-800">Rp <?php echo e(number_format($order->total, 0, ',', '.')); ?></p>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                
                <div class="bg-coffee-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-coffee-500 uppercase mb-2">Rincian Pesanan</p>
                    <div class="space-y-1">
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-coffee-700"><?php echo e($item->menu_name); ?> x<?php echo e($item->qty); ?></span>
                                <span class="text-coffee-800 font-medium">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if($order->note): ?>
                        <p class="text-xs text-coffee-500 italic mt-2">Catatan: <?php echo e($order->note); ?></p>
                    <?php endif; ?>
                </div>

                
                <div>
                    <?php if($order->isCash()): ?>
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 h-full flex flex-col items-center justify-center text-center">
                            <span class="text-3xl mb-1">💵</span>
                            <p class="font-semibold text-orange-700 text-sm">Pelanggan memilih bayar TUNAI di kasir</p>
                            <p class="text-orange-500 text-xs mt-1">Pastikan uang sudah diterima sebelum menekan ACC.</p>
                        </div>
                    <?php else: ?>
                        <p class="text-xs font-semibold text-coffee-500 uppercase mb-2">Bukti Pembayaran</p>
                        <?php if($order->payment_proof): ?>
                            <a href="<?php echo e(asset('storage/'.$order->payment_proof)); ?>" target="_blank">
                                <img src="<?php echo e(asset('storage/'.$order->payment_proof)); ?>" class="w-full h-40 object-contain rounded-xl bg-coffee-50 border border-coffee-100 hover:opacity-90 transition">
                            </a>
                        <?php else: ?>
                            <div class="w-full h-40 rounded-xl bg-coffee-50 border border-coffee-100 flex items-center justify-center text-coffee-300 text-sm">Belum ada bukti</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div x-data="{ rejectOpen: false }" class="mt-4">
                <div class="flex gap-3">
                    <form action="<?php echo e(route('owner.verification.approve', $order)); ?>" method="POST" class="flex-1">
                        <?php echo csrf_field(); ?>
                        <button class="btn-press w-full <?php echo e($order->isCash() ? 'bg-orange-600 hover:bg-orange-700' : 'bg-emerald-600 hover:bg-emerald-700'); ?> text-white font-semibold py-2.5 rounded-xl transition text-sm">
                            <?php echo e($order->isCash() ? '💵 Tunai Diterima & ACC' : '✅ ACC Pembayaran'); ?>

                        </button>
                    </form>
                    <button type="button" @click="rejectOpen = !rejectOpen" class="btn-press flex-1 bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2.5 rounded-xl transition text-sm border border-red-200">
                        ❌ Tolak Pembayaran
                    </button>
                </div>

                
                <div x-show="rejectOpen" x-transition class="mt-3 p-4 bg-red-50/80 rounded-xl border border-red-200">
                    <form action="<?php echo e(route('owner.verification.reject', $order)); ?>" method="POST" class="space-y-3">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label class="block text-xs font-bold text-red-800 mb-1">Pesan / Alasan Penolakan untuk Pelanggan:</label>
                            <input type="text" name="rejection_reason" placeholder="Contoh: Bukti transfer tidak jelas / nominal kurang..." required
                                   class="w-full border border-red-300 rounded-lg px-3.5 py-2 text-sm bg-white focus:ring-2 focus:ring-red-400 focus:outline-none">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="rejectOpen = false" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-coffee-600 bg-white border border-coffee-200">Batal</button>
                            <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-bold text-white bg-red-600 hover:bg-red-700 transition">Kirim & Tolak Pembayaran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center py-16 bg-white rounded-2xl border border-coffee-100">
            <span class="text-4xl">📭</span>
            <p class="text-coffee-400 mt-2 text-sm">Tidak ada pesanan yang menunggu verifikasi.</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\lila\resources\views/owner/verification/index.blade.php ENDPATH**/ ?>