<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Book a ride</title>
    
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
        <a class="logo"
           href="../index.html"><span class="mark">↗</span>TriGo<b>.</b></a>
        <nav>
          <a class="button text" href="../employee/employee.html">Rider preview</a><a class="button text" href="../admin/admin.html">Admin</a><a class="button outline" href="../index.html">Log out</a>
        </nav>
      </header>
      <div class="phonearea">
        <div class="ridegrid">
          <div class="map">
            <svg
              viewBox="0 0 600 600"
              preserveAspectRatio="none"
              role="img"
              aria-label="Simulated San Isidro street map"
            >
              <rect width="600" height="600" fill="#e7ede2" />
              <g fill="#d2e1c9">
                <rect x="50" y="40" width="140" height="140" rx="15" />
                <rect x="390" y="340" width="180" height="190" rx="40" />
                <rect x="300" y="40" width="180" height="110" rx="15" />
              </g>
              <g stroke="white" stroke-width="20" fill="none">
                <path
                  d="M-20 130L620 480M-20 360L620 75M70 0L300 620M390 -20L160 620M-20 520L600 210"
                />
              </g>
              <g fill="#82947b" font-size="12" font-family="sans-serif">
                <text x="70" y="95">SAN ISIDRO</text>
                <text x="390" y="410">Community Park</text>
                <text x="345" y="85">Municipal Hall</text>
                <text x="65" y="448">Public Market</text>
                <text x="375" y="245" transform="rotate(28 375 245)">
                  Rizal Street
                </text>
              </g>
              <path
                d="M140 410L227 367 185 264 355 185 388 110"
                stroke="#a4d6b2"
                stroke-width="16"
                fill="none"
              />
              <path
                d="M140 410L227 367 185 264 355 185 388 110"
                stroke="#087f5b"
                stroke-width="6"
                fill="none"
              />
              <circle
                cx="140"
                cy="410"
                r="10"
                fill="#087f5b"
                stroke="white"
                stroke-width="5"
              />
              <circle
                cx="388"
                cy="110"
                r="10"
                fill="#e5b838"
                stroke="white"
                stroke-width="5"
              />
            </svg>
            <div class="maplabel">
              ◉ San Isidro <span class="muted"> · Demo map</span>
            </div>
            <div class="credits">
              Simulated route · 3.8 km · Edit pins using location fields
            </div>
          </div>
          <div class="card ridepanel">
            <div class="eyebrow">A GOOD DAY TO GO SOMEWHERE</div>
            <h1>Where are we headed?</h1>
            <p>A familiar ride, just a few taps away.</p>
            
<!-- PHP integration: add action/method and server validation; UI preview never sends data. -->
<form data-frontend-form
             
            >
              <label>● Pickup location</label
              ><input
                id="pickup"
                required
                value="San Isidro Public Market"
              /><label>◇ Drop-off location</label
              ><input id="dropoff" required value="Municipal Hall" /><button
                class="text"
               
               type="button" data-location>
                ⌖ Use my current location
              </button>
              <div class="notice">
                Editable demo pins · Fixed sample road route: Public Market →
                Municipal Hall, 3.8 km. Live location search is not connected.
              </div>
              <button class="full" type="button" data-booking-preview>Preview route & fare →</button>
            </form>
            <p class="small">Your fare is shown before you confirm.</p>
          </div>
        </div>
        <div class="row between" style="margin-top: 15px">
          <span class="muted small"
            >Local routes. Familiar faces. A community on the move.</span
          ><button class="text" type="button" data-states>Demo states</button>
        </div>
      </div>
      <div class="bottomnav">
        <a class="button text" href="user.html">⌂ Book a ride</a><a class="button text" href="history.html">
          ◷ History</a><a class="button text" href="profile.html">♙ Profile</a>
      </div>
    </div>
    
  <dialog id="ui-dialog" aria-labelledby="ui-dialog-title">
  <h2 id="ui-dialog-title"></h2>
  <div id="ui-dialog-content"></div>
  <div class="row"><button type="button" data-close-dialog>Close</button></div>
</dialog>
</body>
</html>
