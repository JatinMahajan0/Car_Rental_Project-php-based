<?php
require 'config.php';

$id  = (int)($_GET['id'] ?? 0);
$car = $pdo->prepare("SELECT * FROM cars WHERE id=?");
$car->execute([$id]);
$car = $car->fetch();
if (!$car) { http_response_code(404); redirect('/car-rental/fleet.php'); }

// Related cars
$related = $pdo->prepare("SELECT * FROM cars WHERE type=? AND id!=? AND status='available' LIMIT 4");
$related->execute([$car['type'], $id]);
$related = $related->fetchAll();

// Wishlist check
$inWishlist = false;
if (isLoggedIn()) {
    $ws = $pdo->prepare("SELECT 1 FROM wishlist WHERE user_id=? AND car_id=?");
    $ws->execute([$_SESSION['user_id'], $id]);
    $inWishlist = (bool)$ws->fetch();
}

// Reviews
$reviews = $pdo->prepare("SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id=u.id WHERE r.car_id=? ORDER BY r.created_at DESC LIMIT 5");
$reviews->execute([$id]);
$reviews = $reviews->fetchAll();
$avgRating = $pdo->prepare("SELECT AVG(rating) as avg, COUNT(*) as cnt FROM reviews WHERE car_id=?");
$avgRating->execute([$id]);
$ratingData = $avgRating->fetch();

$page_title = htmlspecialchars($car['name']) . " - Velocity Elite";
require 'includes/header.php';
?>

<div style="background:#030810; min-height:100vh;">

