<?php $__env->startSection('content'); ?>
<?php $title = 'Pengaturan WhatsApp'; ?>

<div class="max-w-lg bg-white rounded-2xl border border-coffee-100 shadow-sm p-6">
    <form action="<?php echo e(route('owner.settings.whatsapp.update')); ?>" method="POST" class="space-y-4">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div>
            <label class="text-xs font-semibold text-coffee-600">Nama Usaha</label>
            <input name="business_name" type="text" required value="<?php echo e(old('business_name', $business->business_name)); ?>"
                   class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
        </div>

        <div>
            <label class="text-xs font-semibold text-coffee-600">Alamat (opsional)</label>
            <input name="address" type="text" value="<?php echo e(old('address', $business->address)); ?>"
                   class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
        </div>

        <div class="pt-2 border-t border-coffee-100">
            <label class="text-xs font-semibold text-coffee-600">📲 Nomor WhatsApp Owner</label>
            <p class="text-xs text-coffee-400 mb-1">Menerima kiriman pesanan dari pelanggan.</p>
            <input name="wa_owner_number" type="text" required value="<?php echo e(old('wa_owner_number', $business->wa_owner_number)); ?>" placeholder="62812xxxxxxx"
                   class="w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
        </div>

        <div>
            <label class="text-xs font-semibold text-coffee-600">🍳 Nomor WhatsApp Dapur</label>
            <p class="text-xs text-coffee-400 mb-1">Menerima pesanan yang sudah di-ACC Owner.</p>
            <input name="wa_dapur_number" type="text" required value="<?php echo e(old('wa_dapur_number', $business->wa_dapur_number)); ?>" placeholder="62812xxxxxxx"
                   class="w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
        </div>

        <button type="submit" class="btn-press w-full bg-coffee-800 hover:bg-coffee-900 text-white font-bold py-3 rounded-xl transition">
            Simpan Pengaturan
        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\lila\resources\views/owner/settings/whatsapp.blade.php ENDPATH**/ ?>