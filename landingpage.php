<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Great Community</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: "#111827",         // teks judul (abu gelap)
            primary: "#b71c1c",       // warna brand merah (dipakai dikit)
            accent: "#f97316",        // aksen oranye halus
            panel: "#f5f7fb",         // panel putih-kebiruan lembut (mirip screenshot)
            lilac: "#8b5cf6",         // ungu untuk Staff List
          }
        }
      }
    }
  </script>

  <!-- Inter font untuk nuansa clean -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji","Segoe UI Emoji"; }
    /* dot slider */
    .dot { width:8px; height:8px; border-radius:9999px; background:#d1d5db; }
    .dot.active { background:#6b7280; }
    /* soft shadow */
    .card { box-shadow: 0 8px 20px rgba(0,0,0,.06); }
    .soft-border { border:1px solid rgba(0,0,0,.06); }
  </style>
</head>
<body class="bg-white text-gray-700">

  <!-- Navbar -->
  <header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <!-- ganti logo_great.png dengan logo kamu -->
        <img src="logo_great.png" class="w-8 h-8 rounded-full ring-1 ring-gray-200" alt="Logo">
        <span class="text-[18px] font-semibold text-brand">Great Roleplay</span>
      </div>
      <nav class="hidden md:flex items-center gap-8 text-[15px]">
        <a href="#about" class="hover:text-gray-900">About</a>
        <a href="#stats" class="hover:text-gray-900">Server Stats</a>
        <a href="#features" class="hover:text-gray-900">Features</a>
        <a href="#admin" class="hover:text-gray-900">Staff</a>
        <a href="#links" class="hover:text-gray-900">Usefull Links</a>
      </nav>
      <div class="flex items-center gap-2">
        <a href="login.php" class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-black transition">Login</a>
        <a href="index.php" class="px-4 py-2 rounded-lg border border-gray-300 text-sm hover:border-gray-400 transition">Register</a>
      </div>
    </div>
  </header>

  <!-- Hero / About -->
  <section id="about" class="max-w-6xl mx-auto px-4 sm:px-6 py-16 md:py-24">
    <div class="grid md:grid-cols-2 gap-10 items-center">
      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-brand tracking-tight">Great Roleplay</h1>
        <p class="mt-4 text-[15.5px] text-gray-600 leading-7">
          Great Roleplay adalah server GTA: San Andreas Multiplayer yang didedikasikan untuk seluruh player SA:MP.
          Fokus kami memberikan pengalaman roleplay yang rapi, seru, dan nyaman untuk dimainkan.
        </p>
        <div class="mt-6 flex gap-3">
          <a href="#discord" class="px-5 py-3 rounded-lg bg-gray-900 text-white text-sm hover:bg-black transition">Join Discord</a>
          <a href="#stats" class="px-5 py-3 rounded-lg border border-gray-300 text-sm hover:border-gray-400 transition">Server Stats</a>
        </div>
      </div>
      <div class="relative">
        <!-- ganti ilustrasi sesuai aset kamu -->
        <img src="http://localhost/Relived-LandingPage-master/assets/img/illustrations/about.svg" class="w-full h-auto" alt="illustration">
      </div>
    </div>
  </section>

  <!-- Features (panel putih kebiruan + slider gambar) -->
  <section id="features" class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="rounded-3xl soft-border card" style="background: linear-gradient(180deg,#fbfbfd, #f6f8fb);">
      <div class="px-6 sm:px-10 pt-8 text-center">
        <h2 class="text-3xl font-extrabold text-brand">Features</h2>
        <p class="text-gray-500 mt-2 text-[15px]">
          Some of the features and advantages that we provide for those of you
        </p>
      </div>

      <!-- slider sederhana (scroll-snap) -->
      <div class="mt-6 px-4 sm:px-10 pb-10">
        <div class="flex gap-2 ml-4">
          <span class="dot active"></span>
          <span class="dot"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>
        <div class="mt-3 overflow-x-auto scroll-smooth snap-x snap-mandatory rounded-2xl soft-border">
          <div class="min-w-[900px] w-full h-[380px] bg-black/5">
            <!-- ganti 3 gambar ini sesuai screenshot/gambar server kamu -->
            <img src="sa-mp-001.png" class="w-full h-[380px] object-cover snap-start" alt="">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Server Statistics (tampilan mirip: ilustrasi kiri, info kanan di panel lembut) -->
  <section id="stats" class="max-w-6xl mx-auto px-4 sm:px-6 mt-16">
    <div class="bg-[#fdeff3] rounded-[28px] card soft-border overflow-hidden">
      <div class="grid md:grid-cols-2 gap-0">
        <div class="p-8 md:p-10">
          <!-- ilustrasi statistik (placeholder) -->
          <img src="server-stats.svg" class="rounded-2xl w-full h-[260px] object-cover" alt="">
        </div>
        <div class="p-8 md:p-10">
          <h3 class="text-3xl font-extrabold text-brand">Server Statistics</h3>
          <div class="mt-4 space-y-3">
            <div class="rounded-xl bg-white/70 soft-border p-4">
              <p class="text-[14px] text-gray-500">Server Status:</p>
              <p id="server-status" class="mt-1 font-semibold text-red-600">ONLINE</p>
            </div>
            <div class="rounded-xl bg-white/70 soft-border p-4 grid grid-cols-2 gap-3 text-[15px]">
              <div class="flex items-center justify-between"><span>Player Online</span><strong id="player-count">0/100</strong></div>
              <div class="flex items-center justify-between"><span>Total Akun</span><strong>241</strong></div>
              <div class="flex items-center justify-between"><span>Faction</span><strong>4</strong></div>
              <div class="flex items-center justify-between"><span>Jobs</span><strong>12</strong></div>
              <div class="flex items-center justify-between"><span>Gangs</span><strong>8</strong></div>
              <div class="flex items-center justify-between"><span>Uptime</span><strong>-</strong></div>
            </div>
            <p class="text-[13px] text-gray-500 italic">*Angka dapat dihubungkan ke backend / query SA-MP server.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Staff / Administrator List (panel ungu seperti screenshot) -->
  <section id="admin" class="max-w-6xl mx-auto px-4 sm:px-6 mt-16">
    <div class="rounded-2xl card" style="background: linear-gradient(180deg,#a78bfa,#8b5cf6);">
      <div class="px-6 sm:px-10 py-10">
        <h2 class="text-3xl font-extrabold text-white text-center">Staff List</h2>
        <div class="mt-8 grid md:grid-cols-2 gap-6">
          <!-- Management Team -->
          <div class="bg-white/85 rounded-xl soft-border overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
              <h3 class="font-semibold text-brand text-center">Management Team</h3>
            </div>
            <table class="w-full text-left text-[14px]">
              <thead>
                <tr class="text-gray-500">
                  <th class="px-5 py-3 border-b">Nama</th>
                  <th class="px-5 py-3 border-b">Rank</th>
                </tr>
              </thead>
              <tbody>
                <tr><td class="px-5 py-3">Rizky</td><td class="px-5 py-3">Founder</td></tr>
                <tr class="bg-gray-50"><td class="px-5 py-3">Nabil</td><td class="px-5 py-3">Developer</td></tr>
                <tr><td class="px-5 py-3">Anen</td><td class="px-5 py-3">Developer Support</td></tr>
                <tr class="bg-gray-50"><td class="px-5 py-3">Lucas</td><td class="px-5 py-3">Server Manager</td></tr>
                <tr><td class="px-5 py-3">Bagus</td><td class="px-5 py-3">Server Support</td></tr>
              </tbody>
            </table>
          </div>

          <!-- Administrator Team -->
          <div class="bg-white/85 rounded-xl soft-border overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
              <h3 class="font-semibold text-brand text-center">Administrator Team</h3>
            </div>
            <table class="w-full text-left text-[14px]">
              <thead>
                <tr class="text-gray-500">
                  <th class="px-5 py-3 border-b">Nama</th>
                  <th class="px-5 py-3 border-b">Rank</th>
                </tr>
              </thead>
              <tbody>
                <tr><td class="px-5 py-3">Ray</td><td class="px-5 py-3">Admin Level 1</td></tr>
                <tr class="bg-gray-50"><td class="px-5 py-3">Dikaa</td><td class="px-5 py-3">Admin Level 1</td></tr>
                <tr><td class="px-5 py-3">Faddil35</td><td class="px-5 py-3">Admin Level 1</td></tr>
                <tr class="bg-gray-50"><td class="px-5 py-3">Angga</td><td class="px-5 py-3">Admin Level 1</td></tr>
                <tr><td class="px-5 py-3">Fnama</td><td class="px-5 py-3">Admin Level 1</td></tr>
                <tr class="bg-gray-50"><td class="px-5 py-3">dottskuvy</td><td class="px-5 py-3">Helper</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Useful Links + Social Media (tata letak mirip screenshot) -->
  <section id="links" class="max-w-6xl mx-auto px-4 sm:px-6 mt-16">
    <hr class="border-gray-200 mb-6">
    <div class="grid md:grid-cols-3 gap-8 items-start">
      <!-- kiri: logo + alamat -->
      <div class="space-y-4">
        <div class="flex items-center gap-3">
          <img src="logo_great.png" class="w-10 h-10 rounded-full ring-1 ring-gray-200" alt="logo">
          <span class="font-semibold text-brand">Great Roleplay</span>
        </div>
        <div class="text-[14.5px] text-gray-600 leading-7">
          <p class="pt-2"><a class="text-gray-700 hover:underline" href="rizkycuaks@gmail.com">info@greatcommunity.org</a></p>
        </div>
        <p class="text-[13px] text-gray-500 pt-2">&copy; 2025 Great Roleplay.</p>
      </div>

      <!-- tengah: useful links -->
      <div>
        <h4 class="text-xl font-extrabold text-brand">Usefull Links</h4>
        <ul class="mt-4 space-y-3 text-[15px]">
          <li><a href="#" class="hover:underline">Our Discord</a></li>
          <li><a href="#" class="hover:underline">UCP</a></li>
        </ul>
      </div>

      <!-- kanan: social media -->
      <div>
        <h4 class="text-xl font-extrabold text-brand">Social Media</h4>
        <div class="mt-4 flex items-center gap-4 text-2xl">
          <a href="#" class="text-gray-700 hover:text-black" aria-label="Facebook">&#xf09a;</a>
          <a href="#" class="text-gray-700 hover:text-black" aria-label="Instagram">&#xf16d;</a>
        </div>
        <!-- FontAwesome unicode via CSS class -->
        <style>
          [aria-label="Facebook"], [aria-label="Instagram"] { font-family:"Font Awesome 6 Free"; font-weight: 900; }
        </style>
      </div>
    </div>
  </section>

  <!-- Footer tipis -->
  <footer class="max-w-6xl mx-auto px-4 sm:px-6 mt-10 mb-12 text-center text-[13.5px] text-gray-500">
    Copyright © 2025 Great Roleplay.
  </footer>

  <!-- Script kecil untuk demo angka -->
  <script>
    // Demo angka (ganti dgn fetch backend/Query samp)
    document.getElementById("server-status").textContent = "ONLINE";
    document.getElementById("player-count").textContent = "18/100";
  </script>
</body>
</html>
