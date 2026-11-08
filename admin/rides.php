<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Rides</title>
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
            <span>▤</span>Franchise Management</a
          >
          <a class="sideitem" href="employees.php"> <span>♙</span>Employees</a>
          <a class="sideitem active" href="rides.php"> <span>↗</span>Rides</a>
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
            <strong>Rides</strong>
            <div class="row">
              <span class="small muted">San Isidro, Philippines</span>
              <span class="avatar">G3</span>
              <a class="button outline" href="../logout.php">Log out</a>
            </div>
          </div>
          <div class="content">
            <div class="pagehead row between">
              <div>
                <h1>Every journey, in one place.</h1>
                <p>
                  Monitor routes, fares, ride status, and payment collection.
                </p>
              </div>
            </div>
            <div class="card tablecard">
              <div class="tablewrap">
                <table>
                  <thead>
                    <tr>
                      <th>Ride</th>
                      <th>Customer / Rider</th>
                      <th>Pickup → Drop-off</th>
                      <th>Distance</th>
                      <th>Fare</th>
                      <th>Payment</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>TG-1042</td>
                      <td>Alex Reyes / Carlo Mendoza</td>
                      <td>Public Market → Municipal Hall</td>
                      <td>3.8 km</td>
                      <td>₱53</td>
                      <td>Collected</td>
                      <td>
                        <span class="badge">Completed</span>
                      </td>
                    </tr>
                    <tr>
                      <td>TG-1041</td>
                      <td>Mia Santos / Liza Bautista</td>
                      <td>Rizal St → Community Park</td>
                      <td>2.4 km</td>
                      <td>₱39</td>
                      <td>Pending</td>
                      <td>
                        <span class="badge">On the Way</span>
                      </td>
                    </tr>
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
