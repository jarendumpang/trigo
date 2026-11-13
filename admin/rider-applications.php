
<?php
session_start();
include "../config/database.php";
require_once "../config/email.php";

$search = $_GET['search'] ?? "";

// Update applicant status
if (isset($_POST['update_status'])) {

    $id = (int) $_POST['applicant_id'];
    $newStatus = $_POST['status'];
    $allowedStatuses = ['pending', 'accepted', 'rejected'];

    if (in_array($newStatus, $allowedStatuses, true)) {

        // Get applicant information
        $stmt = mysqli_prepare(
            $conn,
            "SELECT full_name, email, status
             FROM rider_application
             WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $applicant = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($applicant) {

            $oldStatus = $applicant['status'];

            if ($oldStatus !== $newStatus) {

                // Update database
                $update = mysqli_prepare(
                    $conn,
                    "UPDATE rider_application SET status = ? WHERE id = ?"
                );

                mysqli_stmt_bind_param($update, "si", $newStatus, $id);
                $updated = mysqli_stmt_execute($update);
                mysqli_stmt_close($update);

                if ($updated) {

                    // Send email only for accepted or rejected
                    if ($newStatus === 'accepted' || $newStatus === 'rejected') {

                        $emailSent = sendRiderStatusEmail(
                            $applicant['email'],
                            $applicant['full_name'],
                            $newStatus
                        );

                        if ($emailSent) {
                            $_SESSION['flash'] = "Status updated and email sent successfully!";
                        } else {
                            $_SESSION['flash'] = "Status updated, but email failed to send. Check your email settings.";
                        }

                    } else {
                        $_SESSION['flash'] = "Applicant status updated to Pending.";
                    }

                } else {
                    $_SESSION['flash'] = "Failed to update applicant status.";
                }

            } else {
                $_SESSION['flash'] = "No status change detected. No email was sent.";
            }

        } else {
            $_SESSION['flash'] = "Applicant not found.";
        }

    } else {
        $_SESSION['flash'] = "Invalid status selected.";
    }

    $redirectSearch = $_POST['search'] ?? '';

    header("Location: rider-applications.php" .
        ($redirectSearch !== '' ? '?search=' . urlencode($redirectSearch) : '')
    );
    exit;
}

// Display notification
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

// Search applicants safely
$sql = "SELECT * FROM rider_application
        WHERE full_name LIKE ?
        AND status = 'pending'
        ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);
$searchValue = "%" . $search . "%";

mysqli_stmt_bind_param($stmt, "s", $searchValue);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$total = mysqli_num_rows($result);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — Rider Applications</title>

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

        <a class="sideitem" href="admin.php"><span>◫</span>Overview</a>

        <a class="sideitem active" href="rider-applications.php">
            <span>♧</span>Rider Applications
            <span class="badge amber"><?php echo $total; ?></span>
        </a>

        <a class="sideitem" href="franchise-management.php">
            <span>▤</span>Franchise Management
        </a>

        <a class="sideitem" href="employees.php"><span>♙</span>Employees</a>
        <a class="sideitem" href="rides.php"><span>↗</span>Rides</a>
        <a class="sideitem" href="income-reports.php"><span>▥</span>Income Reports</a>
        <a class="sideitem" href="fare-settings.php"><span>⚙</span>Fare Settings</a>
        <a class="sideitem" href="settings.php"><span>☷</span>Settings</a>

        <div class="sidebottom">
            <div class="notice">
                <strong>Here for your community.</strong>
                <br />A smoother day starts with TriGo.
            </div>

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
            <strong>Rider Applications</strong>

            <div class="row">
                <span class="small muted">San Isidro, Philippines</span>
                <span class="avatar">G3</span>
                <a class="button outline" href="../logout.php">Log out</a>
            </div>
        </div>

        <div class="content">

            <div class="pagehead row between">
                <div>
                    <h1>New opportunities start here.</h1>
                    <p>Review rider and franchise applications independently.</p>
                </div>
            </div>

            <?php if ($flash !== '') { ?>
                <div class="notice" style="margin-bottom: 15px;">
                    <?php echo htmlspecialchars($flash); ?>
                </div>
            <?php } ?>

            <div class="card tablecard">

                <div class="tabletitle row between">
                    <div>
                        <h3>Applications awaiting your review</h3>
                        <span class="small muted">
                            Help our community get on the road.
                        </span>
                    </div>

                    <form method="GET" action="rider-applications.php">
                        <input
                            class="search"
                            type="text"
                            name="search"
                            placeholder="Search applicants..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        />

                        <button type="submit">Search</button>
                        <a href="rider-applications.php">Clear</a>
                    </form>
                </div>

                <div class="tablewrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Application</th>
                                <th>Submitted</th>
                                <th>Documents</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($row["full_name"]); ?>
                                </td>

                                <td>Rider</td>

                                <td>
                                    <?php echo htmlspecialchars($row["submitted_at"]); ?>
                                </td>

                                <td>
                                    <button
                                        class="text"
                                        type="button"
                                        data-title="Application documents"
                                        data-message="The original contains 3 sample documents. Connect your PHP endpoint to load the actual applicant files."
                                    >
                                        ▧ 3 documents
                                    </button>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["status"]); ?>
                                </td>

                                <td>
                                    <form method="POST" action="rider-applications.php">
                                        <input
                                            type="hidden"
                                            name="applicant_id"
                                            value="<?php echo (int) $row['id']; ?>"
                                        />

                                        <input
                                            type="hidden"
                                            name="search"
                                            value="<?php echo htmlspecialchars($search); ?>"
                                        />

                                        <select name="status" required>
                                            <option value="pending"
                                                <?php echo $row['status'] === 'pending' ? 'selected' : ''; ?>>
                                                Pending
                                            </option>

                                            <option value="accepted"
                                                <?php echo $row['status'] === 'accepted' ? 'selected' : ''; ?>>
                                                Hired / Accepted
                                            </option>

                                            <option value="rejected"
                                                <?php echo $row['status'] === 'rejected' ? 'selected' : ''; ?>>
                                                Rejected
                                            </option>
                                        </select>

                                        <button type="submit" name="update_status">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        <?php } ?>

                        </tbody>
                    </table>
                </div>
            </div>
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

<?php
mysqli_stmt_close($stmt);
?>
