<?php
$is_invalid=false;
if($_SERVER["REQUEST_METHOD"] === "POST"){

    $mysqli = require __DIR__ . "/database.php";
    $email = $_POST["email"];
    $password = $_POST["password"];

    $domain = substr(strrchr($email, "@"), 1);

    if($domain === "accoms.ac.za"){
      $table = "admin";
      $id_col = "AdminID";
      $redirect = "admin-panel.php";
    } else{

      $table = "student";
      $id_col = "StudNum";
      $redirect = "homepage.php";
    }

    $sql = sprintf("SELECT * FROM $table WHERE Email ='%s'",
                  $mysqli->real_escape_string($email));
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();

    if($user && password_verify($password, $user["Password"])){
      session_start();
      session_regenerate_id();
      $_SESSION["user_id"] = $user[$id_col];
      $_SESSION["role"] = $table;
      header("Location: $redirect");
      exit;

    }

    $is_invalid = true;
    /*$sql = sprintf("SELECT * FROM student
                    WHERE email ='%s'",
                    $mysqli->real_escape_string($_POST["email"]));

              $result = $mysqli->query($sql);

              $user = $result->fetch_assoc();

                    if($user){

                        if(password_verify($_POST["password"], $user["Password"])){

                            session_start();

                            session_regenerate_id();

                            $_SESSION["user_id"]= $user["IDNum"];//Database value is IDNum, for some reason this being different didn't affect logging in
                                                                //with ID_NUM however it seems to affect profile so change it to database column name

                            header("Location: homepage.html");
                            //this needs to be changed to a different location
                            exit;

                        }

                    }
                    $is_invalid=true;
                    */
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
    <?php if($is_invalid): ?>
      <em>Invalid Login</em>
      <?php endif; ?>

    <form method ="post">
      <label for ="email">Email</label>
      <input type="email" name="email" id="email"  
      value="<?= htmlspecialchars($_POST["email"] ?? "")?>" required/>

      <label for ="password">Password</label>
      <input type="password" name="password" id ="password" required />


      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.html">Register here</a></p>
  </main>
</body>
</html>
