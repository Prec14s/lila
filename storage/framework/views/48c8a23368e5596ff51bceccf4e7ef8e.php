<?php $__env->startSection('content'); ?>
<div x-data="{ method: '<?php echo e(old('payment_method', $paymentSettings->first()->type ?? '')); ?>', preview: null,
    isCash() { return this.method === 'cash'; },
    setPreview(e) { const f = e.target.files[0]; if (f) this.preview = URL.createObjectURL(f); } }"
     class="max-w-2xl mx-auto px-4 py-8 sm:py-12">

    <div class="text-center mb-6">
        <span class="text-4xl">💳</span>
        <h1 class="text-xl font-extrabold text-coffee-800 mt-2">Selesaikan Pembayaran</h1>
        <p class="text-coffee-500 text-sm">No. Order: <span class="font-semibold"><?php echo e($order->order_number); ?></span></p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-coffee-100 p-5 mb-5 card-hover">
        <p class="text-coffee-500 text-xs uppercase tracking-wide mb-2">Ringkasan Pesanan</p>
        <div class="space-y-1.5">
            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between text-sm">
                    <span class="text-coffee-700"><?php echo e($item->menu_name); ?> <span class="text-coffee-400">x<?php echo e($item->qty); ?></span></span>
                    <span class="font-medium text-coffee-800">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="border-t border-dashed border-coffee-200 mt-3 pt-3 flex justify-between items-center">
            <span class="font-semibold text-coffee-700">Total Bayar</span>
            <span class="font-extrabold text-lg text-coffee-800">Rp <?php echo e(number_format($order->total, 0, ',', '.')); ?></span>
        </div>
    </div>

    <form action="<?php echo e(route('order.upload-proof', $order->order_number)); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
        <?php echo csrf_field(); ?>

        
        <div>
            <p class="text-sm font-semibold text-coffee-700 mb-2">Pilih Cara Bayar</p>
            <div class="grid grid-cols-2 xs:grid-cols-<?php echo e(min(3, max(2, $paymentSettings->count()))); ?> gap-2.5">
                <?php $__currentLoopData = $paymentSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label :class="method === '<?php echo e($ps->type); ?>' ? 'border-coffee-700 bg-coffee-50 shadow scale-[1.02]' : 'border-coffee-200'"
                           class="cursor-pointer border-2 rounded-xl p-2.5 sm:p-3 text-center transition-all duration-200 btn-press flex flex-col items-center justify-center">
                        <input type="radio" name="payment_method" value="<?php echo e($ps->type); ?>" x-model="method" class="hidden">
                        <span class="text-2xl block mb-1"><?php echo e($ps->icon()); ?></span>
                        <span class="text-[11px] font-semibold text-coffee-700 leading-tight block"><?php echo e($ps->displayLabel()); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="flex justify-center gap-2 mt-3 text-[11px] flex-wrap">
                <span class="px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 font-semibold">📲 Non-Tunai = QRIS / Transfer</span>
                <span class="px-2.5 py-1 rounded-full bg-orange-100 text-orange-700 font-semibold">💵 Tunai = Bayar di Kasir</span>
            </div>
        </div>

        
        <?php $__currentLoopData = $paymentSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(! $ps->isCash()): ?>
                <div x-show="method === '<?php echo e($ps->type); ?>'" x-transition class="bg-coffee-50 rounded-2xl p-4 text-center">
                    <?php if($ps->type === 'qris'): ?>
                        <?php if($ps->qris_image): ?>
                            <img src="<?php echo e(asset('storage/'.$ps->qris_image)); ?>" alt="QRIS" class="w-48 h-48 object-contain mx-auto rounded-xl bg-white p-2 shadow">
                        <?php else: ?>
                            <div class="w-48 h-48 mx-auto rounded-xl bg-white flex items-center justify-center text-coffee-300 text-sm shadow">QRIS belum diunggah</div>
                        <?php endif; ?>
                        <p class="text-xs text-coffee-500 mt-2">Scan QRIS di atas menggunakan aplikasi e-wallet / m-banking kamu.</p>
                    <?php else: ?>
                        <p class="text-coffee-500 text-xs uppercase tracking-wide">Transfer ke Rekening</p>
                        <p class="text-lg font-extrabold text-coffee-800 mt-1"><?php echo e($ps->bank_name); ?></p>
                        <p class="text-2xl font-mono font-bold text-coffee-700 tracking-wider mt-1"><?php echo e($ps->account_number); ?></p>
                        <p class="text-sm text-coffee-500 mt-1">a.n. <?php echo e($ps->account_holder); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php $cashSetting = $paymentSettings->firstWhere('type', 'cash'); ?>
        <?php if($cashSetting): ?>
            <div x-show="method === 'cash'" x-transition class="bg-orange-50 border border-orange-200 rounded-2xl p-4 text-center">
                <span class="text-3xl block mb-1">💵</span>
                <p class="font-bold text-orange-700">Bayar Tunai di Kasir</p>
                <p class="text-orange-600 text-sm mt-1"><?php echo e($cashSetting->instruction ?: 'Silakan bayar langsung ke kasir saat mengambil pesanan.'); ?></p>
                <p class="text-orange-500 text-xs mt-2">Owner akan mengonfirmasi pembayaranmu setelah kamu bayar di kasir.</p>
            </div>
        <?php endif; ?>

        
        <div x-show="!isCash()" x-transition>
            <label class="text-sm font-semibold text-coffee-700 mb-2 block">Unggah Bukti Pembayaran</label>
            <label class="block border-2 border-dashed border-coffee-300 rounded-2xl p-4 text-center cursor-pointer hover:border-coffee-500 transition">
                <template x-if="preview">
                    <img :src="preview" class="w-full h-40 object-contain rounded-xl mb-2">
                </template>
                <template x-if="!preview">
                    <div class="py-6">
                        <span class="text-3xl block mb-1">📤</span>
                        <span class="text-sm text-coffee-500">Ketuk untuk pilih foto/screenshot</span>
                    </div>
                </template>
                <input type="file" name="payment_proof" accept="image/*" :required="!isCash()" class="hidden" @change="setPreview($event)">
            </label>
        </div>

        <button type="submit"
                class="btn-press w-full text-white font-bold py-3.5 rounded-xl transition shadow-lg"
                :class="isCash() ? 'bg-orange-600 hover:bg-orange-700' : 'bg-coffee-800 hover:bg-coffee-900'">
            <span x-show="!isCash()">Konfirmasi Pembayaran ✅</span>
            <span x-show="isCash()">Konfirmasi Pesanan (Bayar di Kasir) 💵</span>
        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\lila\resources\views/customer/payment.blade.php ENDPATH**/ ?>