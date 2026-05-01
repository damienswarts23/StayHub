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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Manage Bookings</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Inter", sans-serif;
}

body {
  min-height: 100vh;
  background: #07111d;
  color: #fff;
}

.admin-layout {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 280px 1fr;
}

.sidebar {
  padding: 26px;
  background: rgba(255,255,255,0.06);
  border-right: 1px solid rgba(255,255,255,0.12);
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
  color: #fff;
  text-decoration: none;
  font-weight: 800;
  letter-spacing: 1px;
  margin-bottom: 38px;
}

.logo img {
  width: 42px;
  height: 42px;
  object-fit: contain;
}

.admin-box {
  padding: 18px;
  border-radius: 22px;
  background: linear-gradient(135deg, rgba(0,217,255,0.15), rgba(255,255,255,0.06));
  border: 1px solid rgba(255,255,255,0.14);
  margin-bottom: 28px;
}

.admin-box small {
  color: rgba(255,255,255,0.55);
}

.admin-box h3 {
  margin-top: 6px;
  font-size: 18px;
}

.side-nav {
  display: grid;
  gap: 10px;
}

.side-nav a {
  text-decoration: none;
  color: rgba(255,255,255,0.75);
  padding: 14px 16px;
  border-radius: 16px;
  transition: 0.25s;
  font-weight: 600;
}

.side-nav a:hover,
.side-nav a.active {
  background: rgba(255,255,255,0.1);
  color: #fff;
}

.logout-link {
  margin-top: 24px;
  color: #ffb4b4 !important;
}

.main-area {
  padding: 30px;
  background:
    radial-gradient(circle at top right, rgba(0,217,255,0.18), transparent 32%),
    linear-gradient(135deg, #07111d, #102b38);
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 26px;
}

.topbar h1 {
  font-size: 32px;
  letter-spacing: -1px;
}

.topbar p {
  color: rgba(255,255,255,0.62);
  margin-top: 6px;
}

.table-panel {
  padding: 26px;
  border-radius: 28px;
  background: rgba(255,255,255,0.96);
  color: #07111d;
  overflow-x: auto;
}

.panel-title {
  margin-bottom: 22px;
}

.panel-title small {
  color: #647482;
  text-transform: uppercase;
  font-weight: 800;
  letter-spacing: 1px;
}

.panel-title h2 {
  font-size: 28px;
  margin-top: 6px;
}

table {
  width: 100%;
  min-width: 1050px;
  border-collapse: collapse;
}

th {
  text-align: left;
  padding: 15px;
  color: #647482;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 1px;
  border-bottom: 1px solid #dce9ee;
}

td {
  padding: 16px 15px;
  border-bottom: 1px solid #e8eef2;
  font-weight: 600;
  vertical-align: middle;
}

tr:hover td {
  background: #f3f8fa;
}

.badge {
  display: inline-block;
  padding: 8px 13px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 800;
  background: #e8f7fb;
  color: #087990;
}

.badge.pending {
  background: #fff4df;
  color: #b66b00;
}

.badge.confirmed {
  background: #e9fbf3;
  color: #0c8a55;
}

.badge.rejected {
  background: #ffe8e8;
  color: #b42318;
}

.amount {
  color: #087990;
  font-weight: 900;
}

.action-forms {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.action-forms form {
  display: inline-block;
}

.approve-btn,
.reject-btn {
  border: none;
  padding: 9px 14px;
  border-radius: 999px;
  font-weight: 800;
  cursor: pointer;
}

.approve-btn {
  background: #e9fbf3;
  color: #0c8a55;
}

.reject-btn {
  background: #ffe8e8;
  color: #b42318;
}

.empty-box {
  padding: 24px;
  border-radius: 20px;
  background: #f3f8fa;
  border: 1px solid #dce9ee;
  color: #647482;
}

@media (max-width: 950px) {
  .admin-layout {
    grid-template-columns: 1fr;
  }

  .sidebar {
    border-right: none;
    border-bottom: 1px solid rgba(255,255,255,0.12);
  }

  .topbar {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
}
</style>
</head>

<body>

<div class="admin-layout">

  <aside class="sidebar">
    <a class="logo" href="index.html">
      <img src="logo_enhanced.png" alt="stay hub logo">
      <span>STAY HUB</span>
    </a>

    <div class="admin-box">
      <small>Admin Area</small>
      <h3>Booking Manager</h3>
    </div>

    <nav class="side-nav">
      <a href="admin-panel.php">Dashboard</a>
      <a href="add_accommodation.html">Add Accommodation</a>
      <a class="active" href="admins-booking.php">Bookings</a>
      <a href="admin-occupants.php">Occupants</a>
      <a href="admins-profile.php">Profile</a>
      <a class="logout-link" href="logout.php">Logout</a>
    </nav>
  </aside>

  <main class="main-area">

    <section class="topbar">
      <div>
        <h1>Manage Bookings</h1>
        <p>Review student booking applications and approve or reject pending requests.</p>
      </div>
    </section>

    <section class="table-panel">
      <div class="panel-title">
        <small>Applications</small>
        <h2>Booking Requests</h2>
      </div>

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
                  <br>
                  <span style="color:#647482; font-size:13px;">
                    <?= htmlspecialchars($b["StudNum"]) ?>
                  </span>
                </td>

                <td><?= htmlspecialchars($b["AccommodationName"]) ?></td>
                <td><?= htmlspecialchars($b["RmType"]) ?></td>

                <td>
                  <?= htmlspecialchars($b["StartDate"]) ?> →
                  <?= htmlspecialchars($b["EndDate"]) ?>
                </td>

                <td class="amount">R<?= number_format($b["TotCost"], 2) ?></td>

                <td>
                  <?php
                    $statusClass = strtolower($b["BkStatus"]);
                  ?>
                  <span class="badge <?= htmlspecialchars($statusClass) ?>">
                    <?= htmlspecialchars($b["BkStatus"]) ?>
                  </span>
                </td>

                <td>
                  <span class="badge">
                    <?= htmlspecialchars($b["PaymentStatus"]) ?>
                  </span>
                </td>

                <td>
                  <?php if ($b["BkStatus"] === "Pending"): ?>

                    <div class="action-forms">
                      <form action="update-booking-status.php" method="post">
                        <input type="hidden" name="booking_id" value="<?= $b['BookingID'] ?>">
                        <input type="hidden" name="action" value="approve">
                        <button class="approve-btn" type="submit">Approve</button>
                      </form>

                      <form action="update-booking-status.php" method="post">
                        <input type="hidden" name="booking_id" value="<?= $b['BookingID'] ?>">
                        <input type="hidden" name="action" value="reject">
                        <button class="reject-btn" type="submit">Reject</button>
                      </form>
                    </div>

                  <?php else: ?>
                    <span class="badge <?= htmlspecialchars($statusClass) ?>">
                      <?= htmlspecialchars($b["BkStatus"]) ?>
                    </span>
                  <?php endif; ?>
                </td>

              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

      <?php else: ?>
        <div class="empty-box">
          No bookings found for your accommodations.
        </div>
      <?php endif; ?>

    </section>

  </main>

</div>

</body>
</html>