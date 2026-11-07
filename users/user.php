<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/database.php";

// CUSTOMER LOGIN CHECK
if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'user'
) {
    header("Location: ../login.php");
    exit;
}

$customer_id = (int) $_SESSION['user_id'];

$bookingError = "";

// CSRF TOKEN
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// CONFIRM BOOKING
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['confirm_booking'])
) {

    // VERIFY CSRF
    if (
        !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token'] ?? ''
        )
    ) {
        $bookingError = "Invalid request. Please refresh the page.";
    } else {

        $pickup = trim($_POST['pickup_location'] ?? '');
        $dropoff = trim($_POST['dropoff_location'] ?? '');

        if ($pickup === '' || $dropoff === '') {

            $bookingError = "Please enter your pickup and drop-off locations.";

        } elseif (
            strlen($pickup) > 255 ||
            strlen($dropoff) > 255
        ) {

            $bookingError = "Location must not exceed 255 characters.";

        } else {

            /*
             * DEMO ROUTE VALUES
             * Replace these with your actual server-side
             * route and fare calculation when implemented.
             */

            $distance_km = 3.80;
            $fare = 65.00;

            // INSERT CUSTOMER BOOKING
            $sql = "INSERT INTO bookings (
                        customer_id,
                        rider_id,
                        pickup_location,
                        dropoff_location,
                        distance_km,
                        fare,
                        status
                    )
                    VALUES (?, NULL, ?, ?, ?, ?, 'pending')";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {

                $bookingError = "Unable to process booking. Please try again.";

            } else {

                $stmt->bind_param(
                    "issdd",
                    $customer_id,
                    $pickup,
                    $dropoff,
                    $distance_km,
                    $fare
                );

                if ($stmt->execute()) {

                    $stmt->close();

                    header("Location: user.php?booking=success");
                    exit;

                } else {

                    $bookingError = "Booking failed. Please try again.";

                }

                $stmt->close();
            }
        }
    }
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>TriGo — Book a ride</title>

    <!-- BOOTSTRAP GRID -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css"
    />

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="../assets/css/style.css" />

    <!-- EXISTING JAVASCRIPT -->
    <script src="../assets/js/ui.js" defer></script>
  </head>

  <body>
    <div id="app">
      <!-- HEADER -->
      <header>
        <a class="logo" href="../index.php">
          <span class="mark">↗</span>TriGo<b>.</b>
        </a>

        <nav>
          <a class="button text" href="../employee/employee.php">
            Rider preview
          </a>

          <a class="button text" href="../admin/admin.php"> Admin </a>

          <a class="button outline" href="../logout.php"> Log out </a>
        </nav>
      </header>

      <!-- MAIN CONTENT -->
      <div class="phonearea">
        <div class="ridegrid">
          <!-- MAP -->
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

          <!-- BOOKING PANEL -->
          <div class="card ridepanel">
            <div class="eyebrow">A GOOD DAY TO GO SOMEWHERE</div>

            <h1>Where are we headed?</h1>

            <p>A familiar ride, just a few taps away.</p>

            <!-- SUCCESS MESSAGE -->
            <?php if (isset($_GET['booking']) && $_GET['booking'] ===
            'success'): ?>

            <div class="notice booking-success" role="status">
              <strong>Booking submitted successfully!</strong>

              <br />

              Your ride request is now waiting for a rider.
            </div>

            <?php endif; ?>

            <!-- ERROR MESSAGE -->
            <?php if ($bookingError !== ''): ?>

            <div class="notice booking-error" role="alert">
              <?= htmlspecialchars($bookingError) ?>
            </div>

            <?php endif; ?>

            <!-- BOOKING FORM -->
            <form method="POST" action="user.php" id="bookingForm">
              <!-- CSRF TOKEN -->
              <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
              />

              <!-- PICKUP LOCATION -->
              <label for="pickup"> ● Pickup location </label>

              <input
                type="text"
                id="pickup"
                name="pickup_location"
                value="San Isidro Public Market"
                maxlength="255"
                required
              />

              <!-- DROP-OFF LOCATION -->
              <label for="dropoff"> ◇ Drop-off location </label>

              <input
                type="text"
                id="dropoff"
                name="dropoff_location"
                value="Municipal Hall"
                maxlength="255"
                required
              />

              <!-- CURRENT LOCATION -->
              <button class="text" type="button" data-location>
                ⌖ Use my current location
              </button>

              <!-- ROUTE INFORMATION -->
              <div class="notice">
                Editable demo pins · Fixed sample road route: Public Market →
                Municipal Hall, 3.8 km. Live location search is not connected.
              </div>

              <!-- PREVIEW BUTTON -->
              <button class="full" type="button" id="previewBooking">
                Preview route & fare →
              </button>

              <!-- ROUTE PREVIEW RESULT -->
              <div
                class="notice route-preview"
                id="routePreview"
                style="display: none"
              >
                <strong>Route Preview</strong>

                <div class="preview-details">
                  <div>
                    <small>ESTIMATED DISTANCE</small>
                    <h3>3.8 km</h3>
                  </div>

                  <div>
                    <small>ESTIMATED FARE</small>
                    <h3>₱65.00</h3>
                  </div>
                </div>

                <p class="small">
                  Please review your booking details before confirming.
                </p>
              </div>

              <!-- CONFIRM BOOKING BUTTON -->
              <button
                class="full confirm-booking"
                type="submit"
                name="confirm_booking"
                id="confirmBooking"
                style="display: none"
              >
                Confirm Booking →
              </button>
            </form>

            <p class="small">Your fare is shown before you confirm.</p>
          </div>
        </div>

        <!-- FOOTER -->
        <div class="row between" style="margin-top: 15px">
          <span class="muted small">
            Local routes. Familiar faces. A community on the move.
          </span>

          <button class="text" type="button" data-states>Demo states</button>
        </div>
      </div>

      <!-- BOTTOM NAVIGATION -->
      <div class="bottomnav">
        <a class="button text" href="user.php"> ⌂ Book a ride </a>

        <a class="button text" href="history.php"> ◷ History </a>

        <a class="button text" href="profile.php"> ♙ Profile </a>
      </div>
    </div>

    <!-- EXISTING DIALOG -->
    <dialog id="ui-dialog" aria-labelledby="ui-dialog-title">
      <h2 id="ui-dialog-title"></h2>

      <div id="ui-dialog-content"></div>

      <div class="row">
        <button type="button" data-close-dialog>Close</button>
      </div>
    </dialog>

    <!-- BOOKING PREVIEW JAVASCRIPT -->
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("bookingForm");

        const pickup = document.getElementById("pickup");

        const dropoff = document.getElementById("dropoff");

        const previewButton = document.getElementById("previewBooking");

        const previewBox = document.getElementById("routePreview");

        const confirmButton = document.getElementById("confirmBooking");

        // PREVIEW ROUTE AND FARE
        previewButton.addEventListener("click", function () {
          if (!form.reportValidity()) {
            return;
          }

          // SHOW SAMPLE ROUTE DETAILS
          previewBox.style.display = "block";

          // SHOW CONFIRM BUTTON
          confirmButton.style.display = "block";

          previewButton.textContent = "Update route & fare →";
        });

        // RESET PREVIEW WHEN LOCATIONS CHANGE
        function resetPreview() {
          previewBox.style.display = "none";

          confirmButton.style.display = "none";

          previewButton.textContent = "Preview route & fare →";
        }

        pickup.addEventListener("input", resetPreview);

        dropoff.addEventListener("input", resetPreview);
      });
    </script>
  </body>
</html>
