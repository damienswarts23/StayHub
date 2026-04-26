<?php
session_start();

if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "student") {
    $mysqli = require __DIR__ . "/database.php";
    
    $sql = "SELECT * FROM student WHERE StudNum = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StayHub Profile</title>

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

    nav a:hover {
      background: #fff;
      color: #071722;
    }

    main {
      width: min(1180px, 92%);
      margin: 30px auto 70px;
    }

    .dashboard {
      display: grid;
      grid-template-columns: 1.5fr 0.8fr;
      gap: 28px;
    }

    .hero-card {
      min-height: 440px;
      border-radius: 42px;
      padding: 42px;
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
      flex-direction: column;
      justify-content: space-between;
    }

    .hero-card::after {
      content: "";
      position: absolute;
      width: 420px;
      height: 420px;
      border: 1px solid rgba(255,255,255,0.18);
      border-radius: 50%;
      left: -120px;
      top: 65px;
    }

    .pill {
      width: fit-content;
      padding: 10px 18px;
      border-radius: 30px;
      background: rgba(255,255,255,0.14);
      border: 1px solid rgba(255,255,255,0.28);
      backdrop-filter: blur(14px);
      font-size: 13px;
      position: relative;
      z-index: 2;
    }

    .hero-content {
      max-width: 640px;
      position: relative;
      z-index: 2;
    }

    .hero-content h1 {
      font-size: clamp(38px, 5vw, 68px);
      line-height: 0.95;
      margin-bottom: 18px;
      letter-spacing: -2px;
    }

    .hero-content p {
      max-width: 560px;
      font-size: 16px;
      line-height: 1.8;
      color: rgba(255,255,255,0.82);
    }

    .hero-actions {
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
      margin-top: 28px;
    }

    .main-btn,
    .ghost-btn {
      text-decoration: none;
      padding: 14px 25px;
      border-radius: 35px;
      font-weight: 700;
      transition: 0.3s ease;
    }

    .main-btn {
      background: #fff;
      color: #071722;
    }

    .ghost-btn {
      color: #fff;
      border: 1px solid rgba(255,255,255,0.35);
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(12px);
    }

    .main-btn:hover,
    .ghost-btn:hover {
      transform: translateY(-4px);
    }

    .side-profile {
      border-radius: 42px;
      padding: 28px;
      background:
        linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.75)),
        url("./images/Side/Side 1.jpg");
      background-size: cover;
      background-position: center;
      border: 1px solid rgba(255,255,255,0.24);
      backdrop-filter: blur(20px);
      box-shadow: 0 30px 80px rgba(0,0,0,0.35);
    }

    .avatar {
      width: 110px;
      height: 110px;
      border-radius: 35px;
      display: grid;
      place-items: center;
      background: rgba(255,255,255,0.18);
      border: 1px solid rgba(255,255,255,0.3);
      backdrop-filter: blur(14px);
      font-size: 42px;
      font-weight: 800;
      margin-bottom: 22px;
    }

    .side-profile h2 {
      font-size: 28px;
      margin-bottom: 8px;
    }

    .side-profile p {
      color: rgba(255,255,255,0.78);
      line-height: 1.6;
      margin-bottom: 24px;
    }

    .mini-info {
      display: grid;
      gap: 12px;
    }

    .mini-info div {
      padding: 14px;
      border-radius: 20px;
      background: rgba(255,255,255,0.14);
      border: 1px solid rgba(255,255,255,0.18);
      backdrop-filter: blur(12px);
    }

    .mini-info strong {
      display: block;
      font-size: 12px;
      color: rgba(255,255,255,0.62);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 4px;
    }

    .content-grid {
      display: grid;
      grid-template-columns: 1fr 0.8fr;
      gap: 28px;
      margin-top: 28px;
    }

    .info-panel,
    .quick-panel {
      border-radius: 38px;
      padding: 34px;
      box-shadow: 0 30px 80px rgba(0,0,0,0.32);
      position: relative;
      overflow: hidden;
    }

    .info-panel {
      background: rgba(255,255,255,0.96);
      color: #071722;
    }

    .info-panel::before {
      content: "";
      position: absolute;
      right: -80px;
      top: -80px;
      width: 240px;
      height: 240px;
      background: rgba(35, 177, 210, 0.18);
      border-radius: 50%;
    }

    .quick-panel {
      background:
        linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.7)),
        url("./images/Side/Side 2.jpg");
      background-size: cover;
      background-position: center;
      color: #fff;
      border: 1px solid rgba(255,255,255,0.22);
      backdrop-filter: blur(18px);
    }

    .section-heading {
      position: relative;
      z-index: 2;
      margin-bottom: 25px;
    }

    .section-heading span {
      font-size: 13px;
      color: #5d7b8b;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.5px;
    }

    .quick-panel .section-heading span {
      color: rgba(255,255,255,0.6);
    }

    .section-heading h2 {
      font-size: 34px;
      margin-top: 6px;
      letter-spacing: -1px;
    }

    .info-list {
      position: relative;
      z-index: 2;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
    }

    .info-item {
      padding: 18px;
      border-radius: 22px;
      background: #f3f8fa;
      border: 1px solid #dce9ee;
    }

    .info-item strong {
      display: block;
      font-size: 12px;
      color: #668392;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 8px;
    }

    .info-item span {
      font-size: 17px;
      font-weight: 700;
      word-break: break-word;
    }

    .quick-actions {
      display: grid;
      gap: 16px;
      position: relative;
      z-index: 2;
    }

    .quick-card {
      text-decoration: none;
      color: #fff;
      padding: 22px;
      border-radius: 26px;
      background: rgba(255,255,255,0.14);
      border: 1px solid rgba(255,255,255,0.22);
      backdrop-filter: blur(12px);
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: 0.3s ease;
    }

    .quick-card:hover {
      transform: translateX(8px);
      background: rgba(255,255,255,0.22);
    }

    .quick-card div h3 {
      margin-bottom: 5px;
      font-size: 18px;
    }

    .quick-card div p {
      font-size: 13px;
      color: rgba(255,255,255,0.72);
    }

    .arrow {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      display: grid;
      place-items: center;
      background: rgba(255,255,255,0.18);
      font-size: 22px;
    }

    .logout {
      background: rgba(255, 75, 75, 0.23);
    }

    .image-strip {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 18px;
      margin-top: 28px;
    }

    .stay-card {
      height: 190px;
      border-radius: 28px;
      overflow: hidden;
      position: relative;
      border: 1px solid rgba(255,255,255,0.22);
      box-shadow: 0 20px 50px rgba(0,0,0,0.28);
    }

    .stay-card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .stay-card::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(transparent, rgba(0,0,0,0.75));
    }

    .stay-card span {
      position: absolute;
      left: 18px;
      bottom: 18px;
      z-index: 2;
      font-weight: 700;
    }

    .not-logged {
      padding: 50px;
      border-radius: 35px;
      background: rgba(255,255,255,0.12);
      backdrop-filter: blur(18px);
      border: 1px solid rgba(255,255,255,0.25);
      text-align: center;
    }

    footer {
      text-align: center;
      padding: 30px;
      color: rgba(255,255,255,0.62);
    }

    @media (max-width: 950px) {
      .dashboard,
      .content-grid {
        grid-template-columns: 1fr;
      }

      .info-list,
      .image-strip {
        grid-template-columns: 1fr;
      }

      header {
        flex-direction: column;
        border-radius: 30px;
        gap: 15px;
      }

      nav {
        flex-wrap: wrap;
        justify-content: center;
      }

      .hero-card {
        min-height: 520px;
        padding: 30px;
      }
    }
  </style>
