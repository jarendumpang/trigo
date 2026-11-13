<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

// Send email based on admin decision
function sendRiderStatusEmail($riderEmail, $riderName, $status)
{
    $mail = new PHPMailer(true);

    try {
        // Gmail SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        $mail->Username   = 'trigocompany4@gmail.com';
        $mail->Password   = 'wuan grnr njhg jcsu';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Sender and recipient
        $mail->setFrom(
            'YOUR_GMAIL@gmail.com',
            'TRIGO Rider Application'
        );

        $mail->addAddress($riderEmail, $riderName);
        $mail->isHTML(true);

        // Accepted / Hired
        if ($status === 'accepted') {

            $mail->Subject = 'Congratulations! You are hired - TRIGO';

            $mail->Body = "
                <div style='font-family:Arial;padding:20px;'>
                    <h2 style='color:#16a34a;'>Congratulations!</h2>
                    <p>Hello <b>" . htmlspecialchars($riderName, ENT_QUOTES, 'UTF-8') . "</b>,</p>
                    <p>Your application has been accepted.</p>
                    <p>You are now hired as a TRIGO rider!</p>
                    <p>Welcome to the TRIGO team.</p>
                </div>
            ";

            $mail->AltBody = "Congratulations! Your TRIGO rider application has been accepted.";

        // Rejected
        } elseif ($status === 'rejected') {

            $mail->Subject = 'TRIGO Rider Application Update';

            $mail->Body = "
                <div style='font-family:Arial;padding:20px;'>
                    <h2>Application Update</h2>
                    <p>Hello <b>" . htmlspecialchars($riderName, ENT_QUOTES, 'UTF-8') . "</b>,</p>
                    <p>Thank you for applying to become a TRIGO rider.</p>
                    <p>Unfortunately, your application was not accepted.</p>
                    <p>Thank you for your interest in TRIGO.</p>
                </div>
            ";

            $mail->AltBody = "Unfortunately, your TRIGO rider application was not accepted.";

        } else {
            return true;
        }

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("TRIGO Email Error: " . $mail->ErrorInfo);
        return false;
    }
}

?>