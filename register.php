<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST["role"];
    $id_num = $_POST["id_num"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $stud_number = $_POST["stud_number"];
    $email = $_POST["email"];
    $cell_number = $_POST["cell_number"];
    $enrollment_year = (int) $_POST["enrollment_year"];
    $funding = $_POST["funding"];

    // Hash the password before saving
    $hashed_password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO students 
            (role, id_num, first_name, last_name, stud_number, email, cell_number, enrollment_year, password, funding)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
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

    if ($stmt->execute()) {
        alert("Registration successful!");
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>