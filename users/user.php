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

  // MAP PICKER SERVICES. Existing bookings table and fare policy are unchanged.
  function mapJson($data, $status = 200) {
      http_response_code($status);
      header('Content-Type: application/json; charset=utf-8');
      header('Cache-Control: no-store');
      echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
      exit;
  }

  function mapFetch($url, $geocoding = false) {
      if (!function_exists('curl_init')) {
          throw new RuntimeException('Enable the PHP cURL extension to use location search and routing.');
      }
      // Shared cache/lock keeps this application's public geocoder usage below 1 request/second.
      $lock = null;
      $cache = sys_get_temp_dir() . '/trigo-geocode-' . hash('sha256', $url) . '.json';
      if ($geocoding) {
          $lock = fopen(sys_get_temp_dir() . '/trigo-geocode.lock', 'c+');
          if (!$lock || !flock($lock, LOCK_EX)) {
              throw new RuntimeException('Location search is busy. Please try again.');
          }
      }
      try {
          if ($geocoding && is_file($cache) && filemtime($cache) > time() - 86400) {
              $cached = json_decode(file_get_contents($cache), true);
              if (is_array($cached)) return $cached;
          }
          if ($geocoding) {
              rewind($lock);
              $last = (float) stream_get_contents($lock);
              $wait = 1.1 - (microtime(true) - $last);
              if ($wait > 0) usleep((int) ($wait * 1000000));
              rewind($lock);
              ftruncate($lock, 0);
              fwrite($lock, (string) microtime(true));
              fflush($lock);
          }
          $curl = curl_init($url);
          curl_setopt_array($curl, [
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_CONNECTTIMEOUT => 5,
              CURLOPT_TIMEOUT => 20,
              CURLOPT_USERAGENT => getenv('TRIGO_MAP_USER_AGENT') ?: 'TriGoBooking/1.0',
              CURLOPT_HTTPHEADER => ['Accept: application/json', 'Accept-Language: en'],
          ]);
          $body = curl_exec($curl);
          $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
          curl_close($curl);
          if ($body === false || $status !== 200) {
              throw new RuntimeException('The map service is unavailable. Please try again shortly.');
          }
          $data = json_decode($body, true);
          if (!is_array($data)) throw new RuntimeException('The map service returned an invalid response.');
          if ($geocoding) file_put_contents($cache, $body, LOCK_EX);
          return $data;
      } finally {
          if ($lock) { flock($lock, LOCK_UN); fclose($lock); }
      }
  }

  function mapLocation($label, $lat, $lng) {
      $lat = filter_var($lat, FILTER_VALIDATE_FLOAT);
      $lng = filter_var($lng, FILTER_VALIDATE_FLOAT);
      if ($lat === false || $lng === false || abs($lat) > 90 || abs($lng) > 180) {
          throw new RuntimeException('Invalid map coordinates. Please pick another location.');
      }
      $token = bin2hex(random_bytes(16));
      $location = ['token' => $token, 'label' => $label, 'lat' => $lat, 'lng' => $lng];
      $_SESSION['map_locations'][$token] = $location;
      // Bound session size while keeping recent search results and selected pins.
      $_SESSION['map_locations'] = array_slice($_SESSION['map_locations'], -80, null, true);
      return $location;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['map_action'])) {
      if (!is_string($_POST['csrf_token'] ?? null) ||
          !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
          mapJson(['error' => 'Your session expired. Refresh the page.'], 403);
      }
      try {
          $action = $_POST['map_action'];
          $geocoder = rtrim(getenv('TRIGO_GEOCODER_URL') ?: 'https://nominatim.openstreetmap.org', '/');
          $router = rtrim(getenv('TRIGO_ROUTER_URL') ?: 'https://router.project-osrm.org', '/');
          if ($action === 'search') {
              $query = trim(is_string($_POST['query'] ?? null) ? $_POST['query'] : '');
              if (strlen($query) < 3 || strlen($query) > 255) {
                  mapJson(['error' => 'Enter a location between 3 and 255 characters.'], 422);
              }
              $rows = mapFetch($geocoder . '/search?' . http_build_query([
                  'format' => 'jsonv2', 'q' => $query, 'limit' => 5,
              ]), true);
              $results = [];
              foreach ($rows as $row) {
                  if (isset($row['display_name'], $row['lat'], $row['lon'])) {
                      $results[] = mapLocation($row['display_name'], $row['lat'], $row['lon']);
                  }
              }
              mapJson(['results' => $results]);
          }
          if ($action === 'reverse') {
              $lat = filter_var($_POST['lat'] ?? null, FILTER_VALIDATE_FLOAT);
              $lng = filter_var($_POST['lng'] ?? null, FILTER_VALIDATE_FLOAT);
              if ($lat === false || $lng === false || abs($lat) > 90 || abs($lng) > 180) {
                  mapJson(['error' => 'Invalid map coordinates.'], 422);
              }
              $row = mapFetch($geocoder . '/reverse?' . http_build_query([
                  'format' => 'jsonv2', 'lat' => $lat, 'lon' => $lng, 'zoom' => 18,
              ]), true);
              // Keep the selected coordinates, rather than moving the pin to the nearest named building.
              $label = $row['display_name'] ?? sprintf('Pinned location (%.6f, %.6f)', $lat, $lng);
              mapJson(['location' => mapLocation($label, $lat, $lng)]);
          }
          if ($action === 'route') {
              $pickupToken = is_string($_POST['pickup_token'] ?? null) ? $_POST['pickup_token'] : '';
              $dropoffToken = is_string($_POST['dropoff_token'] ?? null) ? $_POST['dropoff_token'] : '';
              $from = $_SESSION['map_locations'][$pickupToken] ?? null;
              $to = $_SESSION['map_locations'][$dropoffToken] ?? null;
              if (!$from || !$to) mapJson(['error' => 'Select both locations again.'], 422);
              if (strlen($from['label']) > 255 || strlen($to['label']) > 255) {
                  mapJson(['error' => 'This address exceeds your database limit of 255 bytes. Pick a nearby named place or a more specific search result.'], 422);
              }
              $coordinates = $from['lng'] . ',' . $from['lat'] . ';' . $to['lng'] . ',' . $to['lat'];
              $data = mapFetch($router . '/route/v1/driving/' . $coordinates . '?overview=full&geometries=geojson&alternatives=false&radiuses=100;100');
              $route = $data['routes'][0] ?? null;
              if (($data['code'] ?? '') !== 'Ok' || !$route || !isset($route['distance'], $route['geometry'])) {
                  mapJson(['error' => 'No drivable road route was found. Move the pins closer to accessible roads.'], 422);
              }
              $km = (float) $route['distance'] / 1000;
              if (!is_finite($km) || round($km, 2) <= 0) mapJson(['error' => 'Choose two different locations with a drivable route of at least 0.01 km.'], 422);
              $token = bin2hex(random_bytes(24));
              $_SESSION['booking_routes'][$token] = [
                  'pickup' => $from['label'], 'dropoff' => $to['label'],
                  'distance_km' => $km, 'expires' => time() + 1800,
              ];
              // $_SESSION['booking_routes'] = array_slice($_SESSION['booking_routes'], -10, null, true);
              // mapJson(['token' => $token, 'distance_km' => $km, 'fare' => 65.00, 'geometry' => $route['geometry']]);
              $distance = round($km, 2);
              $fare = $distance * 25;

              mapJson([
                  'token' => $token,  
                  'distance_km' => $distance,
                  'fare' => $fare,
                  'geometry' => $route['geometry']
              ]);
          }
          mapJson(['error' => 'Unknown map action.'], 400);
      } catch (Throwable $error) {
          mapJson(['error' => $error instanceof RuntimeException ? $error->getMessage() : 'Unable to load the map request. Please try again.'], 503);
      }
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
          $routeToken = is_string($_POST['route_token'] ?? null) ? $_POST['route_token'] : '';
          $submittedRoute = $_SESSION['booking_routes'][$routeToken] ?? null;


          if ($pickup === '' || $dropoff === '') {

              $bookingError = "Please enter your pickup and drop-off locations.";

          } elseif (
              strlen($pickup) > 255 ||
              strlen($dropoff) > 255
          ) {

              $bookingError = "Location must not exceed 255 characters.";

          } elseif (
              !$submittedRoute || $submittedRoute['expires'] < time() ||
              $submittedRoute['pickup'] !== $pickup || $submittedRoute['dropoff'] !== $dropoff
          ) {
              $bookingError = "Please select both map locations and preview the route again.";
          } else {
              // Distance comes from the server-side road route, never a submitted distance field.
              $distance_km = round($submittedRoute['distance_km'], 2);
              $fare = $distance_km * 25; // Preserve your existing fare until you define a fare formula.

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
                      unset($_SESSION['booking_routes'][$routeToken]);

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

    // $submittedRoute = $_SESSION['booking_routes'][$routeToken] ?? null;

    // $fare = mysqli_query($conn, "SELECT SUM(per_km_rate) AS fare FROM fare_settings");
    // $fare_row = mysqli_fetch_assoc($fare);
    // $fare = (int)$fare_row['fare'];

    // $total = $submittedRoute * 25;
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

      <!-- LIVE MAP -->
      <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
      <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>

      <!-- MAIN CSS -->
      <link rel="stylesheet" href="../assets/css/style.css" />

      <!-- EXISTING JAVASCRIPT -->
      <script src="../assets/js/ui.js" defer></script>
      <style>
        .map.booking-map-shell { display: flex; flex-direction: column; min-width: 0; min-height: 600px; overflow: hidden; }
        .map-tools { padding: 12px; background: #fff; color: #084d73; }
        .map-tools label { display: block; margin: 0 0 5px; font-size: 13px; font-weight: 600; }
        .map-tools select { width: 100%; padding: 9px; margin-bottom: 8px; border: 1px solid #d6e0dc; border-radius: 7px; background: #fff; color: inherit; }
        .map-search-row { display: flex; gap: 8px; }
        .map-search-row input { min-width: 0; flex: 1; padding: 10px; border: 1px solid #d6e0dc; border-radius: 7px; }
        .map-search-row button { flex: none; padding: 9px 14px; border-radius: 7px; }
        #mapHelp { margin: 8px 0 0; font-size: 12px; }
        #mapSearchResults { max-height: 190px; overflow-y: auto; }
        #mapSearchResults button { display: block; width: 100%; padding: 10px; text-align: left; background: #fff; color: #084d73; border: 0; border-bottom: 1px solid #e1e9e5; border-radius: 0; white-space: normal; }
        #mapSearchResults button:hover, #mapSearchResults button:focus-visible { background: #e4f0f2; }
        #bookingMap { width: 100%; min-height: 420px; flex: 1; z-index: 0; }
        .booking-pin { background: transparent; border: 0; }
        .booking-pin span { display: grid; place-items: center; width: 30px; height: 30px; border: 2px solid white; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); background: #075885; color: white; box-shadow: 0 2px 6px #0005; font: bold 13px sans-serif; }
        .booking-pin.dropoff span { background: #96651a; }
        .location-coordinates { display: block; min-height: 16px; margin: 4px 0 12px; font-size: 11px; color: #587385; }
        #mapStatus { margin-top: 16px; }
        .map-tools button:focus-visible { outline: 3px solid #075885; outline-offset: -3px; }
        @media (max-width: 767px) { .map.booking-map-shell { min-height: 470px; } #bookingMap { min-height: 330px; } }
      </style>
    </head>

    <body>
      <div id="app">
        <!-- HEADER -->
        <header>
          <a class="logo" href="../index.php">
            <span class="mark">↗</span>TriGo<b>.</b>
          </a>

          <nav>
            <!-- <a class="button text" href="../employee/employee.php">
              Rider preview
            </a> -->

            <!-- <a class="button text" href="../admin/admin.php"> Admin </a> -->

            <a class="button outline" href="../logout.php"> Log out </a>
          </nav>
        </header>

        <!-- MAIN CONTENT -->
        <div class="phonearea">
          <div class="ridegrid">
            <!-- MAP -->
            <div class="map booking-map-shell">
              <div class="map-tools">
                <label for="mapTarget">Choose a location for</label>
                <select id="mapTarget"><option value="pickup">Pickup location</option><option value="dropoff">Drop-off location</option></select>
                <label for="mapSearch">Search location</label>
                <div class="map-search-row">
                  <input id="mapSearch" type="search" placeholder="Street, landmark, or town" maxlength="255" autocomplete="off" />
                  <button id="mapSearchButton" type="button">Search</button>
                </div>
                <div id="mapSearchResults" aria-label="Location search results"></div>
                <p id="mapHelp">Click the map to set pickup, or search for an address.</p>
              </div>
              <div id="bookingMap" role="region" aria-label="Pickup and drop-off location map"></div>
              <!-- <svg
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
              </div> -->
            </div>

            <!-- BOOKING PANEL -->
            <div class="card ridepanel">
              <div class="eyebrow">A GOOD DAY TO GO SOMEWHERE</div>

              <h1>Ready to book a Ride</h1>

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
                  value=""
                  placeholder="Search or select pickup on the map"
                  autocomplete="off"
                  maxlength="255"
                  required
                />

                <small id="pickupCoordinates" class="location-coordinates"></small>

                <!-- DROP-OFF LOCATION -->
                <label for="dropoff"> ◇ Drop-off location </label>

                <input
                  type="text"
                  id="dropoff"
                  name="dropoff_location"
                  value=""
                  placeholder="Search or select drop-off on the map"
                  autocomplete="off"
                  maxlength="255"
                  required
                />

                <small id="dropoffCoordinates" class="location-coordinates"></small>
                <input type="hidden" name="route_token" id="routeToken" value="" />

                <!-- CURRENT LOCATION -->
                <button class="text" type="button" data-location>
                  ⌖ Use my current location
                </button>

                <!-- ROUTE INFORMATION -->
                <div class="notice" id="mapStatus" role="status" aria-live="polite">
                  Choose pickup and drop-off by searching or clicking the map. Road distance will appear once both are selected.
                </div>
                <noscript><div class="notice" role="alert">Enable JavaScript to select locations and book a ride.</div></noscript>

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
                      <h3 id="distanceValue">— km</h3>
                    </div>

                    <div>
                      <small>COMPUTED FARE (25/KM)</small>
                      <h3 id='fareValue'><?php echo $fare;?></h3>
                    </div>
                  </div>

                  <p class="small">
                    Please check both pins before confirming. Road distance is estimated; GPS and address coverage can vary.
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

      <!-- LIVE MAP AND BOOKING PREVIEW JAVASCRIPT -->
      <script>
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bookingForm');
    const fields = { pickup: document.getElementById('pickup'), dropoff: document.getElementById('dropoff') };
    const status = document.getElementById('mapStatus');
    const results = document.getElementById('mapSearchResults');
    const search = document.getElementById('mapSearch');
    const preview = document.getElementById('routePreview');
    const confirm = document.getElementById('confirmBooking');
    const previewButton = document.getElementById('previewBooking');
    const routeToken = document.getElementById('routeToken');
    const target = document.getElementById('mapTarget');
    const locations = { pickup: null, dropoff: null };
    const markers = {};
    const versions = { pickup: 0, dropoff: 0 };
    let revision = 0;
    let routeLayer;
    let searchVersion = 0;
    let routingRevision = -1;
    const tell = message => { status.textContent = message; };
    const csrf = form.elements.csrf_token.value;

    // Capture only our controls so the existing ui.js demo handlers cannot replace real locations/routes.
    function ownClick(element, handler) {
      element.addEventListener('click', event => {
        event.preventDefault();
        event.stopImmediatePropagation();
        handler();
      }, true);
    }
    function invalidate() {
      revision++;
      routeToken.value = '';
      preview.style.display = 'none';
      confirm.style.display = 'none';
      confirm.disabled = true;
      previewButton.textContent = 'Preview route & fare →';
      if (routeLayer) { routeLayer.remove(); routeLayer = null; }
    }
    invalidate();
    if (!window.L) {
      tell('The map could not load. Check your internet connection and refresh.');
      previewButton.disabled = true;
      return;
    }
    // Initial view only; this never sets a booking location. Search is worldwide.
    const map = L.map('bookingMap').setView([12.8797, 121.7740], 6);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);
    if (window.ResizeObserver) new ResizeObserver(() => map.invalidateSize()).observe(document.getElementById('bookingMap'));

    async function api(action, parameters = {}) {
      const response = await fetch(form.action, {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ map_action: action, csrf_token: csrf, ...parameters }),
      });
      let data;
      try { data = await response.json(); } catch (_) { throw new Error('Your session may have expired. Refresh the page and sign in again.'); }
      if (!response.ok || data.error) throw new Error(data.error || 'Unable to load map data. Try again.');
      return data;
    }
    function setTarget(kind) {
      if (target.value !== kind) { searchVersion++; results.replaceChildren(); }
      target.value = kind;
      document.getElementById('mapHelp').textContent = `Click the map to set ${kind === 'pickup' ? 'pickup' : 'drop-off'}, or search for an address.`;
    }
    function select(kind, location) {
      if (new TextEncoder().encode(location.label).length > 255) {
        tell('This address is longer than your database allows. Select a more specific named place.');
        return;
      }
      versions[kind]++;
      invalidate();
      locations[kind] = location;
      fields[kind].value = location.label;
      fields[kind].setCustomValidity('');
      document.getElementById(kind + 'Coordinates').textContent = `${Number(location.lat).toFixed(6)}, ${Number(location.lng).toFixed(6)}`;
      if (markers[kind]) markers[kind].remove();
      const icon = L.divIcon({
        className: 'booking-pin ' + kind,
        html: `<span>${kind === 'pickup' ? 'P' : 'D'}</span>`, iconSize: [32, 32], iconAnchor: [16, 32],
      });
      markers[kind] = L.marker([location.lat, location.lng], { icon, draggable: true, title: kind === 'pickup' ? 'Pickup' : 'Drop-off' }).addTo(map);
      markers[kind].bindPopup(document.createTextNode(location.label));
      markers[kind].on('dragstart', () => {
        versions[kind]++;
        locations[kind] = null;
        invalidate();
      });
      markers[kind].on('dragend', () => resolvePin(kind, markers[kind].getLatLng()));
      results.replaceChildren();
      map.setView([location.lat, location.lng], 16);
      tell(`${kind === 'pickup' ? 'Pickup' : 'Drop-off'} selected. You can drag its pin to adjust it.`);
      if (locations.pickup && locations.dropoff) updateRoute();
      else if (kind === 'pickup') setTarget('dropoff');
    }
    async function resolvePin(kind, point) {
      const version = ++versions[kind];
      locations[kind] = null;
      fields[kind].value = '';
      fields[kind].setCustomValidity('Choose this location on the map or from search results.');
      document.getElementById(kind + 'Coordinates').textContent = '';
      if (markers[kind]) { markers[kind].remove(); delete markers[kind]; }
      invalidate();
      tell('Finding the address for your pin…');
      try {
        const data = await api('reverse', { lat: point.lat, lng: point.lng });
        if (versions[kind] === version) select(kind, data.location);
      } catch (error) { if (versions[kind] === version) tell(error.message); }
    }
    async function updateRoute() {
      if (!locations.pickup || !locations.dropoff) {
        tell('Choose both locations from the map or search results first.');
        return;
      }
      const currentRevision = revision;
      if (routingRevision === currentRevision) return;
      routingRevision = currentRevision;
      tell('Calculating road distance…');
      try {
        const data = await api('route', { pickup_token: locations.pickup.token, dropoff_token: locations.dropoff.token });
        const distanceKm = Number(data.distance_km) || 0;
        const fare = Number(data.fare) || 0;
        if (currentRevision !== revision) return;
        routeToken.value = data.token;
        document.getElementById('distanceValue').textContent = `${data.distance_km.toFixed(2)} km`;
        document.getElementById('fareValue').textContent = `₱${Number(data.fare).toFixed(2)}`;
        routeLayer = L.geoJSON(data.geometry, { style: { color: '#075885', weight: 5 } }).addTo(map);
        map.fitBounds(routeLayer.getBounds().extend(markers.pickup.getLatLng()).extend(markers.dropoff.getLatLng()), { padding: [35, 35], maxZoom: 17 });
        preview.style.display = 'block';
        confirm.style.display = 'block';
        confirm.disabled = false;
        previewButton.textContent = 'Update route & fare →';
        tell(`Road distance: ${data.distance_km.toFixed(2)} km. Review both pins before confirming.`);
      } catch (error) { if (currentRevision === revision) tell(error.message); }
      finally { if (routingRevision === currentRevision) routingRevision = -1; }
    }
    async function findLocations() {
      const query = search.value.trim();
      const kind = target.value;
      const version = ++searchVersion;
      results.replaceChildren();
      if (query.length < 3) { tell('Enter at least 3 characters to search.'); return; }
      tell('Searching for locations…');
      try {
        const data = await api('search', { query });
        if (version !== searchVersion) return;
        data.results.forEach(location => {
          const button = document.createElement('button');
          button.type = 'button';
          button.textContent = location.label;
          button.addEventListener('click', () => { searchVersion++; select(kind, location); });
          results.append(button);
        });
        tell(data.results.length ? `Choose a result for ${kind === 'pickup' ? 'pickup' : 'drop-off'}.` : 'No locations found. Try a town, street, or landmark.');
      } catch (error) { if (version === searchVersion) tell(error.message); }
    }
    target.addEventListener('change', () => { searchVersion++; results.replaceChildren(); setTarget(target.value); });
    search.addEventListener('input', () => { searchVersion++; results.replaceChildren(); });
    Object.entries(fields).forEach(([kind, field]) => {
      field.addEventListener('focus', () => setTarget(kind));
      field.addEventListener('input', () => {
        versions[kind]++;
        locations[kind] = null;
        invalidate();
        field.setCustomValidity('Select a matching search result or pick this location on the map.');
        document.getElementById(kind + 'Coordinates').textContent = '';
        if (markers[kind]) { markers[kind].remove(); delete markers[kind]; }
        setTarget(kind);
        search.value = field.value;
        tell('Press Search and choose an address, or click the map.');
      });
      field.addEventListener('keydown', event => {
        if (event.key === 'Enter') { event.preventDefault(); setTarget(kind); search.value = field.value; findLocations(); }
      });
    });
    map.on('click', event => resolvePin(target.value, event.latlng));
    ownClick(document.getElementById('mapSearchButton'), findLocations);
    search.addEventListener('keydown', event => { if (event.key === 'Enter') { event.preventDefault(); findLocations(); } });
    ownClick(previewButton, () => { invalidate(); updateRoute(); });
    ownClick(document.querySelector('[data-location]'), () => {
      const kind = target.value;
      if (!navigator.geolocation || !window.isSecureContext) { tell('Current location needs HTTPS or localhost and browser location permission. You can still search or click the map.'); return; }
      const version = ++versions[kind];
      locations[kind] = null;
      fields[kind].value = '';
      invalidate();
      tell('Requesting your current location…');
      navigator.geolocation.getCurrentPosition(position => {
        if (version !== versions[kind]) return;
        resolvePin(kind, { lat: position.coords.latitude, lng: position.coords.longitude });
      }, error => {
        if (version !== versions[kind]) return;
        tell(error.code === 1 ? 'Location permission was denied. Search or click the map instead.' : 'Could not get your location. Search or click the map instead.');
      }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
    });
    form.addEventListener('submit', event => {
      event.stopImmediatePropagation();
      if (!routeToken.value || !locations.pickup || !locations.dropoff ||
          fields.pickup.value !== locations.pickup.label || fields.dropoff.value !== locations.dropoff.label) {
        event.preventDefault();
        invalidate();
        tell('Select both locations and wait for the road distance before confirming.');
      }
    }, true);
    setTarget('pickup');
  });

      </script>
    </body>
  </html>
