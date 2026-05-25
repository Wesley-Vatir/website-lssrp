<?php
session_start();

// Proteksi: harus login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['ucp'])) {
  header("Location: login.php");
  exit;
}

/* ========== DB CONFIG - sesuaikan jika perlu ========== */
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'grit'; // ganti ke 'grit' jika DB-mu bernama itu
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
  die("Koneksi DB gagal: " . $conn->connect_error);
}

/* ========== QUERY: Total players ========== */
$totalPlayers = 0;
if ($res = $conn->query("SELECT COUNT(*) AS cnt FROM players")) {
  $row = $res->fetch_assoc();
  $totalPlayers = (int)$row['cnt'];
  $res->free();
}

/* ========== QUERY: Top uptime players (top 10) ========== */
$topUptime = [];
if ($res = $conn->query("SELECT reg_id, username, hours, minutes, seconds FROM players ORDER BY hours DESC, minutes DESC LIMIT 10")) {
  while ($r = $res->fetch_assoc()) $topUptime[] = $r;
  $res->free();
}

/* ========== QUERY: Characters milik user (by ucp) ========== */
$myChars = [];
$ucp = $_SESSION['ucp'];
if ($stmt = $conn->prepare("SELECT reg_id, username, ucp, level, money, hours, minutes, seconds, last_login, skin FROM players WHERE ucp = ?")) {
  $stmt->bind_param("s", $ucp);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($r = $res->fetch_assoc()) $myChars[] = $r;
  $stmt->close();
}

/* ========== QUERY: Vehicles milik karakter user ========== */
$myVehicles = [];
if (count($myChars) > 0) {
  $ids = array_column($myChars, 'reg_id');
  // buat placeholders dan types
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $types = str_repeat('i', count($ids));
  $sql = "SELECT id, owner, model, plate, health, fuel, x, y, z FROM vehicle WHERE owner IN ($placeholders) LIMIT 500";
  if ($vehStmt = $conn->prepare($sql)) {
    // bind params dinamically
    // mysqli_stmt::bind_param membutuhkan variable references
    $bind_names[] = $types;
    for ($i=0; $i<count($ids); $i++) {
      $bind_name = 'bind' . $i;
      $$bind_name = $ids[$i];
      $bind_names[] = &$$bind_name;
    }
    call_user_func_array([$vehStmt, 'bind_param'], $bind_names);
    $vehStmt->execute();
    $res = $vehStmt->get_result();
    while ($r = $res->fetch_assoc()) $myVehicles[] = $r;
    $vehStmt->close();
  }
}

/* ========== helper: format waktu playtime ========== */
function format_playtime($h, $m, $s = 0) {
  $h = (int)$h; $m = (int)$m; $s = (int)$s;
  return "{$h}h " . str_pad($m, 2, '0', STR_PAD_LEFT) . "m " . str_pad($s, 2, '0', STR_PAD_LEFT) . "s";
}

