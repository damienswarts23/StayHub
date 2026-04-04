<?php
$mysqli = require __DIR__ . "/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $role = $_POST["role"] ?? "";

    // Common fields
    $id_num = trim($_POST["id_num"] ?? "");
    $first_name = trim($_POST["first_name"] ?? "");
    $last_name = trim($_POST["last_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $cell_number = trim($_POST["cell_number"] ?? "");
    $password = $_POST["password"] ?? "";

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if ($role === "student") {
        $stud_number = trim($_POST["stud_number"] ?? "");
        $enrollment_year = (int) ($_POST["enrollment_year"] ?? 0);
        $funding = trim($_POST["funding"] ?? "");

        $sql = "INSERT INTO students 
                (role, id_num, first_name, last_name, stud_number, email, cell_number, enrollment_year, password, funding)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $mysqli->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }

        $stmt->bind_param(
            "sssssssiss",
            $role,
            $id_num,
            $first_name,
            $last_name,
            $stud_number,
            $email,
            $cell_number,
            $enrollment_year,
            $hashed_password,
            $funding
        );

    } elseif ($role === "admin" || $role === "host") {

        $sql = "INSERT INTO hosts 
                (role, id_num, first_name, last_name, email, cell_number, password)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $mysqli->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }

        $stmt->bind_param(
            "sssssss",
            $role,
            $id_num,
            $first_name,
            $last_name,
            $email,
            $cell_number,
            $hashed_password
        );

    } else {
        die("Invalid role selected.");
    }

    if ($stmt->execute()) {
        header("Location: login.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();

} else {
    echo "Invalid request method.";
}
?>