<?php
session_start();
include "../config/database.php";
require_once "../includes/rider-face.php";
if (file_exists(__DIR__ . "/../vendor/autoload.php")) {
    require_once __DIR__ . "/../vendor/autoload.php";
}

// --- ADDED: 2FA Configuration & Helper Functions ---
define('TWO_FA_EXPIRY_SECONDS', 300);      // 5 minutes expiration
define('TWO_FA_RESEND_COOLDOWN', 60);      // 60 seconds resend cooldown
define('TWO_FA_MAX_ATTEMPTS', 5);          // Brute-force attempt limit

// ============================================================================
// SMTP EMAIL CONFIGURATION (REQUIRED FOR LOCAL XAMPP / WAMP / LARAGON)
// ----------------------------------------------------------------------------
// 1. Put your Gmail address in SMTP_USER below.
// 2. Generate a 16-character Google App Password at:
//    https://myaccount.google.com/apppasswords
//    (Requires 2-Step Verification turned ON in your Google Account)
// 3. Paste that 16-character App Password into SMTP_PASS below
//    (do NOT use your normal Gmail login password).
// ============================================================================
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', (int)(getenv('SMTP_PORT') ?: 587));
define('SMTP_USER', getenv('SMTP_USER') ?: 'trigocompany4@gmail.com'); // Your Gmail address
define('SMTP_PASS', getenv('SMTP_PASS') ?: 'wuan grnr njhg jcsu');                         // <-- PASTE YOUR 16-CHAR GOOGLE APP PASSWORD HERE
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: (SMTP_USER ?: 'no-reply@trigo.local'));
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'TriGo Security');

/**
 * Ensures the optional 2FA verification codes table exists in the database
 * without altering any existing tables.
 */
function ensure_2fa_table($conn) {
    if (!$conn) return false;
    try {
        $sql = "CREATE TABLE IF NOT EXISTS user_2fa_codes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            email VARCHAR(255) NOT NULL,
            code_hash VARCHAR(255) NOT NULL,
            attempts INT NOT NULL DEFAULT 0,
            is_invalidated TINYINT(1) NOT NULL DEFAULT 0,
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_active (user_id, is_invalidated)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        return (bool)mysqli_query($conn, $sql);
    } catch (Throwable $e) {
        error_log('2FA table init notice: ' . $e->getMessage());
        return false;
    }
}

/**
 * Generates a cryptographically secure 6-digit code, invalidates any prior
 * active codes for the user, and stores only the hashed code in session & DB.
 */
