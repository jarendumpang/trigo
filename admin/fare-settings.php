<?php

  session_start();
  include "../config/database.php";

  if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin"){
    header("Location: ../index.php");
    exit;
  } 

  $base_fare = mysqli_query($conn, "SELECT SUM(base_fare) AS base_fare FROM fare_settings");
  $income_row = mysqli_fetch_assoc($base_fare);
  $base_fare = (int)$income_row['base_fare'];

  $included_distance = mysqli_query($conn, "SELECT SUM(included_distance) AS included_distance FROM fare_settings");
  $income_row = mysqli_fetch_assoc($included_distance);
  $included_distance = (int)$income_row['included_distance'];

  $per_km_rate = mysqli_query($conn, "SELECT SUM(per_km_rate) AS per_km_rate FROM fare_settings");
  $income_row = mysqli_fetch_assoc($per_km_rate);
  $per_km_rate = (int)$income_row['per_km_rate'];

  $company_commission = mysqli_query($conn, "SELECT SUM(company_commission) AS company_commission FROM fare_settings");
  $income_row = mysqli_fetch_assoc($company_commission);
  $company_commission = (int)$income_row['company_commission'];


  $message = "";

    if(isset($_POST["update"])){
        $base_fare= $_POST["base_fare"];
        $included= $_POST["included"];
        $pk_rate= $_POST["pk_rate"];
        $company_commission=$_POST["company_commission"];

        $sql = "UPDATE fare_settings SET base_fare= '$base_fare',
                                included_distance = '$included ',
                                per_km_rate= '$pk_rate',
                                company_commission='$company_commission'
                               ";


        if(mysqli_query($conn, $sql)){
            header("Location: fare-settings.php");
            exit;
        }
        else{
            $message = "Could not Update";
        }
 
    }


?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Fare Settings</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css"
    />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <script src="../assets/js/ui.js" defer></script>
  </head>
  <body>
    <div id="app">
      <div class="app">
        <aside class="sidebar">
          <a class="logo">
            <span class="mark">↗</span>TriGo<b>.</b>
          </a>
          <div class="eyebrow" style="padding-left: 14px; color: #9baa9b">
            WORKSPACE
          </div>
          <a class="sideitem" href="admin.php"> <span>◫</span>Overview</a>
          <a class="sideitem" href="rider-applications.php">
            <span>♧</span>Rider Applications<span class="badge amber">3</span>
          </a>
          <a class="sideitem" href="franchise-management.php">
            <span>▤</span>Franchise Management</a
          >
          <a class="sideitem" href="employees.php"> <span>♙</span>Employees</a>
          <a class="sideitem" href="rides.php"> <span>↗</span>Rides</a>
          <a class="sideitem" href="income-reports.php">
            <span>▥</span>Income Reports</a
          >
          <a class="sideitem active" href="fare-settings.php">
            <span>⚙</span>Fare Settings</a
          >
          <a class="sideitem" href="settings.php"> <span>☷</span>Settings</a>
          <div class="sidebottom">
            <div class="notice">
              <strong>Here for your community.</strong>
              <br />A smoother day starts with TriGo.
            </div>
            <!-- <a class="sideitem" href="../index.php">↶ Back to website</a> -->
            <div class="row">
              <div class="avatar">G3</div>
              <div>
                <strong class="small">Jamie dela Cruz</strong>
                <br />
                <span class="small muted">Administrator</span>
              </div>
            </div>
          </div>
        </aside>
        <main class="main">
          <div class="topbar row between">
            <strong>Fare Settings</strong>
            <div class="row">
              <span class="small muted">San Isidro, Philippines</span>
              <span class="avatar">G3</span>
              <a class="button outline" href="../logout.php">Log out</a>
            </div>
          </div>
          <div class="content">
            <div class="pagehead row between">
              <div>
                <h1>Clear fares. Fair journeys.</h1>
                <p>Configure example rates for the booking preview.</p>
              </div>
            </div>

            <!-- PHP integration: add action/method and server validation; UI preview never sends data. -->
            <form data-frontend-form method="POST" class="card" style="max-width: 650px">
            <form data-frontend-form class="card" style="max-width: 650px">
              <div class="grid split">
                <div>
                  <label>Base fare</label>
                  <input
                    id="base"
                    type="number"
                    name="base_fare"
                    min="0"
                    step="0.1"
                    required
                    value="<?php echo $base_fare; ?>"
                    value="25"
                    
                  />
                </div>
                <div>
                  <label>Included distance (km)</label>
                  <input
                    id="included"
                    type="number"
                    name="included"
                    min="0"
                    step="0.1"
                    required
                    value="<?php echo $included_distance; ?>"
                    value="1"
                  />
                </div>
                <div>
                  <label>Per-kilometer rate</label>
                  <input
                    id="rate"
                    type="number"
                    name="pk_rate"
                    min="0"
                    step="0.1"
                    required
                    value="<?php echo $per_km_rate; ?>"
                    value="10"
                  />
                </div>
                <div>
                  <label>Company commission (%)</label>
                  <input
                    id="commission"
                    type="number"
                    name="company_commission"
                    min="0"
                    max="100"
                    step="0.1"
                    required
                    value="<?php echo $company_commission; ?>"
                    value="15"
                  />
                </div>
              </div>
              <div class="notice">
                Fare = base fare + max(0, route km − included km) × rate.<br />3.8
                km example → ₱53. Revised prices require customer confirmation.
              </div>
              
              <button type="submit" name="update" data-fare-preview>
                Review & save rates
              </button>
            </form>
          </div>
        </main>
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