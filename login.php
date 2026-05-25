<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - SA-MP UCP</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

    <?php if (isset($_SESSION['notif'])): ?>
      <div class="mb-4 px-4 py-2 rounded <?= $_SESSION['notif_type'] == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
        <?= $_SESSION['notif']; unset($_SESSION['notif'], $_SESSION['notif_type']); ?>
      </div>
    <?php endif; ?>

    <form action="php/login.php" method="POST" class="space-y-4">
      <input type="text" name="ucp" placeholder="Username" class="w-full px-4 py-2 border rounded" required>
      <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 border rounded" required>
      <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Login</button>
    </form>

    <p class="mt-4 text-center text-sm">Don't Have Account? <a href="index.php" class="text-blue-600 hover:underline">Register</a></p>
  </div>
</body>
</html>
