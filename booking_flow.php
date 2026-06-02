<?php
require 'config.php';
requireLogin();

$car_id      = (int)($_GET['car_id'] ?? 0);
$pickup_date = $_GET['pickup'] ?? date('Y-m-d');
$return_date = $_GET['return_date'] ?? date('Y-m-d', strtotime('+3 days'));
$step        = (int)($_GET['step'] ?? 1);

$car = $pdo->prepare("SELECT * FROM cars WHERE id=? AND status='available'");
$car->execute([$car_id]);
$car = $car->fetch();
if (!$car) { flash('error','Vehicle not available.','error'); redirect('/car-rental/fleet.php'); }

$days  = max(1, (int)ceil((strtotime($return_date) - strtotime($pickup_date)) / 86400));
$base  = $car['price'] * $days;

$page_title  = "Book {$car['name']} - Velocity Elite";
$hide_navbar = true;
require 'includes/header.php';
?>

<div class="min-h-screen" style="background:#030810;">

    <!-- Top Bar -->
    <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:rgba(255,255,255,0.06); background:#030810;">
        <a href="/car-rental/index.php" class="flex items-center gap-2">
            <svg class="w-6 h-6" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="8" fill="#00E5FF" fill-opacity="0.15"/>
                <path d="M6 20l4-8h12l4 8M8 20h16" stroke="#00E5FF" stroke-width="2" stroke-linecap="round"/>
                <circle cx="11" cy="22" r="2" fill="#00E5FF"/>
                <circle cx="21" cy="22" r="2" fill="#00E5FF"/>
            </svg>
            <span class="font-black text-white tracking-tight">Premium <span style="color:#00E5FF;">Velocity</span></span>
        </a>
        <a href="/car-rental/car_details.php?id=<?= $car_id ?>" class="flex items-center gap-1.5 text-slate-400 hover:text-white text-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Cancel Booking
        </a>
    </div>

    <div class="max-w-6xl mx-auto px-4 md:px-6 py-8">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- LEFT: Car Summary + Steps -->
            <div class="w-full lg:w-80 flex-shrink-0">

                <!-- Car Image Hero Card -->
                <div class="relative rounded-2xl overflow-hidden mb-6" style="height:220px;">
                    <img src="<?= htmlspecialchars($car['image_path']) ?>" class="w-full h-full object-cover">
                    <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(3,8,16,0.95) 0%, rgba(3,8,16,0.3) 60%);"></div>
                    <div class="absolute top-3 left-3">
                        <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-black" style="background:#00E5FF;">Confirm Selection</span>
                    </div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <h2 class="font-black text-2xl text-white leading-tight"><?= htmlspecialchars($car['name']) ?></h2>
                        <div class="flex items-center gap-3 mt-1 text-slate-400 text-xs">
                            <span>⚡ 3.5s (0-60 mph)</span>
                            <span>·</span>
                            <span><?= htmlspecialchars($car['transmission']) ?></span>
                            <span>·</span>
                            <span><?= htmlspecialchars($car['fuel']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Step Indicator (Stitch: circles with connecting line) -->
                <div class="p-5 rounded-2xl mb-6" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
                    <div class="flex items-center justify-between relative">
                        <!-- Connecting line -->
                        <div class="absolute left-0 right-0 top-4 h-0.5 mx-10 z-0" style="background:rgba(255,255,255,0.1);"></div>
                        <div class="absolute left-0 top-4 h-0.5 z-0 transition-all" id="progress-line"
                             style="background:#00E5FF; width:<?= $step >= 2 ? ($step >= 3 ? '82%' : '41%') : '0%' ?>; left:10%;"></div>

                        <?php foreach ([1=>'SELECT', 2=>'DETAILS', 3=>'PAY'] as $num => $label): ?>
                        <div class="flex flex-col items-center z-10 relative">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-sm transition-all"
                                 style="<?= $step > $num ? 'background:#00E5FF; color:#000;' : ($step === $num ? 'background:#00E5FF; color:#000; box-shadow:0 0 16px rgba(0,229,255,0.5);' : 'background:rgba(255,255,255,0.1); color:#64748b;') ?>">
                                <?= $step > $num ? '✓' : $num ?>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider mt-2" style="color:<?= $step >= $num ? '#00E5FF' : '#64748b' ?>;"><?= $label ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Booking Summary Card -->
                <div class="p-5 rounded-2xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
                    <h3 class="text-white font-bold text-sm mb-4">Booking Summary</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Pickup</span>
                            <span class="text-white font-medium"><?= date('d M Y', strtotime($pickup_date)) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Return</span>
                            <span class="text-white font-medium"><?= date('d M Y', strtotime($return_date)) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Duration</span>
                            <span class="text-white font-medium"><?= $days ?> day<?= $days>1?'s':'' ?></span>
                        </div>
                        <div class="border-t pt-3" style="border-color:rgba(255,255,255,0.06);">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Base Rate</span>
                                <span class="text-white">₹<?= number_format($base) ?></span>
                            </div>
                            <div class="flex justify-between mt-1" id="addon-line" style="display:none!important;">
                                <span class="text-slate-400">Add-ons</span>
                                <span class="text-white" id="addon-total">₹0</span>
                            </div>
                        </div>
                        <div class="border-t pt-3" style="border-color:rgba(255,255,255,0.06);">
                            <div class="flex justify-between items-baseline">
                                <span class="text-white font-bold">Total</span>
                                <span class="font-black text-xl" style="color:#00E5FF;" id="grand-total">₹<?= number_format($base) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Form Steps -->
            <div class="flex-1 min-w-0">

                <?php if ($step === 1): ?>
                <!-- ===== STEP 1: Personal Details ===== -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-sm text-black" style="background:#00E5FF;">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                        </div>
                        <h2 class="text-white font-black text-2xl">Personal Details</h2>
                    </div>
                    <form method="POST" action="/car-rental/actions/booking_step1.php" class="space-y-5">
                        <?= csrfField() ?>
                        <input type="hidden" name="car_id" value="<?= $car_id ?>">
                        <input type="hidden" name="pickup_date" value="<?= htmlspecialchars($pickup_date) ?>">
                        <input type="hidden" name="return_date" value="<?= htmlspecialchars($return_date) ?>">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-400 text-sm mb-2">Full Name</label>
                                <input type="text" name="full_name" value="<?= htmlspecialchars($_SESSION['name']) ?>" required
                                       class="w-full px-4 py-3 rounded-xl text-white text-sm outline-none transition-all"
                                       style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                                       onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-sm mb-2">Email Address</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" required
                                       class="w-full px-4 py-3 rounded-xl text-white text-sm outline-none transition-all"
                                       style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                                       onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-sm mb-2">Phone Number</label>
                                <input type="tel" name="phone" value="<?= htmlspecialchars($_SESSION['phone'] ?? '') ?>" required
                                       class="w-full px-4 py-3 rounded-xl text-white text-sm outline-none transition-all"
                                       style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                                       onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-sm mb-2">Pickup Location</label>
                                <input type="text" name="pickup_location" value="<?= htmlspecialchars($car['location']) ?>" required
                                       class="w-full px-4 py-3 rounded-xl text-white text-sm outline-none transition-all"
                                       style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                                       onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                            </div>
                        </div>

                        <div>
                            <label class="block text-slate-400 text-sm mb-2">Driver's License Number</label>
                            <input type="text" name="license_number" required placeholder="DL-1234567890"
                                   class="w-full px-4 py-3 rounded-xl text-white text-sm outline-none transition-all"
                                   style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);"
                                   onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                        </div>

                        <!-- Add-ons -->
                        <div class="p-5 rounded-2xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
                            <h3 class="text-white font-bold text-base mb-4">Optional Add-ons</h3>
                            <div class="space-y-3">
                                <?php foreach ([
                                    ['gps','GPS Navigation','Stay on track anywhere.','₹299/day'],
                                    ['insurance','Premium Insurance','Comprehensive full coverage.','₹599/day'],
                                    ['chauffeur','Chauffeur Service','Professional driver for comfort.','₹1,499/day'],
                                ] as [$key,$name,$desc,$price]): ?>
                                <label class="flex items-center justify-between p-3 rounded-xl cursor-pointer transition-all hover:bg-white/5" style="border:1px solid rgba(255,255,255,0.06);">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="addons[]" value="<?= $key ?>"
                                               class="w-4 h-4 rounded" style="accent-color:#00E5FF;">
                                        <div>
                                            <p class="text-white text-sm font-semibold"><?= $name ?></p>
                                            <p class="text-slate-500 text-xs"><?= $desc ?></p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold" style="color:#00E5FF;"><?= $price ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 rounded-xl text-black font-black text-sm uppercase tracking-wider hover:brightness-110 transition-all" style="background:#00E5FF;">
                            Continue to Payment →
                        </button>
                    </form>
                </div>

                <?php elseif ($step === 2): ?>
                <!-- ===== STEP 2: PAYMENT ===== -->
                <?php
                $bookingData = $_SESSION['booking_draft'] ?? [];
                $addonCost   = (int)($bookingData['addon_total'] ?? 0);
                $grandTotal  = $base + $addonCost;
                ?>
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-sm text-black" style="background:#00E5FF;">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                        </div>
                        <h2 class="text-white font-black text-2xl">Secure Payment</h2>
                    </div>

                    <form method="POST" action="/car-rental/actions/booking_pay.php" id="pay-form" class="space-y-5">
                        <?= csrfField() ?>

                        <!-- Payment Method Tabs -->
                        <div class="flex gap-3 mb-2">
                            <?php foreach (['card'=>'💳 Card','upi'=>'📱 UPI','wallet'=>'👛 Wallet'] as $k=>$v): ?>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="pay_method" value="<?= $k ?>" <?= $k==='card'?'checked':'' ?> class="hidden" onchange="togglePayMethod('<?= $k ?>')">
                                <div class="pay-tab py-3 text-sm font-bold text-center rounded-xl transition-all <?= $k==='card'?'active':'inactive' ?>"
                                     id="tab-<?= $k ?>"
                                     style="<?= $k==='card'?'background:rgba(0,229,255,0.12); color:#00E5FF; border:1px solid rgba(0,229,255,0.4);':'background:#0d1b2a; color:#64748b; border:1px solid rgba(255,255,255,0.07);' ?>">
                                    <?= $v ?>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>

                        <!-- Card Fields -->
                        <div id="card-fields" class="space-y-4 p-5 rounded-2xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
                            <div>
                                <label class="block text-slate-400 text-sm mb-2">Card Number</label>
                                <input type="text" id="card-num" name="card_number" maxlength="19" placeholder="1234 5678 9012 3456"
                                       oninput="formatCard(this)"
                                       class="w-full px-4 py-3 rounded-xl text-white text-sm outline-none font-mono tracking-wider transition-all"
                                       style="background:#111827; border:1px solid rgba(255,255,255,0.1);"
                                       onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-slate-400 text-sm mb-2">Expiry Date</label>
                                    <input type="text" name="card_expiry" maxlength="5" placeholder="MM/YY"
                                           oninput="formatExpiry(this)"
                                           class="w-full px-4 py-3 rounded-xl text-white text-sm outline-none font-mono tracking-wider transition-all"
                                           style="background:#111827; border:1px solid rgba(255,255,255,0.1);"
                                           onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-sm mb-2">CVV</label>
                                    <input type="text" name="card_cvv" maxlength="4" placeholder="•••"
                                           class="w-full px-4 py-3 rounded-xl text-white text-sm outline-none font-mono tracking-wider transition-all"
                                           style="background:#111827; border:1px solid rgba(255,255,255,0.1);"
                                           onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                                </div>
                            </div>
                            <div>
                                <label class="block text-slate-400 text-sm mb-2">Cardholder Name</label>
                                <input type="text" name="card_name" value="<?= htmlspecialchars($_SESSION['name']) ?>" required
                                       class="w-full px-4 py-3 rounded-xl text-white text-sm outline-none transition-all"
                                       style="background:#111827; border:1px solid rgba(255,255,255,0.1);"
                                       onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                            </div>
                        </div>

                        <!-- UPI Fields (hidden) -->
                        <div id="upi-fields" class="hidden space-y-4 p-5 rounded-2xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
                            <div>
                                <label class="block text-slate-400 text-sm mb-2">UPI ID</label>
                                <input type="text" name="upi_id" placeholder="yourname@upi"
                                       class="w-full px-4 py-3 rounded-xl text-white text-sm outline-none transition-all"
                                       style="background:#111827; border:1px solid rgba(255,255,255,0.1);"
                                       onfocus="this.style.borderColor='#00E5FF'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                            </div>
                            <p class="text-slate-500 text-xs">A payment request will be sent to your UPI app.</p>
                        </div>

                        <!-- Wallet Fields (hidden) -->
                        <div id="wallet-fields" class="hidden space-y-3 p-5 rounded-2xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
                            <?php foreach (['Paytm','PhonePe','Amazon Pay','MobiKwik'] as $w): ?>
                            <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer hover:bg-white/5 transition-all" style="border:1px solid rgba(255,255,255,0.06);">
                                <input type="radio" name="wallet_provider" value="<?= $w ?>" style="accent-color:#00E5FF;" class="w-4 h-4">
                                <span class="text-white text-sm font-medium"><?= $w ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>

                        <!-- Total & Submit -->
                        <div class="p-5 rounded-2xl" style="background:rgba(0,229,255,0.06); border:1px solid rgba(0,229,255,0.2);">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-slate-300 font-semibold">Amount to Pay</span>
                                <span class="font-black text-2xl" style="color:#00E5FF;">₹<?= number_format($grandTotal) ?></span>
                            </div>
                            <button type="submit" id="pay-btn"
                                    class="w-full py-4 rounded-xl text-black font-black text-base uppercase tracking-wider hover:brightness-110 transition-all flex items-center justify-center gap-2"
                                    style="background:#00E5FF; box-shadow:0 0 24px rgba(0,229,255,0.4);">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Confirm Booking — ₹<?= number_format($grandTotal) ?>
                            </button>
                            <p class="text-center text-slate-500 text-xs mt-3">🔒 Secured by 256-bit SSL encryption</p>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<script>
function togglePayMethod(method) {
    ['card','upi','wallet'].forEach(m => {
        const tab = document.getElementById('tab-' + m);
        const fields = document.getElementById(m + '-fields');
        if (m === method) {
            tab.style.background = 'rgba(0,229,255,0.12)';
            tab.style.color = '#00E5FF';
            tab.style.border = '1px solid rgba(0,229,255,0.4)';
            fields?.classList.remove('hidden');
        } else {
            tab.style.background = '#0d1b2a';
            tab.style.color = '#64748b';
            tab.style.border = '1px solid rgba(255,255,255,0.07)';
            fields?.classList.add('hidden');
        }
    });
}
function formatCard(el) {
    let v = el.value.replace(/\D/g,'').substring(0,16);
    el.value = v.match(/.{1,4}/g)?.join(' ') || v;
}
function formatExpiry(el) {
    let v = el.value.replace(/\D/g,'');
    if (v.length >= 2) v = v.substring(0,2) + '/' + v.substring(2,4);
    el.value = v;
}
document.getElementById('pay-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('pay-btn');
    setTimeout(() => {
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Processing...';
        }
    }, 100);
});
</script>

<?php require 'includes/footer.php'; ?>
