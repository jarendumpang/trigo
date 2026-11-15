
<?php
session_start();
include "../config/database.php";

// Count pending rider applications
$total = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM rider_application
     WHERE status = 'pending'"
);

$pendingCount = mysqli_fetch_assoc($total)['total'] ?? 0;

// Get all approved riders
$riders = mysqli_query(
    $conn,
    "SELECT *
     FROM riders
     ORDER BY id DESC"
);

if (!$riders) {
    die("Failed to load employees: " . mysqli_error($conn));
}

// Generate initials from full name
function getInitials($name) {
    $parts = preg_split('/\s+/', trim($name));
    $initials = '';

    foreach ($parts as $part) {
        if ($part !== '') {
            $initials .= strtoupper(substr($part, 0, 1));
        }
    }

    return substr($initials, 0, 2);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>TriGo — Employees</title>

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

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <a class="logo">
            <span class="mark">↗</span>TriGo<b>.</b>
        </a>

        <div class="eyebrow" style="padding-left: 14px; color: #9baa9b">
            WORKSPACE
        </div>

        <a class="sideitem" href="admin.php">
            <span>◫</span>Overview
        </a>

        <a class="sideitem" href="rider-applications.php">
            <span>♧</span>Rider Applications
            <span class="badge amber">
                <?php echo (int)$pendingCount; ?>
            </span>
        </a>

        <a class="sideitem" href="franchise-management.php">
            <span>▤</span>Franchise Management
        </a>

        <a class="sideitem active" href="employees.php">
            <span>♙</span>Employees
        </a>

        <a class="sideitem" href="rides.php">
            <span>↗</span>Rides
        </a>

        <a class="sideitem" href="income-reports.php">
            <span>▥</span>Income Reports
        </a>

        <a class="sideitem" href="fare-settings.php">
            <span>⚙</span>Fare Settings
        </a>

        <a class="sideitem" href="settings.php">
            <span>☷</span>Settings
        </a>

        <div class="sidebottom">

            <div class="notice">
                <strong>Here for your community.</strong>
                <br />A smoother day starts with TriGo.
            </div>

            <div class="row">
                <div class="avatar">JD</div>

                <div>
                    <strong class="small">Jamie dela Cruz</strong>
                    <br />
                    <span class="small muted">Administrator</span>
                </div>
            </div>

        </div>

    </aside>


    <!-- MAIN CONTENT -->
    <main class="main">

        <div class="topbar row between">

            <strong>Employees</strong>

            <div class="row">
                <span class="small muted">
                    San Isidro, Philippines
                </span>

                <span class="avatar">G3</span>

                <a class="button outline" href="../logout.php">
                    Log out
                </a>
            </div>

        </div>


        <div class="content">

            <div class="pagehead row between">

                <div>
                    <h1>The people behind every ride.</h1>

                    <p>
                        Approved riders and your operations team.
                    </p>
                </div>

            </div>


            <!-- EMPLOYEE CARDS -->
            <div class="grid employees">

                <?php if (mysqli_num_rows($riders) > 0) { ?>

                    <?php while ($row = mysqli_fetch_assoc($riders)) { ?>

                        <?php
                            $name = htmlspecialchars(
                                $row['full_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            $number = htmlspecialchars(
                                $row['number'],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            $plate = htmlspecialchars(
                                $row['plate_number'],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            $initials = getInitials($row['full_name']);
                        ?>

                        <div class="card">

                            <div class="avatar big">
                                <?php echo htmlspecialchars($initials); ?>
                            </div>

                            <h3>
                                <?php echo $name; ?>
                            </h3>

                            <span class="badge">
                                Active · Rider
                            </span>

                            <p>
                                <?php echo $number; ?>
                                <br />

                                Assigned tricycle:
                                <?php echo $plate; ?>
                            </p>

                           <a
                                class="button outline"
                                href="rider-profile.php?id=<?php echo (int)$row['id']; ?>"
                            >
                                View profile
                            </a>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <div class="card">
                        <h3>No approved riders yet.</h3>

                        <p>
                            Accepted rider applications will
                            automatically appear here.
                        </p>

                        <a
                            class="button outline"
                            href="rider-applications.php"
                        >
                            View Applications
                        </a>
                    </div>

                <?php } ?>

            </div>

        </div>

    </main>

</div>
</div>


<dialog id="ui-dialog" aria-labelledby="ui-dialog-title">

    <h2 id="ui-dialog-title"></h2>

    <div id="ui-dialog-content"></div>

    <div class="row">
        <button type="button" data-close-dialog>
            Close
        </button>
    </div>

</dialog>

</body>
</html>
