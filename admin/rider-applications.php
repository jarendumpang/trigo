<?php
session_start();
include "../config/database.php";

// Search applicants
$search = "";

if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sql = "SELECT * FROM rider_application
        WHERE full_name LIKE '%$search%'";

$result = mysqli_query($conn, $sql);

$total = mysqli_num_rows($result);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Rider Applications</title>
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
          <a class="sideitem active" href="rider-applications.php">
            <span>♧</span>Rider Applications<span class="badge amber"><?php echo $total; ?></span>
          </a>
          <a class="sideitem" href="franchise-management.php">
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
            <strong>Rider Applications</strong>
            <div class="row">
              <span class="small muted">San Isidro, Philippines</span>
              <span class="avatar">G3</span>
              <a class="button outline" href="../logout.php">Log out</a>
            </div>
          </div>
          <div class="content">
            <div class="pagehead row between">
              <div>
                <h1>New opportunities start here.</h1>
                <p>Review rider and franchise applications independently.</p>
              </div>
            </div>
            <div class="card tablecard">
              <div class="tabletitle row between">
                <div>
                  <h3>Applications awaiting your review</h3>
                  <span class="small muted"
                    >Help our community get on the road.</span
                  >
              </div>
              <form method="GET" action="rider-applications.php">

                <input
                  class="search"
                  type="text"
                  name="search"
                  placeholder="Search applicants..."
                  value="<?php echo htmlspecialchars($search); ?>"
              />

              <button type="submit">Search</button>

              <a href="rider-applications.php">Clear</a>

              </form>
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
                    <?php while($row = mysqli_fetch_assoc($result)) {?>
                    <tr>
                      <td><?php echo htmlspecialchars($row["full_name"]);?></td>
                      <td>Rider</td>
                      <td><?php echo htmlspecialchars($row["submitted_at"]);?></td>
                      <td>
                        <button
                          class="text"
                          type="button"
                          data-title="Application documents"
                          data-message="The original contains 3 sample documents. Connect your PHP endpoint to load the actual applicant files."
                        >
                          ▧ 3 documents
                        </button>
                      </td>
                      <td><?php echo htmlspecialchars($row["status"]);?></td>
                    </tr>
                    <?php }?>
                    <!-- <tr data-person="miguel santos">
                      <td>
                        <div class="row">
                          <div class="avatar">MS</div>
                          <div>
                            <strong>Miguel Santos</strong>
                            <br />
                            <span class="small muted">Bajaj RE · ABC 1234</span>
                          </div> 
                        </div>
                      </td>
                      <td>Rider</td>
                      <td>Oct 31, 2026</td>
                      <td>
                        <button
                          class="text"
                          type="button"
                          data-title="Application documents"
                          data-message="The original contains 3 sample documents. Connect your PHP endpoint to load the actual applicant files."
                        >
                          ▧ 3 documents
                        </button>
                      </td>
                      <td>
                        <span class="badge amber">Pending review</span>
                      </td>
                      <td>
                        <a class="button text" href="rider-applications.html"
                          >Review →</a
                        >
                      </td>
                    </tr>
                    <tr data-person="ana reyes">
                      <td>
                        <div class="row">
                          <div class="avatar">AR</div>
                          <div>
                            <strong>Ana Reyes</strong>
                            <br />
                            <span class="small muted">TVS King · DEF 5678</span>
                          </div>
                        </div>
                      </td>
                      <td>Rider</td>
                      <td>Oct 31, 2026</td>
                      <td>
                        <button
                          class="text"
                          type="button"
                          data-title="Application documents"
                          data-message="The original contains 3 sample documents. Connect your PHP endpoint to load the actual applicant files."
                        >
                          ▧ 3 documents
                        </button>
                      </td>
                      <td>
                        <span class="badge amber">Pending review</span>
                      </td>
                      <td>
                        <a class="button text" href="rider-applications.html"
                          >Review →</a
                        >
                      </td>
                    </tr>
                    <tr data-person="roberto cruz">
                      <td>
                        <div class="row">
                          <div class="avatar">RC</div>
                          <div>
                            <strong>Roberto Cruz</strong>
                            <br />
                            <span class="small muted">Bajaj RE · GHI 9012</span>
                          </div>
                        </div>
                      </td>
                      <td>Franchise</td>
                      <td>Oct 31, 2026</td>
                      <td>
                        <button
                          class="text"
                          type="button"
                          data-title="Application documents"
                          data-message="The original contains 3 sample documents. Connect your PHP endpoint to load the actual applicant files."
                        >
                          ▧ 3 documents
                        </button>
                      </td>
                      <td>
                        <span class="badge amber">Pending review</span>
                      </td>
                      <td>
                        <a class="button text" href="rider-applications.html"
                          >Review →</a
                        >
                      </td>
                    </tr> -->
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
