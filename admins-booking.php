<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

// Ensure only admins can view
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    die("Unauthorized access.");
}

$admin_id = $_SESSION["user_id"];

// Fetch all bookings for this admin
$sql = "
    SELECT b.BookingID, b.StartDate, b.EndDate, b.BookingDate, b.TotCost, 
           b.BkStatus, b.PaymentStatus,
           s.FirstName, s.LastName, s.StudNum, s.Email,
           a.Name AS AccommodationName,
           r.RmType
    FROM booking b
    JOIN student s ON b.StudNum = s.StudNum
    JOIN rooms r ON b.RmNum = r.RmNum
    JOIN accommodation a ON b.AccommodationID = a.AccommodationID
    WHERE a.AdminID = ?
    ORDER BY b.BookingDate DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$bookings = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Bookings</title>
</head>
<body>

<header>
  <div>STAY HUB</div>
  <nav>
    <a href="admin-panel.php">Dashboard</a>
    <a href="admin-occupants.php">Occupants</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>
  <h1>Manage Bookings</h1>

  <?php if ($bookings->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Student</th>
          <th>Accommodation</th>
          <th>Room Type</th>
          <th>Dates</th>
          <th>Total Cost</th>
          <th>Status</th>
          <th>Payment</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <?php while ($b = $bookings->fetch_assoc()): ?>
          <tr>
            <td>
              <?= htmlspecialchars($b["FirstName"] . " " . $b["LastName"]) ?>
              (<?= $b["StudNum"] ?>)
            </td>

            <td><?= htmlspecialchars($b["AccommodationName"]) ?></td>
            <td><?= htmlspecialchars($b["RmType"]) ?></td>

            <td>
              <?= htmlspecialchars($b["StartDate"]) ?> →
              <?= htmlspecialchars($b["EndDate"]) ?>
            </td>

            <td>R<?= number_format($b["TotCost"], 2) ?></td>
            <td><?= htmlspecialchars($b["BkStatus"]) ?></td>
            <td><?= htmlspecialchars($b["PaymentStatus"]) ?></td>

            <td>
              <?php if ($b["BkStatus"] === "Pending"): ?>

                <form action="update-booking-status.php" method="post">
                  <input type="hidden" name="booking_id" value="<?= $b['BookingID'] ?>">
                  <input type="hidden" name="action" value="approve">
                  <button type="submit">Approve</button>
                </form>

                <form action="update-booking-status.php" method="post">
                  <input type="hidden" name="booking_id" value="<?= $b['BookingID'] ?>">
                  <input type="hidden" name="action" value="reject">
                  <button type="submit">Reject</button>
                </form>

              <?php else: ?>
                <?= htmlspecialchars($b["BkStatus"]) ?>
              <?php endif; ?>
            </td>

          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

  <?php else: ?>
    <p>No bookings found for your accommodations.</p>
  <?php endif; ?>

</main>

</body>
</html>