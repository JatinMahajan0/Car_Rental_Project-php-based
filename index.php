<?php
require 'config.php';
$page_title = "Velocity Elite - Find Your Perfect Ride";
require 'includes/header.php';

$featured = $pdo->query("SELECT * FROM cars WHERE status='available' ORDER BY price DESC LIMIT 6")->fetchAll();
$wishlistIds = [];
if (isLoggedIn()) {
    $ws = $pdo->prepare("SELECT car_id FROM wishlist WHERE user_id=?");
    $ws->execute([$_SESSION['user_id']]);
    $wishlistIds = $ws->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!-- HERO SECTION -->
<section class="hero-section relative w-full min-h-[100vh] flex flex-col justify-end overflow-hidden -mt-[72px]">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1544636331-e26879cd4d9b?q=80&w=1920&fit=crop" 
             alt="" class="w-full h-full object-cover">
        <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.55) 50%, rgba(0,0,0,0.92) 100%);"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 flex flex-col items-center justify-center text-center px-6 pb-0 pt-32 flex-1">
        <h1 class="text-5xl md:text-7xl font-black text-white mb-4 leading-tight tracking-tight">Find Your Perfect Ride</h1>
        <p class="text-base md:text-lg text-slate-300 max-w-2xl mb-0">
            Book cars instantly at affordable prices. Experience the peak of automotive luxury and<br class="hidden md:block"> performance with our curated fleet.
        </p>
    </div>

    <!-- Search Bar -->
    <div class="relative z-10 w-full pb-16 pt-8 px-4">
        <form action="/car-rental/fleet.php" method="GET" class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row items-stretch gap-0 rounded-xl overflow-hidden border border-slate-700/60 shadow-2xl" style="background:#111827;">
                <!-- Pickup Location -->
                <div class="flex-1 flex items-center gap-3 px-5 py-4 border-b md:border-b-0 md:border-r border-slate-700/60">
                    <div class="flex flex-col w-full">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-cyan-400 mb-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                            Pickup Location
                        </span>
                        <input name="location" type="text" placeholder="Delhi Airport (DEL)"
                               class="bg-transparent text-white text-sm placeholder-slate-500 outline-none w-full">
                    </div>
                </div>

                <!-- Drop Location -->
                <div class="flex-1 flex items-center gap-3 px-5 py-4 border-b md:border-b-0 md:border-r border-slate-700/60">
                    <div class="flex flex-col w-full">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1 flex items-center gap-2">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M21.71 11.29l-9-9a1 1 0 0 0-1.42 0l-9 9a1 1 0 0 0 0 1.42l9 9a1 1 0 0 0 1.42 0l9-9a1 1 0 0 0 0-1.42z"/></svg>
                            Drop Location
                            <label class="flex items-center gap-1 ml-2 cursor-pointer">
                                <input type="checkbox" class="w-3 h-3 accent-cyan-400" checked>
                                <span class="text-[9px] text-slate-500">Same as Pickup</span>
                            </label>
                        </span>
                        <input type="text" placeholder="Mumbai Airport (BOM)"
                               class="bg-transparent text-white text-sm placeholder-slate-500 outline-none w-full">
                    </div>
                </div>

                <!-- Pickup Date -->
                <div class="flex-1 flex items-center gap-3 px-5 py-4 border-b md:border-b-0 md:border-r border-slate-700/60">
                    <div class="flex flex-col w-full">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                            Pickup Date &amp; Time
                        </span>
                        <input name="pickup" type="date" min="<?= date('Y-m-d') ?>"
                               class="bg-transparent text-white text-sm outline-none [color-scheme:dark] w-full">
                    </div>
                </div>

                <!-- Return Date -->
                <div class="flex-1 flex items-center gap-3 px-5 py-4 border-b md:border-b-0 border-slate-700/60">
                    <div class="flex flex-col w-full">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-red-400 mb-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                            Return Date &amp; Time
                        </span>
                        <input name="return_date" type="date" min="<?= date('Y-m-d') ?>"
                               class="bg-transparent text-white text-sm outline-none [color-scheme:dark] w-full">
                    </div>
                </div>

                <!-- Search Button -->
                <button type="submit"
                        class="flex items-center justify-center gap-2 px-8 py-4 font-bold text-sm uppercase tracking-wider whitespace-nowrap text-black transition-all hover:brightness-110"
                        style="background:#00E5FF; min-width:160px;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Search Fleet
                </button>
            </div>
        </form>
    </div>
</section>

