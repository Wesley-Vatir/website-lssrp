<?php
$host = '139.59.243.158';
$db   = 's45_tutorial';
$user = 'u45_1xemgigrhq';
$pass = '=7Xtasv+88lPmZAmCvzEEYCt';

$conn = new mysqli($host, $user, $pass, $db);

if (isset($_POST['submit'])) {
    $username = $conn->real_escape_string($_POST['username']);

    // Cek apakah username sudah ada
    $check = $conn->query("SELECT ucp FROM playerucp WHERE ucp = '$username'");
    if ($check->num_rows > 0) {
        die("Username sudah terpakai! <a href='index.php'>Kembali</a>");
    }

    // Insert data dengan nilai default 0
    $sql = "INSERT INTO playerucp (ucp, verifycode, DiscordID, password, salt, extrac) 
            VALUES ('$username', 0, 0, 0, 0, 0)";

    if ($conn->query($sql) === TRUE) {
        header("Location: done.html");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
