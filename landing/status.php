<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — status</title>
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
      <div class="card formbox center">
        <div class="icon">◷</div>
        <div class="eyebrow">RIDER APPLICATION</div>
        <h2>Your application is in good hands.</h2>
        <span class="badge amber">Pending review</span>
        <p>Our team is reviewing your submission. Check here for an update.</p>
        <div class="notice">
          Rider: Pending review<br />Franchise: Not applied
        </div>
        <button
          class="outline"
          type="button"
          data-title="Application status"
          data-message="This page displays sample status only. Connect PHP to retrieve the current application status."
        >
          Refresh status
        </button>
        <a class="button text" href="../admin/admin.php"
          >Preview admin review</a
        >
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
