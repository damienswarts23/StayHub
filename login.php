<?php
session_start();

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mysqli = require __DIR__ . "/db.php";

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // 1. Check students table first
    $sql = "SELECT * FROM students WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["student_id"];
            $_SESSION["role"] = "student";
            $_SESSION["email"] = $user["email"];
            $_SESSION["first_name"] = $user["first_name"];
            $_SESSION["last_name"] = $user["last_name"];
            $_SESSION["stud_number"] = $user["stud_number"];

            header("Location: homepage.php");
            exit;
        }
    } else {
        // 2. If not found in students, check hosts table
        $sql = "SELECT * FROM hosts WHERE email = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["host_id"];
            $_SESSION["role"] = "host";
            $_SESSION["email"] = $user["email"];
            $_SESSION["first_name"] = $user["first_name"];
            $_SESSION["last_name"] = $user["last_name"];

            header("Location: admin-panel.php");
            exit;
        }
    }

    $is_invalid = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>
    <link rel="stylesheet" href="login.css" />
</head>
<body>
    <header>
        <div class="logo">StayHub</div>
        <nav>
            <a href="index.html">Home</a>
            <a href="register.html">Register</a>
        </nav>
    </header>

    <main class="form-container">
        <h2>Login</h2>

        <?php if ($is_invalid): ?>
            <em>Invalid email or password</em>
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
            <input 
                type="password" 
                name="password" 
                id="password" 
                required
            />

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.html">Register here</a></p>
    </main>
</body>
</html>