function create_2fa_verification($conn, array $user): string {
    $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $code_hash = password_hash($code, PASSWORD_DEFAULT);
    $now = time();
    $expires_at = $now + TWO_FA_EXPIRY_SECONDS;

    // Store hashed code and metadata in session (invalidating any previous code)
    $_SESSION['two_fa_pending'] = [
        'user'         => $user,
        'code_hash'    => $code_hash,
        'expires_at'   => $expires_at,
        'last_sent_at' => $now,
        'attempts'     => 0,
        'invalidated'  => false,
    ];

    // Also persist in database if available, invalidating previous codes first
    if (ensure_2fa_table($conn)) {
        try {
            $uid = (int)$user['id'];
            mysqli_query($conn, "UPDATE user_2fa_codes SET is_invalidated = 1 WHERE user_id = $uid AND is_invalidated = 0");

            $stmt = mysqli_prepare($conn, "INSERT INTO user_2fa_codes (user_id, email, code_hash, attempts, is_invalidated, expires_at) VALUES (?, ?, ?, 0, 0, ?)");
            if ($stmt) {
                $exp_str = date('Y-m-d H:i:s', $expires_at);
                $email_str = (string)$user['email'];
                mysqli_stmt_bind_param($stmt, "isss", $uid, $email_str, $code_hash, $exp_str);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        } catch (Throwable $e) {
            error_log('2FA DB store warning: ' . $e->getMessage());
        }
    }

    return $code;
}

/**
 * Sends a multi-line or single-line SMTP command and reads response.
 */
function smtp_command($fp, ?string $cmd, array $expected_codes, ?string &$last_response = null): bool {
    if ($cmd !== null) {
        fwrite($fp, $cmd . "\r\n");
    }
    $response = '';
    while (($line = fgets($fp, 515)) !== false) {
        $response .= $line;
        if (isset($line[3]) && $line[3] === ' ') {
            break;
        }
    }
    $last_response = trim($response);
    $code = (int)substr($response, 0, 3);
    return in_array($code, $expected_codes, true);
}

/**
 * Sends the 6-digit verification code via PHPMailer (if present), direct SMTP
 * over TLS/SSL (with XAMPP-compatible SSL context), or PHP mail() fallback.
 */
function send_2fa_email(string $to_email, string $full_name, string $code, ?string &$mail_error = null): bool {
    $subject = "Your TriGo Login Verification Code";
    $safe_name = htmlspecialchars($full_name ?: 'Valued User', ENT_QUOTES, 'UTF-8');
    $safe_code = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
    $clean_pass = str_replace(' ', '', SMTP_PASS); // Strip spaces from Google 16-char App Passwords

    $html_body = "
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset='UTF-8'>
    </head>
    <body style='margin:0;padding:0;background-color:#f4f6f8;font-family:Arial,sans-serif;color:#1f2937;'>
      <table width='100%' cellpadding='0' cellspacing='0' style='padding:24px 0;'>
        <tr>
          <td align='center'>
            <table width='520' cellpadding='0' cellspacing='0' style='max-width:520px;background:#ffffff;border-radius:8px;border:1px solid #e5e7eb;padding:32px;'>
              <tr>
                <td>
                  <h2 style='margin:0 0 12px 0;color:#111827;'>TriGo Login Verification</h2>
                  <p style='margin:0 0 16px 0;font-size:15px;line-height:1.5;'>Hello {$safe_name},</p>
                  <p style='margin:0 0 20px 0;font-size:15px;line-height:1.5;'>We received a request to sign in to your TriGo account. Please enter the following 6-digit verification code to complete your login:</p>
                  <div style='text-align:center;margin:24px 0;padding:18px;background:#f3f4f6;border-radius:6px;border:1px dashed #9ca3af;font-size:28px;font-weight:bold;letter-spacing:8px;color:#111827;'>
                    {$safe_code}
                  </div>
                  <p style='margin:0 0 12px 0;font-size:14px;color:#4b5563;'>This verification code will expire in <strong>5 minutes</strong>.</p>
                  <hr style='border:none;border-top:1px solid #e5e7eb;margin:20px 0;'>
                  <p style='margin:0;font-size:12px;color:#6b7280;line-height:1.5;'>
                    <strong>Security Notice:</strong> This code is for login verification only. Never share this code with anyone. If you did not attempt to log in, please ignore this email and secure your account password.
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </body>
    </html>";

    // 1. Use PHPMailer if available in the project
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        try {
            $mailClass = 'PHPMailer\PHPMailer\PHPMailer';
            $mail = new $mailClass(true);
            if (SMTP_USER !== '' && $clean_pass !== '') {
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USER;
                $mail->Password   = $clean_pass;
                $mail->SMTPSecure = (SMTP_PORT === 465) ? 'ssl' : 'tls';
                $mail->Port       = SMTP_PORT;
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true,
                    ],
                ];
            }
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($to_email, $full_name);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $html_body;
            $mail->AltBody = "Hello {$full_name},\n\nYour TriGo login verification code is: {$code}\nThis code expires in 5 minutes.\n\nNotice: This code is for login verification only.";
            return $mail->send();
        } catch (Throwable $e) {
            $mail_error = 'PHPMailer error: ' . $e->getMessage();
            error_log($mail_error);
        }
    }

    // 2. Built-in direct SMTP client (works out-of-the-box on XAMPP/WAMP without Composer)
    if (SMTP_HOST !== '' && SMTP_USER !== '' && $clean_pass !== '') {
        try {
            $remote = (SMTP_PORT === 465 ? 'ssl://' : 'tcp://') . SMTP_HOST . ':' . SMTP_PORT;
            // Relax peer verification for local XAMPP/Windows environments lacking cacert.pem
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ]);
            $fp = @stream_socket_client($remote, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $context);
            if ($fp) {
                stream_set_timeout($fp, 15);
                $last_resp = '';
                $ok = smtp_command($fp, null, [220], $last_resp)
                   && smtp_command($fp, 'EHLO localhost', [250], $last_resp);
                if ($ok && SMTP_PORT === 587) {
                    if (smtp_command($fp, 'STARTTLS', [220], $last_resp)) {
                        $crypto_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;
                        if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                            $crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                        }
                        if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
                            $crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
                        }
                        if (!@stream_socket_enable_crypto($fp, true, $crypto_method)) {
                            $mail_error = 'TLS handshake failed. Ensure php_openssl is enabled in php.ini.';
                            $ok = false;
                        } else {
                            $ok = smtp_command($fp, 'EHLO localhost', [250], $last_resp);
                        }
                    } else {
                        $mail_error = 'STARTTLS rejected by server: ' . $last_resp;
                        $ok = false;
                    }
                }
                if ($ok) {
                    if (!smtp_command($fp, 'AUTH LOGIN', [334], $last_resp)
                        || !smtp_command($fp, base64_encode(SMTP_USER), [334], $last_resp)
                        || !smtp_command($fp, base64_encode($clean_pass), [235], $last_resp)
                    ) {
                        $mail_error = 'SMTP authentication failed. Make sure SMTP_PASS is a 16-character Google App Password (not your regular Gmail password).';
                        fclose($fp);
                        return false;
                    }
                    if (smtp_command($fp, 'MAIL FROM:<' . SMTP_FROM_EMAIL . '>', [250], $last_resp)
                        && smtp_command($fp, 'RCPT TO:<' . $to_email . '>', [250, 251], $last_resp)
                        && smtp_command($fp, 'DATA', [354], $last_resp)
                    ) {
                        $headers  = "Date: " . date('r') . "\r\n";
                        $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
                        $headers .= "To: <" . $to_email . ">\r\n";
                        $headers .= "Subject: " . $subject . "\r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
                        $message  = $headers . $html_body . "\r\n.";
                        $sent     = smtp_command($fp, $message, [250], $last_resp);
                        smtp_command($fp, 'QUIT', [221]);
                        fclose($fp);
                        if ($sent) {
                            return true;
                        }
                        $mail_error = 'SMTP DATA send failed: ' . $last_resp;
                    } else {
                        $mail_error = 'SMTP recipient rejected: ' . $last_resp;
                        fclose($fp);
                    }
                } else {
                    fclose($fp);
                }
            } else {
                $mail_error = "Could not connect to SMTP host " . SMTP_HOST . ":" . SMTP_PORT . " ({$errstr}).";
            }
        } catch (Throwable $e) {
            $mail_error = 'Direct SMTP error: ' . $e->getMessage();
            error_log($mail_error);
        }
        return false;
    }

    if ($clean_pass === '') {
        $mail_error = 'SMTP_PASS is not set in login.php. Please paste your 16-character Google App Password into define(\'SMTP_PASS\', \'...\') at the top of login.php.';
    }

    // 3. Fallback to standard PHP mail()
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    return @mail($to_email, $subject, $html_body, $headers);
}
// --- END ADDED 2FA Helpers ---

