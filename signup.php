<?php
require 'config.php';
if (isLoggedIn()) redirect('/car-rental/user/index.php');
$page_title = "Create Account - Velocity Elite";
$hide_navbar = true;
$returnUrl  = $_GET['return_url'] ?? '';
require 'includes/header.php';
$err = getFlash('signup_error');
?>

<div class="flex w-full min-h-screen" style="background:#030810;">

    <!-- Left: Branding Panel -->
    <div class="hidden lg:flex lg:w-[45%] relative overflow-hidden">
        <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1920&fit=crop"
             alt="" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0" style="background:linear-gradient(to top, #030810 0%, rgba(3,8,16,0.6) 50%, rgba(3,8,16,0.3) 100%);"></div>

        <div class="relative z-10 w-full flex flex-col justify-end p-10">
            <!-- Logo — links to index -->
            <a href="/car-rental/index.php" class="flex items-center gap-2 mb-6 group w-fit">
                <svg class="w-7 h-7 group-hover:scale-110 transition-transform" viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="8" fill="#00E5FF" fill-opacity="0.2"/>
                    <path d="M6 20l4-8h12l4 8M8 20h16" stroke="#00E5FF" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="11" cy="22" r="2" fill="#00E5FF"/>
                    <circle cx="21" cy="22" r="2" fill="#00E5FF"/>
                </svg>
                <span class="font-black text-xl tracking-tight text-white group-hover:text-cyan-300 transition-colors">
                    Velocity <span style="color:#00E5FF;">Elite</span>
                </span>
            </a>
            <h1 class="font-black text-4xl text-white mb-3 leading-tight">Frictionless<br>Premium Mobility.</h1>
            <p class="text-slate-400 text-base leading-relaxed max-w-sm">
                Experience the pinnacle of automotive engineering with instant access to our exclusive fleet.
            </p>
        </div>
    </div>

    <!-- Right: Sign Up Form -->
    <div class="w-full lg:w-[55%] flex items-center justify-center p-8 md:p-14 overflow-y-auto">
        <div class="w-full max-w-md">

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
                <h2 class="font-black text-3xl text-white mb-2">Create Account</h2>
                <p class="text-slate-400 text-sm">Enter your details to access the premium fleet.</p>
            </div>

            <?php if ($err): ?>
            <div class="flex items-center gap-3 p-4 rounded-xl border mb-6" style="background:rgba(239,68,68,0.08); border-color:rgba(239,68,68,0.4); border-left:3px solid #ef4444;">
                <svg class="w-4 h-4 flex-shrink-0 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span class="text-red-400 text-sm"><?= htmlspecialchars($err['msg']) ?></span>
            </div>
            <?php endif; ?>

            <form class="space-y-4" method="POST" action="/car-rental/actions/signup_action.php" id="signup-form">
                <?= csrfField() ?>
                <input type="hidden" name="return_url" value="<?= htmlspecialchars($returnUrl) ?>">

                <!-- Name Row -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 text-sm mb-1.5">First Name</label>
                        <input type="text" name="firstname" required placeholder="John"
                               class="w-full px-4 py-3 rounded-xl text-white text-sm outline-none transition-all"
                               style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                               onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-sm mb-1.5">Last Name</label>
                        <input type="text" name="lastname" required placeholder="Doe"
                               class="w-full px-4 py-3 rounded-xl text-white text-sm outline-none transition-all"
                               style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                               onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-slate-400 text-sm mb-1.5">Email Address</label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <input type="email" name="email" required placeholder="john@company.com"
                               class="w-full pl-11 pr-4 py-3 rounded-xl text-white text-sm outline-none transition-all"
                               style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                               onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                    </div>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-slate-400 text-sm mb-1.5">Phone Number</label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <input type="tel" name="phone" placeholder="+1 (555) 000-0000"
                               class="w-full pl-11 pr-4 py-3 rounded-xl text-white text-sm outline-none transition-all"
                               style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                               onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-slate-400 text-sm mb-1.5">Password</label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <input type="password" name="password" id="pw-main" required placeholder="Min. 8 characters"
                               class="w-full pl-11 pr-12 py-3 rounded-xl text-white text-sm outline-none transition-all"
                               style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                               onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                        <button type="button" onclick="togglePw('pw-main',this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-slate-400 text-sm mb-1.5">Confirm Password</label>
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <input type="password" name="confirm" id="pw-confirm" required placeholder="Confirm password"
                               class="w-full pl-11 pr-4 py-3 rounded-xl text-white text-sm outline-none transition-all"
                               style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                               onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                    </div>
                </div>

                <!-- Terms -->
                <label class="flex items-start gap-3 cursor-pointer pt-1">
                    <input type="checkbox" id="terms" required class="w-4 h-4 mt-0.5 rounded flex-shrink-0" style="accent-color:#00E5FF;">
                    <span class="text-slate-400 text-sm leading-relaxed">
                        I agree to the <a href="#" class="transition-colors" style="color:#00E5FF;">Terms of Service</a>
                        and <a href="#" class="transition-colors" style="color:#00E5FF;">Privacy Policy</a>.
                    </span>
                </label>

                <!-- Submit -->
                <button type="submit" id="submit-btn"
                        class="w-full py-4 rounded-xl text-black font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2 transition-all hover:brightness-110 hover:scale-[1.01]"
                        style="background:#00E5FF; box-shadow:0 0 20px rgba(0,229,255,0.25);">
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center space-y-3">
                <p class="text-slate-500 text-sm">
                    Already have an account?
                    <a href="/car-rental/login.php" class="font-semibold ml-1 transition-colors" style="color:#00E5FF;">Login</a>
                </p>
                <!-- Browse without account -->
                <a href="/car-rental/index.php" class="inline-flex items-center gap-1.5 text-slate-600 hover:text-slate-400 text-sm transition-colors group">
                    <svg class="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Browse without an account
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePw(id, btn) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}
document.getElementById('signup-form').addEventListener('submit', function(e) {
    const pw  = document.getElementById('pw-main').value;
    const cfm = document.getElementById('pw-confirm').value;
    if (pw !== cfm) {
        e.preventDefault();
        alert('Passwords do not match.');
        return;
    }
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.textContent = 'Creating Account...';
    btn.style.opacity = '0.8';
    // Merge firstname + lastname into fullname
    const form = this;
    const fn = form.querySelector('[name="firstname"]').value;
    const ln = form.querySelector('[name="lastname"]').value;
    const hidden = document.createElement('input');
    hidden.type = 'hidden'; hidden.name = 'fullname'; hidden.value = (fn + ' ' + ln).trim();
    form.appendChild(hidden);
});
</script>

<?php require 'includes/footer.php'; ?>
