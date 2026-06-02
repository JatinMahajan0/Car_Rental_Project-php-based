<?php
require 'config.php';
$page_title  = "Create New Password - Velocity Elite";
$hide_navbar = true;
require 'includes/header.php';

$token = $_GET['token'] ?? '';
if (!$token) redirect('/car-rental/forgot_password.php');

// Validate token
$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token=? AND expires_at > NOW()");
$stmt->execute([$token]);
$reset = $stmt->fetch();
if (!$reset) {
    flash('forgot_error', 'This reset link has expired or is invalid. Please request a new one.', 'error');
    redirect('/car-rental/forgot_password.php');
}
$err = getFlash('reset_error');
?>

<div class="flex w-full min-h-screen" style="background:#030810;">

    <!-- Left Panel -->
    <div class="hidden lg:block lg:w-[40%] relative overflow-hidden">
        <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1920&fit=crop"
             alt="" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0" style="background:linear-gradient(to right, #030810 0%, rgba(3,8,16,0.5) 100%);"></div>
        <div class="absolute inset-0" style="background:linear-gradient(to top, #030810 0%, transparent 60%);"></div>
        <div class="absolute bottom-12 left-10 right-10">
            <p class="text-slate-500 text-sm tracking-widest uppercase mb-3">Velocity Elite</p>
            <h2 class="font-black text-3xl text-white leading-tight">Secure your<br>account.</h2>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="w-full lg:w-[60%] flex items-center justify-center p-8 md:p-14">
        <div class="w-full max-w-md">
            <!-- Logo + Back -->
            <div class="flex items-center justify-between mb-10">
                <a href="/car-rental/index.php" class="flex items-center gap-2 group">
                    <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none">
                        <rect width="32" height="32" rx="8" fill="#00E5FF" fill-opacity="0.15"/>
                        <path d="M6 20l4-8h12l4 8M8 20h16" stroke="#00E5FF" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="11" cy="22" r="2" fill="#00E5FF"/>
                        <circle cx="21" cy="22" r="2" fill="#00E5FF"/>
                    </svg>
                    <span class="font-black text-lg tracking-tight text-white">VELOCITY</span>
                </a>
                <a href="/car-rental/login.php" class="flex items-center gap-1.5 text-slate-500 hover:text-slate-300 text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Sign In
                </a>
            </div>

            <div class="mb-8">
                <h1 class="font-black text-3xl text-white mb-3">Create New Password</h1>
                <p class="text-slate-400 text-sm leading-relaxed">Please enter your new password below. Ensure it is strong and secure.</p>
            </div>

            <?php if ($err): ?>
            <div class="p-4 rounded-xl mb-5 border" style="background:rgba(239,68,68,0.08); border-color:rgba(239,68,68,0.4);">
                <p class="text-red-400 text-sm"><?= htmlspecialchars($err['msg']) ?></p>
            </div>
            <?php endif; ?>

            <form method="POST" action="/car-rental/actions/reset_password_action.php" id="reset-form">
                <?= csrfField() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <!-- New Password -->
                <div class="mb-5">
                    <label class="block text-slate-400 text-sm mb-2">New Password</label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <input type="password" name="password" id="pw1" required placeholder="Enter new password"
                               oninput="checkStrength(this.value)"
                               class="w-full pl-11 pr-12 py-3.5 rounded-xl text-white text-sm outline-none transition-all"
                               style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                               onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                        <button type="button" onclick="togglePw('pw1')" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <!-- Strength bar -->
                    <div class="mt-2 flex gap-1">
                        <div id="s1" class="flex-1 h-1 rounded-full transition-all" style="background:rgba(255,255,255,0.1);"></div>
                        <div id="s2" class="flex-1 h-1 rounded-full transition-all" style="background:rgba(255,255,255,0.1);"></div>
                        <div id="s3" class="flex-1 h-1 rounded-full transition-all" style="background:rgba(255,255,255,0.1);"></div>
                        <div id="s4" class="flex-1 h-1 rounded-full transition-all" style="background:rgba(255,255,255,0.1);"></div>
                    </div>
                    <p id="strength-text" class="text-xs mt-1" style="color:#ef4444;">Weak – Add numbers and special characters</p>
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label class="block text-slate-400 text-sm mb-2">Confirm Password</label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <input type="password" name="confirm" id="pw2" required placeholder="Confirm new password"
                               class="w-full pl-11 pr-12 py-3.5 rounded-xl text-white text-sm outline-none transition-all"
                               style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                               onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                        <button type="button" onclick="togglePw('pw2')" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <p id="match-err" class="text-xs mt-1 text-red-400 hidden">Passwords do not match</p>
                </div>

                <button type="submit" id="reset-btn"
                        class="w-full py-4 rounded-xl text-black font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2 hover:brightness-110 transition-all"
                        style="background:#00E5FF;">
                    Reset Password
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>

                <p class="text-center text-slate-500 text-sm mt-6">
                    Need help?
                    <a href="/car-rental/contact.php" class="transition-colors ml-1" style="color:#00E5FF;">Contact Support</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
function togglePw(id) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}
function checkStrength(pw) {
    const bars = ['s1','s2','s3','s4'];
    let score = 0;
    if (pw.length >= 8)            score++;
    if (/[A-Z]/.test(pw))          score++;
    if (/[0-9]/.test(pw))          score++;
    if (/[^A-Za-z0-9]/.test(pw))  score++;
    const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
    const labels = ['Weak – Add numbers and special characters','Fair – Add uppercase letters','Good – Add special characters','Strong'];
    bars.forEach((id,i) => {
        document.getElementById(id).style.background = i < score ? colors[score-1] : 'rgba(255,255,255,0.1)';
    });
    const txt = document.getElementById('strength-text');
    txt.textContent = score ? labels[score-1] : '';
    txt.style.color = score ? colors[score-1] : '#94a3b8';
}
document.getElementById('reset-form').addEventListener('submit', function(e) {
    const p1 = document.getElementById('pw1').value;
    const p2 = document.getElementById('pw2').value;
    if (p1 !== p2) {
        e.preventDefault();
        document.getElementById('match-err').classList.remove('hidden');
        return;
    }
    document.getElementById('match-err').classList.add('hidden');
    document.getElementById('reset-btn').disabled = true;
    document.getElementById('reset-btn').textContent = 'Resetting...';
});
document.getElementById('pw2').addEventListener('input', function() {
    const match = this.value === document.getElementById('pw1').value;
    document.getElementById('match-err').classList.toggle('hidden', match || !this.value);
});
</script>

<?php require 'includes/footer.php'; ?>
