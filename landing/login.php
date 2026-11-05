<?php
session_start();
include "../config/database.php";

//send the user to correct dashboard
if(isset($_SESSION["role"])){
    if(($_SESSION["role"]) == "admin"){
        header("Location: ../admin/admin.php");
        exit;
    }
    else  if($_SESSION(["role"]) == "rider"){
        header("Location: employee/employee.php");
    }
    else {
        header("Location: users/user.php");
    }
}
$error = "";

if(isset($_POST["login"])){
    $email = mysqli_real_escape_string($conn,$_POST["email"]);
    $password = $_POST["password"];
    
    //Find typed username
    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["role"] = $user["role"];

            if($user["role"] == "admin"){
                header("Location: ../admin/admin.php");
            }
           
  
    }
    $error = "Invalid Username or Password";    

}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — login</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css"
    />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <script src="../assets/js/ui.js" defer></script>
  </head>
  <body>
    <div id="app">
      <header>
        <a class="logo" href="../index.php">
          <span class="mark">↗</span>TriGo<b>.</b>
        </a>
        <nav>
          <a href="../index.php">Overview</a>
          <a href="../index.php#how">How It Works</a>
          <a href="franchise.php">Tricycle Franchising</a>
          <a href="apply.php">Apply as a Rider</a>
          <a class="button outline" href="login.php">Log in ↗</a>
        </nav>
      </header>
      <div class="card formbox">
        <div class="eyebrow">WELCOME BACK</div>
        <h2>Let’s get you moving.</h2>
        <p>Choose a demo role to explore your dashboard.</p>

        <!-- PHP integration: add action/method and server validation; UI preview never sends data. -->
        <form method="POST">
          <label>Email address</label>
          <input type="email" name="email" required />
          <label>Password</label>
          <input type="password" name="password" required />
          <label>Demo account</label>
          <select id="role">
            <option value="admin">Administrator</option>
            <option value="rider">Approved rider</option>
            <option value="customer">Customer</option>
            <option value="status">Pending rider applicant</option>
          </select>
          <button class="full" type="submit" name="login">Login →</button>
        </form>
        <a class="button text" href="reset-password.php">Forgot password?</a>
        <p>
          New to TriGo?
          <a style="color: var(--g)" href="register.php"
            >Create a customer account</a
          >
        </p>
        <div class="notice">
          Preview role selection only. Authentication and permissions require a
          production server.
        </div>
      </div>
    </div>
    <dialog id="ui-dialog" aria-labelledby="ui-dialog-title">
      <h2 id="ui-dialog-title"></h2>
      <div id="ui-dialog-content"></div>
      <div class="row">
        <button type="button" data-close-dialog>Close</button>
      </div>
    </dialog>
  </body>
</html>
