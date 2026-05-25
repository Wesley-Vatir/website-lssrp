<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['ucp'])) {
    header("Location: login.php");
    exit;
}

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'grit';
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Koneksi DB gagal: " . $conn->connect_error);
}

$ucp = $_SESSION['ucp'];

// Ambil semua reg_id karakter milik user berdasarkan ucp
$stmtChar = $conn->prepare("SELECT reg_id, username FROM players WHERE ucp = ?");
$stmtChar->bind_param("s", $ucp);
$stmtChar->execute();
$resChar = $stmtChar->get_result();
$regIds = [];
$charNames = [];
while ($row = $resChar->fetch_assoc()) {
    $regIds[] = $row['reg_id'];
    $charNames[$row['reg_id']] = $row['username']; // untuk tampilan nama karakter
}
$stmtChar->close();

$vehicles = [];
if (count($regIds) > 0) {
    // Buat placeholders dan types untuk IN clause
    $placeholders = implode(',', array_fill(0, count($regIds), '?'));
    $types = str_repeat('i', count($regIds));
    $sql = "SELECT * FROM vehicle WHERE owner IN ($placeholders) LIMIT 500";

    $stmtVeh = $conn->prepare($sql);

    // bind_param butuh reference, jadi kita siapkan array
    $bind_names[] = $types;
    foreach ($regIds as $key => $id) {
        $bind_names[] = &$regIds[$key];
    }

    call_user_func_array([$stmtVeh, 'bind_param'], $bind_names);
    $stmtVeh->execute();
    $resVeh = $stmtVeh->get_result();
    while ($row = $resVeh->fetch_assoc()) {
        $vehicles[] = $row;
    }
    $stmtVeh->close();
}

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

