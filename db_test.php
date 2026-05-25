<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

$dbHost = 'localhost';
$dbName = 'jgrp';   // ganti sesuai nama DB
$dbUser = 'root';       // ganti kalau bukan root
$dbPass = '';           // ganti password jika ada

try {
  $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",$dbUser,$dbPass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
  ]);
  echo "DB OK — connected to $dbName";
} catch (PDOException $e) {
  echo "DB ERROR: " . $e->getMessage();
}
