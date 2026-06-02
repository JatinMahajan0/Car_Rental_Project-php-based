<?php
require 'config.php';
requireLogin('/car-rental/payment.php?' . http_build_query($_GET));

$carId = (int)($_GET['car_id'] ?? 0);
$draft = $_SESSION['booking_draft'] ?? null;

if (!$draft || ($carId && $draft['car_id'] !== $carId)) {
    flash('booking_error', 'Please start your booking from the car details page.', 'error');
    redirect('/car-rental/fleet.php');
}

$carId = $draft['car_id'];

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$carId]);
$car = $stmt->fetch();
if (!$car) redirect('/car-rental/fleet.php');

// Process payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfVerify();

    $method = $_POST['payment_method'] ?? 'card';

    // Create the booking
    $pdo->prepare("INSERT INTO bookings (user_id, car_id, pickup_date, return_date, total_price, add_ons) VALUES (?,?,?,?,?,?)")
        ->execute([
            $_SESSION['user_id'],
            $draft['car_id'],
            $draft['pickup_date'],
            $draft['return_date'],
            $draft['total_price'],
            json_encode($draft['add_ons'] ?? []),
        ]);
    $bookingId = (int)$pdo->lastInsertId();

    // Create payment record
    $txnId = strtoupper(bin2hex(random_bytes(8)));
    $pdo->prepare("INSERT INTO payments (booking_id, amount, status, method, transaction_id) VALUES (?,?,?,?,?)")
        ->execute([$bookingId, $draft['total_price'], 'completed', $method, $txnId]);

    // Notifications
    notify($pdo, $_SESSION['user_id'],
        "Booking confirmed for {$car['name']}! Pickup on " . date('M j, Y', strtotime($draft['pickup_date'])) . ".",
        'success', "/car-rental/user/booking_details.php?id=$bookingId");

    unset($_SESSION['booking_draft']);

    redirect("/car-rental/confirmation.php?booking_id=$bookingId");
}

$page_title = "Payment - LuxeDrive";
require 'includes/header.php';
require 'includes/toast.php';

$total  = $draft['total_price'];
$addOns = $draft['add_ons'] ?? [];
?>

<!-- Step Indicator -->
<div class="max-w-3xl mx-auto pt-8 mb-8">
    <div class="flex items-center gap-0">
        <?php foreach (['Select Dates','Add-Ons','Payment','Confirm'] as $i => $label): 
            $num = $i+1; $active = $num===3; $done = $num<3;
        ?>
        <div class="flex items-center <?= $i < 3 ? 'flex-1' : '' ?>">
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm border-2
                    <?= $done ? 'bg-primary-container border-primary-container text-on-primary-container' : ($active ? 'border-primary-fixed text-primary-fixed bg-surface-container-high' : 'border-outline-variant text-on-surface-variant bg-surface-container') ?>">
                    <?= $done ? '✓' : $num ?>
                </div>
                <span class="text-[11px] mt-1 font-label-sm <?= $active ? 'text-primary-fixed' : 'text-on-surface-variant' ?>"><?= $label ?></span>
            </div>
            <?php if ($i < 3): ?>
            <div class="flex-1 h-[2px] mx-2 <?= $done ? 'bg-primary-container' : 'bg-outline-variant/40' ?>"></div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
