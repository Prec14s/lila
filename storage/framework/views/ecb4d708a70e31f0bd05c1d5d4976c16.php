<?php $__env->startSection('content'); ?>
<?php $title = 'Pesanan Masuk'; ?>

<div class="mb-6 flex items-center gap-3">
    <div class="bg-white rounded-2xl border border-coffee-100 shadow-sm px-5 py-3">
        <p class="text-xs text-coffee-400">Selesai Hari Ini</p>
        <p class="text-xl font-extrabold text-coffee-800"><?php echo e($completedToday); ?></p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card-pop bg-white rounded-2xl border border-coffee-100 shadow-sm p-4 sm:p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="font-bold text-coffee-800"><?php echo e($order->order_number); ?></p>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo e($order->orderStatusColor()); ?>"><?php echo e($order->orderStatusLabel()); ?></span>
                </div>
                <div class="flex items-center justify-between mb-3 bg-coffee-50/70 p-2.5 rounded-xl border border-coffee-100/50">
                    <p class="text-sm text-coffee-700 font-semibold truncate">👤 <?php echo e($order->customer_name); ?></p>
                    <span class="text-xs font-extrabold px-2.5 py-1 rounded-lg bg-coffee-800 text-white shadow-sm shrink-0">🪑 <?php echo e($order->table_number ?? '-'); ?></span>
                </div>

                <div class="bg-coffee-50 rounded-xl p-3 space-y-1.5 mb-4">
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-coffee-800 font-medium"><?php echo e($item->menu_name); ?></span>
                            <span class="font-extrabold text-coffee-900 bg-white px-2 py-0.5 rounded shadow-xs text-xs">x<?php echo e($item->qty); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php if($order->note): ?>
                    <p class="text-xs text-amber-800 bg-amber-50 p-2.5 rounded-xl border border-amber-200/60 italic mb-4">📝 Note: <?php echo e($order->note); ?></p>
                <?php endif; ?>
            </div>

            <?php if($order->order_status === 'waiting'): ?>
                <form action="<?php echo e(route('dapur.orders.process', $order)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button class="btn-press w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-extrabold py-3 rounded-xl text-sm sm:text-base transition shadow-md">
                        🍳 Mulai Proses
                    </button>
                </form>
            <?php elseif($order->order_status === 'processing'): ?>
                <form action="<?php echo e(route('dapur.orders.complete', $order)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button class="btn-press w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold py-3 rounded-xl text-sm sm:text-base transition shadow-md">
                        ✅ Tandai Selesai
                    </button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-coffee-100">
            <span class="text-4xl">🍽️</span>
            <p class="text-coffee-400 mt-2 text-sm">Belum ada pesanan yang masuk.</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\lila\resources\views/dapur/dashboard.blade.php ENDPATH**/ ?>