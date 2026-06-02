<?php
require 'config.php';
$page_title = "Fleet - Velocity Elite";
require 'includes/header.php';

// Filters
$type     = $_GET['type'] ?? '';
$maxPrice = (int)($_GET['max_price'] ?? 0);
$fuel     = $_GET['fuel'] ?? '';
$location = $_GET['location'] ?? '';
$seats    = (int)($_GET['seats'] ?? 0);
$sort     = $_GET['sort'] ?? 'price_asc';

$query  = "SELECT * FROM cars WHERE status='available'";
$params = [];
if ($type)     { $query .= " AND type=?";     $params[] = $type; }
if ($fuel)     { $query .= " AND fuel=?";     $params[] = $fuel; }
if ($seats)    { $query .= " AND seats>=?";   $params[] = $seats; }
if ($maxPrice) { $query .= " AND price<=?";   $params[] = $maxPrice; }
if ($location) { $query .= " AND location LIKE ?"; $params[] = "%$location%"; }

$orderMap = ['price_asc'=>'price ASC','price_desc'=>'price DESC','name_asc'=>'name ASC'];
$query .= " ORDER BY " . ($orderMap[$sort] ?? 'price ASC');

$stmt = $pdo->prepare($query); $stmt->execute($params);
$cars = $stmt->fetchAll();

// Fetch real ratings for all cars in one query
$carIds = array_column($cars, 'id');
$ratings = [];
if ($carIds) {
    $placeholders = implode(',', array_fill(0, count($carIds), '?'));
    $ratingRows = $pdo->prepare("SELECT car_id, ROUND(AVG(rating),1) as avg, COUNT(*) as cnt FROM reviews WHERE car_id IN ($placeholders) GROUP BY car_id");
    $ratingRows->execute($carIds);
    foreach ($ratingRows->fetchAll() as $row) {
        $ratings[$row['car_id']] = $row;
    }
}

$types     = $pdo->query("SELECT DISTINCT type FROM cars WHERE status='available' ORDER BY type")->fetchAll(PDO::FETCH_COLUMN);
$fuels     = $pdo->query("SELECT DISTINCT fuel FROM cars WHERE status='available' ORDER BY fuel")->fetchAll(PDO::FETCH_COLUMN);
$maxDbPrice= $pdo->query("SELECT MAX(price) FROM cars WHERE status='available'")->fetchColumn() ?: 2000;

$wishlistIds = [];
if (isLoggedIn()) {
    $ws = $pdo->prepare("SELECT car_id FROM wishlist WHERE user_id=?");
    $ws->execute([$_SESSION['user_id']]);
    $wishlistIds = $ws->fetchAll(PDO::FETCH_COLUMN);
}
?>

