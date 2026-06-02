<?php
require 'config.php';
requireLogin();
$id   = (int)($_GET['booking_id'] ?? 0);
$stmt = $pdo->prepare("SELECT b.*, c.name as car_name, c.type, c.image_path, c.location, u.name as user_name, u.email as user_email, u.phone as user_phone FROM bookings b JOIN cars c ON b.car_id=c.id JOIN users u ON b.user_id=u.id WHERE b.id=? AND (b.user_id=? OR ?=1)");
$stmt->execute([$id, $_SESSION['user_id'], (int)isAdmin()]); $b = $stmt->fetch();
if (!$b) redirect('/car-rental/user/bookings.php');

$days = max(1, ceil((strtotime($b['return_date']) - strtotime($b['pickup_date'])) / 86400));
$hide_navbar = true;
$page_title = "Invoice #LD-".str_pad($id,4,'0',STR_PAD_LEFT);
require 'includes/header.php';
?>

<div class="min-h-screen py-10 px-4" style="background:#030810;">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 print:mb-4">
            <a href="javascript:history.back()" class="flex items-center gap-2 text-slate-400 hover:text-white text-sm transition-colors print:hidden">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
            <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 rounded-lg text-black font-bold text-sm print:hidden" style="background:#00E5FF;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print / Download
            </button>
        </div>

        <!-- Invoice Card -->
        <div id="invoice" class="rounded-2xl overflow-hidden" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
            <!-- Invoice Header -->
            <div class="p-8 flex justify-between items-start" style="background:linear-gradient(135deg, #0d1b2a 0%, #111f35 100%); border-bottom:1px solid rgba(255,255,255,0.07);">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none">
                            <rect width="32" height="32" rx="8" fill="#00E5FF" fill-opacity="0.15"/>
                            <path d="M6 20l4-8h12l4 8M8 20h16" stroke="#00E5FF" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="11" cy="22" r="2" fill="#00E5FF"/>
                            <circle cx="21" cy="22" r="2" fill="#00E5FF"/>
                        </svg>
                        <span class="font-black text-white text-lg">Velocity Elite</span>
                    </div>
                    <p class="text-slate-500 text-xs">Premium Car Rental Services</p>
                    <p class="text-slate-500 text-xs">support@velocityelite.com</p>
                </div>
                <div class="text-right">
                    <p class="text-slate-400 text-xs uppercase tracking-widest mb-1">Invoice</p>
                    <p class="font-black text-2xl" style="color:#00E5FF;">#LD-<?= str_pad($id,4,'0',STR_PAD_LEFT) ?></p>
                    <p class="text-slate-500 text-xs mt-1">Issued: <?= date('d M Y') ?></p>
                    <span class="inline-block mt-2 px-2 py-0.5 rounded text-xs font-bold" style="background:rgba(0,229,255,0.1); color:#00E5FF;">
                        <?= ucfirst($b['status']) ?>
                    </span>
                </div>
            </div>

            <!-- Customer + Vehicle -->
            <div class="grid grid-cols-2 gap-6 p-8 border-b" style="border-color:rgba(255,255,255,0.06);">
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-widest mb-3">Billed To</p>
                    <p class="text-white font-bold"><?= htmlspecialchars($b['user_name']) ?></p>
                    <p class="text-slate-400 text-sm"><?= htmlspecialchars($b['user_email']) ?></p>
                    <p class="text-slate-400 text-sm"><?= htmlspecialchars($b['user_phone'] ?? '') ?></p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-widest mb-3">Vehicle</p>
                    <p class="text-white font-bold"><?= htmlspecialchars($b['car_name']) ?></p>
                    <p class="text-slate-400 text-sm"><?= htmlspecialchars($b['type']) ?></p>
                    <p class="text-slate-400 text-sm"><?= htmlspecialchars($b['location']) ?></p>
                </div>
            </div>

            <!-- Line Items -->
            <div class="p-8">
                <table class="w-full text-sm mb-6">
                    <thead>
                        <tr class="border-b" style="border-color:rgba(255,255,255,0.08);">
                            <th class="text-left text-slate-500 text-xs uppercase tracking-wider pb-3">Description</th>
                            <th class="text-right text-slate-500 text-xs uppercase tracking-wider pb-3">Qty</th>
                            <th class="text-right text-slate-500 text-xs uppercase tracking-wider pb-3">Rate</th>
                            <th class="text-right text-slate-500 text-xs uppercase tracking-wider pb-3">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b" style="border-color:rgba(255,255,255,0.04);">
                            <td class="py-3">
                                <p class="text-white"><?= htmlspecialchars($b['car_name']) ?> Rental</p>
                                <p class="text-slate-500 text-xs"><?= date('d M Y', strtotime($b['pickup_date'])) ?> → <?= date('d M Y', strtotime($b['return_date'])) ?></p>
                            </td>
                            <td class="text-slate-300 text-right py-3"><?= $days ?> days</td>
                            <td class="text-slate-300 text-right py-3">₹<?= number_format($b['total_price'] / $days) ?></td>
                            <td class="text-white font-bold text-right py-3">₹<?= number_format($b['total_price']) ?></td>
                        </tr>
                        <?php if (!empty($b['add_ons'])): ?>
                        <tr class="border-b" style="border-color:rgba(255,255,255,0.04);">
                            <td class="py-3"><p class="text-white">Add-on Services</p><p class="text-slate-500 text-xs"><?= htmlspecialchars($b['add_ons']) ?></p></td>
                            <td class="text-slate-300 text-right py-3">—</td>
                            <td class="text-slate-300 text-right py-3">—</td>
                            <td class="text-white font-bold text-right py-3">Included</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Total -->
                <div class="flex justify-end">
                    <div class="w-64">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-slate-400">Subtotal</span>
                            <span class="text-white">₹<?= number_format($b['total_price']) ?></span>
                        </div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-slate-400">Tax (18% GST)</span>
                            <span class="text-white">Included</span>
                        </div>
                        <div class="flex justify-between text-lg font-black border-t pt-3 mt-3" style="border-color:rgba(255,255,255,0.1);">
                            <span class="text-white">Total</span>
                            <span style="color:#00E5FF;">₹<?= number_format($b['total_price']) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-8 py-5 border-t text-center" style="border-color:rgba(255,255,255,0.06); background:rgba(0,0,0,0.2);">
                <p class="text-slate-500 text-xs">Thank you for choosing Velocity Elite. For support, contact elite@velocity.com or call +1 (800) 555-0199.</p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: white !important; }
    #invoice { background: white !important; border: 1px solid #ddd !important; color: #000; }
    .text-white, .text-slate-300, .text-slate-400 { color: #000 !important; }
    .text-slate-500 { color: #666 !important; }
}
</style>

<?php require 'includes/footer.php'; ?>
