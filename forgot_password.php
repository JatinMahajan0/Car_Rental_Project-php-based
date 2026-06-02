<?php
require 'config.php';
if (isLoggedIn()) redirect('/car-rental/user/index.php');
$page_title  = "Reset Password - Velocity Elite";
$hide_navbar = true;
require 'includes/header.php';
$err = getFlash('forgot_error');
$ok  = getFlash('forgot_ok');
?>

<div class="flex w-full min-h-screen" style="background:#030810;">

    <!-- Left Panel: Image -->
    <div class="hidden lg:block lg:w-[45%] relative overflow-hidden">
        <img src="https://images.unsplash.com/photo-1494976388531-d1058494cdd8?q=80&w=1920&fit=crop"
             alt="" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0" style="background:linear-gradient(to right, #030810 0%, rgba(3,8,16,0.4) 60%, rgba(3,8,16,0.7) 100%);"></div>
        <div class="absolute inset-0" style="background:linear-gradient(to top, #030810 0%, transparent 60%);"></div>
        <div class="absolute bottom-12 left-10 right-10 z-10">
            <h2 class="font-black text-4xl text-white mb-3 leading-tight">Unleash the<br>Extraordinary.</h2>
            <p class="text-slate-400 text-base leading-relaxed border-l-2 border-cyan-400 pl-4">
                Experience the pinnacle of automotive engineering.<br>Your journey back to elite performance starts here.
            </p>
        </div>
    </div>

    <!-- Right Panel: Form -->
    <div class="w-full lg:w-[55%] flex items-center justify-center p-8 md:p-14">
        <div class="w-full max-w-md">

            <!-- Logo -->
            <a href="/car-rental/index.php" class="flex items-center gap-2 mb-10 group w-fit">
                <svg class="w-7 h-7 group-hover:scale-110 transition-transform" viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="8" fill="#00E5FF" fill-opacity="0.15"/>
                    <path d="M6 20l4-8h12l4 8M8 20h16" stroke="#00E5FF" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="11" cy="22" r="2" fill="#00E5FF"/>
                    <circle cx="21" cy="22" r="2" fill="#00E5FF"/>
                </svg>
                <span class="font-black text-lg tracking-tight text-white">Velocity <span style="color:#00E5FF;">Elite</span></span>
            </a>

            <div class="mb-8">
                <h1 class="font-black text-3xl text-white mb-2">Reset Password</h1>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Enter the email address associated with your<br>
                    VELOCITY account, and we'll send you a secure link<br>
                    to reset your password.
                </p>
            </div>

            <?php if ($err): ?>
            <div class="p-4 rounded-xl mb-5 border" style="background:rgba(239,68,68,0.08); border-color:rgba(239,68,68,0.4); border-left:3px solid #ef4444;">
                <p class="text-red-400 text-sm"><?= htmlspecialchars($err['msg']) ?></p>
            </div>
            <?php endif; ?>

            <form method="POST" action="/car-rental/actions/forgot_password_action.php" id="forgot-form">
                <?= csrfField() ?>

                <div class="mb-6">
                    <label class="block text-slate-400 text-sm mb-2">Email Address</label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <input type="email" name="email" required placeholder="name@example.com"
                               class="w-full pl-11 pr-4 py-3.5 rounded-xl text-white text-sm outline-none transition-all"
                               style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                               onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                    </div>
                </div>

                <button type="submit" id="send-btn"
                        class="w-full py-4 rounded-xl text-black font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2 transition-all hover:brightness-110"
                        style="background:#00E5FF; box-shadow:0 0 20px rgba(0,229,255,0.25);">
                    <span id="btn-text">Send Reset Link</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="/car-rental/login.php" class="inline-flex items-center gap-2 text-slate-400 hover:text-white text-sm transition-colors group">
                    <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Login
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('forgot-form').addEventListener('submit', function() {
    const btn = document.getElementById('send-btn');
    btn.disabled = true;
    document.getElementById('btn-text').textContent = 'Sending...';
    btn.style.opacity = '0.8';
});
</script>

<?php require 'includes/footer.php'; ?>
