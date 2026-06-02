<?php
require 'config.php';
$page_title = "Elite Support, Always On. - Velocity Elite";
require 'includes/header.php';

$successMsg = getFlash('contact_success');
$errorMsg   = getFlash('contact_error');
?>

<!-- Hero -->
<div class="text-center py-16 px-6">
    <h1 class="text-4xl md:text-5xl font-black text-white mb-4">
        Elite Support, <span style="color:#00E5FF;">Always On.</span>
    </h1>
    <p class="text-slate-400 max-w-2xl mx-auto text-base">
        Whether you require immediate roadside assistance or need to modify a corporate booking, our dedicated concierges are ready.
    </p>
</div>

<?php if ($successMsg): ?>
<div class="max-w-6xl mx-auto px-6 mb-6">
    <div class="flex items-center gap-3 p-4 rounded-xl border" style="background:rgba(0,229,255,0.08); border-color:rgba(0,229,255,0.3);">
        <svg class="w-5 h-5 flex-shrink-0" style="color:#00E5FF;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span class="text-white text-sm"><?= htmlspecialchars($successMsg['msg']) ?></span>
    </div>
</div>
<?php endif; ?>

<!-- Main Content -->
<div class="max-w-6xl mx-auto px-6 pb-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- LEFT: Contact Info Cards -->
        <div class="space-y-4">
            <!-- 24/7 Roadside -->
            <div class="p-6 rounded-2xl" style="background:#0d1b2a; border:1px solid rgba(239,68,68,0.3); border-left: 3px solid #ef4444;">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(239,68,68,0.15);">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg mb-1">24/7 Roadside</h3>
                        <p class="text-slate-400 text-sm mb-3">Immediate assistance for active rentals.</p>
                        <a href="tel:+18005550199" class="font-bold text-lg" style="color:#00E5FF;">+1 (800) 555-0199</a>
                    </div>
                </div>
            </div>

            <!-- Concierge Desk -->
            <div class="p-6 rounded-2xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(0,229,255,0.1);">
                        <svg class="w-5 h-5" style="color:#00E5FF;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg mb-1">Concierge Desk</h3>
                        <p class="text-slate-400 text-sm mb-3">Booking modifications & general inquiries.</p>
                        <a href="mailto:elite@velocity.com" class="flex items-center gap-1 text-sm font-semibold" style="color:#00E5FF;">
                            elite@velocity.com
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Map / HQ — clicking opens Google Maps -->
            <a href="https://www.google.com/maps/search/One+Velocity+Plaza+Los+Angeles+CA" target="_blank" rel="noopener"
               class="block rounded-2xl overflow-hidden group" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
                <div class="h-48 relative overflow-hidden" style="background:linear-gradient(135deg, #0a1628 0%, #1a2a40 100%);">
                    <!-- Stylised map pattern -->
                    <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 400 200" fill="none">
                        <line x1="0" y1="50"  x2="400" y2="50"  stroke="#00E5FF" stroke-width="0.5"/>
                        <line x1="0" y1="100" x2="400" y2="100" stroke="#00E5FF" stroke-width="0.5"/>
                        <line x1="0" y1="150" x2="400" y2="150" stroke="#00E5FF" stroke-width="0.5"/>
                        <line x1="50"  y1="0" x2="50"  y2="200" stroke="#00E5FF" stroke-width="0.5"/>
                        <line x1="150" y1="0" x2="150" y2="200" stroke="#00E5FF" stroke-width="0.5"/>
                        <line x1="250" y1="0" x2="250" y2="200" stroke="#00E5FF" stroke-width="0.5"/>
                        <line x1="350" y1="0" x2="350" y2="200" stroke="#00E5FF" stroke-width="0.5"/>
                        <circle cx="200" cy="100" r="8" fill="#00E5FF" opacity="0.8"/>
                        <circle cx="200" cy="100" r="20" fill="#00E5FF" opacity="0.15"/>
                    </svg>
                    <!-- Hover overlay -->
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                         style="background:rgba(0,229,255,0.08);">
                        <span class="text-sm font-bold px-4 py-2 rounded-lg" style="background:rgba(0,229,255,0.15); color:#00E5FF; border:1px solid rgba(0,229,255,0.3);">
                            Open in Google Maps →
                        </span>
                    </div>
                    <div class="absolute bottom-4 left-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" style="color:#00E5FF;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                            <div>
                                <p class="text-white font-bold text-sm">Global Headquarters</p>
                                <p class="text-slate-400 text-xs">One Velocity Plaza, Los Angeles, CA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- RIGHT: Contact Form -->
        <div class="p-8 rounded-2xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
            <h2 class="text-white font-bold text-2xl mb-6">Send a Message</h2>
            <form method="POST" action="/car-rental/actions/contact_submit.php" id="contact-form" class="space-y-4">
                <?= csrfField() ?>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-400 text-sm mb-2">Full Name</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <input type="text" name="name" required placeholder="John Doe"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl text-white text-sm outline-none transition-all focus:border-cyan-400"
                                   style="background:#111827; border:1px solid rgba(255,255,255,0.1);"
                                   onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-400 text-sm mb-2">Email Address</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <input type="email" name="email" required placeholder="john@company.com"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl text-white text-sm outline-none transition-all focus:border-cyan-400"
                                   style="background:#111827; border:1px solid rgba(255,255,255,0.1);">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-400 text-sm mb-2">Inquiry Type</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <select name="subject" class="w-full pl-10 pr-4 py-3 rounded-xl text-white text-sm outline-none transition-all appearance-none cursor-pointer"
                                style="background:#111827; border:1px solid rgba(255,255,255,0.1);">
                            <option value="" class="bg-gray-900">Select category...</option>
                            <option value="Booking Inquiry" class="bg-gray-900">Booking Inquiry</option>
                            <option value="Booking Modification" class="bg-gray-900">Booking Modification</option>
                            <option value="Roadside Assistance" class="bg-gray-900">Roadside Assistance</option>
                            <option value="Corporate Fleet" class="bg-gray-900">Corporate Fleet</option>
                            <option value="General Inquiry" class="bg-gray-900">General Inquiry</option>
                            <option value="Complaint" class="bg-gray-900">Complaint</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-400 text-sm mb-2">Message</label>
                    <textarea name="message" required rows="5" placeholder="How can we assist you today?"
                              class="w-full px-4 py-3 rounded-xl text-white text-sm outline-none transition-all resize-none focus:border-cyan-400"
                              style="background:#111827; border:1px solid rgba(255,255,255,0.1);"></textarea>
                </div>

                <button type="submit" id="send-btn"
                        class="w-full py-4 rounded-xl text-black font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2 hover:brightness-110 transition-all"
                        style="background:#00E5FF;">
                    Send Transmission
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('contact-form').addEventListener('submit', function() {
    const btn = document.getElementById('send-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Sending...';
});
</script>

<?php require 'includes/footer.php'; ?>
