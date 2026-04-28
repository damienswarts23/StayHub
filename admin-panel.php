<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    die("Unauthorized access.");
}

$mysqli = require __DIR__ . "/database.php";
$admin_id = $_SESSION["user_id"];

$sql = "SELECT FirstName, LastName FROM admin WHERE AdminID = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

$sql = "SELECT COUNT(*) FROM accommodation WHERE AdminID = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($total_accom);
$stmt->fetch();
$stmt->close();

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>

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

.top-actions {
  display: flex;
  gap: 12px;
}

.top-actions a {
  text-decoration: none;
  padding: 12px 18px;
  border-radius: 14px;
  background: #fff;
  color: #07111d;
  font-weight: 800;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
  margin-bottom: 22px;
}

.stat-card {
  padding: 24px;
  border-radius: 24px;
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.13);
  box-shadow: 0 18px 45px rgba(0,0,0,0.24);
}

.stat-card span {
  color: rgba(255,255,255,0.55);
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
}

.stat-card h2 {
  margin-top: 14px;
  font-size: 44px;
}

.stat-card p {
  color: rgba(255,255,255,0.55);
  margin-top: 8px;
}

.admin-panel {
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  gap: 22px;
}

.management {
  padding: 26px;
  border-radius: 28px;
  background: rgba(255,255,255,0.96);
  color: #07111d;
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

.action-list {
  display: grid;
  gap: 14px;
}

.action-card {
  text-decoration: none;
  color: #07111d;
  padding: 20px;
  border-radius: 20px;
  background: #f3f8fa;
  border: 1px solid #dce9ee;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: 0.25s;
}

.action-card:hover {
  transform: translateX(6px);
  background: #edf6f9;
}

.action-card h3 {
  margin-bottom: 6px;
}

.action-card p {
  color: #647482;
  font-size: 14px;
}

.arrow {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  background: #07111d;
  color: #fff;
  font-size: 22px;
}

.activity-panel {
  padding: 26px;
  border-radius: 28px;
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.13);
}

.activity-panel h2 {
  font-size: 24px;
  margin-bottom: 18px;
}

.activity-item {
  padding: 16px;
  border-radius: 18px;
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.1);
  margin-bottom: 12px;
}

.activity-item strong {
  display: block;
  margin-bottom: 5px;
}

.activity-item p {
  color: rgba(255,255,255,0.62);
  font-size: 14px;
}

footer {
  margin-top: 26px;
  color: rgba(255,255,255,0.55);
  text-align: center;
}

@media (max-width: 950px) {
  .admin-layout {
    grid-template-columns: 1fr;
  }

  .sidebar {
    border-right: none;
    border-bottom: 1px solid rgba(255,255,255,0.12);
  }

  .dashboard-grid,
  .admin-panel {
    grid-template-columns: 1fr;
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
      <small>Logged in as</small>
      <h3><?= htmlspecialchars($admin["FirstName"]) ?> <?= htmlspecialchars($admin["LastName"]) ?></h3>
    </div>

    <nav class="side-nav">
      <a class="active" href="admin-dashboard.php">Dashboard</a>
      <a href="add_accommodation.html">Add Accommodation</a>
      <a href="admins-booking.php">Applications</a>
      <a href="admin-occupants.php">Occupants</a>
      <a href="admins-profile.php">Profile</a>
      <a class="logout-link" href="logout.php">Logout</a>
    </nav>
  </aside>

  <main class="main-area">

    <section class="topbar">
      <div>
        <h1>Admin Dashboard</h1>
        <p>Welcome back, <?= htmlspecialchars($admin["FirstName"]) ?>. Manage your StayHub operations here.</p>
      </div>

      <div class="top-actions">
        <a href="add_accommodation.html">+ Add Accommodation</a>
      </div>
    </section>

    <section class="dashboard-grid">
      <div class="stat-card">
        <span>Total Properties</span>
        <h2><?= $total_accom ?></h2>
        <p>Accommodations under your account</p>
      </div>

      <div class="stat-card">
        <span>Needs Review</span>
        <h2><?= $pending_bookings ?></h2>
        <p>Pending student booking requests</p>
      </div>

      <div class="stat-card">
        <span>Active Occupants</span>
        <h2><?= $occupants ?></h2>
        <p>Confirmed students currently listed</p>
      </div>
    </section>

    <section class="admin-panel">

      <div class="management">
        <div class="panel-title">
          <small>Management Center</small>
          <h2>Admin Controls</h2>
        </div>

        <div class="action-list">
          <a class="action-card" href="add_accommodation.html">
            <div>
              <h3>Add Accommodation</h3>
              <p>Create and register a new property listing.</p>
            </div>
            <span class="arrow">→</span>
          </a>

          <a class="action-card" href="admins-booking.php">
            <div>
              <h3>Review Applications</h3>
              <p>Approve or manage student booking requests.</p>
            </div>
            <span class="arrow">→</span>
          </a>

          <a class="action-card" href="admin-occupants.php">
            <div>
              <h3>Current Occupants</h3>
              <p>View confirmed students staying in your residences.</p>
            </div>
            <span class="arrow">→</span>
          </a>
        </div>
      </div>

      <aside class="activity-panel">
        <h2>Admin Overview</h2>

        <div class="activity-item">
          <strong><?= $pending_bookings ?> pending bookings</strong>
          <p>Applications waiting for admin review.</p>
        </div>

        <div class="activity-item">
          <strong><?= $total_accom ?> accommodations</strong>
          <p>Properties currently assigned to your account.</p>
        </div>

        <div class="activity-item">
          <strong><?= $occupants ?> confirmed occupants</strong>
          <p>Students with confirmed accommodation bookings.</p>
        </div>
      </aside>

    </section>

    <footer>
      <p>© 2025 STAY HUB. All rights reserved.</p>
    </footer>

  </main>

</div>

</body>
</html>