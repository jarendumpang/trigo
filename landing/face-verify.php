<?php
session_start();
require_once '../includes/rider-face.php';
header('Cache-Control: no-store');
if (rider_face_pending_id() === null) {
    rider_face_redirect('login.php');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TriGo — rider face verification</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css" />
  <link rel="stylesheet" href="../assets/css/style.css" />
  <script src="../assets/js/rider-face.js" defer></script>
  <style>
    .face-camera { display:block; width:100%; max-height:320px; background:#111; border-radius:5px; transform:scaleX(-1); }
    .face-message { margin:12px 0; }
    .face-message[data-error="true"] { color:#842029; background:#f8d7da; border:1px solid #f5c2c7; padding:12px; border-radius:5px; }
  </style>
</head>
<body>
  <div id="app">
    <header>
      <a class="logo" href="../index.php"><span class="mark">↗</span>TriGo<b>.</b></a>
      <nav>
        <a href="../index.php">Overview</a>
        <a href="../index.php#how">How It Works</a>
        <a href="franchise.php">Tricycle Franchising</a>
        <a href="apply.php">Apply as a Rider</a>
        <a class="button outline" href="login.php">Log in ↗</a>
      </nav>
    </header>
    <div class="card formbox">
      <div class="eyebrow">RIDER TIME-IN</div>
      <h2>Verify your face.</h2>
      <p>Face the camera in good lighting. Your face must match your registered face before time-in is saved.</p>
      <video class="face-camera" id="face-camera" autoplay muted playsinline></video>
      <p class="face-message" id="face-message" role="status" aria-live="polite">Start your camera to continue.</p>
      <form id="face-form" action="face-verify-api.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['rider_face_csrf'], ENT_QUOTES, 'UTF-8') ?>" />
        <button class="full" type="button" id="start-camera">Start camera</button>
        <button class="full" type="submit" id="verify-face" disabled>Verify face &amp; time in →</button>
      </form>
      <noscript>Enable JavaScript and camera access to verify your face.</noscript>
    </div>
  </div>
</body>
</html>
