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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Summary</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Outfit", sans-serif;
}

body {
  min-height: 100vh;
  color: #fff;
  background:
    radial-gradient(circle at top left, rgba(0, 217, 255, 0.25), transparent 35%),
    linear-gradient(rgba(2, 14, 24, 0.72), rgba(2, 14, 24, 0.96)),
    url("./images/Background/Background 8.jpg");
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
}

body::before {
  content: "";
  position: fixed;
  inset: 0;
  background-image:
    linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
  background-size: 70px 70px;
  pointer-events: none;
  z-index: -1;
}

header {
  width: min(1180px, 92%);
  margin: 28px auto;
  padding: 14px 18px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border: 1px solid rgba(255,255,255,0.22);
  border-radius: 50px;
  background: rgba(255,255,255,0.08);
  backdrop-filter: blur(18px);
  box-shadow: 0 20px 60px rgba(0,0,0,0.35);
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  color: #fff;
  font-weight: 800;
  letter-spacing: 1px;
}

.logo img {
  width: 46px;
  height: 46px;
  object-fit: contain;
}

nav a {
  color: #fff;
  text-decoration: none;
  padding: 11px 24px;
  border-radius: 35px;
  border: 1px solid rgba(255,255,255,0.25);
  background: rgba(255,255,255,0.06);
  transition: 0.3s ease;
  font-size: 14px;
}

nav a:hover {
  background: #fff;
  color: #071722;
}

main {
  width: min(1180px, 92%);
  margin: 30px auto 70px;
}

.hero {
  min-height: 360px;
  border-radius: 42px;
  padding: 42px;
  margin-bottom: 28px;
  background:
    linear-gradient(120deg, rgba(0,0,0,0.58), rgba(0,0,0,0.15)),
    url("./images/Main/Main 3.jpg");
  background-size: cover;
  background-position: center;
  border: 1px solid rgba(255,255,255,0.22);
  box-shadow: 0 30px 80px rgba(0,0,0,0.45);
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: flex-end;
}

.hero::after {
  content: "";
  position: absolute;
  width: 420px;
  height: 420px;
  border: 1px solid rgba(255,255,255,0.18);
  border-radius: 50%;
  left: -120px;
  top: 65px;
}

.hero-content {
  position: relative;
  z-index: 2;
  max-width: 700px;
}

.eyebrow {
  display: inline-block;
  padding: 10px 18px;
  margin-bottom: 16px;
  border-radius: 30px;
  background: rgba(255,255,255,0.14);
  border: 1px solid rgba(255,255,255,0.28);
  backdrop-filter: blur(14px);
  font-size: 13px;
}

.hero h1 {
  font-size: clamp(38px, 5vw, 68px);
  line-height: 0.95;
  margin-bottom: 18px;
  letter-spacing: -2px;
}

.hero p {
  max-width: 620px;
  font-size: 16px;
  line-height: 1.8;
  color: rgba(255,255,255,0.82);
}

.content {
  border-radius: 38px;
  padding: 34px;
  background: rgba(255,255,255,0.96);
  color: #071722;
  box-shadow: 0 30px 80px rgba(0,0,0,0.32);
  position: relative;
  overflow: hidden;
}

.content::before {
  content: "";
  position: absolute;
  right: -80px;
  top: -80px;
  width: 240px;
  height: 240px;
  background: rgba(35,177,210,0.18);
  border-radius: 50%;
}

.section-head {
  position: relative;
  z-index: 2;
  margin-bottom: 25px;
}

.section-head span {
  font-size: 13px;
  color: #5d7b8b;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.5px;
}

.section-head h2 {
  font-size: 34px;
  margin-top: 6px;
}

.section-head p {
  color: #647482;
  margin-top: 8px;
}

.table-wrap {
  position: relative;
  z-index: 2;
  padding: 8px;
  border-radius: 26px;
  background: #f3f8fa;
  border: 1px solid #dce9ee;
  overflow-x: auto;
}

table {
  width: 100%;
  min-width: 980px;
  border-collapse: collapse;
}

