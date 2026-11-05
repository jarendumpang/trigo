<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Your community. Your ride.</title>
    
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css"
    />
    <link rel="stylesheet" href="assets/css/style.css" />
    
  <script src="assets/js/ui.js" defer></script>
</head>
  <body>
    
    <div id="app">
      <header>
        <a class="logo"
           href="index.php"><span class="mark">↗</span>TriGo<b>.</b></a>
        <nav>
          <a href="index.php">Overview</a>
          <a href="index.php#how">How It Works</a>

          <a href="landing/franchise.php">Tricycle Franchising</a>
          <a href="landing/apply.php">Apply as a Rider</a>
          <a class="button outline" href="landing/login.php">Log in ↗</a>
        </nav>
      </header>
      <section class="hero">
        <div>
          <div class="eyebrow">MOVING COMMUNITIES FORWARD</div>
          <h1>
            Your community.<br />Your ride.<br /><span style="color: var(--g)"
              >TriGo.</span
            >
          </h1>
          <p>
            A better way to get around. Book a trusted local tricycle, grow your
            livelihood, and keep your community moving.
          </p>
          <div class="row">
            <a class="button " href="user/user.php">Book a Ride ↗</a><a class="button outline" href="landing/apply.php">
              Apply as a Rider
            </a>
          </div>
          <small
            >✓ Trusted local riders &nbsp; ✓ Transparent fares &nbsp; ✓
            Community first</small
          >
        </div>
        <div class="trike">
          <img src="Philippine Tricycle Infographic Ride.png" alt="" >
        </div>
      </section>

      <section class="section">
        <div class="eyebrow">ONE PLATFORM. MORE POSSIBILITIES.</div>
        <div class="row between">
          <h2>Built for every part of the journey.</h2>
          <span class="muted">Simple, connected, community-driven.</span>
        </div>
        <div class="grid services">
          <div class="card">
            <div class="icon">↗</div>
            <h3>For passengers</h3>
            <p>
              Your everyday destinations, one easy ride away. See your fare
              before you go.
            </p>
            <a class="button text" href="user/user.php">
              Book your first ride →
            </a>
          </div>
          <div class="card">
            <div class="icon">♙</div>
            <h3>For riders</h3>
            <p>
              Turn familiar roads into new opportunities. Ride on your terms,
              earn with confidence.
            </p>
            <a class="button text" href="landing/apply.php">
              Start your application →
            </a>
          </div>
          <div class="card">
            <div class="icon">▤</div>
            <h3>For franchise operators</h3>
            <p>
              Manage applications, vehicles, and renewals in one organized
              place.
            </p>
            <a class="button text" href="landing/franchise.php">
              Explore franchising →
            </a>
          </div>
        </div>
      </section>

      <section class="section" id="how" style="background: white">
        <div class="eyebrow">HOW IT WORKS</div>
        <h2>Your next step is a simple one.</h2>
        <div class="grid split">
          <div>
            <h3>Going somewhere?</h3>
            <div class="step"><b>01</b>Choose your pickup and destination</div>
            <div class="step">
              <b>02</b>Preview your route and transparent fare
            </div>
            <div class="step">
              <b>03</b>Match with a rider and enjoy your ride
            </div>
          </div>
          <div>
            <h3>Ready to join the road?</h3>
            <div class="step"><b>01</b>Create a rider account</div>
            <div class="step">
              <b>02</b>Send your license, vehicle details, and documents
            </div>
            <div class="step">
              <b>03</b>Get approved and start accepting rides
            </div>
          </div>
        </div>
      </section>
      <section class="section">
        <div class="card row between">
          <div>
            <div class="eyebrow">A STRONGER LOCAL TRANSPORT NETWORK</div>
            <h2>Your franchise, moving forward.</h2>
            <p>
              Clear requirements. Simple applications. Transparent status
              tracking.
            </p>
          </div>
          <a class="button " href="landing/franchise.php">
            Explore franchising ↗
          </a>
        </div>
      </section>

      <footer class="footer">
        <div>
          <a class="logo"
             href="index.php"><span class="mark">↗</span>TriGo<b>.</b></a>
          <p>Your community. Your ride.</p>
        </div>
        <div>
          <strong>Here to help</strong>
          <p>
            support@trigo.example<br />Sample contact · San Isidro, Philippines
          </p>
        </div>
        <div>
          <button
            class="outline"
           
           type="button" data-title="Support" data-message="Demo contact: support@trigo.example">
            Support
          </button>
          <p>
            <a
             
               href="#" data-message="This frontend does not save your form data. Your PHP application will provide its privacy policy." data-title="Privacy policy">Privacy policy</a>
            ·
            <a
             
               href="#" data-message="This frontend is a classroom preview. Your PHP application will provide its terms." data-title="Terms">Terms</a>
          </p>
        </div>
      </footer>
    </div>
    
  <dialog id="ui-dialog" aria-labelledby="ui-dialog-title">
  <h2 id="ui-dialog-title"></h2>
  <div id="ui-dialog-content"></div>
  <div class="row"><button type="button" data-close-dialog>Close</button></div>
</dialog>
</body>
</html>
