<?php $__env->startSection('content'); ?>
<?php $title = 'Log Aktivitas Pengguna'; ?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-coffee-800">Log Aktivitas Pengguna</h2>
        <p class="text-sm text-coffee-500">Catatan riwayat tindakan seluruh pengguna (SuperAdmin, Owner, Dapur, & Pelanggan).</p>
    </div>
    
    <?php if($logs->total() > 0): ?>
        <form action="<?php echo e(route('superadmin.logs.clear')); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh log aktivitas pengguna?');">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button class="btn-press text-xs font-bold px-4 py-2.5 rounded-xl bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 transition">
                🗑️ Bersihkan Semua Log
            </button>
        </form>
    <?php endif; ?>
</div>


<form method="GET" class="flex flex-wrap gap-3 mb-6 bg-white p-4 rounded-2xl border border-coffee-100 shadow-sm">
    <div class="flex-1 min-w-[240px]">
        <input name="search" value="<?php echo e(request('search')); ?>" type="text" placeholder="🔎 Cari Nama, Action, Detail, atau IP Address..."
               class="w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
    </div>

    <select name="role" class="border border-coffee-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
        <option value="">Semua Peran (Role)</option>
        <option value="superadmin" <?php if(request('role') === 'superadmin'): echo 'selected'; endif; ?>>Super Admin</option>
        <option value="owner" <?php if(request('role') === 'owner'): echo 'selected'; endif; ?>>Owner</option>
        <option value="dapur" <?php if(request('role') === 'dapur'): echo 'selected'; endif; ?>>Staf Dapur</option>
        <option value="customer" <?php if(request('role') === 'customer'): echo 'selected'; endif; ?>>Pelanggan / Guest</option>
    </select>

    <button class="btn-press bg-coffee-800 hover:bg-coffee-900 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition">
        Filter
    </button>

    <?php if(request()->filled('search') || request()->filled('role')): ?>
        <a href="<?php echo e(route('superadmin.logs.index')); ?>" class="btn-press bg-coffee-100 hover:bg-coffee-200 text-coffee-700 font-semibold px-4 py-2.5 rounded-xl text-sm flex items-center">
            Reset
        </a>
    <?php endif; ?>
</form>


<div class="bg-white rounded-2xl border border-coffee-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-coffee-50 text-coffee-600 text-xs uppercase font-bold border-b border-coffee-100">
                <tr>
                    <th class="px-5 py-3.5 text-left">Waktu</th>
                    <th class="px-5 py-3.5 text-left">Pengguna</th>
                    <th class="px-5 py-3.5 text-left">Role</th>
                    <th class="px-5 py-3.5 text-left">Aksi</th>
                    <th class="px-5 py-3.5 text-left">Keterangan</th>
                    <th class="px-5 py-3.5 text-right">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-coffee-50">
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-coffee-50/50 transition">
                        <td class="px-5 py-3.5 whitespace-nowrap text-xs text-coffee-500 font-mono">
                            <?php echo e($log->created_at->translatedFormat('d M Y, H:i:s')); ?>

                        </td>
                        <td class="px-5 py-3.5 font-semibold text-coffee-800">
                            <?php echo e($log->user_name); ?>

                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full border <?php echo e($log->roleBadgeColor()); ?>">
                                <?php echo e($log->roleLabel()); ?>

                            </span>
                        </td>
                        <td class="px-5 py-3.5 font-bold text-coffee-700 whitespace-nowrap">
                            <?php echo e($log->action); ?>

                        </td>
                        <td class="px-5 py-3.5 text-coffee-600 text-xs leading-relaxed max-w-xs sm:max-w-md">
                            <?php echo e($log->description); ?>

                        </td>
                        <td class="px-5 py-3.5 text-right text-xs font-mono text-coffee-400 whitespace-nowrap">
                            <?php echo e($log->ip_address ?? '-'); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-coffee-400">
                            <span class="text-3xl block mb-1">📋</span>
                            Belum ada catatan log aktivitas pengguna.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($logs->hasPages()): ?>
        <div class="px-5 py-4 border-t border-coffee-100 bg-coffee-50/30">
            <?php echo e($logs->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\lila\resources\views/superadmin/logs/index.blade.php ENDPATH**/ ?>