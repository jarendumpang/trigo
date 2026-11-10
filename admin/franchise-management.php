<?php 
  session_start();
  include "../config/database.php";

  if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin"){
    header("Location: ../index.php");
    exit;
  }

  $sql = "SELECT * FROM franchise";
  $result = mysqli_query($conn, $sql);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Franchise Management</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css"
    />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <script src="../assets/js/ui.js" defer></script>
  </head>
  <body>
    <div id="app">
      <div class="app">
        <aside class="sidebar">
          <a class="logo">
            <span class="mark">↗</span>TriGo<b>.</b>
          </a>
          <div class="eyebrow" style="padding-left: 14px; color: #9baa9b">
            WORKSPACE
          </div>
          <a class="sideitem" href="admin.php"> <span>◫</span>Overview</a>
          <a class="sideitem" href="rider-applications.php">
            <span>♧</span>Rider Applications<span class="badge amber">3</span>
          </a>
          <a class="sideitem active" href="franchise-management.php">
            <span>▤</span>Franchise Management</a
          >
          <a class="sideitem" href="employees.php"> <span>♙</span>Employees</a>
          <a class="sideitem" href="rides.php"> <span>↗</span>Rides</a>
          <a class="sideitem" href="income-reports.php">
            <span>▥</span>Income Reports</a
          >
          <a class="sideitem" href="fare-settings.php">
            <span>⚙</span>Fare Settings</a
          >
          <a class="sideitem" href="settings.php"> <span>☷</span>Settings</a>
          <div class="sidebottom">
            <div class="notice">
              <strong>Here for your community.</strong>
              <br />A smoother day starts with TriGo.
            </div>
            <!-- <a class="sideitem" href="../index.php">↶ Back to website</a> -->
            <div class="row">
              <div class="avatar">G3</div>
              <div>
                <strong class="small">Jamie dela Cruz</strong>
                <br />
                <span class="small muted">Administrator</span>
              </div>
            </div>
          </div>
        </aside>
        <main class="main">
          <div class="topbar row between">
            <strong>Franchise Management</strong>
            <div class="row">
              <span class="small muted">San Isidro, Philippines</span>
              <span class="avatar">G3</span>
              <a class="button outline" href="../logout.php">Log out</a>
            </div>
          </div>
          <div class="content">
            <div class="pagehead row between">
              <div>
                <h1>Your franchise network.</h1>
                <p>Keep operators, vehicles, and renewals in view.</p>
              </div>
            </div>
            <div class="card tablecard">
              <div class="tablewrap">
                <table>
                  <thead>
                    <tr>
                      <th>Franchise no.</th>
                      <th>Operator</th>
                      <th>Assigned vehicle</th>
                      <th>Issue date</th>
                      <th>Expiry date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)) {?>
                    <tr>
                      <td><?php echo htmlspecialchars("TG-" . str_pad((string)$row["franchise_id"], 3, "0", STR_PAD_LEFT));?></td>
                      <td><?php echo htmlspecialchars($row["operator"]);?></td>
                      <td><?php echo htmlspecialchars($row["assigned_vehicle"]);?></td>
                      <td><?php echo htmlspecialchars($row["issue_date"]);?></td>
                      <td><?php echo htmlspecialchars($row["expiry_date"]);?></td>
                      <td><?php echo htmlspecialchars($row["status"]);?></td>
                    </tr>
                    <?php }?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </main>
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