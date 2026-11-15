<?php
session_start();
include "../config/database.php";

// Get selected rider ID
$riderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$riderId) {
    exit("Invalid rider ID.");
}

// Fetch selected rider
$stmt = $conn->prepare("
    SELECT
        id,
        rider_id,
        full_name,
        number,
        email,
        license_num,
        license_exp,
        model,
        plate_number
    FROM riders
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $riderId);
$stmt->execute();

$rider = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$rider) {
    exit("Rider not found.");
}

// Secure output
function clean($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Generate initials
$parts = preg_split('/\s+/', trim($rider['full_name']));
$initials = '';

foreach ($parts as $part) {
    if ($part !== '') {
        $initials .= strtoupper(substr($part, 0, 1));
    }
}

$initials = substr($initials, 0, 2);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TriGo — Rider Profile</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css"
    >

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div id="app">

    <div class="app">

        <aside class="sidebar">

            <a class="logo">
                <span class="mark">↗</span>TriGo<b>.</b>
            </a>

            <div class="eyebrow" style="padding-left:14px;color:#9baa9b">
                WORKSPACE
            </div>

            <a class="sideitem" href="admin.php">◫ Overview</a>

            <a class="sideitem" href="rider-applications.php">
                ♧ Rider Applications
            </a>

            <a class="sideitem" href="franchise-management.php">
                ▤ Franchise Management
            </a>

            <a class="sideitem active" href="employees.php">
                ♙ Employees
            </a>

            <a class="sideitem" href="rides.php">↗ Rides</a>

            <a class="sideitem" href="income-reports.php">
                ▥ Income Reports
            </a>

            <a class="sideitem" href="fare-settings.php">
                ⚙ Fare Settings
            </a>

            <a class="sideitem" href="settings.php">
                ☷ Settings
            </a>
            
            <div class="sidebottom">
            <div class="notice">
              <strong>Here for your community.</strong>
              <br />A smoother day starts with TriGo.
            </div>
            <!-- <a class="sideitem" href="../logout.php">↶ Back to website</a> -->
            <div class="row">
              <div class="avatar">G3</div>
              <div>
                <strong class="small">Group 3</strong>
                <br />
                <span class="small muted">Administrator</span>
              </div>
            </div>
          </div>

        </aside>


        <main class="main">

            <div class="topbar row between">

                <strong>Rider Profile</strong>

                <a class="button outline" href="../logout.php">
                    Log out
                </a>

            </div>


            <div class="content">

                <div class="pagehead">

                    <h1>Rider Information</h1>

                    <p>
                        Complete information of the approved rider.
                    </p>

                </div>


                <div class="card formbox">

                    <div class="avatar big">
                        <?php echo clean($initials); ?>
                    </div>

                    <h2>
                        <?php echo clean($rider['full_name']); ?>
                    </h2>

                    <span class="badge">
                        Active · Rider
                    </span>

                    <br><br>

                    <h3>Personal Information</h3>

                    <label>Full Name</label>
                    <input
                        type="text"
                        value="<?php echo clean($rider['full_name']); ?>"
                        readonly
                    >

                    <label>Phone Number</label>
                    <input
                        type="text"
                        value="<?php echo clean($rider['number']); ?>"
                        readonly
                    >

                    <label>Email Address</label>
                    <input
                        type="email"
                        value="<?php echo clean($rider['email']); ?>"
                        readonly
                    >

                    <br>

                    <h3>Driver's License Information</h3>

                    <label>License Number</label>
                    <input
                        type="text"
                        value="<?php echo clean($rider['license_num']); ?>"
                        readonly
                    >

                    <label>License Expiration</label>
                    <input
                        type="date"
                        value="<?php echo clean($rider['license_exp']); ?>"
                        readonly
                    >

                    <br>

                    <h3>Vehicle Information</h3>

                    <label>Vehicle Model</label>
                    <input
                        type="text"
                        value="<?php echo clean($rider['model']); ?>"
                        readonly
                    >

                    <label>Plate Number</label>
                    <input
                        type="text"
                        value="<?php echo clean($rider['plate_number']); ?>"
                        readonly
                    >

                    <br><br>

                    <a
                        class="button outline"
                        href="employees.php"
                    >
                        Back to Employees
                    </a>

                </div>

            </div>

        </main>

    </div>

</div>

</body>
</html>