/* ========== sanitize helper ========== */
function e($str) { return htmlspecialchars($str, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard - GREAT UCP</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    /* tambahan transisi */
    .transition-smooth { transition: all 280ms cubic-bezier(.4,0,.2,1); }
    .card-hover:hover { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); }
    /* sidebar mobile overlay */
    .sidebar-overlay { backdrop-filter: blur(4px); background: rgba(0,0,0,0.35); }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

  <div class="min-h-screen flex">

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed z-30 inset-y-0 left-0 w-72 transform -translate-x-full md:translate-x-0 transition-smooth bg-red-900 text-white shadow-lg">
      <div class="flex items-center gap-3 px-6 py-5 border-b border-red-700">
        <!-- logo bulat mirip style -->
        <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center">
          <!-- icon lion from PNG -->
          <img src="logo_great.png" alt="Lion Logo" class="w-8 h-8 object-contain">
        </div>
        <div>
          <div class="text-lg font-bold">GREAT UCP</div>
          <div class="text-xs text-red-200">Welcome, <span class="font-semibold"><?= e($_SESSION['ucp']) ?></span></div>
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

      <!-- HEADER: banner + mobile top bar -->
      <header class="bg-white shadow-sm sticky top-0 z-10">
        <div class="flex items-center justify-between">
          <!-- mobile toggle button -->
          <div class="md:hidden flex items-center gap-3 px-4 py-3">
            <button id="btnToggle" class="p-2 rounded-md bg-red-800 text-white hover:bg-red-700 transition-smooth" aria-label="Open menu">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
            <div class="font-bold text-lg">GREAT UCP</div>
          </div>
      </header>

      <!-- CONTENT AREA -->
      <main class="p-6 space-y-6">

        <!-- OVERVIEW CARDS -->
        <section id="overview" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div class="bg-white rounded-lg p-5 border border-gray-100 card-hover transition-smooth">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-xs text-gray-400">Total Players</div>
                <div class="text-2xl font-bold text-red-800"><?= number_format($totalPlayers) ?></div>
              </div>
              <div class="p-3 rounded-full bg-red-50 text-red-700">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"><path d="M3 13h8V3H3v10zM13 21h8v-6h-8v6z" fill="currentColor"/></svg>
              </div>
            </div>
            <div class="mt-3 text-sm text-gray-500">Jumlah pemain terdaftar di server.</div>
          </div>

          <div class="bg-white rounded-lg p-5 border border-gray-100 card-hover transition-smooth">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-xs text-gray-400">Your Characters</div>
                <div class="text-2xl font-bold text-red-800"><?= count($myChars) ?></div>
              </div>
              <div class="p-3 rounded-full bg-red-50 text-red-700">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"><path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5z" fill="currentColor"/></svg>
              </div>
            </div>
            <div class="mt-3 text-sm text-gray-500">Jumlah karakter yang terhubung ke akunmu.</div>
          </div>

          <div class="bg-white rounded-lg p-5 border border-gray-100 card-hover transition-smooth">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-xs text-gray-400">Your Vehicles</div>
                <div class="text-2xl font-bold text-red-800"><?= count($myVehicles) ?></div>
              </div>
              <div class="p-3 rounded-full bg-red-50 text-red-700">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"><path d="M3 11l1 2h16l1-2-3-5H6L3 11z" fill="currentColor"/></svg>
              </div>
            </div>
            <div class="mt-3 text-sm text-gray-500">Kendaraan yang dimiliki oleh karaktermu.</div>
          </div>
        </section>

        <!-- LAYOUT: main table + right column -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          <!-- main: uptime + characters -->
          <div class="lg:col-span-2 space-y-6">

            <!-- TOP UPTIME -->
            <div class="bg-white rounded-lg p-4 border border-gray-100 card-hover transition-smooth">
              <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-red-800">Top Uptime Players</h3>
                <span class="text-sm text-gray-500">Top 10 by hours</span>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-sm">
                  <thead class="text-gray-600">
                    <tr>
                      <th class="text-left py-2">#</th>
                      <th class="text-left py-2">Username</th>
                      <th class="text-left py-2">Playtime</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($topUptime) === 0): ?>
                      <tr><td colspan="3" class="py-3 text-gray-500">No data.</td></tr>
                    <?php else: ?>
                      <?php foreach ($topUptime as $i => $p): ?>
                        <tr class="border-t">
                          <td class="py-2"><?= $i+1 ?></td>
                          <td class="py-2"><?= e($p['username']) ?> <span class="text-xs text-gray-400">#<?= (int)$p['reg_id'] ?></span></td>
                          <td class="py-2"><?= e(format_playtime($p['hours'],$p['minutes'],$p['seconds'])) ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- CHARACTERS -->
            <div id="characters" class="bg-white rounded-lg p-4 border border-gray-100 card-hover transition-smooth">
              <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-red-800">Your Characters</h3>
                <a href="characters.php" class="text-sm text-yellow-400 hover:underline">Manage account</a>
              </div>

              <?php if (count($myChars) === 0): ?>
                <div class="text-gray-500">Belum ada karakter yang terhubung dengan akun ini.</div>
              <?php else: ?>
                <div class="space-y-3">
                  <?php foreach ($myChars as $c): ?>
                    <div class="flex items-center justify-between border rounded p-3">
                      <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-red-50 rounded flex items-center justify-center">
                          <svg class="w-6 h-6 text-red-700" viewBox="0 0 24 24" fill="none"><path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5z" fill="currentColor"/></svg>
                        </div>
                        <div>
                          <div class="font-medium"><?= e($c['username']) ?> <span class="text-xs text-gray-400">#<?= (int)$c['reg_id'] ?></span></div>
                          <div class="text-xs text-gray-500">Level <?= (int)$c['level'] ?> · $ <?= number_format($c['money']) ?> · Last login: <?= e($c['last_login']) ?></div>
                        </div>
                      </div>
                      <div class="text-right">
                        <div class="text-sm text-gray-600"><?= e(format_playtime($c['hours'],$c['minutes'],$c['seconds'])) ?></div>
                        <a href="characters.php?reg_id=<?= (int)$c['reg_id'] ?>" class="mt-2 inline-block text-sm text-yellow-400 hover:underline">Details</a>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>

          </div>

          <!-- RIGHT COLUMN: vehicles + quick stats -->
          <aside class="space-y-6">

            <div id="vehicles" class="bg-white rounded-lg p-4 border border-gray-100 card-hover transition-smooth">
              <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-red-800">Your Vehicles</h3>
                <span class="text-sm text-gray-500"><?= count($myVehicles) ?> items</span>
              </div>

              <?php if (count($myVehicles) === 0): ?>
                <div class="text-gray-500">Tidak ada kendaraan.</div>
              <?php else: ?>
                <div class="space-y-2">
                  <?php foreach ($myVehicles as $v): ?>
                    <div class="border rounded p-3 flex items-center justify-between">
                      <div>
                        <div class="font-medium">Model <?= (int)$v['model'] ?> <span class="text-xs text-gray-400">ID #<?= (int)$v['id'] ?></span></div>
                        <div class="text-xs text-gray-500">Plate: <?= e($v['plate']) ?: '—' ?> · Health: <?= (int)$v['health'] ?></div>
                      </div>
                      <div class="text-sm text-gray-600">Fuel: <?= (int)$v['fuel'] ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>

            <div id="stats" class="bg-white rounded-lg p-4 border border-gray-100 card-hover transition-smooth">
              <h3 class="font-semibold text-red-800 mb-2">Quick Server Stats</h3>
              <div class="text-sm text-gray-600 space-y-2">
                <div>Total players: <span class="font-medium text-gray-800"><?= number_format($totalPlayers) ?></span></div>
                <div>Server uptime: <span class="font-medium text-gray-800">-- (set manual)</span></div>
                <div>Last DB sync: <span class="font-medium text-gray-800"><?= date('Y-m-d H:i') ?></span></div>
              </div>
            </div>

          </aside>

        </section>

      </main>

    </div>
  </div>

  <!-- Scripts: sidebar toggle & overlay -->
  <script>
    const btn = document.getElementById('btnToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    function openSidebar() {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
      overlay.classList.add('block');
    }
    function closeSidebar() {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
      overlay.classList.remove('block');
    }

    if (btn) {
      btn.addEventListener('click', () => {
        if (sidebar.classList.contains('-translate-x-full')) openSidebar();
        else closeSidebar();
      });
    }
    if (overlay) {
      overlay.addEventListener('click', closeSidebar);
    }
  </script>
</body>
</html>
