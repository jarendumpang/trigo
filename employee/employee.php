<?php

session_start();

include "../config/database.php";

// CHECK RIDER LOGIN
if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'rider'
) {
    exit("Unauthorized access. Rider login required.");
}

// if(!isset($_SESSION["role"]) || $_SESSION["role"] != "rider"){
//   header("Location: ../login.php");
//   exit;
// }


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

// GET THIS RIDER'S ACCEPTED BOOKING (for the live map)
$active_booking = null;

$sql_active = "SELECT
                   b.id,
                   b.pickup_location,
                   b.dropoff_location,
                   b.distance_km,
                   u.full_name

               FROM bookings b

               INNER JOIN users u
                   ON b.customer_id = u.id

               WHERE b.rider_id = ?
               AND b.status = 'accepted'

               ORDER BY b.accepted_at DESC
               LIMIT 1";

$stmt_active = $conn->prepare($sql_active);

if ($stmt_active) {
    $stmt_active->bind_param("i", $rider_id);
    $stmt_active->execute();
    $res_active = $stmt_active->get_result();
    $active_booking = $res_active ? $res_active->fetch_assoc() : null;
    $stmt_active->close();
}

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
    <?php if ($active_booking): ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <?php endif; ?>
    
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
            <?php if ($active_booking): ?>
            <div id="live-map" style="position:absolute;inset:0;z-index:1;isolation:isolate;"></div>
            <div id="live-map-status" style="position:absolute;left:12px;bottom:12px;z-index:2;background:#fff;padding:6px 10px;border-radius:8px;font-size:12px;box-shadow:0 1px 4px rgba(0,0,0,.2);">
              Booking #<?= (int)$active_booking['id'] ?> · Locating route…
            </div>
            <?php endif; ?>
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

<?php if ($active_booking): ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
  var booking = <?= json_encode([
      'id'      => (int)$active_booking['id'],
      'customer'=> $active_booking['full_name'],
      'pickup'  => $active_booking['pickup_location'],
      'dropoff' => $active_booking['dropoff_location'],
  ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

  var statusEl = document.getElementById('live-map-status');
  function setStatus(t) { if (statusEl) statusEl.textContent = t; }

  var map = L.map('live-map', { zoomControl: true }).setView([15.1450, 120.5887], 13);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  function geocode(query) {
    var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=ph&q=' +
              encodeURIComponent(query);
    return fetch(url, { headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (!d || !d.length) { return null; }
        return [parseFloat(d[0].lat), parseFloat(d[0].lon)];
      });
  }

  function pin(latlng, color, label) {
    return L.circleMarker(latlng, {
      radius: 10, color: '#fff', weight: 4, fillColor: color, fillOpacity: 1
    }).addTo(map).bindPopup(label);
  }

  var pickupPos = null;

  geocode(booking.pickup)
    .then(function (p) {
      pickupPos = p;
      // Nominatim allows ~1 request per second
      return new Promise(function (res) { setTimeout(res, 1100); });
    })
    .then(function () { return geocode(booking.dropoff); })
    .then(function (dropPos) {
      if (!pickupPos && !dropPos) {
        setStatus('Could not locate pickup or drop-off on the map.');
        return;
      }

      var bounds = [];

      if (pickupPos) {
        pin(pickupPos, '#143027',
            '<b>Pickup</b><br>' + escapeHtml(booking.customer) + '<br>' + escapeHtml(booking.pickup));
        bounds.push(pickupPos);
      }
      if (dropPos) {
        pin(dropPos, '#e5b838', '<b>Drop-off</b><br>' + escapeHtml(booking.dropoff));
        bounds.push(dropPos);
      }

      if (pickupPos && dropPos) {
        var routeUrl = 'https://router.project-osrm.org/route/v1/driving/' +
          pickupPos[1] + ',' + pickupPos[0] + ';' + dropPos[1] + ',' + dropPos[0] +
          '?overview=full&geometries=geojson';

        fetch(routeUrl)
          .then(function (r) { return r.json(); })
          .then(function (d) {
            var line;
            if (d && d.routes && d.routes.length) {
              line = L.geoJSON(d.routes[0].geometry, {
                style: { color: '#087f5b', weight: 6, opacity: 0.9 }
              }).addTo(map);
              map.fitBounds(line.getBounds(), { padding: [40, 40] });
              setStatus('Booking #' + booking.id + ' · ' +
                        (d.routes[0].distance / 1000).toFixed(1) + ' km route');
            } else {
              throw new Error('no route');
            }
          })
          .catch(function () {
            L.polyline([pickupPos, dropPos], { color: '#087f5b', weight: 4, dashArray: '8 8' }).addTo(map);
            map.fitBounds(bounds, { padding: [40, 40] });
            setStatus('Booking #' + booking.id + ' · Pickup to drop-off');
          });
      } else {
        map.fitBounds(bounds, { padding: [40, 40], maxZoom: 16 });
        setStatus('Booking #' + booking.id + ' · Only one location could be found');
      }
    })
    .catch(function () {
      setStatus('Map lookup failed. Check your connection.');
    });

  function escapeHtml(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
})();
</script>
<?php endif; ?>
</body>
</html>