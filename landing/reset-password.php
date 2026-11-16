
<?php
session_start();

include "../config/database.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "../PHPMailer/src/Exception.php";
require_once "../PHPMailer/src/PHPMailer.php";
require_once "../PHPMailer/src/SMTP.php";

$message = "";
$error = "";

$email = "";
$token = $_GET['token'] ?? $_POST['token'] ?? "";

// CHANGE THIS if your project has a different folder name.
$resetPage = "http://localhost/trigo-main/landing/reset-password.php";

// SMTP SETTINGS
// Use the same Gmail SMTP credentials as your existing config/email.php.
$smtpEmail = "trigocompany4@gmail.com";
$smtpPassword = "wuan grnr njhg jcsu";

// -------------------------------------------------
// STEP 1: REQUEST PASSWORD RESET
// -------------------------------------------------

if (isset($_POST['send_verification'])) {

    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {

        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($userId);

        $userExists = $stmt->fetch();
        $stmt->close();

        // Use a generic response to avoid revealing registered accounts.
        $message = "If this email is registered, a verification link will be sent.";

        if ($userExists) {

            $token = bin2hex(random_bytes(32));

            // Store only the SHA-256 hash of the token.
            $tokenHash = hash('sha256', $token);

            $expires = date(
                "Y-m-d H:i:s",
                time() + (30 * 60)
            );

            $stmt = $conn->prepare(
                "UPDATE users
                 SET reset_token_hash = ?,
                     reset_expires_at = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "ssi",
                $tokenHash,
                $expires,
                $userId
            );

            if ($stmt->execute()) {

                $stmt->close();

                $resetLink = $resetPage .
                    "?email=" . urlencode($email) .
                    "&token=" . urlencode($token);

                $mail = new PHPMailer(true);

                try {

                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $smtpEmail;
                    $mail->Password = $smtpPassword;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom($smtpEmail, 'TriGo');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'TriGo Password Reset Verification';

                    $mail->Body = "
                        <div style='font-family:Arial;padding:25px;'>
                            <h2 style='color:#198754;'>TriGo Password Reset</h2>

                            <p>We received a request to reset your TriGo password.</p>

                            <p>Click the button below to verify your email
                            and create a new password.</p>

                            <a href='$resetLink'
                               style='display:inline-block;
                               background:#198754;
                               color:white;
                               padding:14px 25px;
                               text-decoration:none;
                               border-radius:6px;'>
                               Verify Email
                            </a>

                            <p>This verification link expires in 30 minutes.</p>

                            <p>If you did not request this, ignore this email.</p>

                            <p>Thank you,<br>TriGo Team</p>
                        </div>
                    ";

                    $mail->AltBody =
                        "Verify your email and reset your password: $resetLink";

                    $mail->send();

                } catch (Exception $e) {

                    error_log("Password reset email error: " . $mail->ErrorInfo);

                    // Invalidate the token if email sending fails.
                    $stmt = $conn->prepare(
                        "UPDATE users
                         SET reset_token_hash = NULL,
                             reset_expires_at = NULL
                         WHERE id = ?"
                    );

                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $stmt->close();
                }

            } else {
                $stmt->close();
                error_log("Password reset database update failed.");
            }
        }
    }
}

// -------------------------------------------------
// STEP 2: VERIFY TOKEN AND UPDATE PASSWORD
// -------------------------------------------------

$email = $_GET['email'] ?? $_POST['email'] ?? $email;

$verified = false;

// Detect whether the user opened a verification link.
$linkSubmitted = (
    isset($_GET['token']) ||
    (
        isset($_POST['token']) &&
        !isset($_POST['send_verification'])
    )
);

if (
    !isset($_POST['send_verification']) &&
    !empty($token) &&
    !empty($email)
) {

    $stmt = $conn->prepare(
        "SELECT reset_token_hash, reset_expires_at
         FROM users
         WHERE email = ?
         LIMIT 1"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($storedHash, $expiresAt);

    if ($stmt->fetch()) {

        if (
            !empty($storedHash) &&
            !empty($expiresAt) &&
            hash_equals($storedHash, hash('sha256', $token)) &&
            strtotime($expiresAt) >= time()
        ) {
            $verified = true;
        }
    }

    $stmt->close();
}

// -------------------------------------------------
// STEP 3: SAVE NEW PASSWORD
// -------------------------------------------------

if (isset($_POST['update_password'])) {

    $password = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (!$verified) {
        $error = "Verification link is invalid or expired.";

    } elseif (strlen($password) < 8) {
        $error = "Password must contain at least 8 characters.";

    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";

    } else {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $conn->prepare(
            "UPDATE users
             SET password = ?,
                 reset_token_hash = NULL,
                 reset_expires_at = NULL
             WHERE email = ?"
        );

        $stmt->bind_param(
            "ss",
            $hashedPassword,
            $email
        );

        if ($stmt->execute() && $stmt->affected_rows === 1) {

            $stmt->close();

            header("Location: login.php?reset=success");
            exit;

        } else {
            $error = "Unable to update password. Please try again.";
            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TriGo - Forgot Password</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .formbox {
            max-width: 450px;
            margin: 70px auto;
        }

        .alert-success {
            background: #d1e7dd;
            color: #0f5132;
            padding: 12px;
            border-radius: 5px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 5px;
        }

        .full {
            width: 100%;
        }
    </style>

</head>

<body>

<div id="app">

    <header>
        <a class="logo" href="../index.php">
            <span class="mark">↗</span>TriGo<b>.</b>
        </a>
    </header>

    <div class="card formbox">

        <?php if ($verified): ?>

            <div class="eyebrow">EMAIL VERIFIED</div>

            <h2>Create New Password</h2>

            <p>Enter your new password below.</p>

        <?php else: ?>

            <div class="eyebrow">ACCOUNT RECOVERY</div>

            <h2>Forgot Password?</h2>

            <p>Enter your registered email address to receive a verification link.</p>

        <?php endif; ?>


        <?php if ($message != ""): ?>
            <div class="alert-success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>


        <?php if ($error != ""): ?>
            <div class="alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>


        <?php if ($verified): ?>

            <form method="POST">

                <input type="hidden" name="email"
                       value="<?php echo htmlspecialchars($email); ?>">

                <input type="hidden" name="token"
                       value="<?php echo htmlspecialchars($token); ?>">

                <label>New Password</label>

                <input type="password"
                       name="new_password"
                       minlength="8"
                       required>

                <label>Confirm Password</label>

                <input type="password"
                       name="confirm_password"
                       minlength="8"
                       required>

                <button class="full" type="submit"
                        name="update_password">
                    Update Password →
                </button>

            </form>

            <?php elseif ($linkSubmitted): ?>

                <div class="alert-danger">
                    Your verification link is invalid or expired.
                    Please request a new verification email.
                </div>

                <a class="button full" href="reset-password.php">
                    Request New Verification
                </a>

            <?php else: ?>
            <form method="POST">

                <label>Email Address</label>

                <input type="email"
                       name="email"
                       value="<?php echo htmlspecialchars($email); ?>"
                       placeholder="Enter your registered email"
                       required>

                <button class="full"
                        type="submit"
                        name="send_verification">
                    Send Verification Email →
                </button>

            </form>

        <?php endif; ?>


        <p class="mt-3">
            Remember your password?
            <a href="login.php">Back to Login</a>
        </p>

    </div>

</div>

</body>
</html>
