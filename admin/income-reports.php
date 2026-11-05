<?php 
  session_start();
  include "../config/database.php";

  if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin"){
    header("Location: ../index.php");
    exit;
  } 

  $pending_app = mysqli_query($conn, "SELECT app_id FROM pending_applications");
  $reg_franchise = mysqli_query($conn, "SELECT franchise_id FROM franchises");
  $active_riders = mysqli_query($conn, "SELECT rider_id FROM riders");
  $completed_rides = mysqli_query($conn, "SELECT ride_id FROM completed_rides");

  $income = mysqli_query($conn, "SELECT SUM(total_income) AS total FROM income");
  $income_row = mysqli_fetch_assoc($income);
  $income = (int)$income_row['total'];

  $rider_earnings = mysqli_query($conn, "SELECT SUM(rider_earnings) AS total FROM income");
  $rider_row = mysqli_fetch_assoc($rider_earnings);
  $rider_earnings = (int)$rider_row['total'];

  $company_income = mysqli_query($conn, "SELECT SUM(company_income) AS total FROM income");
  $company_row = mysqli_fetch_assoc($company_income);
  $company_income =(int)$company_row['total'];


?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Income Reports</title>
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
          <a class="sideitem" href="franchise-management.php">
            <span>▤</span>Franchise Management</a>
          <a class="sideitem" href="employees.php"> <span>♙</span>Employees</a>
          <a class="sideitem" href="rides.php"> <span>↗</span>Rides</a>
          <a class="sideitem active" href="income-reports.php">
            <span>▥</span>Income Reports</a>
          <a class="sideitem" href="fare-settings.php">
            <span>⚙</span>Fare Settings</a>
          <a class="sideitem" href="settings.php"> <span>☷</span>Settings</a>
          <div class="sidebottom">
            <div class="notice">
              <strong>Here for your community.</strong>
              <br />A smoother day starts with TriGo.
            </div>
            <a class="sideitem" href="../index.php">↶ Back to website</a>
            <div class="row">
              <div class="avatar">JD</div>
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
            <strong>Income Reports</strong>
            <div class="row">
              <span class="small muted">San Isidro, Philippines</span>
              <a class="button outline" href="../user/user.php"
                >↗ Preview app</a>
              <span class="avatar">JD</span>
            </div>
          </div>
          <div class="content">
            <div class="pagehead row between">
              <div>
                <h1>Every ride, accounted for.</h1>
                <p>Track fares, rider earnings, and company income.</p>
              </div>
              <span class="badge outline">◷ Oct 31, 2026</span>
            </div>
            <div class="grid cards">
              <div class="card">
                <div class="row between">
                  <span class="small muted">Pending applications</span>
                  <span class="icon">♧</span>
                </div>
                <div class="stat"><?php echo mysqli_num_rows($pending_app);?></div>
                <span class="small" style="color: var(--g)"
                  >Awaiting your review</span
                >
              </div>
              <div class="card">
                <div class="row between">
                  <span class="small muted">Registered franchises</span>
                  <span class="icon">▤</span>
                </div>
                <div class="stat"><?php echo mysqli_num_rows($reg_franchise);?></div>
                <span class="small" style="color: var(--g)"
                  >Across our community</span
                >
              </div>
              <div class="card">
                <div class="row between">
                  <span class="small muted">Active riders</span>
                  <span class="icon">♙</span>
                </div>
                <div class="stat"><?php echo mysqli_num_rows($active_riders);?></div>
                <span class="small" style="color: var(--g)"
                  >↑ 8 riders this month</span
                >
              </div>
              <div class="card">
                <div class="row between">
                  <span class="small muted">Completed rides</span>
                  <span class="icon">↗</span>
                </div>
                <div class="stat"><?php echo mysqli_num_rows($completed_rides);?></div>
                <span class="small" style="color: var(--g)"
                  >↑ 12.8% this month</span
                >
              </div>
            </div>
            <div class="grid twocol">
              <div class="card">
                <div class="row between">
                  <div>
                    <h3>Income over time</h3>
                    <span class="small muted"
                      >Weekly activity · sample data</span
                    >
                  </div>
                  <div class="tabs">
                    <button class="" type="button" data-period="Daily">Daily</button>
                    <button class="active" type="button" data-period="Weekly">Weekly</button>
                    <button class="" type="button" data-period="Monthly">Monthly</button>
                    <button class="" type="button" data-period="Yearly">Yearly</button>
                  </div>
                </div>
                <div class="chart">
                  <div
                    class="bar"
                    style="height: 54px"
                    title="54 sample units"
                  ></div>
                  <div
                    class="bar"
                    style="height: 80px"
                    title="80 sample units"
                  ></div>
                  <div
                    class="bar"
                    style="height: 68px"
                    title="68 sample units"
                  ></div>
                  <div
                    class="bar"
                    style="height: 98px"
                    title="98 sample units"
                  ></div>
                  <div
                    class="bar"
                    style="height: 87px"
                    title="87 sample units"
                  ></div>
                  <div
                    class="bar"
                    style="height: 139px"
                    title="139 sample units"
                  ></div>
                  <div
                    class="bar"
                    style="height: 108px"
                    title="108 sample units"
                  ></div>
                </div>
                <div class="chartlabels">
                  <span>Mon</span>
                  <span>Tue</span>
                  <span>Wed</span>
                  <span>Thu</span>
                  <span>Fri</span>
                  <span>Sat</span>
                  <span>Sun</span>
                </div>
              </div>
              <div class="card">
                <div class="row between">
                  <h3>Income overview</h3>
                  <span class="badge">Sample figures</span>
                </div>
                <div class="stat"><?php echo $income;?></div>
                <span class="small muted">Total customer fares · weekly</span>
                <div class="line">
                  <span>Rider earnings</span>
                  <strong><?php echo $rider_earnings;?></strong>
                </div>
                <div class="line">
                  <span>Company income</span>
                  <strong style="color: var(--g)"><?php echo $company_income;?></strong>
                </div>
                <p class="small">15% configurable company commission</p>
                <div
                  style="height: 8px; background: #dcecdf; border-radius: 8px"
                >
                  <div
                    style="
                      height: 100%;
                      width: 85%;
                      background: var(--g);
                      border-radius: 8px;
                    "
                  ></div>
                </div>
              </div>
            </div>
            <div class="notice">
              Sample financial figures. Company income = total customer fares ×
              commission. Rider earnings = fares − company income.
            </div>
          </div>
        </main>
      </div>
    </div>
  <dialog id="ui-dialog" aria-labelledby="ui-dialog-title">
  <h2 id="ui-dialog-title"></h2>
  <div id="ui-dialog-content"></div>
  <div class="row"><button type="button" data-close-dialog>Close</button></div>
</dialog>
</body>
</html>
