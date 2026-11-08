<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Ride history</title>
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
          <a class="button outline" href="../index.php">Log out</a>
        </nav>
      </header>
      <div class="phonearea">
        <div class="pagehead row between">
          <div>
            <h1>Your ride history.</h1>
            <p>A record of the places you’ve been.</p>
          </div>
        </div>
        <div class="card tablecard">
          <div class="tablewrap">
            <table>
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Route</th>
                  <th>Distance</th>
                  <th>Fare</th>
                  <th>Payment</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Oct 30, 2026</td>
                  <td>Public Market → Municipal Hall</td>
                  <td>3.8 km</td>
                  <td>₱53</td>
                  <td>Collected</td>
                  <td>
                    <span class="badge">Completed</span>
                  </td>
                </tr>
                <tr>
                  <td>Oct 29, 2026</td>
                  <td>Rizal St → Community Park</td>
                  <td>2.4 km</td>
                  <td>₱39</td>
                  <td>Collected</td>
                  <td>
                    <span class="badge">Completed</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <br />
        <a class="button outline" href="employee.php">Back</a>
      </div>
      <div class="bottomnav">
        <a class="button text" href="employee.php">⌂ Dashboard</a>
        <a class="button text" href="history.php">◷ History</a>
        <a class="button text" href="profile.php">♙ Profile</a>
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
