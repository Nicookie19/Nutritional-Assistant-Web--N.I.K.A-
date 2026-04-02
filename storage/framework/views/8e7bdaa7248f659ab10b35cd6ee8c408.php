<?php $__env->startSection('title', 'Admin Sign in - NutriAssist'); ?>

<?php $__env->startSection('body_class', ''); ?>

<?php $__env->startSection('content'); ?>
<header class="bg-orange-600 border-b border-orange-700 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex items-center justify-between gap-8">
        <div class="flex items-center gap-3">
            <div class="rounded-full bg-white/20 p-2 text-white shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C9.65 2 8 3.4 8 5s1.65 3 4 3 4-1.4 4-3-1.65-3-4-3z"/><path d="M12 8c-4.4 0-8 2.2-8 4.9 0 5.8 7.1 9.2 7.5 9.4a1.5 1.5 0 0 0 1 0c.4-.2 7.5-3.6 7.5-9.4C20 10.2 16.4 8 12 8zm0 12.2C9.2 18.5 4 15.7 4 12.9 4 11 7.6 9.7 12 9.7s8 1.3 8 3.2c0 2.8-5.2 5.6-8 6.6z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">NutriAssist Admin</h1>
                <p class="text-xs text-orange-100">Management Dashboard</p>
            </div>
        </div>
    </div>
</header>

<main class="relative z-0 flex-1 px-4 py-16 sm:px-6 lg:px-8 max-w-4xl mx-auto">
    <div class="max-w-2xl mx-auto space-y-8">
        <div class="rounded-3xl bg-white p-12 shadow-2xl border border-slate-100">
            <h2 class="text-3xl font-bold text-slate-900 mb-2 text-center">
                Admin Sign In
            </h2>
            <p class="text-slate-600 text-lg text-center mb-10 max-w-md mx-auto">
                Access the NutriAssist management dashboard
            </p>
            <form method="POST" action="<?php echo e(route('admin.login')); ?>" class="space-y-8">
                <?php echo csrf_field(); ?>
                <div class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-900 mb-3">
                            Email Address <span class="text-orange-600">*</span>
                        </label>
                        <input id="email" name="email" type="email" autocomplete="email" required value="<?php echo e(old('email')); ?>" 
                               class="w-full px-5 py-4 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-sm transition-all duration-200 text-lg <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 ring-1 ring-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="admin@example.com">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="admin_code" class="block text-sm font-semibold text-slate-900 mb-3">
                            Admin Code <span class="text-orange-600">*</span>
                        </label>
                        <input id="admin_code" name="admin_code" type="text" required value="<?php echo e(old('admin_code')); ?>" 
                               class="w-full px-5 py-4 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-sm transition-all duration-200 text-lg <?php $__errorArgs = ['admin_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 ring-1 ring-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="1000808790">
                        <?php $__errorArgs = ['admin_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-900 mb-3">
                        Password <span class="text-orange-600">*</span>
                    </label>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               class="w-full pr-12 pl-5 py-4 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-sm transition-all duration-200 text-lg <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 ring-1 ring-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> pr-12" 
                               placeholder="••••••••">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password')">
                            <svg id="eye-icon" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg id="eye-slash-icon" class="h-5 w-5 text-slate-400 hover:text-slate-600 transition-colors hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path></svg>
                        </button>
                    </div>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-5 w-5 text-orange-600 border-slate-300 rounded focus:ring-orange-500">
                        <span class="ml-3 block text-sm font-medium text-slate-700">Remember me</span>
                    </label>
                    <div class="flex justify-end">
                        <a href="#" class="text-sm font-medium text-orange-600 hover:text-orange-500">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <button type="submit" class="w-full bg-orange-600 border border-transparent rounded-2xl px-6 py-5 text-xl font-bold text-white shadow-[0_10px_20px_-5px_rgba(234,88,12,0.4)] hover:bg-orange-700 focus:outline-none focus:ring-4 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-300 hover:shadow-[0_15px_30px_-5px_rgba(234,88,12,0.5)] hover:-translate-y-1">
                    Sign In as Admin
                </button>

                <div class="pt-4 mt-4 border-t border-slate-200">
                    <a href="<?php echo e(route('login')); ?>" class="flex items-center justify-center gap-2 w-full text-center bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 hover:bg-slate-100 transition-all">
                        <span>👤</span> Back to User Portal
                    </a>
                </div>
            </form>
        </div>
        <div class="text-center space-y-2">
            <p class="text-sm text-slate-600">
                Don't have admin access? <a href="<?php echo e(route('register')); ?>" class="font-semibold text-orange-600 hover:text-orange-500 transition-colors duration-200">Contact support</a>
            </p>
        </div>
    </div>
</main>

<script>
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const eyeIcon = document.getElementById('eye-icon');
    const eyeSlashIcon = document.getElementById('eye-slash-icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeSlashIcon.classList.remove('hidden');
    } else {
        passwordField.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeSlashIcon.classList.add('hidden');
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/nico/Herd/N.I.K.A/resources/views/auth/admin-login.blade.php ENDPATH**/ ?>