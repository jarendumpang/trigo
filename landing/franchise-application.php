<?php
session_start();
    include "../config/database.php";
    
    $message = "";

    if(isset($_POST["submit"])){

        // Get all Data From form.
        $name = $_POST["name"];
        $phone = $_POST["phone"];
        $email = $_POST["email"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $address = $_POST["address"];
        $business_reg = $_POST["business_reg"];
        $model = $_POST["model"];
        $plate_number = $_POST["plate_number"];
        $img = $_POST["img"];

        // SQL Command to insert record
        $sql = "INSERT INTO franchise_application (
                        `full_name`,
                        `number`,
                        `email`,
                        `password`,
                        `op_address`,
                        `business_reg`,
                        `model`,
                        `plate_number`,
                        `image`
                        ) 
                        VALUES (
                        '$name',
                        '$phone',
                        '$email',
                        '$password',
                        '$address',
                        '$business_reg',
                        '$model',
                        '$plate_number',
                        '$img'
                        )";

      if(mysqli_query($conn, $sql)){
        header("Location: ../index.php?message=Rider Application sent successfully");
        exit;
      }
      else{
        $message = "Failed to sent";
      }
        
    }
   
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — franchiseform</title>
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
        <a class="logo" href="../index.html">
          <span class="mark">↗</span>TriGo<b>.</b>
        </a>
        <nav>
          <a href="../index.html">Overview</a>
          <a href="../index.html#how">How It Works</a>
          <a href="franchise.html">Tricycle Franchising</a>
          <a href="apply.html">Apply as a Rider</a>
          <a class="button outline" href="login.php">Log in ↗</a>
        </nav>
      </header>
      <div class="card formbox">
        <div class="eyebrow">JOIN THE TRIGO COMMUNITY</div>
        <h2>Franchise application</h2>
        <p>Your application will be reviewed before approval.</p>
        <?php if($message != ""){?>
                    <div class="alert alert-danger"><?php echo $message;?></div>
        <?php }?>
        <!-- PHP integration: add action/method and server validation; UI preview never sends data. -->
        <form method="POST">
          <div class="grid split">
            <div>
              <label>Full name</label>
              <input type="text" class="form-control" name="name"  id="name" required placeholder="Enter Full_name" />
            </div>
            <div>
              <label>Phone</label>
              <input type="text" class="form-control" name="phone" required placeholder="09XX XXX XXXX" />
            </div>
          </div>
          <label>Email</label>
          <input type="email" class="form-control" name="email" required placeholder="you@example.com" />
          <label>Password</label>
          <input
            type="password"
            class="form-control"
            name="password" required
            placeholder="At least 8 characters"
          />
          <label>Operator address</label>
          <input type="text" class="form-control" name="address"  required />
          <div class="grid split">
            <div>
              <label>Business registration</label>
              <input type="text" class="form-control" name="business_reg" required />
            </div>
            <div>
              <label>Vehicle model</label>
              <input type="text" name="model" required placeholder="Bajaj RE" />
            </div>
          </div>
          <label>Plate number</label>
          <input type="text" name="plate_number" required placeholder="ABC 1234" />
          <label>Business permit, valid ID, and proof of ownership</label>
          <input type="file" accept=".pdf,.png,.jpg,.jpeg" name="img" multiple required />
          <div class="notice">
            Use sample files only. Files are not uploaded. Rider and franchise
            approval are independent.
          </div>
          <label class="row">
            <input style="width: auto; margin: 0" type="checkbox" required /> I
            agree to the demo terms and privacy policy.</label
          >
          <br />
          <button class="full" type="submit" name="submit" data-backend-action>
            Submit application →
          </button>
        </form>
      </div>
    </div>
    <dialog id="ui-dialog" aria-labelledby="ui-dialog-title">
      <h2 id="ui-dialog-title"></h2>
      <div id="ui-dialog-content"></div>
      <div class="row">
        <button type="submit" name="submit" data-close-dialog>Close</button>
      </div>
    </dialog>
  </body>
</html>