<!-- ===== HERO / IMAGE SECTION ===== -->
<section class="relative w-full" style="height:65vh; min-height:420px; max-height:600px;">
    <!-- Main Image -->
    <img id="main-car-img"
         src="<?= htmlspecialchars($car['image_path']) ?>"
         alt="<?= htmlspecialchars($car['name']) ?>"
         class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300">
    <div class="absolute inset-0" style="background:linear-gradient(to bottom, rgba(3,8,16,0.2) 0%, rgba(3,8,16,0.5) 60%, #030810 100%);"></div>

    <!-- Badges top-left -->
    <div class="absolute top-5 left-6 flex items-center gap-2">
        <span class="px-3 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider text-black" style="background:#00E5FF;">
            Instant Book
        </span>
        <span class="px-3 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider text-white border border-white/30" style="background:rgba(255,255,255,0.1); backdrop-filter:blur(10px);">
            Top Rated
        </span>
    </div>

    <!-- Wishlist + Share top-right -->
    <div class="absolute top-5 right-6 flex items-center gap-3">
        <button id="wl-btn" onclick="toggleWishlist(this, <?= $car['id'] ?>)"
                class="w-10 h-10 rounded-full flex items-center justify-center transition-all hover:scale-110"
                style="background:rgba(0,0,0,0.5); backdrop-filter:blur(10px); color:<?= $inWishlist?'#f87171':'#fff' ?>;">
            <svg class="w-5 h-5" fill="<?= $inWishlist?'currentColor':'none' ?>" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        </button>
        <button onclick="shareVehicle()" class="w-10 h-10 rounded-full flex items-center justify-center text-white transition-all hover:scale-110" style="background:rgba(0,0,0,0.5); backdrop-filter:blur(10px);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
        </button>
    </div>

    <!-- Thumbnail strip — only shows images that exist -->
    <?php
    $galleryImages = array_filter([
        $car['image_path']   ?? null,
        $car['image_path_2'] ?? null,
        $car['image_path_3'] ?? null,
    ]);
    ?>
    <?php if (count($galleryImages) > 1): ?>
    <div class="absolute bottom-6 left-6 flex items-center gap-2" id="thumb-strip">
        <?php foreach (array_values($galleryImages) as $i => $imgSrc): ?>
        <div class="thumb-item w-16 h-12 rounded-lg overflow-hidden cursor-pointer transition-all hover:opacity-100"
             style="<?= $i === 0 ? 'outline:2px solid #00E5FF; opacity:1;' : 'outline:1px solid rgba(255,255,255,0.2); opacity:0.65;' ?>"
             onclick="switchImage('<?= htmlspecialchars($imgSrc) ?>', this)">
            <img src="<?= htmlspecialchars($imgSrc) ?>" class="w-full h-full object-cover" alt="View <?= $i+1 ?>">
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<!-- ===== CONTENT ===== -->
<div class="max-w-6xl mx-auto px-6 pb-24">
    <div class="flex flex-col lg:flex-row gap-8 -mt-8 relative z-10">

        <!-- LEFT: Car Info -->
        <div class="flex-1 min-w-0">
            <!-- Name & Rating -->
            <div class="mb-6">
                <h1 class="font-black text-4xl md:text-5xl text-white mb-3 leading-tight"><?= htmlspecialchars($car['name']) ?></h1>
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="flex items-center gap-1.5">
                        <?php if ($ratingData['cnt'] > 0): ?>
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <span class="text-white font-semibold text-sm"><?= number_format($ratingData['avg'], 1) ?></span>
                            <span class="text-slate-500 text-sm">(<?= $ratingData['cnt'] ?> review<?= $ratingData['cnt'] != 1 ? 's' : '' ?>)</span>
                        <?php else: ?>
                            <svg class="w-4 h-4 text-slate-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <span class="text-slate-500 text-sm">No reviews yet</span>
                        <?php endif; ?>
                    </div>
                    <span class="text-slate-700">·</span>
                    <div class="flex items-center gap-1.5 text-slate-400 text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        <?= htmlspecialchars($car['location']) ?>
                    </div>
                    <span class="px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider rounded-full" style="background:rgba(0,229,255,0.1); color:#00E5FF; border:1px solid rgba(0,229,255,0.3);">
                        <?= htmlspecialchars($car['type']) ?>
                    </span>
                </div>
            </div>

            <!-- Specs Grid (Stitch: 2x2 with cyan icons) -->
            <div class="grid grid-cols-2 gap-4 mb-8">
                <?php foreach ([
                    ['icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label'=>'0-60 MPH', 'value'=>'3.5s', 'sub'=>'0-60 mph'],
                    ['icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label'=>'SEATING', 'value'=>$car['seats'].' Seats', 'sub'=>'Luxury Leather'],
                    ['icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'label'=>'TRANSMISSION', 'value'=>$car['transmission'], 'sub'=>'PDK Transmission'],
                    ['icon'=>'M19.77 7.23l.01-.01-3.72-3.72L15 4.56l2.11 2.11c-.94.36-1.61 1.26-1.61 2.33 0 1.38 1.12 2.5 2.5 2.5.36 0 .69-.08 1-.21v7.21c0 .55-.45 1-1 1s-1-.45-1-1V14c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v16h10v-7.5h1.5v5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V9c0-.69-.28-1.32-.73-1.77z', 'label'=>'FUEL', 'value'=>$car['fuel'], 'sub'=>'Premium Fuel'],
                ] as $spec): ?>
                <div class="p-5 rounded-xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:rgba(0,229,255,0.1);">
                        <svg class="w-5 h-5" style="color:#00E5FF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $spec['icon'] ?>"/>
                        </svg>
                    </div>
                    <p class="font-black text-white text-lg"><?= htmlspecialchars($spec['value']) ?></p>
                    <p class="text-slate-500 text-xs uppercase tracking-wider mt-0.5"><?= $spec['sub'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Description -->
            <?php if (!empty($car['description'])): ?>
            <div class="mb-8 p-5 rounded-xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
                <h2 class="text-white font-bold text-lg mb-3">About this Vehicle</h2>
                <p class="text-slate-400 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($car['description'])) ?></p>
            </div>
            <?php endif; ?>

            <!-- Features (add-ons/extras) -->
            <?php
            $features = ['GPS Navigation','Bluetooth Audio','Climate Control','Parking Sensors','Leather Interior','Sunroof'];
            ?>
            <div class="mb-8">
                <h2 class="text-white font-bold text-lg mb-4">Features & Amenities</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <?php foreach ($features as $feat): ?>
                    <div class="flex items-center gap-2 text-sm text-slate-300">
                        <svg class="w-4 h-4 flex-shrink-0" style="color:#00E5FF;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <?= $feat ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Reviews -->
            <?php if (!empty($reviews)): ?>
            <div class="mb-8">
                <h2 class="text-white font-bold text-lg mb-4">Guest Reviews</h2>
                <div class="space-y-3">
                    <?php foreach ($reviews as $review): ?>
                    <div class="p-4 rounded-xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs text-black" style="background:#00E5FF;">
                                    <?= strtoupper(substr($review['user_name'],0,1)) ?>
                                </div>
                                <span class="text-white font-semibold text-sm"><?= htmlspecialchars($review['user_name']) ?></span>
                            </div>
                            <div class="flex">
                                <?php for ($i=0;$i<5;$i++): ?>
                                <svg class="w-3.5 h-3.5 <?= $i < $review['rating'] ? 'text-yellow-400' : 'text-slate-700' ?>" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="text-slate-400 text-sm leading-relaxed"><?= htmlspecialchars($review['comment'] ?? '') ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT: Sticky Booking Card -->
        <div class="w-full lg:w-80 flex-shrink-0">
            <div class="sticky top-[80px] p-6 rounded-2xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.1);">
                <!-- Price -->
                <div class="mb-5">
                    <div class="flex items-baseline gap-1 mb-1">
                        <span class="font-black text-4xl" style="color:#00E5FF;">₹<?= number_format($car['price']) ?></span>
                        <span class="text-slate-500 text-sm">/day</span>
                    </div>
                    <p class="text-slate-500 text-xs">Taxes and insurance included</p>
                </div>

                <!-- Date Pickers -->
                <div class="space-y-3 mb-5">
                    <div class="p-3 rounded-xl" style="background:#111827; border:1px solid rgba(255,255,255,0.08);">
                        <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#00E5FF;">Pickup Date</p>
                        <input type="date" id="pickup-date" min="<?= date('Y-m-d') ?>"
                               class="w-full bg-transparent text-white text-sm outline-none [color-scheme:dark]"
                               onchange="calcTotal()">
                    </div>
                    <div class="p-3 rounded-xl" style="background:#111827; border:1px solid rgba(255,255,255,0.08);">
                        <p class="text-xs font-bold uppercase tracking-wider mb-1 text-red-400">Return Date</p>
                        <input type="date" id="return-date" min="<?= date('Y-m-d') ?>"
                               class="w-full bg-transparent text-white text-sm outline-none [color-scheme:dark]"
                               onchange="calcTotal()">
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div id="price-breakdown" class="hidden mb-5 space-y-2 p-4 rounded-xl" style="background:#111827; border:1px solid rgba(255,255,255,0.06);">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">₹<?= number_format($car['price']) ?> × <span id="days-count">0</span> days</span>
                        <span class="text-white" id="base-price">₹0</span>
                    </div>
                    <div class="flex justify-between text-sm border-t pt-2" style="border-color:rgba(255,255,255,0.06);">
                        <span class="text-white font-bold">Total</span>
                        <span class="font-black" style="color:#00E5FF;" id="total-price">₹0</span>
                    </div>
                </div>

                <!-- Book Button -->
                <?php if ($car['status'] === 'available'): ?>
                <a href="#" id="book-btn"
                   onclick="handleBookNow(event)"
                   class="block w-full py-4 rounded-xl text-black font-black text-base text-center uppercase tracking-wider hover:brightness-110 transition-all"
                   style="background:#00E5FF; box-shadow:0 0 24px rgba(0,229,255,0.3);">
                    Book This Vehicle
                </a>
                <?php else: ?>
                <div class="w-full py-4 rounded-xl text-center font-bold text-base text-slate-500 uppercase tracking-wider" style="background:#1a2a3a;">
                    Currently Unavailable
                </div>
                <?php endif; ?>

                <!-- Wishlist -->
                <button onclick="toggleWishlist(document.getElementById('wl-btn'), <?= $car['id'] ?>)"
                        class="w-full mt-3 py-3 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-all hover:bg-white/5"
                        style="border:1px solid rgba(255,255,255,0.12); color:<?= $inWishlist ? '#f87171' : '#94a3b8' ?>;">
                    <svg class="w-4 h-4" fill="<?= $inWishlist ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <?= $inWishlist ? 'Saved to Wishlist' : 'Add to Wishlist' ?>
                </button>

                <!-- Trust badges -->
                <div class="mt-5 pt-4 border-t grid grid-cols-3 gap-2 text-center" style="border-color:rgba(255,255,255,0.06);">
                    <?php foreach ([['🔒','Secure','Payment'],['🛡️','Free','Cancellation'],['⚡','Instant','Booking']] as [$ic,$l1,$l2]): ?>
                    <div>
                        <span class="text-lg block mb-0.5"><?= $ic ?></span>
                        <p class="text-[10px] text-slate-500 leading-tight"><?= $l1 ?><br><?= $l2 ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Vehicles -->
    <?php if (!empty($related)): ?>
    <div class="mt-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-white font-black text-2xl">Similar Vehicles</h2>
            <a href="/car-rental/fleet.php?type=<?= urlencode($car['type']) ?>" class="text-sm font-bold" style="color:#00E5FF;">View All →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($related as $rc): ?>
            <a href="/car-rental/car_details.php?id=<?= $rc['id'] ?>"
               class="rounded-xl overflow-hidden block hover:-translate-y-1 transition-all"
               style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
                <div class="h-36 overflow-hidden">
                    <img src="<?= htmlspecialchars($rc['image_path']) ?>" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-3">
                    <p class="text-white font-bold text-sm truncate"><?= htmlspecialchars($rc['name']) ?></p>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs text-slate-500"><?= htmlspecialchars($rc['type']) ?></span>
                        <span class="text-sm font-black" style="color:#00E5FF;">₹<?= number_format($rc['price']) ?>/day</span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

</div>

<script>
const pricePerDay = <?= (int)$car['price'] ?>;
const carId = <?= $car['id'] ?>;

function calcTotal() {
    const pickup = document.getElementById('pickup-date').value;
    const ret    = document.getElementById('return-date').value;
    if (!pickup || !ret) { document.getElementById('price-breakdown').classList.add('hidden'); return; }
    const d1 = new Date(pickup), d2 = new Date(ret);
    const days = Math.ceil((d2 - d1) / 86400000);
    if (days <= 0) { showToast('Return date must be after pickup date', 'error'); return; }
    const total = days * pricePerDay;
    document.getElementById('days-count').textContent = days;
    document.getElementById('base-price').textContent = '₹' + total.toLocaleString('en-IN');
    document.getElementById('total-price').textContent = '₹' + total.toLocaleString('en-IN');
    document.getElementById('price-breakdown').classList.remove('hidden');
}

function handleBookNow(e) {
    e.preventDefault();
    const pickup = document.getElementById('pickup-date').value;
    const ret    = document.getElementById('return-date').value;
    <?php if (!isLoggedIn()): ?>
    window.location = '/car-rental/login.php?return_url=' + encodeURIComponent('/car-rental/car_details.php?id=<?= $car['id'] ?>');
    return;
    <?php endif; ?>
    if (!pickup || !ret) { showToast('Please select pickup and return dates', 'warning'); return; }
    window.location = `/car-rental/booking_flow.php?car_id=<?= $car['id'] ?>&pickup=${pickup}&return_date=${ret}`;
}

function shareVehicle() {
    if (navigator.share) {
        navigator.share({ title: '<?= addslashes($car['name']) ?>', url: window.location.href });
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => showToast('Link copied to clipboard', 'success'));
    }
}

function switchImage(src, thumbEl) {
    const mainImg = document.getElementById('main-car-img');
    // Fade out
    mainImg.style.opacity = '0';
    setTimeout(() => {
        mainImg.src = src;
        mainImg.style.opacity = '1';
    }, 200);

    // Update active thumbnail outline
    document.querySelectorAll('.thumb-item').forEach(el => {
        el.style.outline = '1px solid rgba(255,255,255,0.2)';
        el.style.opacity = '0.65';
    });
    thumbEl.style.outline = '2px solid #00E5FF';
    thumbEl.style.opacity = '1';
}
</script>

<?php require 'includes/footer.php'; ?>
