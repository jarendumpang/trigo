<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Employees</title>
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
          <a class="logo" href="../index.php">
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
          <a class="sideitem active" href="employees.php">
            <span>♙</span>Employees</a
          >
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
            <strong>Employees</strong>
            <div class="row">
              <span class="small muted">San Isidro, Philippines</span>
              <a class="button outline" href="../user/user.php"
                >↗ Preview app</a
              >
              <span class="avatar">JD</span>
            </div>
          </div>
          <div class="content">
            <div class="pagehead row between">
              <div>
                <h1>The people behind every ride.</h1>
                <p>Approved riders and your operations team.</p>
              </div>
            </div>
            <div class="grid employees">
              <div class="card">
                <div class="avatar big">CM</div>
                <h3>Carlo Mendoza</h3>
                <span class="badge">Active · Rider</span>
                <p>09XX XXX 0421<br />Assigned tricycle: ABC 1234</p>
                <button class="outline" type="button" data-card-preview>
                  View profile
                </button>
              </div>
              <div class="card">
                <div class="avatar big">LB</div>
                <h3>Liza Bautista</h3>
                <span class="badge">Active · Rider</span>
                <p>09XX XXX 0421<br />Assigned tricycle: DEF 5678</p>
                <button class="outline" type="button" data-card-preview>
                  View profile
                </button>
              </div>
              <div class="card">
                <div class="avatar big">JD</div>
                <h3>Jamie dela Cruz</h3>
                <span class="badge">Active · Administrator</span>
                <p>09XX XXX 0421<br />Assigned tricycle: —</p>
                <button class="outline" type="button" data-card-preview>
                  View profile
                </button>
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