//send the user to correct dashboard
if(isset($_SESSION["role"])){
    if(($_SESSION["role"]) == "admin"){
        header("Location: ../admin/admin.php");
    }
    else  if(($_SESSION["role"]) == "rider"){
        try {
            if (rider_face_ready($conn)) {
                header("Location: ../employee/employee.php");
            } else {
                rider_face_begin((int)$_SESSION["user_id"]);
                header("Location: face-verify.php");
            }
        } catch (Throwable $e) {
            error_log('Rider login guard: ' . $e->getMessage());
            http_response_code(503);
            exit('Attendance is unavailable. Please try again shortly.');
        }
    }
    else {
        header("Location: ../users/user.php");
    }
    exit;
}
$error = "";
$success = "";

// --- ADDED: Cancel / Back to Login from 2FA ---
if (isset($_POST["cancel_2fa"])) {
    if (isset($_SESSION["two_fa_pending"]["user"]["id"]) && ensure_2fa_table($conn)) {
        $uid = (int)$_SESSION["two_fa_pending"]["user"]["id"];
        @mysqli_query($conn, "UPDATE user_2fa_codes SET is_invalidated = 1 WHERE user_id = $uid");
    }
    unset($_SESSION["two_fa_pending"]);
}

// --- ADDED: Resend 2FA Verification Code Handler (with 60s Cooldown) ---
if (isset($_POST["resend_code"]) && isset($_SESSION["two_fa_pending"])) {
    $pending = &$_SESSION["two_fa_pending"];
    $elapsed = time() - (int)$pending["last_sent_at"];

    if ($elapsed < TWO_FA_RESEND_COOLDOWN) {
        $wait = TWO_FA_RESEND_COOLDOWN - $elapsed;
        $error = "Please wait {$wait} seconds before requesting a new verification code.";
    } else {
        $user_data = $pending["user"];
        $new_code = create_2fa_verification($conn, $user_data);
        $mail_err = null;
        if (send_2fa_email($user_data["email"], $user_data["full_name"] ?? "", $new_code, $mail_err)) {
            $success = "A new 6-digit verification code has been sent to your email.";
        } else {
            $error = $mail_err ?: "Failed to send verification email. Please check your SMTP settings in login.php.";
        }
    }
}