<div class="lg:col-span-2">
    <div class="glass-card rounded-2xl p-8">
        <h2 class="font-headline-lg text-2xl text-white mb-2">Secure Payment</h2>
        <p class="text-on-surface-variant font-body-md mb-6">All transactions are encrypted and secure.</p>

        <form method="POST" action="" id="payment-form">
            <?= csrfField() ?>

            <!-- Payment Method -->
            <div class="mb-6">
                <label class="font-label-sm text-xs text-on-surface-variant uppercase tracking-wider block mb-3">Payment Method</label>
                <div class="grid grid-cols-3 gap-3">
                    <?php foreach (['card'=>'credit_card', 'paypal'=>'account_balance_wallet', 'crypto'=>'currency_bitcoin'] as $method => $icon): ?>
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="<?= $method ?>" class="hidden peer" <?= $method==='card'?'checked':'' ?>>
                        <div class="flex flex-col items-center gap-2 p-4 rounded-xl border border-outline-variant bg-surface-container peer-checked:border-primary-container peer-checked:bg-primary-container/10 transition-all">
                            <span class="material-symbols-outlined text-2xl text-on-surface-variant peer-checked:text-primary-container"><?= $icon ?></span>
                            <span class="text-xs font-label-sm capitalize text-on-surface-variant"><?= $method ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Card Fields (shown by default) -->
            <div id="card-fields" class="space-y-4 mb-8">
                <div>
                    <label class="font-label-sm text-xs text-on-surface-variant uppercase tracking-wider block mb-2">Card Number</label>
                    <input type="text" placeholder="4242 4242 4242 4242" maxlength="19"
                           class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 text-white placeholder:text-on-surface-variant/40 focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none"
                           oninput="formatCard(this)">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-label-sm text-xs text-on-surface-variant uppercase tracking-wider block mb-2">Expiry</label>
                        <input type="text" placeholder="MM / YY" maxlength="7"
                               class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 text-white placeholder:text-on-surface-variant/40 focus:border-primary-container outline-none"
                               oninput="formatExpiry(this)">
                    </div>
                    <div>
                        <label class="font-label-sm text-xs text-on-surface-variant uppercase tracking-wider block mb-2">CVV</label>
                        <input type="text" placeholder="•••" maxlength="4"
                               class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 text-white placeholder:text-on-surface-variant/40 focus:border-primary-container outline-none"
                               oninput="this.value=this.value.replace(/\D/g,'')">
                    </div>
                </div>
                <div>
                    <label class="font-label-sm text-xs text-on-surface-variant uppercase tracking-wider block mb-2">Cardholder Name</label>
                    <input type="text" placeholder="Name on card"
                           class="w-full bg-surface-container border border-outline-variant rounded-xl px-4 py-3 text-white placeholder:text-on-surface-variant/40 focus:border-primary-container outline-none">
                </div>
            </div>

            <button type="submit" id="pay-btn"
                    class="w-full bg-primary-container text-on-primary-container font-label-sm py-4 rounded-xl hover:bg-primary-fixed transition-all shadow-[0_0_25px_rgba(0,240,255,0.3)] flex items-center justify-center gap-2 text-lg">
                <span class="material-symbols-outlined">lock</span>
                Pay $<?= number_format($total, 2) ?> Securely
            </button>
            <p class="text-center text-xs text-on-surface-variant mt-3 flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-[14px]">security</span>
                256-bit SSL encrypted · No card data stored
            </p>
        </form>
    </div>
</div>

<!-- Order Summary -->
<div class="lg:col-span-1">
    <div class="glass-card rounded-2xl overflow-hidden sticky top-24">
        <div class="h-36 overflow-hidden relative">
            <img src="<?= htmlspecialchars($car['image_path']) ?>" alt="" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-[#051424] to-transparent"></div>
        </div>
        <div class="p-6">
            <h3 class="font-headline-md text-lg text-white mb-1"><?= htmlspecialchars($car['name']) ?></h3>
            <p class="text-xs text-on-surface-variant mb-4"><?= date('M j', strtotime($draft['pickup_date'])) ?> → <?= date('M j, Y', strtotime($draft['return_date'])) ?> (<?= $draft['days'] ?> days)</p>

            <div class="space-y-2 text-sm border-t border-outline-variant/30 pt-4">
                <div class="flex justify-between text-on-surface-variant">
                    <span>Base rental</span>
                    <span>$<?= number_format($draft['base_price'], 2) ?></span>
                </div>
                <?php foreach ($addOns as $ao): ?>
                <div class="flex justify-between text-on-surface-variant">
                    <span><?= htmlspecialchars($ao) ?></span>
                    <span class="text-primary-fixed">included</span>
                </div>
                <?php endforeach; ?>
                <?php if ($draft['extra_price'] > 0): ?>
                <div class="flex justify-between text-on-surface-variant">
                    <span>Add-ons total</span>
                    <span>$<?= number_format($draft['extra_price'], 2) ?></span>
                </div>
                <?php endif; ?>
                <hr class="border-outline-variant/30">
                <div class="flex justify-between font-bold text-white text-base">
                    <span>Total</span>
                    <span class="text-primary-fixed">$<?= number_format($total, 2) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
function formatCard(el) {
    let v = el.value.replace(/\D/g,'').substring(0,16);
    el.value = v.match(/.{1,4}/g)?.join(' ') || v;
}
function formatExpiry(el) {
    let v = el.value.replace(/\D/g,'').substring(0,4);
    if (v.length >= 2) v = v.substring(0,2) + ' / ' + v.substring(2);
    el.value = v;
}

document.querySelectorAll('input[name="payment_method"]').forEach(r => {
    r.addEventListener('change', () => {
        document.getElementById('card-fields').style.display = r.value === 'card' ? '' : 'none';
    });
});

document.getElementById('payment-form').addEventListener('submit', function(e) {
    const btn = document.getElementById('pay-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin">progress_activity</span> Processing...';
});
</script>

<?php require 'includes/footer.php'; ?>
