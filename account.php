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

// Notif
$notif = "";
$notif_type = "";

// Jika form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $newpass = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($current) || empty($newpass) || empty($confirm)) {
        $notif = "Semua field wajib diisi.";
        $notif_type = "error";
    } elseif ($newpass !== $confirm) {
        $notif = "Konfirmasi password tidak cocok.";
        $notif_type = "error";
    } else {
        $ucp = $_SESSION['ucp'];

        // Cek password lama
        $stmt = $conn->prepare("SELECT password, salt FROM playerucp WHERE ucp = ?");
        $stmt->bind_param("s", $ucp);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $hashCheck = strtoupper(hash('sha256', $current . $row['salt']));
            if ($hashCheck !== $row['password']) {
                $notif = "Password lama salah.";
                $notif_type = "error";
            } else {
                // Hash password baru
                $newSalt = $row['salt']; // bisa tetap sama atau diganti
                $newHash = strtoupper(hash('sha256', $newpass . $newSalt));

                $update = $conn->prepare("UPDATE playerucp SET password = ? WHERE ucp = ?");
                $update->bind_param("ss", $newHash, $ucp);
                if ($update->execute()) {
                    $notif = "Password berhasil diubah.";
                    $notif_type = "success";
                } else {
                    $notif = "Gagal mengubah password: " . $conn->error;
                    $notif_type = "error";
                }
                $update->close();
            }
        }
        $stmt->close();
    }
}

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Account - GREAT UCP</title>
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
            <div class="bg-white rounded-lg p-6 border border-gray-100 max-w-lg mx-auto card-hover transition-smooth">
                <h2 class="text-2xl font-bold text-red-800 mb-4">Ganti Password</h2>

                <?php if ($notif): ?>
                    <div class="mb-4 px-4 py-2 rounded <?= $notif_type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                        <?= e($notif) ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Password Lama</label>
                        <input type="password" name="current_password" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Password Baru</label>
                        <input type="password" name="new_password" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <button type="submit" class="w-full bg-red-800 hover:bg-red-700 text-white px-4 py-2 rounded transition-smooth">
                        Simpan Perubahan
                    </button>
                </form>
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