<!-- FEATURED CARS SECTION -->
<section class="py-16 px-4 md:px-8" style="background:#050b14;">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-end mb-8">
            <div>
                <p class="text-cyan-400 text-xs font-bold uppercase tracking-widest mb-2">Premium Selection</p>
                <h2 class="text-3xl font-black text-white">Our Featured Fleet</h2>
            </div>
            <a href="/car-rental/fleet.php" class="flex items-center gap-2 text-cyan-400 text-sm font-bold hover:text-cyan-300 transition-colors">
                View All Vehicles
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach ($featured as $car):
                $inWl = in_array($car['id'], $wishlistIds);
            ?>
            <a href="/car-rental/car_details.php?id=<?= $car['id'] ?>" 
               class="vehicle-card group rounded-2xl overflow-hidden block hover:-translate-y-1 transition-all duration-300"
               style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
                <!-- Image -->
                <div class="relative h-48 overflow-hidden">
                    <img src="<?= htmlspecialchars($car['image_path']) ?>" alt="<?= htmlspecialchars($car['name']) ?>"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(13,27,42,0.8) 0%, transparent 60%);"></div>
                    <!-- Wishlist -->
                    <button onclick="event.preventDefault(); toggleWishlist(this, <?= $car['id'] ?>)"
                            class="absolute top-3 right-3 w-8 h-8 rounded-full flex items-center justify-center transition-all hover:scale-110 <?= $inWl ? 'text-red-400' : 'text-slate-400 hover:text-red-400' ?>"
                            style="background:rgba(0,0,0,0.5); backdrop-filter:blur(8px);">
                        <svg class="w-4 h-4" fill="<?= $inWl ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </button>
                    <!-- Type badge -->
                    <span class="absolute top-3 left-3 text-[10px] font-bold uppercase tracking-wider text-white px-2 py-0.5 rounded" style="background:rgba(0,229,255,0.15); border:1px solid rgba(0,229,255,0.3);"><?= htmlspecialchars($car['type']) ?></span>
                </div>

                <!-- Info -->
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-white font-bold text-lg leading-tight"><?= htmlspecialchars($car['name']) ?></h3>
                            <p class="text-slate-400 text-xs uppercase tracking-wider mt-0.5"><?= htmlspecialchars($car['type']) ?></p>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-black" style="color:#00E5FF;">₹<?= number_format($car['price']) ?></span>
                            <span class="text-slate-500 text-xs">/day</span>
                        </div>
                    </div>

                    <!-- Specs -->
                    <div class="flex items-center gap-4 mb-4 text-xs text-slate-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg>
                            <?= $car['seats'] ?> Seats
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <?= $car['transmission'] ?>
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M19.77 7.23l.01-.01-3.72-3.72L15 4.56l2.11 2.11c-.94.36-1.61 1.26-1.61 2.33 0 1.38 1.12 2.5 2.5 2.5.36 0 .69-.08 1-.21v7.21c0 .55-.45 1-1 1s-1-.45-1-1V14c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v16h10v-7.5h1.5v5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V9c0-.69-.28-1.32-.73-1.77z"/></svg>
                            <?= $car['fuel'] ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                            <?= htmlspecialchars($car['location']) ?>
                        </span>
                        <span class="text-xs font-bold px-3 py-1 rounded-full border" 
                              style="color:#00E5FF; border-color:rgba(0,229,255,0.4); background:rgba(0,229,255,0.08);">
                            DETAILS →
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SERVICES SECTION -->
<section class="py-16 px-4 md:px-8" style="background:#03080f; border-top:1px solid rgba(255,255,255,0.05);">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php foreach ([
            ['🚘','Fleet Excellence','Access to over 500 handpicked premium and exotic vehicles maintained to the highest standards.'],
            ['⚡','Instant Booking','Reserve your vehicle in under 60 seconds. Real-time availability with instant confirmation.'],
            ['🛡️','Elite Concierge','24/7 dedicated support, airport delivery, and white-glove service included with every booking.'],
        ] as [$icon, $title, $desc]): ?>
        <div class="p-6 rounded-2xl" style="background:#0d1b2a; border:1px solid rgba(255,255,255,0.07);">
            <span class="text-3xl mb-4 block"><?= $icon ?></span>
            <h3 class="text-white font-bold text-lg mb-2"><?= $title ?></h3>
            <p class="text-slate-400 text-sm leading-relaxed"><?= $desc ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php if (!isLoggedIn()): ?>
<!-- CTA SECTION -->
<section class="py-20 px-4 text-center" style="background:linear-gradient(135deg, #03080f 0%, #0a1628 100%); border-top:1px solid rgba(0,229,255,0.1);">
    <div class="max-w-2xl mx-auto">
        <h2 class="text-4xl font-black text-white mb-4">Ready to Drive Elite?</h2>
        <p class="text-slate-400 mb-8">Join thousands of members who experience the extraordinary every day.</p>
        <div class="flex justify-center gap-4 flex-wrap">
            <a href="/car-rental/signup.php" 
               class="px-8 py-4 rounded-lg font-bold text-black text-sm uppercase tracking-wider hover:brightness-110 transition-all"
               style="background:#00E5FF;">
                Create Free Account
            </a>
            <a href="/car-rental/fleet.php" 
               class="px-8 py-4 rounded-lg font-bold text-white text-sm uppercase tracking-wider border border-slate-600 hover:border-cyan-400 transition-all">
                Browse Fleet
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>
