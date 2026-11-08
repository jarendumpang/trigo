<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — register</title>
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
      <div class="card formbox">
        <div class="eyebrow">JOIN THE TRIGO COMMUNITY</div>
        <h2>Create your account</h2>
        <p>Your next ride starts here.</p>

        <!-- PHP integration: add action/method and server validation; UI preview never sends data. -->
        <form data-frontend-form>
          <div class="grid split">
            <div>
              <label>Full name</label>
              <input id="name" required placeholder="Juan dela Cruz" />
            </div>
            <div>
              <label>Phone</label>
              <input type="tel" required placeholder="09XX XXX XXXX" />
            </div>
          </div>
          <label>Email</label>
          <input type="email" required placeholder="you@example.com" />
          <label>Password</label>
          <input
            type="password"
            minlength="8"
            required
            placeholder="At least 8 characters"
          />
          <label class="row">
            <input style="width: auto; margin: 0" type="checkbox" required /> I
            agree to the demo terms and privacy policy.</label
          >
          <br />
          <button class="full" type="button" data-backend-action>
            Create account →
          </button>
        </form>
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
