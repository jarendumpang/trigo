<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Fare Settings</title>
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
          <a class="sideitem" href="rides.php"> <span>↗</span>Rides</a>
          <a class="sideitem" href="income-reports.php">
            <span>▥</span>Income Reports</a
          >
          <a class="sideitem active" href="fare-settings.php">
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
            <strong>Fare Settings</strong>
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
                <h1>Clear fares. Fair journeys.</h1>
                <p>Configure example rates for the booking preview.</p>
              </div>
            </div>

            <!-- PHP integration: add action/method and server validation; UI preview never sends data. -->
            <form data-frontend-form class="card" style="max-width: 650px">
              <div class="grid split">
                <div>
                  <label>Base fare (PHP)</label>
                  <input
                    id="base"
                    type="number"
                    min="0"
                    step="0.1"
                    required
                    value="25"
                  />
                </div>
                <div>
                  <label>Included distance (km)</label>
                  <input
                    id="included"
                    type="number"
                    min="0"
                    step="0.1"
                    required
                    value="1"
                  />
                </div>
                <div>
                  <label>Per-kilometer rate (PHP)</label>
                  <input
                    id="rate"
                    type="number"
                    min="0"
                    step="0.1"
                    required
                    value="10"
                  />
                </div>
                <div>
                  <label>Company commission (%)</label>
                  <input
                    id="commission"
                    type="number"
                    min="0"
                    max="100"
                    step="0.1"
                    required
                    value="15"
                  />
                </div>
              </div>
              <div class="notice">
                Fare = base fare + max(0, route km − included km) × rate.<br />3.8
                km example → ₱53. Revised prices require customer confirmation.
              </div>
              <button type="button" data-fare-preview>
                Review & save rates
              </button>
            </form>
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
