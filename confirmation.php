<?php
require 'config.php';
requireLogin();
$id   = (int)($_GET['booking_id'] ?? 0);
$stmt = $pdo->prepare("SELECT b.*, c.name as car_name, c.image_path, c.type FROM bookings b JOIN cars c ON b.car_id=c.id WHERE b.id=? AND b.user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]); $b = $stmt->fetch();
if (!$b) redirect('/car-rental/user/bookings.php');
$page_title  = "Booking Confirmed! - Velocity Elite";
$hide_navbar = true;
require 'includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center px-4 py-12" style="background:#030810;">
    <div class="w-full max-w-lg">
        <!-- Success Icon with ring animation -->
        <div class="text-center mb-8">
            <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-5 relative" style="background:rgba(0,229,255,0.1); border:2px solid rgba(0,229,255,0.4);">
                <div class="absolute inset-0 rounded-full animate-ping opacity-30" style="background:rgba(0,229,255,0.2);"></div>
                <svg class="w-12 h-12" style="color:#00E5FF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="font-black text-3xl text-white mb-2">Booking Confirmed!</h1>
            <p class="text-slate-400">Your premium vehicle is reserved and ready.</p>
        </div>

        <!-- Booking Card -->
        <div class="rounded-2xl overflow-hidden mb-5" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
            <div class="h-40 overflow-hidden relative">
                <img src="<?= htmlspecialchars($b['image_path']) ?>" class="w-full h-full object-cover">
                <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(13,27,42,0.9) 0%,transparent 60%);"></div>
                <div class="absolute bottom-4 left-5">
                    <p class="font-black text-xl text-white"><?= htmlspecialchars($b['car_name']) ?></p>
                    <p class="text-slate-400 text-sm"><?= htmlspecialchars($b['type']) ?></p>
                </div>
            </div>
            <div class="p-5 space-y-3 text-sm">
                <?php foreach ([
                    ['Booking Reference', '#LD-'.str_pad($id,4,'0',STR_PAD_LEFT)],
                    ['Pickup Date',       date('d M Y, H:i', strtotime($b['pickup_date']))],
                    ['Return Date',       date('d M Y, H:i', strtotime($b['return_date']))],
                    ['Total Paid',        '₹'.number_format($b['total_price'])],
                ] as [$k,$v]): ?>
                <div class="flex justify-between">
                    <span class="text-slate-400"><?= $k ?></span>
                    <span class="text-white font-semibold <?= $k==='Total Paid'?'text-cyan-400':'' ?>"><?= htmlspecialchars($v) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col gap-3">
            <a href="/car-rental/invoice.php?booking_id=<?= $id ?>"
               class="w-full py-4 rounded-xl text-black font-black text-sm uppercase tracking-wider text-center hover:brightness-110 transition-all"
               style="background:#00E5FF;">
                Download Invoice
            </a>
            <a href="/car-rental/user/bookings.php"
               class="w-full py-3.5 rounded-xl text-white font-bold text-sm text-center border transition-all hover:border-cyan-400"
               style="border-color:rgba(255,255,255,0.12);">
                View My Bookings
            </a>
            <a href="/car-rental/fleet.php"
               class="text-center text-slate-500 hover:text-slate-300 text-sm transition-colors">
                Continue Exploring →
            </a>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
