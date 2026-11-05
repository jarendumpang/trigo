<?php 
  session_start();
  include "../config/database.php";

  if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin"){
    header("Location: ../index.php");
    exit;
  } 

  $pending_app = mysqli_query($conn, "SELECT app_id FROM pending_applications");
  $reg_franchise = mysqli_query($conn, "SELECT franchise_id FROM franchise");
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
  $company_income = (int)$company_row['total'];


?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Overview</title>
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
          <a class="sideitem active" href="admin.php"> <span>◫</span>Overview</a>
          <a class="sideitem" href="rider-applications.php">
            <span>♧</span>Rider Applications<span class="badge amber">3</span>
          </a>
          <a class="sideitem" href="franchise-management.php">
            <span>▤</span>Franchise Management</a>
          <a class="sideitem" href="employees.php"> <span>♙</span>Employees</a>
          <a class="sideitem" href="rides.php"> <span>↗</span>Rides</a>
          <a class="sideitem" href="income-reports.php">
            <span>▥</span>Income Reports</a>
          <a class="sideitem" href="fare-settings.php">
            <span>⚙</span>Fare Settings</a>
          <a class="sideitem" href="settings.php"> <span>☷</span>Settings</a>
          <div class="sidebottom">
            <div class="notice">
              <strong>Here for your community.</strong>
              <br />A smoother day starts with TriGo.
            </div>
            <!-- <a class="sideitem" href="../logout.php">↶ Back to website</a> -->
            <div class="row">
              <div class="avatar">G3</div>
              <div>
                <strong class="small">Group 3</strong>
                <br />
                <span class="small muted">Administrator</span>
              </div>
            </div>
          </div>
        </aside>
        <main class="main">
          <div class="topbar row between">
            <strong>Overview</strong>
            <div class="row">
              <span class="small muted">San Isidro, Philippines</span>
              <span class="avatar">G3</span>
              <a class="button outline" href="../logout.php">Log out</a>
            </div>
          </div>
          <div class="content">
            <div class="pagehead row between">
              <div>
                <h1>A good day to keep things moving.</h1>
                <p>
                  Welcome back, Jamie. Here’s what’s happening in your
                  community.
                </p>
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
                  <strong><?php echo $rider_earnings?></strong>
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
            <div class="tablewrap">
            <table>
              <thead>
                <tr>
                  <th>Applicant</th>
                  <th>Application</th>
                  <th>Submitted</th>
                  <th>Documents</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr data-person="miguel santos">
                  <td>
                    <div class="row">
                      <div class="avatar">MS</div>
                      <div>
                        <strong>Miguel Santos</strong><br /><span
                          class="small muted"
                          >Bajaj RE · ABC 1234</span
                        >
                      </div>
                    </div>
                  </td>
                  <td>Rider</td>
                  <td>Oct 31, 2026</td>
                  <td>
                    <button class="text" type="button" data-title="Application documents" data-message="The original contains 3 sample documents. Connect your PHP endpoint to load the actual applicant files.">▧ 3 documents</button>
                  </td>
                  <td><span class="badge amber">Pending review</span></td>
                  <td>
                    <a class="button text" href="rider-applications.html">
                      Review →
                    </a>
                  </td>
                </tr>
                <tr data-person="ana reyes">
                  <td>
                    <div class="row">
                      <div class="avatar">AR</div>
                      <div>
                        <strong>Ana Reyes</strong><br /><span
                          class="small muted"
                          >TVS King · DEF 5678</span
                        >
                      </div>
                    </div>
                  </td>
                  <td>Rider</td>
                  <td>Oct 31, 2026</td>
                  <td>
                    <button class="text" type="button" data-title="Application documents" data-message="The original contains 3 sample documents. Connect your PHP endpoint to load the actual applicant files.">▧ 3 documents</button>
                  </td>
                  <td><span class="badge amber">Pending review</span></td>
                  <td>
                    <a class="button text" href="rider-applications.html">
                      Review →
                    </a>
                  </td>
                </tr>
                <tr data-person="roberto cruz">
                  <td>
                    <div class="row">
                      <div class="avatar">RC</div>
                      <div>
                        <strong>Roberto Cruz</strong><br /><span
                          class="small muted"
                          >Bajaj RE · GHI 9012</span
                        >
                      </div>
                    </div>
                  </td>
                  <td>Franchise</td>
                  <td>Oct 31, 2026</td>
                  <td>
                    <button class="text" type="button" data-title="Application documents" data-message="The original contains 3 sample documents. Connect your PHP endpoint to load the actual applicant files.">▧ 3 documents</button>
                  </td>
                  <td><span class="badge amber">Pending review</span></td>
                  <td>
                    <a class="button text" href="rider-applications.html">
                      Review →
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
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