</head>

<body>

<header>
  <a class="logo" href="homepage.php">
    <img src="Logo_enhanced.png" alt="StayHub Logo">
    <span>STAY HUB</span>
  </a>

  <nav>
    <a href="homepage.php">Home</a>
    <a href="booking-summary.php">Bookings</a>
    <a href="payment-summary.php">Payments</a>
  </nav>
</header>

<main>
<?php if (isset($user)): ?>

  <section class="dashboard">

    <div class="hero-card">
      <span class="pill">Luxury StayHub Profile</span>

      <div class="hero-content">
        <h1>Hello, <?= htmlspecialchars($user["FirstName"]) ?></h1>
        <p>
          Your personal space for managing accommodation, viewing bookings,
          checking payments, and keeping your StayHub journey smooth and organized.
        </p>

        <div class="hero-actions">
          <a class="main-btn" href="booking-summary.php">View Bookings</a>
          <a class="ghost-btn" href="payment-summary.php">View Payments</a>
        </div>
      </div>
    </div>

    <aside class="side-profile">
      <div class="avatar">
        <?= strtoupper(substr($user["FirstName"], 0, 1)) ?>
      </div>

      <h2><?= htmlspecialchars($user["FirstName"]) ?> <?= htmlspecialchars($user["LastName"]) ?></h2>
      <p>Student guest profile connected to your StayHub account.</p>

      <div class="mini-info">
        <div>
          <strong>Student Number</strong>
          <?= htmlspecialchars($user["StudNum"]) ?>
        </div>
        <div>
          <strong>Email</strong>
          <?= htmlspecialchars($user["Email"]) ?>
        </div>
        <div>
          <strong>Enrollment Year</strong>
          <?= htmlspecialchars($user["EnrollYr"]) ?>
        </div>
      </div>
    </aside>

  </section>

  <section class="content-grid">

    <div class="info-panel">
      <div class="section-heading">
        <span>Personal Details</span>
        <h2>Your Information</h2>
      </div>

      <div class="info-list">
        <div class="info-item">
          <strong>ID Number</strong>
          <span><?= htmlspecialchars($user["IDNum"]) ?></span>
        </div>

        <div class="info-item">
          <strong>First Name</strong>
          <span><?= htmlspecialchars($user["FirstName"]) ?></span>
        </div>

        <div class="info-item">
          <strong>Last Name</strong>
          <span><?= htmlspecialchars($user["LastName"]) ?></span>
        </div>

        <div class="info-item">
          <strong>Student Number</strong>
          <span><?= htmlspecialchars($user["StudNum"]) ?></span>
        </div>

        <div class="info-item">
          <strong>Email Address</strong>
          <span><?= htmlspecialchars($user["Email"]) ?></span>
        </div>

        <div class="info-item">
          <strong>Cell Number</strong>
          <span><?= htmlspecialchars($user["CellNumr"]) ?></span>
        </div>

        <div class="info-item">
          <strong>Enrollment Year</strong>
          <span><?= htmlspecialchars($user["EnrollYr"]) ?></span>
        </div>
      </div>
    </div>

    <div class="quick-panel">
      <div class="section-heading">
        <span>Quick Access</span>
        <h2>Manage Stay</h2>
      </div>

      <div class="quick-actions">
        <a class="quick-card" href="booking-summary.php">
          <div>
            <h3>My Bookings</h3>
            <p>View your current and past accommodation bookings.</p>
          </div>
          <span class="arrow">→</span>
        </a>

        <a class="quick-card" href="payment-summary.php">
          <div>
            <h3>My Payments</h3>
            <p>Check payment history and payment status.</p>
          </div>
          <span class="arrow">→</span>
        </a>

        <a class="quick-card logout" href="logout.php">
          <div>
            <h3>Logout</h3>
            <p>Safely sign out from your StayHub account.</p>
          </div>
          <span class="arrow">→</span>
        </a>
      </div>
    </div>

  </section>

  <section class="image-strip">
    <div class="stay-card">
      <img src="./images/Main/Main 2.jpg" alt="Luxury stay">
      <span>Luxury Stays</span>
    </div>

    <div class="stay-card">
      <img src="./images/Main/Main 3.jpg" alt="Modern rooms">
      <span>Modern Rooms</span>
    </div>

    <div class="stay-card">
      <img src="./images/Side/Side 3.jpg" alt="Peaceful places">
      <span>Peaceful Places</span>
    </div>

    <div class="stay-card">
      <img src="./images/Background/Background 2.jpg" alt="Travel views">
      <span>Travel Views</span>
    </div>
  </section>

<?php else: ?>

  <div class="not-logged">
    <h1>Please log in</h1>
    <p>You need to log in to view your StayHub profile.</p>
  </div>

<?php endif; ?>
</main>

<footer>
  <p>© 2025 STAY HUB. All rights reserved.</p>
</footer>

</body>
</html>