// --- ADDED: Verify 6-Digit 2FA Code Handler ---
if (isset($_POST["verify_2fa"]) && isset($_SESSION["two_fa_pending"])) {
    $pending = &$_SESSION["two_fa_pending"];
    $entered_code = trim($_POST["verification_code"] ?? "");
    $uid = (int)$pending["user"]["id"];

    if ($pending["invalidated"] || $pending["attempts"] >= TWO_FA_MAX_ATTEMPTS) {
        $error = "Too many incorrect attempts. This verification code has been locked. Please request a new code.";
    } elseif (time() > (int)$pending["expires_at"]) {
        $pending["invalidated"] = true;
        if (ensure_2fa_table($conn)) {
            @mysqli_query($conn, "UPDATE user_2fa_codes SET is_invalidated = 1 WHERE user_id = $uid AND is_invalidated = 0");
        }
        $error = "Your verification code has expired (5-minute limit). Please click Resend Code to receive a new one.";
    } elseif (!preg_match('/^\d{6}$/', $entered_code)) {
        $error = "Please enter a valid 6-digit numeric verification code.";
    } else {
        // Verify against the latest active hashed code
        $is_valid = password_verify($entered_code, $pending["code_hash"]);

        if ($is_valid) {
            // Invalidate the code so it cannot be reused
            if (ensure_2fa_table($conn)) {
                @mysqli_query($conn, "UPDATE user_2fa_codes SET is_invalidated = 1 WHERE user_id = $uid AND is_invalidated = 0");
            }
            $user = $pending["user"];
            unset($_SESSION["two_fa_pending"]);

            // Complete the original post-authentication login flow
            if ($user["role"] == "rider") {
                rider_face_begin((int)$user["id"]);
                rider_face_redirect("face-verify.php");
            }
            unset($_SESSION['rider_face_pending'], $_SESSION['rider_face_csrf'], $_SESSION['rider_face_day']);
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["role"] = $user["role"];

            if($user["role"] == "admin"){
                header("Location: ../admin/admin.php");
            }
            else if($user["role"] == "rider"){
              header("Location: ../employee/employee.php");
            }
            else{
              header("Location: ../users/user.php");
            }
            exit;
        } else {
            $pending["attempts"]++;
            if (ensure_2fa_table($conn)) {
                @mysqli_query($conn, "UPDATE user_2fa_codes SET attempts = attempts + 1 WHERE user_id = $uid AND is_invalidated = 0");
            }
            $remaining = TWO_FA_MAX_ATTEMPTS - $pending["attempts"];
            if ($remaining <= 0) {
                $pending["invalidated"] = true;
                if (ensure_2fa_table($conn)) {
                    @mysqli_query($conn, "UPDATE user_2fa_codes SET is_invalidated = 1 WHERE user_id = $uid AND is_invalidated = 0");
                }
                $error = "Maximum verification attempts exceeded. Please click Resend Code to get a new code.";
            } else {
                $error = "Invalid verification code. You have {$remaining} attempt(s) remaining.";
            }
        }
    }
}

