<?php
    session_start();
    include "../config/database.php";
  
    $message = "";

    if(isset($_POST["register"])){
        //Get all Data From form.
        $name = $_POST["name"];
        $phone = $_POST["phone"];
        $email = $_POST["email"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

        //sql Command to insert record
        $sql = "INSERT INTO users(
                        `full_name`,
                        `number`,
                        `email`,
                        password,
                        role
                        ) 
                        VALUES (
                        '$name',
                        '$phone',
                        '$email',
                        '$password',
                        'user'       
                        )";

        if(mysqli_query($conn, $sql)){
            header("Location: login.php?message=Student Record Added Successfully");
            exit;
        }                
        else{
            $message = "Could not save student record!";
        }
    }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — register</title>
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
        <div class="eyebrow">JOIN THE TRIGO COMMUNITY</div>
        <h2>Create your account</h2>
        <p>Your next ride starts here.</p>

        <!-- PHP integration: add action/method and server validation; UI preview never sends data. -->
        <form data-frontend-form method="POST">
          <div class="grid split">
            <div>
              <label>Full name</label>
              <input id="name" name="name" required placeholder="Juan dela Cruz" />
            </div>
            <div>
              <label>Phone</label>
              <input type="tel" name="phone" required placeholder="09XX XXX XXXX" />
            </div>
          </div>
          <label>Email</label>
          <input type="email" name="email" required placeholder="you@example.com" />
          <label>Password</label>
          <input
            type="password"
            minlength="8"
            required
            placeholder="At least 8 characters"
            name="password"
          />
          <label class="row">
            <input style="width: auto; margin: 0" type="checkbox" required /> I
            agree to the demo terms and privacy policy.</label
          >
          <br />
          <button class="full" type="submit" name="register" data-backend-action>
            Create account →
          </button>
        </form>
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
