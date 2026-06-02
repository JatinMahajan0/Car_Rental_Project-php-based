<?php
require 'config.php';
if (isLoggedIn()) redirect(isAdmin() ? '/car-rental/admin/index.php' : '/car-rental/user/index.php');
$page_title  = "Login - Velocity Elite";
$hide_navbar = true;
$returnUrl   = $_GET['return_url'] ?? '';
require 'includes/header.php';
$loginFlash = getFlash('login_success');
$loginError = getFlash('login_error');
?>

<div class="flex w-full min-h-screen" style="background:#030810;">
    <!-- Left: Branding Panel -->
    <div class="hidden lg:flex lg:w-[55%] relative overflow-hidden">
        <img src="https://images.unsplash.com/photo-1544636331-e26879cd4d9b?q=80&w=1920&fit=crop"
             alt="" class="absolute inset-0 w-full h-full object-cover scale-105 hover:scale-100 transition-transform duration-[2s]">
        <div class="absolute inset-0" style="background:linear-gradient(to right, #030810 0%, rgba(3,8,16,0.4) 50%, rgba(3,8,16,0.7) 100%);"></div>
        <div class="absolute inset-0" style="background:linear-gradient(to top, #030810 0%, transparent 60%);"></div>

        <div class="absolute bottom-12 left-10 right-10 z-10">
            <!-- Logo — links to index -->
            <a href="/car-rental/index.php" class="flex items-center gap-2 mb-8 group w-fit">
                <svg class="w-8 h-8 group-hover:scale-110 transition-transform" viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="8" fill="#00E5FF" fill-opacity="0.2"/>
                    <path d="M6 20l4-8h12l4 8M8 20h16" stroke="#00E5FF" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="11" cy="22" r="2" fill="#00E5FF"/>
                    <circle cx="21" cy="22" r="2" fill="#00E5FF"/>
                </svg>
                <span class="font-black text-xl tracking-tight text-white group-hover:text-cyan-300 transition-colors">
                    Velocity <span style="color:#00E5FF;">Elite</span>
                </span>
            </a>
            <h1 class="font-black text-4xl text-white mb-4 leading-tight">Experience the pinnacle<br>of automotive engineering.</h1>
            <p class="text-slate-400 max-w-md text-base border-l-2 border-cyan-400 pl-4 leading-relaxed">
                Exclusive access to the world's most coveted high-performance fleet. Reserved for the elite.
            </p>
        </div>
    </div>

    <!-- Right: Login Form -->
    <div class="w-full lg:w-[45%] flex items-center justify-center p-8 md:p-14 relative overflow-y-auto">
        <div class="absolute top-1/3 -right-20 w-80 h-80 rounded-full pointer-events-none" style="background:radial-gradient(circle, rgba(0,229,255,0.06) 0%, transparent 70%);"></div>

        <div class="w-full max-w-md relative z-10">
            <!-- Mobile Logo -->
            <a href="/car-rental/index.php" class="flex lg:hidden items-center gap-2 mb-8 group w-fit">
                <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="8" fill="#00E5FF" fill-opacity="0.15"/>
                    <path d="M6 20l4-8h12l4 8M8 20h16" stroke="#00E5FF" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="11" cy="22" r="2" fill="#00E5FF"/>
                    <circle cx="21" cy="22" r="2" fill="#00E5FF"/>
                </svg>
                <span class="font-black text-lg tracking-tight text-white">Velocity <span style="color:#00E5FF;">Elite</span></span>
            </a>

            <div class="mb-8">
                <h2 class="font-black text-3xl text-white mb-2" style="color:#7df4ff;">Welcome Back</h2>
                <p class="text-slate-400 text-sm">Enter your credentials to access your fleet.</p>
            </div>

            <!-- Flash Messages -->
            <?php if ($loginFlash): ?>
            <div class="flex items-center gap-3 p-4 rounded-xl border mb-6" style="background:rgba(0,229,255,0.08); border-color:rgba(0,229,255,0.3);">
                <svg class="w-4 h-4 flex-shrink-0" style="color:#00E5FF;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="text-sm" style="color:#00E5FF;"><?= htmlspecialchars($loginFlash['msg']) ?></span>
            </div>
            <?php endif; ?>
            <?php if ($loginError): ?>
            <div class="flex items-center gap-3 p-4 rounded-xl border mb-6" style="background:rgba(239,68,68,0.08); border-color:rgba(239,68,68,0.4); border-left:3px solid #ef4444;">
                <svg class="w-4 h-4 flex-shrink-0 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <div>
                    <p class="font-semibold text-red-400 text-sm">Access Denied</p>
                    <p class="text-red-400/80 text-xs mt-0.5"><?= htmlspecialchars($loginError['msg']) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <form method="POST" action="/car-rental/actions/login_action.php" id="login-form" class="space-y-5" autocomplete="off">
                <?= csrfField() ?>
                <input type="hidden" name="return_url" value="<?= htmlspecialchars($returnUrl) ?>">

                <!-- Email -->
                <div>
                    <label class="block text-slate-400 text-sm mb-2">Email Address</label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <input name="email" type="email" required placeholder="client@luxedrive.com"
                               autocomplete="username"
                               class="w-full pl-11 pr-4 py-3.5 rounded-xl text-white text-sm outline-none transition-all"
                               style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                               onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-slate-400 text-sm">Password</label>
                        <a href="/car-rental/forgot_password.php" class="text-xs font-semibold transition-colors" style="color:#00E5FF;">Forgot Password?</a>
                    </div>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <input name="password" type="password" id="pw-field" required placeholder="••••••••"
                               autocomplete="current-password"
                               class="w-full pl-11 pr-12 py-3.5 rounded-xl text-white text-sm outline-none tracking-widest transition-all"
                               style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                               onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                        <button type="button" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors"
                                onclick="const f=document.getElementById('pw-field');f.type=f.type==='password'?'text':'password';">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <label class="flex items-center gap-3 cursor-pointer w-fit">
                    <input type="checkbox" class="w-4 h-4 rounded" style="accent-color:#00E5FF;">
                    <span class="text-slate-400 text-sm">Remember Me</span>
                </label>

                <!-- Submit -->
                <button type="submit" id="login-btn"
                        class="w-full py-4 rounded-xl text-black font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2 transition-all hover:brightness-110 hover:scale-[1.01]"
                        style="background:#00E5FF; box-shadow:0 0 20px rgba(0,229,255,0.3);">
                    <span id="btn-text">Login</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
            </form>

            <div class="mt-8 text-center space-y-3">
                <p class="text-slate-500 text-sm">
                    Don't have an account?
                    <a href="/car-rental/signup.php<?= $returnUrl ? '?return_url='.urlencode($returnUrl) : '' ?>"
                       class="font-semibold ml-1 transition-colors" style="color:#00E5FF;">Sign up</a>
                </p>
                <!-- Browse without login -->
                <a href="/car-rental/index.php" class="inline-flex items-center gap-1.5 text-slate-600 hover:text-slate-400 text-sm transition-colors group">
                    <svg class="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Browse without logging in
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('login-form').addEventListener('submit', function() {
    const btn = document.getElementById('login-btn');
    setTimeout(() => {
        btn.disabled = true;
        document.getElementById('btn-text').innerHTML = 'Logging in...';
        btn.style.opacity = '0.8';
    }, 100);
});
</script>

<?php require 'includes/footer.php'; ?>
