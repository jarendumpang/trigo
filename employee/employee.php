<?php

session_start();

include "../config/database.php";

// CHECK RIDER LOGIN
// if (
//     !isset($_SESSION['user_id']) ||
//     ($_SESSION['role'] ?? '') !== 'rider'
// ) {
//     exit("Unauthorized access. Rider login required.");
// }

if(!isset($_SESSION["role"]) || $_SESSION["role"] != "rider"){
  header("Location: ../login.php");
  exit;
}


$rider_id = (int) $_SESSION['user_id'];

// CSRF TOKEN
// if (empty($_SESSION['csrf_token'])) {
//     $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
// }

// ACCEPT BOOKING
$message = "";
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['accept_booking'])
) {

    // if (
    //     !hash_equals(
    //         $_SESSION['csrf_token'],
    //         $_POST['csrf_token'] ?? ''
    //     )
    // ) {
    //     exit("Invalid request.");
    // }

    $booking_id = (int)($_POST['booking_id'] ?? 0);

    $sql = "UPDATE bookings
            SET rider_id = ?,
                status = 'accepted',
                accepted_at = NOW()
            WHERE id = ?
            AND status = 'pending'
            AND rider_id IS NULL";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ii",
        $rider_id,
        $booking_id
    );

    $stmt->execute();

    if ($stmt->affected_rows === 1) {
        $_SESSION['message'] = "Booking accepted successfully!";
    } else {
        $_SESSION['message'] = "Booking is no longer available.";
    }

    header("Location: employee.php");
    exit;
}

// GET PENDING CUSTOMER BOOKINGS
$sql = "SELECT
            b.id,
            b.customer_id,
            b.rider_id,
            b.pickup_location,
            b.dropoff_location,
            b.distance_km,
            b.fare,
            b.status,
            b.created_at,
            u.full_name

        FROM bookings b

        INNER JOIN users u
            ON b.customer_id = u.id

        WHERE b.status = 'pending'
        AND b.rider_id IS NULL
        AND u.role = 'user'

        ORDER BY b.created_at ASC";

