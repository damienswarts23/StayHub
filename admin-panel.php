<?php
session_start();

// Must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Only allow hosts/admins
if (isset($_SESSION["role"]) && $_SESSION["role"] !== "host") {
    header("Location: homepage.php");
    exit;
}

$firstName = $_SESSION["first_name"] ?? "Host";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Panel</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      margin: 0;
      padding: 0;
      background: #f4f4f4;
    }

    header {
      background: #222;
      color: white;
      padding: 20px;
    }

    .logo {
      font-size: 24px;
      font-weight: bold;
    }

    nav {
      margin-top: 10px;
    }

    nav a {
      color: white;
      text-decoration: none;
      margin: 0 10px;
    }

    main {
      margin-top: 50px;
    }

    .box {
      background: white;
      width: 320px;
      margin: 30px auto;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    a.button {
      display: block;
      margin: 10px 0;
      padding: 12px;
      background: #222;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }

    a.button:hover {
      background: #444;
    }

    footer {
      margin-top: 40px;
      padding: 15px;
      background: #222;
      color: white;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">StayHub Admin Panel</div>
    <nav>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <div class="box">
      <h2>Welcome, <?= htmlspecialchars($firstName) ?>!</h2>
      <p>You are logged in as a host/admin.</p>

      <a class="button" href="#">Add Accommodation</a>
      <a class="button" href="#">View Applications</a>
      <a class="button" href="#">View Occupants</a>
      <a class="button" href="logout.php">Logout</a>
    </div>
  </main>

  <footer>
    <p>© 2025 StayHub. All rights reserved.</p>
  </footer>
</body>
</html>