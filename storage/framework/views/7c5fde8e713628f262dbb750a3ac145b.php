<?php $__env->startSection('content'); ?>
<div class="min-h-screen flex items-center justify-center px-4 bg-gradient-to-br from-coffee-800 via-coffee-700 to-coffee-600 relative overflow-hidden">
    <div class="absolute -left-10 -top-10 w-40 h-40 bg-coffee-400/20 rounded-full blur-3xl"></div>
    <div class="absolute -right-10 bottom-0 w-52 h-52 bg-coffee-300/10 rounded-full blur-3xl"></div>

    <div class="relative w-full max-w-sm">
        <div class="text-center mb-6 animate-popin">
            <span class="text-5xl inline-block relative">
                ☕
                <span class="absolute -top-2 left-1/2 -translate-x-1/2 w-1.5 h-3 bg-white/40 rounded-full animate-steam"></span>
            </span>
            <h1 class="text-white text-2xl font-extrabold mt-2">Login Internal</h1>
            <p class="text-coffee-200 text-sm">Warkop Samalila</p>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl p-6 animate-popin">
            <?php if($errors->any()): ?>
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login.submit')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Email</label>
                    <input name="email" type="email" value="<?php echo e(old('email')); ?>" required autofocus
                           placeholder="nama@warkopsamalila.test"
                           class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-semibold text-coffee-600">Kata Sandi</label>
                    <input name="password" type="password" required
                           placeholder="••••••••"
                           class="mt-1 w-full border border-coffee-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-coffee-400 focus:outline-none">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-xs text-coffee-600 select-none">
                        <input type="checkbox" name="remember" class="rounded text-coffee-700 focus:ring-coffee-400"> Ingat saya
                    </label>
                </div>
                <button type="submit" class="btn-press w-full bg-coffee-800 hover:bg-coffee-900 text-white font-bold py-3.5 rounded-xl transition shadow-lg">
                    Masuk →
                </button>
            </form>
        </div>

        <a href="<?php echo e(route('menu.index')); ?>" class="block text-center text-coffee-200 text-xs mt-6 underline hover:text-white transition">
            ← Kembali ke halaman pemesanan
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\lila\resources\views/auth/login.blade.php ENDPATH**/ ?>