if (!function_exists('progressBar')) {
    function progressBar($value, $max = 100, $color = 'bg-red-500') {
        $percent = 0;
        if (is_numeric($value) && $value >= 0) {
            $percent = min(100, ($value / $max) * 100);
        }
        return '
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="' . $color . ' h-4 rounded-full" style="width:' . $percent . '%"></div>
        </div>
        ';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Vehicle Stats - GREAT UCP</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
.transition-smooth { transition: all 280ms cubic-bezier(.4,0,.2,1); }
.card-hover:hover { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); }
.sidebar-overlay { backdrop-filter: blur(4px); background: rgba(0,0,0,0.35); }
</style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">
<div class="min-h-screen flex">

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed z-30 inset-y-0 left-0 w-72 transform -translate-x-full md:translate-x-0 transition-smooth bg-red-900 text-white shadow-lg">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-red-700">
            <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center">
                <img src="logo_great.png" alt="Lion Logo" class="w-8 h-8 object-contain">
            </div>
            <div>
                <div class="text-lg font-bold">GREAT UCP</div>
            </div>
        </div>
      <nav class="px-4 py-6 space-y-1">
        <a href="dashboard.php" class="group flex items-center gap-3 px-3 py-2 rounded-md hover:bg-yellow-400 hover:text-black transition-smooth">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M3 13h8V3H3v10zM13 21h8v-6h-8v6zM13 3v8h8V3h-8zM3 21h8v-4H3v4z" fill="currentColor"/></svg>
          <span class="font-medium">Dashboard</span>
        </a>

        <a href="account.php" class="group flex items-center gap-3 px-3 py-2 rounded-md hover:bg-yellow-400 hover:text-black transition-smooth">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zM4 20c0-2.2 3.6-4 8-4s8 1.8 8 4v1H4v-1z" fill="currentColor"/></svg>
          <span class="font-medium">Account</span>
        </a>

        <a href="characters.php" class="group flex items-center gap-3 px-3 py-2 rounded-md hover:bg-yellow-400 hover:text-black transition-smooth">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zM7 20c0-2 4-3 5-3s5 1 5 3v1H7v-1z" fill="currentColor"/></svg>
          <span class="font-medium">Characters</span>
        </a>

        <a href="vehicle.php" class="group flex items-center gap-3 px-3 py-2 rounded-md hover:bg-yellow-400 hover:text-black transition-smooth">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M3 11l1 2h16l1-2-3-5H6L3 11zm3 6a2 2 0 100-4 2 2 0 000 4zm12 0a2 2 0 100-4 2 2 0 000 4z" fill="currentColor"/></svg>
          <span class="font-medium">Vehicles</span>
        </a>

        <a href="#stats" class="group flex items-center gap-3 px-3 py-2 rounded-md hover:bg-yellow-400 hover:text-black transition-smooth">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M3 13h2v8H3v-8zm6-6h2v14h-2V7zm6 4h2v10h-2V11zm6-8h2v18h-2V3z" fill="currentColor"/></svg>
          <span class="font-medium">Server Stats</span>
        </a>

        <a href="php/logout.php" class="group flex items-center gap-3 px-3 py-2 rounded-md mt-4 text-red-100 hover:bg-yellow-400 hover:text-black transition-smooth">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M16 13v-2H7V8l-5 4 5 4v-3zM20 3h-8v2h8v14h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" fill="currentColor"/></svg>
          <span class="font-medium">Logout</span>
        </a>
      </nav>
        <div class="mt-auto p-4 text-xs text-red-200 border-t border-red-700">
            © <?= date('Y') ?> GREAT Roleplay
        </div>
    </aside>

    <!-- OVERLAY FOR MOBILE -->
    <div id="overlay" class="fixed inset-0 z-20 hidden md:hidden sidebar-overlay"></div>

    <!-- MAIN -->
    <div class="flex-1 min-h-screen md:pl-72">
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div class="md:hidden flex items-center gap-3 px-4 py-3">
                    <button id="btnToggle" class="p-2 rounded-md bg-red-800 text-white hover:bg-red-700 transition-smooth">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                    <div class="font-bold text-lg">GREAT UCP</div>
                </div>
            </div>
        </header>

        <main class="p-6">
            <div class="bg-white rounded-lg p-6 border border-gray-100 max-w-5xl mx-auto card-hover transition-smooth">
                <?php if (count($vehicles) === 0): ?>
                    <p class="text-gray-600">Belum ada kendaraan yang terdaftar.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        <?php foreach ($vehicles as $veh): ?>
                            <div class="bg-red-50 rounded-lg border border-red-200 p-4 flex flex-col text-left card-hover transition-smooth">
                                <?php
                                    $skinId = (int)$veh['model'];
                                    $skinId = (int)$veh['model'];
                                    $skinImgUrl = "https://assets.open.mp/assets/images/vehiclePictures/Vehicle_" . $skinId . ".jpg";

                                ?>
                                <img src="<?= e($skinImgUrl) ?>" alt="model <?= e($skinId) ?>" class="w-32 h-32 object-contain mb-4" loading="lazy" onerror="this.src='https://via.placeholder.com/128x128?text=No+Image'">

                                <h3 class="text-xl font-bold text-red-800 mb-1">
                                    Model ID: <?= e($veh['model']) ?> 
                                    (Owner: <?= isset($charNames[$veh['owner']]) ? e($charNames[$veh['owner']]) : 'Unknown' ?>)
                                </h3>
                                <p><strong>Plate:</strong> <?= e($veh['plate']) ?></p>
                                <p><strong>Price:</strong> $<?= number_format($veh['price']) ?></p>

                                <p><strong>Health:</strong> <?= number_format($veh['health'], 2) ?></p>
                                <?= progressBar($veh['health'], 1000, 'bg-red-600') ?>

                                <p><strong>Fuel:</strong> <?= e($veh['fuel']) ?></p>
                                <?= progressBar($veh['fuel'], 1000, 'bg-yellow-400') ?>

                                <p><strong>Locked:</strong> <?= $veh['locked'] ? 'Yes' : 'No' ?></p>
                                <p><strong>Insured:</strong> <?= $veh['insu'] ? 'Yes' : 'No' ?></p>
                                <p><strong>Paintjob:</strong> <?= e($veh['paintjob']) ?></p>
                                <p><strong>Neon:</strong> <?= $veh['neon'] ? 'Yes' : 'No' ?></p>
                                <p><strong>Claimed:</strong> <?= $veh['claim'] ? 'Yes' : 'No' ?></p>
                                <p><strong>Claim Time:</strong> <?= $veh['claim_time'] ? date('d M Y H:i', $veh['claim_time']) : '-' ?></p>
                                <p><strong>Ticket:</strong> <?= e($veh['ticket']) ?></p>
                                <p><strong>Rental Time:</strong> <?= $veh['rental'] ? date('d M Y H:i', $veh['rental']) : '-' ?></p>
                                <p><strong>Broken:</strong> <?= $veh['broken'] ? 'Yes' : 'No' ?></p>
                                <p><strong>Trunk:</strong> <?= e($veh['trunk']) ?></p>
                                <p><strong>Money:</strong> <?= number_format($veh['money']) ?></p>
                                <p><strong>Red Money:</strong> <?= number_format($veh['redmoney']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<script>
const btn = document.getElementById('btnToggle');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
}
function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
}
if (btn) btn.addEventListener('click', () => {
    if (sidebar.classList.contains('-translate-x-full')) openSidebar();
    else closeSidebar();
});
if (overlay) overlay.addEventListener('click', closeSidebar);
</script>
</body>
</html>