$result = $conn->query($sql);

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Employee dashboard</title>
    
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
           href="../index.php"><span class="mark">↗</span>TriGo<b>.</b></a>
        <nav>
        <a class="button outline" href="../logout.php">Log out</a>
        </nav>
      </header>
      <div class="phonearea">
        <div class="pagehead row between">
          <div>
            <h1>Hello, Carlo ☀</h1>
            <p>Let’s make today a good ride.</p>
          </div>
          <!-- <button
            class="outline"
           
           type="button" data-availability>
            ○ Offline
          </button> -->
        </div>
        <div class="grid cards">
          <div class="card">
            <span class="small muted">Today’s earnings</span>
            <div class="stat">₱0</div>
          </div>
          <div class="card">
            <span class="small muted">Completed rides</span>
            <div class="stat">0</div>
          </div>
          <div class="card">
            <span class="small muted">Customer rating</span>
            <div class="stat">4.9 ★</div>
          </div>
          <div class="card">
            <span class="small muted">Payment pending</span>
            <div class="stat">₱0</div>
          </div>
        </div>
        
        <div class="ridegrid" style="margin-top: 24px">
          
          <div class="card booking-queue-card shadow-sm border-0">

              <div class="card-body p-4">

                  <!-- HEADER -->
                  <div class="d-flex justify-content-between align-items-center mb-4">

                      <div>
                          <h4 class="queue-title mt-0">
                              Booking Requests
                          </h4>

                          <p class="text-muted small mb-0 mt-0">
                              Available customer bookings
                          </p>
                          
                      </div>

                      <!-- <span class="queue-live-badge">
                          <span class="live-dot"></span>
                          Live Queue
                      </span> -->
                  </div>

                  <!-- SUCCESS MESSAGE -->
                  <?php if (isset($_SESSION['message'])): ?>

                      <div class="alert alert-info py-2 small">
                          <?= htmlspecialchars($_SESSION['message']) ?>
                      </div>

                      <?php unset($_SESSION['message']); ?>

                  <?php endif; ?>

                  <!-- BOOKING LIST -->
                  <?php if ($result && $result->num_rows > 0): ?>

                      <?php while ($booking = $result->fetch_assoc()): ?>

                          <div class="booking-request-card mb-3">

                              <!-- BOOKING HEADER -->
                              <div class="d-flex justify-content-between align-items-center mb-2">

                                  <h6 class="fw-semibold mb-0 mt-0">
                                      New Ride Request
                                  </h6>

                                  <span class="request-status">
                                      Pending
                                  </span>

                              </div>

                              <!-- CUSTOMER INFORMATION -->
                              <p class="booking-info">
                                  Booking #<?= (int)$booking['id'] ?>
                                  · Customer:
                                  <?= htmlspecialchars($booking['full_name']) ?>
                              </p>

                              <!-- PICKUP LOCATION -->
                              <div class="route-item">

                                  <i class="bi bi-record-circle pickup-marker"></i>

                                  <div>
                                      <small>PICKUP</small>

                                      <p>
                                          <?= htmlspecialchars($booking['pickup_location']) ?>
                                      </p>
                                  </div>

                              </div>

                              <!-- DROP-OFF LOCATION -->
                              <div class="route-item">

                                  <i class="bi bi-geo-alt dropoff-marker"></i>

                                  <div>
                                      <small>DROP-OFF</small>

                                      <p>
                                          <?= htmlspecialchars($booking['dropoff_location']) ?>
                                      </p>
                                  </div>

                              </div>

                              <hr class="booking-divider">

                              <!-- DISTANCE AND FARE -->
                              <div class="d-flex justify-content-between align-items-center">

                                  <!-- DISTANCE -->
                                  <div class="distance-info">

                                      <small>DISTANCE</small>

                                      <p>
                                          <?= $booking['distance_km'] !== null
                                              ? number_format((float)$booking['distance_km'], 1) . ' km'
                                              : 'Not available' ?>
                                      </p>

                                  </div>

                                  <!-- FARE -->
                                  <div class="fare-info text-end">

                                      <small>ESTIMATED FARE</small>

                                      <h3>
                                          ₱<?= number_format((float)$booking['fare'], 2) ?>
                                      </h3>

                                  </div>

                              </div>

                              <!-- ACCEPT BOOKING -->
                              <form method="POST" class="mt-3">

                                  <input
                                      type="hidden"
                                      name="booking_id"
                                      value="<?= (int)$booking['id'] ?>"
                                  >

                                  <button
                                      type="submit"
                                      name="accept_booking"
                                      class="btn accept-request-btn w-100"
                                  >
                                      <i class="bi bi-check-circle me-1"></i>
                                      Accept Booking
                                  </button>

                              </form>

                          </div>

                      <?php endwhile; ?>

                  <?php else: ?>

                      <!-- EMPTY QUEUE -->
                      <div class="empty-booking-state">

                          <i class="bi bi-car-front display-3"></i>

                          <h5 class="mt-3">
                              No Available Bookings
                          </h5>

                          <p>
                              New customer requests will appear here
                              when they book a ride.
                          </p>

                          <button
                              type="button"
                              class="btn btn-sm btn-outline-primary mt-2"
                              onclick="window.location.reload()"
                          >
                              <i class="bi bi-arrow-clockwise me-1"></i>
                              Refresh Queue
                          </button>

                      </div>

                  <?php endif; ?>

              </div>

          </div>



          <!-- <div class="card">
            <h2>You’re offline.</h2>
            <p>Switch online when you’re ready to receive requests.</p>
            <button
              class=""
             
             type="button" data-availability>
              Go online
            </button>
          </div> -->
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
        </div>
        <div class="notice">
          Approved rider demo account. Production must enforce single-rider
          acceptance, booking ownership, role permissions, and valid status
          transitions on the server.
        </div>
      </div>
      <div class="bottomnav">
        <a class="button text" href="employee.php">⌂ Dashboard</a><a class="button text" href="history.php">◷ History</a><a class="button text" href="profile.php">♙ Profile</a>
      </div>
    </div>
    
  <dialog id="ui-dialog" aria-labelledby="ui-dialog-title">
  <h2 id="ui-dialog-title"></h2>
  <div id="ui-dialog-content"></div>
  <div class="row"><button type="button" data-close-dialog>Close</button></div>
</dialog>
</body>
</html>
