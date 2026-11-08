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
          <a class="button text" href="../employee/employee.php"
            >Rider preview</a
          >
          <a class="button text" href="../admin/admin.php">Admin</a>
          <a class="button outline" href="../index.php">Log out</a>
        </nav>
      </header>
      <div class="card formbox">
        <h2>Your profile</h2>
        <div class="avatar big">AR</div>
        <br />
        <label>Name</label>
        <input value="Alex Reyes" />
        <label>Phone</label>
        <input value="09XX XXX 2190" />
        <label>Email</label>
        <input type="email" value="alex@trigo.example" />
        <button class="" type="button" data-backend-action>Save profile</button>
        <a class="button outline" href="user.php">Back</a>
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
