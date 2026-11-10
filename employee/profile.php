<?php
  session_start();
  include "../config/database.php";

  if (!isset($_SESSION['user_id']) ||($_SESSION['role'] ?? '') !== 'rider') {
    exit("Unauthorized access. Rider login required.");
}

  $profile = mysqli_query($conn, "SELECT * FROM users WHERE role='rider' and id='user_id'");
  $result = mysqli_fetch_assoc($profile);
?>





<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Profile</title>
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
          <a class="button text" href="../user/user.php">Customer preview</a>
          <a class="button text" href="../admin/admin.php">Admin</a>
          <a class="button outline" href="../logout.php">Log out</a>
        </nav>
      </header>
      <div class="card formbox">
        <h2>Your profile</h2>
        <div class="avatar big">CM</div>
        <br />
        <label>Name</label>
        <input value="<?php echo ($result[]);?>" />
        <label>Phone</label>
        <input value="09XX XXX 2190" />
        <label>Email</label>
        <input type="email" value="alex@trigo.example" />
        <label>Vehicle</label>
        <input value="Bajaj RE · ABC 1234" />
        <span class="badge">Rider approved</span>
        <span class="badge amber">Franchise approval separate</span>
        <br />
        <br />
        <button class="" type="button" data-backend-action>Save profile</button>
        <a class="button outline" href="employee.php">Back</a>
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