if(isset($_POST["login"])){
    $email = mysqli_real_escape_string($conn,$_POST["email"]);
    $password = $_POST["password"];
    
    //Find typed username
    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);

        if(password_verify($password, $user["password"])){
            // --- ADDED: Trigger 2FA Email Verification before granting session access ---
            $verification_code = create_2fa_verification($conn, $user);
            $mail_err = null;
            if (send_2fa_email($user["email"], $user["full_name"] ?? "", $verification_code, $mail_err)) {
                $success = "A 6-digit verification code has been sent to your registered email address.";
            } else {
                $error = $mail_err ?: "Credentials verified, but we could not send the verification email. Please try resending the code.";
            }

            // Hold dashboard session creation until $_SESSION['two_fa_pending'] is verified
            if (false) {
            if ($user["role"] == "rider") {
                rider_face_begin((int)$user["id"]);
                rider_face_redirect("face-verify.php");
            }
            unset($_SESSION['rider_face_pending'], $_SESSION['rider_face_csrf'], $_SESSION['rider_face_day']);
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["role"] = $user["role"];

            if($user["role"] == "admin"){
                header("Location: ../admin/admin.php");
            }
            else if($user["role"] == "rider"){
              header("Location: ../employee/employee.php");
            }
            else{
              header("Location: ../users/user.php");
            }
            }
        } else {
            $error = "Invalid Username or Password";
        }
    } else {
        $error = "Invalid Username or Password";    
    }

}