<div class="min-h-screen" style="background:#03080f;">

    <!-- Top bar: search summary -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-6 md:px-10 py-5 border-b" style="border-color:rgba(255,255,255,0.06);">
        <div class="flex items-center gap-6 text-sm mb-4 sm:mb-0">
            <div class="flex items-center gap-2 text-slate-400">
                <svg class="w-4 h-4 text-cyan-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                <span>Pickup & Return</span>
                <span class="text-white font-medium"><?= htmlspecialchars($location ?: 'All Locations') ?></span>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-slate-500 text-sm"><?= count($cars) ?> vehicle<?= count($cars)!==1?'s':'' ?> available</span>
            <div class="flex items-center gap-2">
                <label class="text-slate-500 text-sm">Sort by:</label>
                <select name="sort" onchange="applySortFilter(this.value)"
                        class="text-sm text-white py-1.5 px-3 rounded-lg outline-none border cursor-pointer"
                        style="background:#0d1b2a; border-color:rgba(255,255,255,0.1);">
                    <option value="price_asc" <?= $sort==='price_asc'?'selected':'' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Price: High to Low</option>
                    <option value="name_asc" <?= $sort==='name_asc'?'selected':'' ?>>Name A–Z</option>
                </select>
            </div>
        </div>
    </div>

    <div class="flex">

        <!-- ===== SIDEBAR FILTERS ===== -->
        <aside class="hidden lg:block w-72 flex-shrink-0 p-6 sticky top-[66px] h-screen overflow-y-auto" style="background:#0a1221; border-right:1px solid rgba(255,255,255,0.06);">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-white font-bold text-base">Filters</h2>
                <a href="/car-rental/fleet.php" class="text-xs font-bold uppercase tracking-wider" style="color:#00E5FF;">Reset All</a>
            </div>
            <form method="GET" id="filter-form">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">

                <!-- Price Range -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-white font-semibold text-sm uppercase tracking-wider">Price Range</h3>
                    </div>
                    <input type="range" name="max_price" min="50" max="<?= $maxDbPrice ?>"
                           value="<?= $maxPrice ?: $maxDbPrice ?>"
                           class="w-full h-1 rounded-full"
                           oninput="document.getElementById('price-label').textContent='₹'+(this.value*75).toLocaleString('en-IN');">
                    <div class="flex justify-between text-xs text-slate-500 mt-2">
                        <span>₹<?= number_format(50*75) ?></span>
                        <span id="price-label" style="color:#00E5FF;">₹<?= number_format(($maxPrice ?: $maxDbPrice)*75) ?></span>
                        <span>₹<?= number_format($maxDbPrice*75) ?>+</span>
                    </div>
                </div>

                <!-- Car Type -->
                <div class="mb-8">
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-3">Car Type</h3>
                    <div class="space-y-2.5">
                        <?php foreach ($types as $t): ?>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="type" value="<?= htmlspecialchars($t) ?>"
                                   <?= $type===$t?'checked':'' ?>
                                   onchange="this.form.submit()"
                                   class="w-4 h-4 rounded">
                            <span class="text-slate-300 text-sm group-hover:text-white transition-colors"><?= htmlspecialchars($t) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Fuel Type -->
                <div class="mb-8">
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-3">Fuel Type</h3>
                    <div class="space-y-2.5">
                        <?php foreach ($fuels as $f): ?>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="fuel" value="<?= htmlspecialchars($f) ?>"
                                   <?= $fuel===$f?'checked':'' ?>
                                   onchange="this.form.submit()"
                                   class="w-4 h-4 rounded">
                            <span class="text-slate-300 text-sm group-hover:text-white transition-colors"><?= htmlspecialchars($f) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Seating -->
                <div class="mb-6">
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-3">Seating</h3>
                    <div class="flex gap-2 flex-wrap">
                        <?php foreach ([2,4,5,'7+'] as $s): 
                            $sVal = $s==='7+'?7:(int)$s;
                            $active = $seats===$sVal;
                        ?>
                        <button type="button" onclick="setSeat(<?= $sVal ?>)"
                                class="w-10 h-10 rounded-full text-sm font-bold transition-all <?= $active?'text-black':'text-slate-400 hover:text-white' ?>"
                                style="<?= $active?"background:#00E5FF;":"background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);" ?>">
                            <?= $s ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="seats" id="seats-input" value="<?= $seats ?>">
                </div>

                <!-- Apply Button -->
                <button type="submit" class="w-full py-3 rounded-lg text-black font-bold text-sm uppercase tracking-wider transition-all hover:brightness-110" style="background:#00E5FF;">
                    Apply Filters
                </button>
            </form>
        </aside>

        <!-- ===== CAR GRID ===== -->
        <main class="flex-1 p-5 md:p-8">
            <?php if (empty($cars)): ?>
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="text-5xl mb-4">🔍</div>
                <h3 class="text-white font-bold text-xl mb-2">No vehicles found</h3>
                <p class="text-slate-500 text-sm mb-6">Try adjusting your filters.</p>
                <a href="/car-rental/fleet.php" class="px-6 py-3 rounded-lg text-black font-bold text-sm" style="background:#00E5FF;">Clear Filters</a>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-2 gap-5">
                <?php foreach ($cars as $car):
                    $inWl = in_array($car['id'], $wishlistIds);
                ?>
                <div class="vehicle-card rounded-2xl overflow-hidden group">
                    <!-- Car Image -->
                    <div class="relative h-52 overflow-hidden">
                        <img src="<?= htmlspecialchars($car['image_path']) ?>"
                             alt="<?= htmlspecialchars($car['name']) ?>"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(13,27,42,0.8) 0%, transparent 60%);"></div>

                        <!-- Best Value Badge -->
                        <div class="absolute top-3 left-3 flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                             style="background:rgba(0,229,255,0.15); border:1px solid rgba(0,229,255,0.4); color:#00E5FF;">
                            ★ Best Value
                        </div>

                        <!-- Wishlist -->
                        <button onclick="toggleWishlist(this, <?= $car['id'] ?>)"
                                class="absolute top-3 right-3 w-8 h-8 rounded-full flex items-center justify-center transition-all hover:scale-110"
                                style="background:rgba(0,0,0,0.5); backdrop-filter:blur(8px); color:<?= $inWl?'#f87171':'#94a3b8' ?>;">
                            <svg class="w-4 h-4" fill="<?= $inWl?'currentColor':'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Car Info -->
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="text-white font-bold text-lg leading-tight"><?= htmlspecialchars($car['name']) ?></h3>
                                <p class="text-slate-500 text-xs uppercase tracking-wider"><?= htmlspecialchars(strtoupper($car['type'])) ?></p>
                            </div>
                            <!-- Stars (real rating) -->
                            <?php
                                $r   = $ratings[$car['id']] ?? null;
                                $avg = $r ? (float)$r['avg'] : 0;
                                $cnt = $r ? (int)$r['cnt']   : 0;
                                $full  = (int)floor($avg);
                                $half  = ($avg - $full) >= 0.5 ? 1 : 0;
                                $empty = 5 - $full - $half;
                            ?>
                            <div class="flex items-center gap-1">
                                <?php if ($cnt > 0): ?>
                                    <span class="text-yellow-400 text-xs">
                                        <?= str_repeat('★', $full) ?>
                                        <?= $half ? '½' : '' ?>
                                        <?= str_repeat('☆', $empty) ?>
                                    </span>
                                    <span class="text-slate-500 text-xs"><?= number_format($avg, 1) ?></span>
                                <?php else: ?>
                                    <span class="text-slate-600 text-xs">☆☆☆☆☆</span>
                                    <span class="text-slate-600 text-xs">No reviews</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Specs row -->
                        <div class="flex items-center gap-4 py-3 my-3 border-y text-xs text-slate-400" style="border-color:rgba(255,255,255,0.06);">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                                <?= $car['seats'] ?> Seats
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 6V2H8v4H4v11l3 3h10l3-3V6h-8z"/></svg>
                                <?= htmlspecialchars($car['transmission']) ?>
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.77 7.23l.01-.01-3.72-3.72L15 4.56l2.11 2.11c-.94.36-1.61 1.26-1.61 2.33 0 1.38 1.12 2.5 2.5 2.5.36 0 .69-.08 1-.21v7.21c0 .55-.45 1-1 1s-1-.45-1-1V14c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v16h10v-7.5h1.5v5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V9c0-.69-.28-1.32-.73-1.77z"/></svg>
                                <?= htmlspecialchars($car['fuel']) ?>
                            </span>
                        </div>

                        <!-- Price & CTA -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-black" style="color:#00E5FF;">₹<?= number_format($car['price']*75) ?></span>
                                <span class="text-slate-500 text-xs">/day</span>
                                <div class="text-slate-500 text-[11px]">Total: ₹<?= number_format($car['price']*75*6) ?> (6 days)</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="/car-rental/car_details.php?id=<?= $car['id'] ?>"
                                   class="px-5 py-2 rounded-lg text-sm font-bold text-black transition-all hover:brightness-110"
                                   style="background:#00E5FF; outline:2px solid #00E5FF;">
                                    Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
function applySortFilter(val) {
    const url = new URL(window.location);
    url.searchParams.set('sort', val);
    window.location = url.toString();
}
function setSeat(val) {
    document.getElementById('seats-input').value = val;
    document.getElementById('filter-form').submit();
}
</script>

<?php require 'includes/footer.php'; ?>
