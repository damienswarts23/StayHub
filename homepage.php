<?php
session_start();

// Protect page
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$firstName = $_SESSION["first_name"] ?? "Student";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Homepage</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }

        header {
            background: #0077cc;
            color: white;
            padding: 20px;
        }

        main {
            margin-top: 50px;
        }

        .box {
            background: white;
            width: 300px;
            margin: 30px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        a.button {
            display: block;
            margin: 10px 0;
            padding: 12px;
            background: #0077cc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        a.button:hover {
            background: #005fa3;
        }
    </style>
</head>
<body>

<header>
    <h1>StayHub</h1>
</header>

<main>
    <div class="box">
        <h2>Welcome, <?= htmlspecialchars($firstName) ?>!</h2>
        <p>You are logged in successfully.</p>

        <a class="button" href="profile.php">Profile</a>
        <a class="button" href="index.html">Logout</a>
    </div>
</main>

</body>
</html>