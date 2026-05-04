<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("You must be logged in as an admin to add accommodation.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST['name'])) die("Accommodation name is required");
    if (empty($_POST['address'])) die("Address is required");
    if (empty($_POST['contact_num'])) die("Contact number is required");
    if (empty($_POST['amenities'])) die("Amenities are required");

    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $contact_num = trim($_POST['contact_num']);
    $amenities = trim($_POST['amenities']);

    $sql = "INSERT INTO accommodation (AdminID, Name, Address, ContactNum, Amenities)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("issss", $_SESSION['user_id'], $name, $address, $contact_num, $amenities);

    if (!$stmt->execute()) {
        die("Error inserting accommodation: " . $stmt->error);
    }

    $accommodation_id = $stmt->insert_id;
    $stmt->close();

    $rm_types = $_POST['rm_type'] ?? [];
    $tot_rms = $_POST['tot_rms'] ?? [];
    $prices = $_POST['price'] ?? [];

    if (count($rm_types) === 0) {
        die("At least one room type is required.");
    }

    $sql2 = "INSERT INTO rooms (AccommodationID, RmType, TotRms, AvailableRms, PricePerRmType)
             VALUES (?, ?, ?, ?, ?)";
    $stmt2 = $mysqli->prepare($sql2);

    if (!$stmt2) {
        die("Prepare failed: " . $mysqli->error);
    }

    foreach ($rm_types as $index => $type) {
        $type = trim($rm_types[$index]);
        $total = (int) ($tot_rms[$index] ?? 0);
        $available = $total;
        $price = (float) ($prices[$index] ?? 0);

        if ($type === "" || $total < 1 || $price <= 0) {
            continue;
        }

        $stmt2->bind_param("isiid", $accommodation_id, $type, $total, $available, $price);

        if (!$stmt2->execute()) {
            die("Error inserting room: " . $stmt2->error);
        }
    }

    $stmt2->close();

    header("Location: admin-panel.php");
    exit;
}
?>