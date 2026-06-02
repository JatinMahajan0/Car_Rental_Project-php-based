<?php
require 'config.php';
$page_title  = "Check Your Email - Velocity Elite";
$hide_navbar = true;
require 'includes/header.php';
$email  = filter_var($_GET['email'] ?? '', FILTER_VALIDATE_EMAIL)
        ? htmlspecialchars($_GET['email'])
        : '';
$resent = getFlash('resend_ok');
?>

<div class="min-h-screen flex items-center justify-center px-4" style="background:#030810;">
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-96 h-96 rounded-full pointer-events-none" style="background:radial-gradient(circle, rgba(0,229,255,0.06) 0%, transparent 70%);"></div>

    <div class="w-full max-w-md text-center relative z-10">
        <!-- Logo -->
        <a href="/car-rental/index.php" class="flex items-center gap-2 justify-center mb-10 w-fit mx-auto">
            <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="8" fill="#00E5FF" fill-opacity="0.15"/>
                <path d="M6 20l4-8h12l4 8M8 20h16" stroke="#00E5FF" stroke-width="2" stroke-linecap="round"/>
                <circle cx="11" cy="22" r="2" fill="#00E5FF"/>
                <circle cx="21" cy="22" r="2" fill="#00E5FF"/>
            </svg>
            <span class="font-black text-lg tracking-tight text-white">Velocity <span style="color:#00E5FF;">Elite</span></span>
        </a>

        <!-- Card -->
        <div class="p-8 rounded-2xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
            <!-- Icon -->
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6"
                 style="background:rgba(0,229,255,0.1); border:1px solid rgba(0,229,255,0.3);">
                <svg class="w-8 h-8" style="color:#00E5FF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <h1 class="font-black text-2xl text-white mb-3">Check Your Email</h1>
            <p class="text-slate-400 text-sm leading-relaxed mb-2">We sent a password reset link to</p>
            <p class="font-semibold text-sm mb-4" style="color:#00E5FF;">
                <?= $email ?: 'your email address' ?>
            </p>

            <?php if ($resent): ?>
            <div class="flex items-center justify-center gap-2 mb-6 p-3 rounded-lg"
                 style="background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.3);">
                <span style="color:#4ade80;">✓</span>
                <span class="text-xs" style="color:#4ade80;">Reset email resent successfully!</span>
            </div>
            <?php else: ?>
            <p class="text-slate-500 text-xs mb-6">
                Didn't receive the email? Check your spam folder or click below to resend.
            </p>
            <?php endif; ?>

            <!-- Resend: real POST form — triggers actual email send -->
            <?php if ($email): ?>
            <form method="POST" action="/car-rental/actions/forgot_password_action.php" id="resend-form">
                <?= csrfField() ?>
                <input type="hidden" name="email" value="<?= $email ?>">
                <button type="submit" id="resend-btn"
                        class="w-full py-3.5 rounded-xl text-slate-300 font-semibold text-sm border transition-all hover:border-cyan-400 hover:text-cyan-400 mb-4"
                        style="border-color:rgba(255,255,255,0.15); background:transparent; cursor:pointer;">
                    Resend Email
                </button>
            </form>
            <?php else: ?>
            <a href="/car-rental/forgot_password.php"
               class="block w-full py-3.5 rounded-xl text-slate-300 font-semibold text-sm border transition-all hover:border-cyan-400 hover:text-cyan-400 mb-4"
               style="border-color:rgba(255,255,255,0.15);">
                Try Again
            </a>
            <?php endif; ?>

            <a href="/car-rental/login.php"
               class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-300 text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Login
            </a>
        </div>
    </div>
</div>

<script>
document.getElementById('resend-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('resend-btn');
    btn.disabled = true;
    btn.textContent = 'Sending...';
    btn.style.opacity = '0.6';
});
</script>

<?php require 'includes/footer.php'; ?>
