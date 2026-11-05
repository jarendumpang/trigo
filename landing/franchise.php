<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — franchise</title>
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
          <a href="../index.php">Overview</a>
          <a href="../index.php#how">How It Works</a>
          <a href="franchise.php">Tricycle Franchising</a>
          <a href="apply.php">Apply as a Rider</a>
          <a class="button outline" href="login.php">Log in ↗</a>
        </nav>
      </header>
      <section class="section">
        <div class="eyebrow">TRICYCLE FRANCHISING</div>
        <h1>A clearer road to your franchise.</h1>
        <p>Apply, track your review, and join your local transport network.</p>
        <div class="grid split">
          <div class="card">
            <h3>Prepare your requirements</h3>
            <p>✓ Valid government-issued identification</p>
            <p>✓ Business registration and local permit</p>
            <p>✓ Vehicle official receipt and certificate of registration</p>
            <p>✓ Proof of vehicle ownership</p>
            <p>✓ Applicable local franchise requirements</p>
            <a class="button" href="franchise-application.php"
              >Start franchise application →</a
            >
            <p class="small">
              Sample requirements; confirm with your local franchise office.
            </p>
          </div>
          <div class="card">
            <h3>Already applied?</h3>
            <p>
              Track your review and requests for additional documents. Franchise
              approval is separate from rider approval.
            </p>
            <label>Application reference</label>
            <input placeholder="TG-F-2026-0142" />
            <a class="button outline" href="status.php">Track application</a>
          </div>
        </div>
      </section>
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
