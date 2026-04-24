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
  <title>Login</title>
</head>
<body>

  <header>
    <div>Stay Hub</div>
    <nav>
      <a href="index.html">Home</a>
      <a href="register.html">Register</a>
    </nav>
  </header>

  <main>
    <h2>Login</h2>

    <?php if ($is_invalid): ?>
      <em>Invalid Login</em>
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

    <p>Don't have an account? <a href="register.html">Register here</a></p>
  </main>

</body>
</html>