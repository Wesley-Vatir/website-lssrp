<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - SA-MP UCP</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>

    <?php if (isset($_SESSION['notif'])): ?>
      <div class="mb-4 px-4 py-2 rounded <?= $_SESSION['notif_type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
        <?= $_SESSION['notif']; unset($_SESSION['notif'], $_SESSION['notif_type']); ?>
      </div>
    <?php endif; ?>

    <form action="php/register.php" method="POST" class="space-y-4">
      <input type="text" name="ucp" placeholder="Username" class="w-full px-4 py-2 border rounded" required>
      <input type="email" name="email" placeholder="Email" class="w-full px-4 py-2 border rounded" required>
      <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 border rounded" required>

      <div class="flex space-x-2 items-center">
        <input type="text" name="verifycode" placeholder="Verification Code" class="flex-1 px-4 py-2 border rounded text-sm" required>
        <button type="button" onclick="sendCode()" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded whitespace-nowrap">Send Code</button>
      </div>

      <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Register</button>
    </form>

    <p class="mt-4 text-center text-sm">Have A Account? <a href="login.php" class="text-blue-600 hover:underline">Login</a></p>
  </div>

  <script>
    function sendCode() {
      const email = document.querySelector('[name="email"]').value;
      if (!email) return alert("Input Your Email!");

      fetch('mail/send_code.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'email=' + encodeURIComponent(email)
      })
      .then(res => res.text())
      .then(res => alert(res))
      .catch(err => alert("Gagal mengirim kode: " + err));
    }
  </script>
</body>
</html>
