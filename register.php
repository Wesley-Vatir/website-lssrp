<?php
// register.php - untuk tabel `ucp` seperti yang dikirim user
// expects JSON: { "username": "...", "discord": "Name#1234", "password": "..." }
// sakhageloooo

// ---------- CONFIG MYSQL ----------
$dbHost = '139.59.243.158';
$dbName = 's45_tutorial';
$dbUser = 'u45_1xemgigrhq';
$dbPass = '=7Xtasv+88lPmZAmCvzEEYCt';
// ----------------------------------------------------

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// baca input JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$username   = trim($data['username'] ?? '');
$discordTag = trim($data['discord'] ?? '');

// validasi basic
if (strlen($username) < 4) {
    http_response_code(400);
    echo json_encode(['error' => 'Username minimal 4 karakter']);
    exit;
}

if (!preg_match('/^.{2,32}#\d{4}$/', $discordTag)) {
    http_response_code(400);
    echo json_encode(['error' => 'Discord harus dalam format Nama#1234 (contoh: Sakha#1234)']);
    exit;
}

// cek unik username
try {
    $stmt = $pdo->prepare('SELECT ID FROM playerucp WHERE ucp = :u LIMIT 1');
    $stmt->execute([':u' => $username]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'Username sudah terdaftar']);
        exit;
    }

    // cek apakah discord tag sudah ada di kolom verifcode (best-effort)
    $stmt = $pdo->prepare('SELECT ID FROM playerucp WHERE verifycode = :d LIMIT 1');
    $stmt->execute([':d' => $discordTag]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'Discord tag sudah terdaftar']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error (check unique)']);
    exit;
}

// generate next reg_id (table nampaknya non-AUTO_INCREMENT)
try {
    $stmt = $pdo->query('SELECT MAX(ID) AS m FROM playerucp');
    $row = $stmt->fetch();
    $nextId = ($row && $row['m'] !== null) ? (int)$row['m'] + 1 : 1;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error (id generation)']);
    exit;
}

// generate salt (128 hex chars) dan hash sha256(salt + password)
try {
    $salt_bytes = random_bytes(64); // 64 bytes
    $salt = bin2hex($salt_bytes);   // 128 hex chars
} catch (Exception $e) {
    $salt = bin2hex(openssl_random_pseudo_bytes(64));
}
$password_hash = hash('sha256', $salt . $password); // 64 hex chars

// insert ke tabel ucp, sesuai kolom yang lo punya
// per schema yang dikirim: reg_id, username, password (char64), salt (char128), verifemail, sprunk, verifcode, verification_code,
// CharName, CharName2, CharName3, banned, bannedreason, bannedby, referral, pin, DiscordID
try {
    $ins = $pdo->prepare('INSERT INTO ucp
        (ID, ucp, salt, verifycode, DiscordID)
        VALUES
        (:ID, :ucp, :salt, :verifycode, 0)
    ');
    // sesuai default schema, bannedreason and bannedby default 'None'
    $ins->execute([
        ':ID'    => $nextId,
        ':ucp'  => $username,
        ':salt'      => $salt,
        ':verifycode' => $discordTag
    ]);

    http_response_code(201);
    echo json_encode(['message' => 'Akun berhasil dibuat', 'ID' => $nextId]);
    exit;
} catch (Exception $e) {
    // untuk debugging log $e->getMessage() ke file server, jangan tampilkan ke client
    http_response_code(500);
    echo json_encode(['error' => 'Server error (insert failed)']);
    exit;
}
?>
