<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

$studNum = $_SESSION["user_id"];

$sql = "SELECT p.PaymentID, p.PaymentDate, p.PayMethod, p.AmtPaid,
               b.BookingID, a.Name AS AccommodationName
        FROM payment p
        JOIN booking b ON p.BookingID = b.BookingID
        JOIN accommodation a ON b.AccommodationID = a.AccommodationID
        WHERE p.StudNum = ?
        ORDER BY p.PaymentDate DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $studNum);
$stmt->execute();
$payments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payments</title>

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
  overflow-x: hidden;
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
  color: #fff;
  font-weight: 800;
  letter-spacing: 1px;
}

nav {
  display: flex;
  gap: 12px;
  align-items: center;
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

nav a:hover,
nav a.active {
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
  background: rgba(35, 177, 210, 0.18);
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
  letter-spacing: -1px;
}

.section-head p {
  color: #647482;
  margin-top: 8px;
}

.card {
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
  min-width: 720px;
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

.method {
  display: inline-block;
  padding: 8px 14px;
  border-radius: 999px;
  background: #e8f7fb;
  color: #087990;
  font-size: 13px;
  font-weight: 800;
}

.amount {
  color: #087990;
  font-weight: 900;
}

.empty {
  text-align: center;
  padding: 45px;
  border-radius: 24px;
  background: #eef6fa;
}

.empty h2 {
  margin-bottom: 8px;
}

.empty p {
  color: #647482;
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

  nav {
    flex-wrap: wrap;
    justify-content: center;
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
  <div class="logo">STAY HUB</div>

  <nav>
    <a href="homepage.php">Home</a>
    <a href="profile.php">Profile</a>
    <a href="payment-summary.php" class="active">Payments</a>
  </nav>
</header>

<main>

  <section class="hero">
    <div class="hero-content">
      <span class="eyebrow">Payment Summary</span>
      <h1>Your payments, all in one place.</h1>
      <p>
        Track your StayHub accommodation payments, payment methods, dates,
        and booking records in a clean and simple dashboard.
      </p>
    </div>
  </section>

  <section class="content">
    <div class="section-head">
      <span>Payment History</span>
      <h2>Recent Payments</h2>
      <p>View all payments connected to your accommodation bookings.</p>
    </div>

    <div class="card">
      <?php if ($payments->num_rows > 0): ?>
        <table>
          <thead>
            <tr>
              <th>Accommodation</th>
              <th>Method</th>
              <th>Date</th>
              <th>Amount</th>
            </tr>
          </thead>

          <tbody>
            <?php while ($p = $payments->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($p["AccommodationName"]) ?></td>
              <td><span class="method"><?= htmlspecialchars($p["PayMethod"]) ?></span></td>
              <td><?= htmlspecialchars($p["PaymentDate"]) ?></td>
              <td class="amount">R<?= number_format($p["AmtPaid"], 2) ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="empty">
          <h2>No payments yet</h2>
          <p>Your payment history will appear here once you make a payment.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

</main>

<footer>
  <p>© 2025 STAY HUB. All rights reserved.</p>
</footer>

</body>
</html>