th {
  text-align: left;
  padding: 16px;
  color: #668392;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

td {
  padding: 18px 16px;
  border-top: 1px solid #dce9ee;
  font-weight: 700;
}

tr:hover td {
  background: #eef6fa;
}

.badge {
  display: inline-block;
  padding: 8px 14px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 800;
  background: #e8f7fb;
  color: #087990;
}

.badge.paid {
  background: #e9fbf3;
  color: #0c8a55;
}

.badge.pending {
  background: #fff4df;
  color: #b66b00;
}

.badge.cancelled {
  background: #ffe8e8;
  color: #b42318;
}

.pay-link {
  display: inline-block;
  text-decoration: none;
  padding: 9px 15px;
  border-radius: 999px;
  background: #071722;
  color: white;
  font-size: 13px;
  font-weight: 800;
}

.cancel-btn {
  border: none;
  padding: 9px 15px;
  border-radius: 999px;
  background: #ffe8e8;
  color: #b42318;
  font-weight: 800;
  cursor: pointer;
}

.empty {
  position: relative;
  z-index: 2;
  text-align: center;
  padding: 45px;
  border-radius: 24px;
  background: #eef6fa;
}

.empty p {
  color: #647482;
  margin-top: 8px;
}

footer {
  text-align: center;
  padding: 30px;
  color: rgba(255,255,255,0.62);
}

@media (max-width: 800px) {
  header {
    flex-direction: column;
    border-radius: 30px;
    gap: 15px;
  }

  .hero,
  .content {
    padding: 28px;
  }
}
</style>
</head>

<body>

<header>
  <a class="logo" href="homepage.php">
    <img src="Logo_enhanced.png" alt="stay hub logo">
    <span>STAY HUB</span>
  </a>

  <nav>
    <a href="profile.php">Profile</a>
  </nav>
</header>

<main>

  <section class="hero">
    <div class="hero-content">
      <span class="eyebrow">Booking Summary</span>
      <h1>Your bookings, beautifully organized.</h1>
      <p>
        View your StayHub accommodation bookings, room details, payment status,
        and upcoming stay information in one simple dashboard.
      </p>
    </div>
  </section>

  <section class="content">
    <div class="section-head">
      <span>Booking History</span>
      <h2>Your Bookings</h2>
      <p>Manage your bookings and check payment progress.</p>
    </div>

    <?php if (isset($bookings) && $bookings && $bookings->num_rows > 0): ?>
      <div class="table-wrap">
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
                <td class="amount">R<?= number_format($b["TotCost"], 2) ?></td>

                <td>
                  <?php
                    $statusClass = strtolower($b["BkStatus"]);
                    if ($statusClass === "confirmed") {
                        $statusClass = "paid";
                    } elseif ($statusClass === "pending") {
                        $statusClass = "pending";
                    } elseif ($statusClass === "cancelled") {
                        $statusClass = "cancelled";
                    } else {
                        $statusClass = "";
                    }
                  ?>
                  <span class="badge <?= $statusClass ?>">
                    <?= htmlspecialchars($b["BkStatus"]) ?>
                  </span>
                </td>

                <td>
                  <?php if ($b["PaymentStatus"] === "Pending Payment"): ?>
                    <a class="pay-link" href="payment.php?booking_id=<?= $b['BookingID'] ?>">Make Payment</a>
                  <?php elseif ($b["PaymentStatus"] === "Bursary Funded"): ?>
                    <span class="badge">Covered by Bursary</span>
                  <?php elseif ($b["PaymentStatus"] === "Paid"): ?>
                    <span class="badge paid">Paid</span>
                  <?php else: ?>
                    <span class="badge"><?= htmlspecialchars($b["PaymentStatus"]) ?></span>
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
                      <button class="cancel-btn" type="submit">Cancel</button>
                    </form>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty">
        <h2>No bookings yet</h2>
        <p>Your booking history will appear here once you book accommodation.</p>
      </div>
    <?php endif; ?>
  </section>

</main>

<footer>
  <p>© 2025 STAY HUB. All rights reserved.</p>
</footer>

</body>
</html>