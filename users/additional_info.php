<?php
session_start();

// Order data (replace with real values from your session/database)
$total         = isset($_SESSION['order_total']) ? (float) $_SESSION['order_total'] : 330.00;
$contactNumber = isset($_SESSION['contact_number']) ? $_SESSION['contact_number'] : '+63 9097874914';
$driverNotes   = isset($_SESSION['driver_notes']) ? $_SESSION['driver_notes'] : '';
$location      = isset($_SESSION['delivery_location']) ? $_SESSION['delivery_location'] : '';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driverNotes   = trim($_POST['driver_notes'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? '');
    $location      = trim($_POST['location'] ?? '');

    if ($contactNumber === '' || !preg_match('/^\+?[0-9\s\-]{7,20}$/', $contactNumber)) {
        $errors[] = 'Please enter a valid contact number.';
    }
    if ($location === '') {
        $errors[] = 'Please enter your delivery location.';
    }

    if (empty($errors)) {
        $_SESSION['driver_notes']      = $driverNotes;
        $_SESSION['contact_number']    = $contactNumber;
        $_SESSION['delivery_location'] = $location;
        header('Location: review_order.php');
        exit;
    }
}

function e($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add More Details</title>
<style>
    :root {
        --blue: #23eecf;
        --text: #2b2b2b;
        --muted: #8a8a8a;
        --border: #e4e4e4;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: Roboto, "Segoe UI", Arial, sans-serif;
        color: var(--text);
        background: #fff;
        min-height: 100vh;
    }
    .page {
        max-width: 480px;
        margin: 0 auto;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    header {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        padding: 22px 16px 0;
    }
    .back {
        position: absolute;
        left: 16px;
        top: 22px;
        background: none;
        border: 0;
        cursor: pointer;
        padding: 4px;
        color: var(--text);
    }
    header h1 { font-size: 22px; font-weight: 500; }
    .steps {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin: 10px 0 24px;
    }
    .steps span {
        width: 64px;
        height: 4px;
        border-radius: 4px;
        background: var(--blue);
    }
    form {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .content { flex: 1; padding: 0 16px 140px; }

    .card {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 16px;
        border: 1px solid var(--border);
        border-radius: 12px;
        margin-bottom: 14px;
        background: #fff;
    }
    .row {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 16px;
        border-bottom: 1px solid var(--border);
    }
    .row:last-of-type { border-bottom: 0; }
    .icon { width: 28px; height: 28px; flex-shrink: 0; color: var(--blue); }
    .field { flex: 1; min-width: 0; }
    .field label { display: block; font-size: 13px; color: var(--muted); margin-top: 2px; }
    .field input, .field textarea {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        font: inherit;
        font-size: 18px;
        color: var(--text);
        resize: none;
    }
    .card .field input, .card .field textarea { font-size: 18px; }
    .field input::placeholder, .field textarea::placeholder { color: var(--muted); font-weight: 500; }
    .chev { color: var(--text); flex-shrink: 0; }
    .divider { height: 1px; background: var(--border); margin: 4px 16px; }

    .errors {
        background: #fdecea;
        color: #1ca4c0;
        border-radius: 10px;
        padding: 10px 14px;
        margin-bottom: 14px;
        font-size: 14px;
    }
    .errors li { margin-left: 18px; }

    .footer {
        position: fixed;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 480px;
        background: #fff;
        border-radius: 24px 24px 0 0;
        box-shadow: 0 -4px 16px rgba(0,0,0,.06);
        padding: 20px 16px 24px;
    }
    .total { display: flex; justify-content: space-between; align-items: flex-start; }
    .total .label { font-size: 18px; }
    .total .amount { font-size: 34px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .btn {
        width: 100%;
        margin-top: 14px;
        background: var(--blue);
        color: #fff;
        border: 0;
        border-radius: 12px;
        padding: 18px;
        font-size: 22px;
        font-weight: 700;
        cursor: pointer;
    }
    .btn:active { filter: brightness(.95); }
</style>
</head>
<body>
<div class="page">
    <header>
        <button type="button" class="back" onclick="history.back()" aria-label="Back">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <h1>Add More Details</h1>
    </header>
    <div class="steps"><span></span><span></span></div>

    <form method="post" action="">
        <div class="content">

            <?php if (!empty($errors)): ?>
                <ul class="errors">
                    <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <!-- Notes to driver -->
            <div class="card">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v12H9l-5 4z"/><line x1="8" y1="8" x2="16" y2="8"/><line x1="8" y1="12" x2="14" y2="12"/></svg>
                <div class="field">
                    <textarea name="driver_notes" rows="1" placeholder="Add notes to your driver"><?= e($driverNotes) ?></textarea>
                </div>
                <svg class="chev" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 6 15 12 9 18"/></svg>
            </div>

            <!-- Contact number -->
            <div class="row">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2z"/></svg>
                <div class="field">
                    <input type="tel" name="contact_number" value="<?= e($contactNumber) ?>" required>
                    <label>Order Contact Number</label>
                </div>
                <svg class="chev" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 6 15 12 9 18"/></svg>
            </div>

            <div class="divider"></div>

            <!-- Location (replaces coupon) -->
            <div class="row">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s7-6.5 7-12a7 7 0 0 0-14 0c0 5.5 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
                <div class="field">
                    <input type="text" name="location" value="<?= e($location) ?>" placeholder="Enter your location" required>
                    <label>Delivery Location</label>
                </div>
                <svg class="chev" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 6 15 12 9 18"/></svg>
            </div>
        </div>

        <div class="footer">
            <div class="total">
                <span class="label">Total</span>
                <span class="amount">₱<?= number_format($total, 2) ?>
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="11" x2="12" y2="17"/><circle cx="12" cy="7.5" r=".6" fill="currentColor"/></svg>
                </span>
            </div>
            <button type="submit" class="btn">Review Order</button>
        </div>
    </form>
</div>
</body>
</html>