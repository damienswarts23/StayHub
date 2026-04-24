<?php
session_start();
$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "/database.php";

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $domain = substr(strrchr($email, "@"), 1);

    if ($domain === "accoms.ac.za") {
        $table = "admin";
        $id_col = "AdminID";
        $redirect = "admin-panel.php";
    } else {
        $table = "student";
        $id_col = "StudNum";
        $redirect = "homepage.php";
    }

    $sql = "SELECT * FROM $table WHERE Email = ?";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user["Password"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user[$id_col];
        $_SESSION["role"] = $table;

        header("Location: $redirect");
        exit;
    }

    $is_invalid = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", "Segoe UI", sans-serif;
    }

    body {
      min-height: 100vh;
      color: #fff;
      background:
        radial-gradient(circle at 20% 20%, rgba(90, 180, 255, 0.18), transparent 25%),
        radial-gradient(circle at 80% 30%, rgba(255, 170, 90, 0.12), transparent 20%),
        radial-gradient(circle at 50% 80%, rgba(100, 140, 255, 0.1), transparent 30%),
        linear-gradient(180deg, #07111d 0%, #0a1d2f 45%, #09131f 100%);
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
      position: relative;
    }

    body::before {
      content: "";
      position: fixed;
      inset: 0;
      background-image:
        radial-gradient(2px 2px at 20px 30px, rgba(255,255,255,0.55), transparent),
        radial-gradient(2px 2px at 140px 90px, rgba(255,255,255,0.35), transparent),
        radial-gradient(1.5px 1.5px at 240px 160px, rgba(255,255,255,0.45), transparent),
        radial-gradient(2px 2px at 420px 80px, rgba(255,255,255,0.28), transparent),
        radial-gradient(1.5px 1.5px at 600px 140px, rgba(255,255,255,0.35), transparent),
        radial-gradient(2px 2px at 760px 40px, rgba(255,255,255,0.4), transparent);
      background-size: 800px 300px;
      opacity: 0.35;
      pointer-events: none;
      z-index: 0;
    }

    header {
      width: 100%;
      padding: 28px 60px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
      z-index: 2;
    }

    .logo {
      font-size: 1.4rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      color: #eef7ff;
    }

    nav {
      display: flex;
      gap: 12px;
      align-items: center;
      flex-wrap: wrap;
      padding: 8px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
    }

    nav a {
      text-decoration: none;
      color: #eaf4ff;
      font-size: 0.9rem;
      padding: 10px 18px;
      border-radius: 999px;
      transition: 0.3s ease;
    }

    nav a:hover {
      background: rgba(255, 255, 255, 0.09);
      color: #ffffff;
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 30px 20px 60px;
      position: relative;
      z-index: 1;
    }

    .panel {
      width: 100%;
      max-width: 1180px;
      min-height: 78vh;
      border-radius: 36px;
      overflow: hidden;
      position: relative;
      background:
        linear-gradient(rgba(5, 15, 25, 0.4), rgba(5, 15, 25, 0.75)),
        url("Register background.png") center/cover no-repeat;
      border: 1px solid rgba(255, 255, 255, 0.14);
      box-shadow:
        0 30px 80px rgba(0, 0, 0, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    .panel::before {
      content: "";
      position: absolute;
      inset: 0;
      background:
        radial-gradient(circle at 20% 20%, rgba(90, 180, 255, 0.18), transparent 25%),
        radial-gradient(circle at 80% 70%, rgba(255, 140, 70, 0.22), transparent 20%);
      pointer-events: none;
    }

    .hero-content {
      position: relative;
      z-index: 2;
      width: 100%;
      min-height: 78vh;
      display: flex;
      align-items: center;
      justify-content: flex-start;
      padding: 70px;
    }

    .card {
      width: 100%;
      max-width: 470px;
      padding: 32px 28px;
      border-radius: 28px;
      background: rgba(9, 22, 36, 0.42);
      border: 1px solid rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      box-shadow:
        0 18px 50px rgba(0, 0, 0, 0.28),
        inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    .tag {
      display: inline-block;
      padding: 8px 14px;
      border-radius: 999px;
      margin-bottom: 18px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.12);
      color: #dcecff;
      font-size: 0.8rem;
      letter-spacing: 0.5px;
    }

    .card h2 {
      font-size: 2.6rem;
      line-height: 1.1;
      font-weight: 600;
      margin-bottom: 14px;
      color: #ffffff;
    }

    .card p {
      color: rgba(235, 244, 255, 0.82);
      line-height: 1.7;
      font-size: 0.97rem;
      margin-bottom: 26px;
      max-width: 390px;
    }

    .error {
      display: block;
      margin-bottom: 18px;
      padding: 12px 15px;
      border-radius: 16px;
      background: rgba(255, 80, 80, 0.16);
      border: 1px solid rgba(255, 120, 120, 0.28);
      color: #ffd1d1;
      font-style: normal;
      font-size: 0.9rem;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    label {
      font-size: 0.92rem;
      color: #dce9f7;
    }

    input {
      width: 100%;
      padding: 16px 18px;
      border-radius: 999px;
      border: 1px solid rgba(255, 255, 255, 0.14);
      background: rgba(255, 255, 255, 0.08);
      color: #ffffff;
      outline: none;
      font-size: 0.95rem;
      transition: 0.3s ease;
    }

    input:focus {
      border-color: rgba(215, 136, 69, 0.75);
      box-shadow: 0 0 0 4px rgba(215, 136, 69, 0.15);
    }

    button {
      margin-top: 8px;
      padding: 16px 18px;
      border-radius: 999px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      font-size: 0.95rem;
      font-weight: 500;
      color: #ffffff;
      cursor: pointer;
      transition: 0.3s ease;
      text-align: center;
      background: linear-gradient(135deg, #a9682c, #d78845, #9c5f2d);
      box-shadow: 0 10px 25px rgba(176, 111, 53, 0.35);
    }

    button:hover {
      transform: translateY(-2px);
      border-color: rgba(255, 255, 255, 0.2);
    }

    .register-link {
      margin-top: 22px;
      color: rgba(235, 244, 255, 0.82);
      font-size: 0.92rem;
      line-height: 1.6;
    }

    .register-link a {
      color: #f2b06f;
      text-decoration: none;
      font-weight: 600;
    }

    .register-link a:hover {
      text-decoration: underline;
    }

    .floating-note {
      position: absolute;
      right: 40px;
      bottom: 35px;
      z-index: 2;
      padding: 10px 18px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid rgba(255, 255, 255, 0.14);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      color: #dfeeff;
      font-size: 0.82rem;
    }

    @media (max-width: 900px) {
      header {
        padding: 24px 20px;
        flex-direction: column;
        gap: 18px;
      }

      .panel {
        min-height: auto;
      }

      .hero-content {
        min-height: auto;
        padding: 35px 20px;
      }

      .card {
        max-width: 100%;
      }

      .card h2 {
        font-size: 2rem;
      }

      .floating-note {
        position: static;
        margin: 18px 20px 20px;
        display: inline-block;
      }
    }

    @media (max-width: 560px) {
      nav {
        justify-content: center;
      }

      nav a {
        padding: 9px 14px;
        font-size: 0.85rem;
      }

      .card {
        padding: 24px 18px;
        border-radius: 24px;
      }

      .card h2 {
        font-size: 1.75rem;
      }

      button,
      input {
        padding: 15px 16px;
      }
    }
  </style>
</head>

<body>

  <header>
    <div class="logo">Stay Hub</div>
    <nav>
      <a href="index.html">Home</a>
      <a href="register.html">Register</a>
    </nav>
  </header>

  <main>
    <section class="panel">
      <div class="hero-content">
        <div class="card">
          <span class="tag">Welcome back to Stay Hub</span>
          <h2>Login</h2>
          <p>
            Access your account to continue exploring stays, managing bookings,
            or handling your accommodation listings.
          </p>

          <?php if ($is_invalid): ?>
            <em class="error">Invalid login. Please check your email and password.</em>
          <?php endif; ?>

          <form method="post">
            <label for="email">Email</label>
            <input
              type="email"
              name="email"
              id="email"
              value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
              required
            />

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required />

            <button type="submit">Login</button>
          </form>

          <p class="register-link">
            Don't have an account? <a href="register.html">Register here</a>
          </p>
        </div>
      </div>

      <div class="floating-note">Secure • Simple • Premium</div>
    </section>
  </main>

</body>
</html>