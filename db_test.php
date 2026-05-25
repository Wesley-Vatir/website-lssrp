<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

$dbHost = '139.59.243.158';
$dbName = 's45_tutorial';   // ganti sesuai nama DB
$dbUser = 'u45_1xemgigrhq';       // ganti kalau bukan root
$dbPass = '=7Xtasv+88lPmZAmCvzEEYCt';           // ganti password jika ada

try {
  $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",$dbUser,$dbPass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
  ]);
  echo "DB OK — connected to $dbName";
} catch (PDOException $e) {
  echo "DB ERROR: " . $e->getMessage();
}