$show_2fa = isset($_SESSION["two_fa_pending"]);
$cooldown_remaining = 0;
if ($show_2fa) {
    $elapsed_since_send = time() - (int)$_SESSION["two_fa_pending"]["last_sent_at"];
    if ($elapsed_since_send < TWO_FA_RESEND_COOLDOWN) {
        $cooldown_remaining = TWO_FA_RESEND_COOLDOWN - $elapsed_since_send;
    }
}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TriGo — login</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css"
    />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <script src="../assets/js/ui.js" defer></script>

    <style>
      .alert-danger {
        color: #842029;
        background-color: #f8d7da;
        border: 1px solid #f5c2c7;
        padding: 12px;
        border-radius: 5px;
     }
      .alert-success {
        color: #0f5132;
        background-color: #d1e7dd;
        border: 1px solid #badbcc;
        padding: 12px;
        border-radius: 5px;
        margin-bottom: 12px;
      }
      .two-fa-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 16px;
      }
      .two-fa-card {
        width: 100%;
        max-width: 440px;
        background: #ffffff;
        color: #111827;
        border-radius: 12px;
        padding: 28px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25);
      }
      .two-fa-code-input {
        width: 100%;
        font-size: 24px;
        letter-spacing: 8px;
        text-align: center;
        font-weight: 700;
        padding: 12px;
        margin: 8px 0 16px 0;
        border: 1px solid #d1d5db;
        border-radius: 6px;
      }
      .two-fa-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 8px;
      }
      .button-secondary {
        background: transparent;
        border: 1px solid #d1d5db;
        color: inherit;
        padding: 10px 16px;
        border-radius: 6px;
        cursor: pointer;
        width: 100%;
      }
      .button-secondary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
      }
    </style>
  </head>
  <body>
    <div id="app">
      <header>
        <a class="logo" href="../index.php">
          <span class="mark">↗</span>TriGo<b>.</b>
        </a>
        <nav>
          <a href="../index.php">Overview</a>
          <a href="../index.php#how">How It Works</a>
          <a href="franchise.php">Tricycle Franchising</a>
          <a href="apply.php">Apply as a Rider</a>
          <a class="button outline" href="login.php">Log in ↗</a>
        </nav>
      </header>
      <div class="card formbox">
        <div class="eyebrow">WELCOME BACK</div>
        <h2>Let’s get you moving.</h2>
        <p>Choose a demo role to explore your dashboard.</p>
        
        <?php if($error != "" && !$show_2fa) {?>
          <div class="alert alert-danger"><?php echo $error;?></div>
        <?php }?>

        <!-- PHP integration: add action/method and server validation; UI preview never sends data. -->
        <form method="POST">
          <label>Email address</label>
          <input type="email" name="email" required />
          <label>Password</label>
          <input type="password" name="password" required />
          <button class="full" type="submit" name="login">Login →</button>
        </form>
        <a class="button text" href="reset-password.php">Forgot password?</a>
        <p>
          New to TriGo?
          <a style="color: var(--g)" href="register.php"
            >Create a customer account</a
          >
        </p>
        <div class="notice">
          Preview role selection only. Authentication and permissions require a
          production server.
        </div>
      </div>
    </div>

    <?php if ($show_2fa) { ?>
    <!-- ADDED: Two-Factor Authentication Verification Modal -->
    <div class="two-fa-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="two-fa-title">
      <div class="card formbox two-fa-card">
        <div class="eyebrow">TWO-FACTOR AUTHENTICATION</div>
        <h2 id="two-fa-title">Verify Your Email</h2>
        <p>
          Enter the 6-digit verification code sent to
          <strong><?php echo htmlspecialchars($_SESSION["two_fa_pending"]["user"]["email"] ?? "", ENT_QUOTES, 'UTF-8'); ?></strong>.
          The code expires in 5 minutes.
        </p>

        <?php if ($success != "") { ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php } ?>

        <?php if ($error != "") { ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php } ?>

        <form method="POST" autocomplete="off">
          <label for="verification_code">6-Digit Verification Code</label>
          <input
            type="text"
            id="verification_code"
            name="verification_code"
            class="two-fa-code-input"
            maxlength="6"
            pattern="\d{6}"
            inputmode="numeric"
            placeholder="000000"
            required
            autofocus
          />
          <div class="two-fa-actions">
            <button class="full" type="submit" name="verify_2fa">Verify →</button>
          </div>
        </form>

        <form method="POST" class="two-fa-actions">
          <button
            type="submit"
            name="resend_code"
            id="resend-btn"
            class="button-secondary"
            <?php echo ($cooldown_remaining > 0) ? 'disabled' : ''; ?>
          >
            <?php if ($cooldown_remaining > 0) { ?>
              Resend Code (<span id="cooldown-timer"><?php echo (int)$cooldown_remaining; ?></span>s)
            <?php } else { ?>
              Resend Code
            <?php } ?>
          </button>
          <button type="submit" name="cancel_2fa" class="button text" style="text-align:center;">
            ← Back to login
          </button>
        </form>
      </div>
    </div>
    <script>
      (function () {
        var timerEl = document.getElementById('cooldown-timer');
        var resendBtn = document.getElementById('resend-btn');
        if (!timerEl || !resendBtn) return;
        var remaining = parseInt(timerEl.textContent, 10) || 0;
        var interval = setInterval(function () {
          remaining -= 1;
          if (remaining <= 0) {
            clearInterval(interval);
            resendBtn.disabled = false;
            resendBtn.textContent = 'Resend Code';
          } else {
            timerEl.textContent = remaining;
          }
        }, 1000);
      })();
    </script>
    <?php } ?>

    <dialog id="ui-dialog" aria-labelledby="ui-dialog-title">
      <h2 id="ui-dialog-title"></h2>
      <div id="ui-dialog-content"></div>
      <div class="row">
        <button type="button" data-close-dialog>Close</button>
      </div>
    </dialog>
  </body>
</html>