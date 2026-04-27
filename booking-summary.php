<?php
session_start();

if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "student") {
    $mysqli = require __DIR__ . "/database.php";

    $sql = "SELECT b.BookingID, b.RmNum, b.StartDate, b.EndDate, b.TotCost, 
               b.PaymentStatus, b.BkStatus,
               a.Name AS AccommodationName,
               r.RmType
        FROM booking b
        JOIN accommodation a ON b.AccommodationID = a.AccommodationID
        JOIN rooms r ON b.RmNum = r.RmNum
        WHERE b.StudNum = ?
        ORDER BY b.BookingDate DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $bookings = $stmt->get_result();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Booking Summary</title>
</head>
<body>
  <header>
    <div>
      <a href="homepage.php">
        <img src="Logo_enhanced.png" alt="stay hub logo">
        STAY HUB
      </a>
    </div>
    <nav>
    </nav>
  </header>

  <main>
    <h1>Your Bookings</h1>

    <div>
      <a href="profile.php">Back to Profile</a>
    </div>

    <?php if ($bookings && $bookings->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>Accommodation</th>
            <th>Room Type</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Total Cost</th>
            <th>Status</th>
            <th>Payment</th>
            <th>Cancel</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($b = $bookings->fetch_assoc()): ?>
            <?php $startDate = strtotime($b["StartDate"]); ?>
            <tr>
              <td><?= htmlspecialchars($b["AccommodationName"]) ?></td>
              <td><?= htmlspecialchars($b["RmType"]) ?></td>
              <td><?= htmlspecialchars($b["StartDate"]) ?></td>
              <td><?= htmlspecialchars($b["EndDate"]) ?></td>
              <td>R<?= number_format($b["TotCost"], 2) ?></td>
              <td><?= htmlspecialchars($b["BkStatus"]) ?></td>
              <td>
                <?php if ($b["PaymentStatus"] === "Pending Payment"): ?>
                  <a href="payment.php?booking_id=<?= $b['BookingID'] ?>">Make Payment</a>
                <?php elseif ($b["PaymentStatus"] === "Bursary Funded"): ?>
                  <span>Covered by Bursary</span>
                <?php elseif ($b["PaymentStatus"] === "Paid"): ?>
                  <span>Paid</span>
                <?php else: ?>
                  <span><?= htmlspecialchars($b["PaymentStatus"]) ?></span>
                <?php endif; ?>
              </td>
              <td>
                <?php if (
                    ($b["BkStatus"] === "Pending" || $b["BkStatus"] === "Confirmed") &&
                    $b["PaymentStatus"] !== "Paid" &&
                    $startDate > time()
                ): ?>
                  <form method="post" action="cancel-booking.php" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                    <input type="hidden" name="booking_id" value="<?= $b['BookingID'] ?>">
                    <input type="hidden" name="room_id" value="<?= $b['RmNum'] ?>">
                    <button type="submit">Cancel</button>
                  </form>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No bookings yet.</p>
    <?php endif; ?>
  </main>

  <footer>
    <p>© 2025 STAY HUB. All rights reserved.</p>
  </footer>
</body>
</html>