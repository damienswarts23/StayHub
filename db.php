<?php
$host = "db.qaozkczlbxyapynzjuou.supabase.co";
$port = "5432";
$dbname = "StayHub";
$user = "postgres";
$password = "#Chelsea4life";

try {
    $dsn = "postgresql:postgres:#Chelsea4life@db.StayHub.supabase.co:5432/StayHub";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

