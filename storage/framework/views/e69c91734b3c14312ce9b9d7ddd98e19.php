<?php $__env->startSection('content'); ?>
<?php $title = 'Riwayat & Cek Order'; ?>

<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <input name="order_number" value="<?php echo e(request('order_number')); ?>" type="text" placeholder="🔎 Cari Nomor Order..."
           class="flex-1 min-w-[200px] border border-coffee-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
    <select name="status" class="border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
        <option value="">Semua Status Bayar</option>
        <option value="waiting_payment" <?php if(request('status')==='waiting_payment'): echo 'selected'; endif; ?>>Menunggu Pembayaran</option>
        <option value="waiting_verification" <?php if(request('status')==='waiting_verification'): echo 'selected'; endif; ?>>Menunggu Verifikasi</option>
        <option value="approved" <?php if(request('status')==='approved'): echo 'selected'; endif; ?>>Disetujui</option>
        <option value="rejected" <?php if(request('status')==='rejected'): echo 'selected'; endif; ?>>Ditolak</option>
    </select>
    <button class="btn-press bg-coffee-800 hover:bg-coffee-900 text-white font-semibold px-5 rounded-xl text-sm">Cari</button>
</form>

<div class="bg-white rounded-2xl border border-coffee-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-coffee-50 text-coffee-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">No. Order</th>
                <th class="px-5 py-3 text-left">Pelanggan</th>
                <th class="px-5 py-3 text-left">Cara Bayar</th>
                <th class="px-5 py-3 text-left">Status Bayar</th>
                <th class="px-5 py-3 text-left">Status Pesanan</th>
                <th class="px-5 py-3 text-right">Total</th>
                <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-coffee-50">
            <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-coffee-50/60 transition">
                    <td class="px-5 py-3 font-semibold text-coffee-800"><?php echo e($order->order_number); ?></td>
                    <td class="px-5 py-3 text-coffee-600"><?php echo e($order->customer_name); ?> <span class="text-xs font-bold text-coffee-800 bg-coffee-100 px-1.5 py-0.5 rounded">🪑 <?php echo e($order->table_number ?? '-'); ?></span></td>
                    <td class="px-5 py-3">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo e($order->paymentCategoryColor()); ?>">
                            <?php echo e($order->paymentCategoryIcon()); ?> <?php echo e($order->paymentCategoryLabel()); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3"><span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo e($order->paymentStatusColor()); ?>"><?php echo e($order->paymentStatusLabel()); ?></span></td>
                    <td class="px-5 py-3"><span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo e($order->orderStatusColor()); ?>"><?php echo e($order->orderStatusLabel()); ?></span></td>
                    <td class="px-5 py-3 text-right font-semibold text-coffee-800">Rp <?php echo e(number_format($order->total, 0, ',', '.')); ?></td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <?php if($order->payment_status === 'rejected'): ?>
                                <form action="<?php echo e(route('owner.orders.destroy', $order)); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ditolak ini?')" class="inline-block">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn-press text-xs font-semibold px-2.5 py-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition border border-red-200">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="<?php echo e(route('owner.orders.receipt', $order)); ?>" target="_blank" class="btn-press text-xs font-semibold px-3 py-1.5 rounded-lg border border-coffee-200 text-coffee-600 hover:bg-coffee-50">
                                    🧾 Struk
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="px-5 py-10 text-center text-coffee-400">Tidak ada data pesanan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-5"><?php echo e($orders->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\lila\resources\views/owner/orders/index.blade.php ENDPATH**/ ?>