<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

// Ensure admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    die("Unauthorized access.");
}

$admin_id = $_SESSION["user_id"];

// Fetch admin info
$sql = "SELECT * FROM admin WHERE AdminID = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Fetch accommodations owned by this admin
$sql = "SELECT * FROM accommodation WHERE AdminID = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$accommodations = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Profile</title>

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

.back-btn {
  text-decoration: none;
  padding: 12px 18px;
  border-radius: 14px;
  background: #fff;
  color: #07111d;
  font-weight: 800;
}

.profile-grid {
  display: grid;
  grid-template-columns: 0.9fr 1.1fr;
  gap: 22px;
}

.profile-card,
.accommodation-card {
  border-radius: 28px;
  padding: 26px;
}

.profile-card {
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.13);
}

.accommodation-card {
  background: rgba(255,255,255,0.96);
  color: #07111d;
}

.panel-title {
  margin-bottom: 22px;
}

.panel-title small {
  color: rgba(255,255,255,0.55);
  text-transform: uppercase;
  font-weight: 800;
  letter-spacing: 1px;
}

.accommodation-card .panel-title small {
  color: #647482;
}

.panel-title h2 {
  font-size: 28px;
  margin-top: 6px;
}

.info-list {
  display: grid;
  gap: 14px;
}

.info-item {
  padding: 16px;
  border-radius: 18px;
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.1);
}

.info-item strong {
  display: block;
  margin-bottom: 6px;
  color: rgba(255,255,255,0.58);
  font-size: 13px;
  text-transform: uppercase;
}

.accom-list {
  display: grid;
  gap: 14px;
}

.accom-item {
  padding: 18px;
  border-radius: 20px;
  background: #f3f8fa;
  border: 1px solid #dce9ee;
}

.accom-item strong {
  display: block;
  margin-bottom: 6px;
  font-size: 17px;
}

.accom-item span {
  color: #647482;
}

.empty-box {
  padding: 20px;
  border-radius: 20px;
  background: #f3f8fa;
  border: 1px solid #dce9ee;
}

.empty-box a {
  color: #07111d;
  font-weight: 800;
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

  .profile-grid {
    grid-template-columns: 1fr;
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
      <a href="admin-panel.php">Dashboard</a>
      <a href="add_accommodation.html">Add Accommodation</a>
      <a href="admins-booking.php">Bookings</a>
      <a href="admin-occupants.php">Occupants</a>
      <a class="active" href="admins-profile.php">Profile</a>
      <a class="logout-link" href="logout.php">Logout</a>
    </nav>
  </aside>

  <main class="main-area">

    <section class="topbar">
      <div>
        <h1>Welcome, <?= htmlspecialchars($admin["FirstName"]) ?></h1>
        <p>Manage your profile information and registered accommodations.</p>
      </div>

      <a class="back-btn" href="admin-panel.php">Back to admin panel</a>
    </section>

    <section class="profile-grid">

      <div class="profile-card">
        <div class="panel-title">
          <small>Admin Account</small>
          <h2>Your Info</h2>
        </div>

        <div class="info-list">
          <div class="info-item">
            <strong>ID Number</strong>
            <?= htmlspecialchars($admin["IDNum"]) ?>
          </div>

          <div class="info-item">
            <strong>Name</strong>
            <?= htmlspecialchars($admin["FirstName"] . " " . $admin["LastName"]) ?>
          </div>

          <div class="info-item">
            <strong>Email</strong>
            <?= htmlspecialchars($admin["Email"]) ?>
          </div>

          <div class="info-item">
            <strong>Cell</strong>
            <?= htmlspecialchars($admin["CellNumber"]) ?>
          </div>
        </div>
      </div>

      <div class="accommodation-card">
        <div class="panel-title">
          <small>Property Management</small>
          <h2>Your Accommodations</h2>
        </div>

        <?php if ($accommodations->num_rows > 0): ?>
          <div class="accom-list">
            <?php while ($a = $accommodations->fetch_assoc()): ?>
              <div class="accom-item">
                <strong><?= htmlspecialchars($a["Name"]) ?></strong>
                <span><?= htmlspecialchars($a["Address"]) ?></span>
              </div>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <div class="empty-box">
            <p>No accommodations registered yet. <a href="add_accommodation.html">Add one here</a>.</p>
          </div>
        <?php endif; ?>
      </div>

    </section>

  </main>

</div>

</body>
</html>