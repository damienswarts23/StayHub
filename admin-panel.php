<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    die("Unauthorized access.");
}

$mysqli = require __DIR__ . "/database.php";
$admin_id = $_SESSION["user_id"];

// Fetch admin info
$sql = "SELECT FirstName, LastName FROM admin WHERE AdminID = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Count accommodations
$sql = "SELECT COUNT(*) FROM accommodation WHERE AdminID = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($total_accom);
$stmt->fetch();
$stmt->close();

// Count pending bookings
$sql = "SELECT COUNT(*)
        FROM booking b
        JOIN accommodation a ON b.AccommodationID = a.AccommodationID
        WHERE a.AdminID = ? AND b.BkStatus = 'Pending'";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($pending_bookings);
$stmt->fetch();
$stmt->close();

// Count confirmed occupants
$sql = "SELECT COUNT(*)
        FROM booking b
        JOIN accommodation a ON b.AccommodationID = a.AccommodationID
        WHERE a.AdminID = ? AND b.BkStatus = 'Confirmed'";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($occupants);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
</head>

<body>

<header>
  <div>
    <a href="index.html">
      <img src="Logo_enhanced.png" alt="StayHub Logo" />
      STAY HUB
    </a>
  </div>

  <nav>
    <a href="admins-profile.php">Profile</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>

  <h1>Admin Dashboard</h1>
  <p>Welcome back, <?= htmlspecialchars($admin["FirstName"]) ?>!</p>

  <div>
    <div>
      <h2><?= $total_accom ?></h2>
      <p>Accommodations</p>
    </div>

    <div>
      <h2><?= $pending_bookings ?></h2>
      <p>Pending Bookings</p>
    </div>

    <div>
      <h2><?= $occupants ?></h2>
      <p>Current Occupants</p>
    </div>
  </div>

  <div>
    <div>
      <h3>Add Accommodation</h3>
      <p>Register a new student residence under your account.</p>
      <a href="add_accommodation.html">Add Now</a>
    </div>

    <div>
      <h3>View Applications</h3>
      <p>Review student booking requests for your residences.</p>
      <a href="admins-booking.php">Manage Applications</a>
    </div>

    <div>
      <h3>View Occupants</h3>
      <p>See the current students staying in your residences.</p>
      <a href="admin-occupants.php">View Occupants</a>
    </div>
  </div>

</main>

<footer>
  <p>© 2025 STAY HUB. All rights reserved.</p>
</footer>

</body>
</html>