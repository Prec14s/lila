<?php $__env->startSection('content'); ?>
<?php $title = 'Kategori Menu'; ?>

<div x-data="{ open: false }" class="mb-6">
    <button @click="open = true" class="btn-press bg-coffee-800 hover:bg-coffee-900 text-white font-semibold px-5 py-2.5 rounded-xl text-sm">
        + Tambah Kategori
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/50" @click="open = false" x-transition.opacity></div>
        <div x-show="open" x-transition class="relative bg-white w-full max-w-sm rounded-3xl p-6 shadow-2xl">
            <h3 class="font-bold text-lg text-coffee-800 mb-4">Tambah Kategori</h3>
            <form action="<?php echo e(route('owner.categories.store')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Ikon (emoji)</label>
                    <input name="icon" type="text" value="🍽️" maxlength="4"
                           class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Nama Kategori</label>
                    <input name="name" type="text" required placeholder="Contoh: Makanan"
                           class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="open = false" class="flex-1 border border-coffee-200 text-coffee-600 font-semibold py-2.5 rounded-xl text-sm">Batal</button>
                    <button type="submit" class="flex-1 bg-coffee-800 hover:bg-coffee-900 text-white font-semibold py-2.5 rounded-xl text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div x-data="{ editOpen: false }" class="card-pop bg-white rounded-2xl border border-coffee-100 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-3xl"><?php echo e($category->icon); ?></span>
                    <div>
                        <p class="font-bold text-coffee-800"><?php echo e($category->name); ?></p>
                        <p class="text-xs text-coffee-400"><?php echo e($category->menus_count); ?> menu</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button @click="editOpen = true" class="flex-1 btn-press text-xs font-semibold px-3 py-2 rounded-lg border border-coffee-200 text-coffee-600 hover:bg-coffee-50">Edit</button>
                <form action="<?php echo e(route('owner.categories.destroy', $category)); ?>" method="POST" class="flex-1" onsubmit="return confirm('Hapus kategori ini?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="w-full btn-press text-xs font-semibold px-3 py-2 rounded-lg border border-red-200 text-red-500 hover:bg-red-50">Hapus</button>
                </form>
            </div>

            <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
                <div class="absolute inset-0 bg-black/50" @click="editOpen = false" x-transition.opacity></div>
                <div x-show="editOpen" x-transition class="relative bg-white w-full max-w-sm rounded-3xl p-6 shadow-2xl">
                    <h3 class="font-bold text-lg text-coffee-800 mb-4">Edit Kategori</h3>
                    <form action="<?php echo e(route('owner.categories.update', $category)); ?>" method="POST" class="space-y-4">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        <div>
                            <label class="text-xs font-semibold text-coffee-600">Ikon (emoji)</label>
                            <input name="icon" type="text" value="<?php echo e($category->icon); ?>" maxlength="4"
                                   class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-coffee-600">Nama Kategori</label>
                            <input name="name" type="text" required value="<?php echo e($category->name); ?>"
                                   class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-2.5 text-sm">
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="editOpen = false" class="flex-1 border border-coffee-200 text-coffee-600 font-semibold py-2.5 rounded-xl text-sm">Batal</button>
                            <button type="submit" class="flex-1 bg-coffee-800 hover:bg-coffee-900 text-white font-semibold py-2.5 rounded-xl text-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-coffee-400 text-sm">Belum ada kategori.</p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\lila\resources\views/owner/categories/index.blade.php ENDPATH**/ ?>