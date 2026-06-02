<?php
require 'config.php';
$page_title  = "Password Updated - Velocity Elite";
$hide_navbar = true;
require 'includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center px-4" style="background:#030810;">
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-96 h-96 rounded-full pointer-events-none" style="background:radial-gradient(circle, rgba(0,229,255,0.05) 0%, transparent 70%);"></div>

    <div class="w-full max-w-sm text-center relative z-10 p-8 rounded-2xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
        <!-- Animated checkmark -->
        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 relative" style="background:rgba(0,229,255,0.1); border:2px solid rgba(0,229,255,0.3);">
            <svg class="w-10 h-10" style="color:#00E5FF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <div class="absolute inset-0 rounded-full animate-ping" style="background:rgba(0,229,255,0.08);"></div>
        </div>

        <h1 class="font-black text-2xl text-white mb-3">Password Updated</h1>
        <p class="text-slate-400 text-sm mb-2 leading-relaxed">Your password has been successfully updated.</p>
        <p class="text-slate-500 text-sm mb-8">You can now sign in to your Velocity Elite account with your new password.</p>

        <a href="/car-rental/login.php"
           class="block w-full py-4 rounded-xl text-black font-bold text-sm uppercase tracking-wider hover:brightness-110 transition-all"
           style="background:#00E5FF;">
            Back to Sign In
        </a>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
