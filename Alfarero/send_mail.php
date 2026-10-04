<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure PHPMailer is installed via Composer

// ─── Configuration ────────────────────────────────────────────────────────────

define('SMTP_HOST',     'smtp.gmail.com');
define('SMTP_PORT',     587);
define('SMTP_USERNAME', 'carlhuri123@gmail.com');  // Your Gmail address
define('SMTP_PASSWORD', 'nbrh ggeo eofs oovz'); // Gmail App Password (not your login password)
define('MAIL_FROM',     'carlhuri123@gmail.com');
define('MAIL_FROM_NAME','Carl Alfarero Portfolio');
define('MAIL_TO',       'carlhuri123@gmail.com');

// ─── Only handle POST requests ────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.html');
    exit;
}

// ─── Sanitize & validate input ────────────────────────────────────────────────

$firstName = trim(htmlspecialchars($_POST['firstName'] ?? '', ENT_QUOTES, 'UTF-8'));
$lastName  = trim(htmlspecialchars($_POST['lastName']  ?? '', ENT_QUOTES, 'UTF-8'));
$email     = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$phone     = trim(htmlspecialchars($_POST['phone']   ?? '', ENT_QUOTES, 'UTF-8'));
$message   = trim(htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'));

$errors = [];

if (empty($firstName)) $errors[] = 'First name is required.';
if (empty($lastName))  $errors[] = 'Last name is required.';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if (empty($message))   $errors[] = 'Message is required.';

if (!empty($errors)) {
    // Redirect back with an error flag (simple approach — no session needed)
    $query = http_build_query(['status' => 'error', 'msg' => implode(' ', $errors)]);
    header("Location: contact.html?$query");
    exit;
}

// ─── Build and send the email ─────────────────────────────────────────────────

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USERNAME;
    $mail->Password   = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;

    // Recipients
    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->addAddress(MAIL_TO, 'Carl Alfarero');
    $mail->addReplyTo($email, "$firstName $lastName"); // Reply goes directly to sender

    // Content
    $mail->isHTML(true);
    $mail->Subject = "New Contact Form Message from $firstName $lastName";
    $mail->Body    = buildHtmlBody($firstName, $lastName, $email, $phone, $message);
    $mail->AltBody = buildPlainBody($firstName, $lastName, $email, $phone, $message);

    $mail->send();

    header('Location: contact.html?status=success');
    exit;

} catch (Exception $e) {
    // Log the error server-side; don't expose details to the user
    error_log("PHPMailer error: {$mail->ErrorInfo}");
    header('Location: contact.html?status=error&msg=Could+not+send+message.+Please+try+again+later.');
    exit;
}

// ─── Email body builders ──────────────────────────────────────────────────────

function buildHtmlBody(string $firstName, string $lastName, string $email, string $phone, string $message): string
{
    $phone = $phone ?: 'Not provided';
    return <<<HTML
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;">
        <h2 style="color: #333; border-bottom: 2px solid #333; padding-bottom: 10px;">New Contact Form Submission</h2>

        <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
            <tr>
                <td style="padding: 8px 12px; font-weight: bold; width: 35%; background: #f5f5f5;">Name</td>
                <td style="padding: 8px 12px;">$firstName $lastName</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-weight: bold; background: #f5f5f5;">Email</td>
                <td style="padding: 8px 12px;"><a href="mailto:$email">$email</a></td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-weight: bold; background: #f5f5f5;">Phone</td>
                <td style="padding: 8px 12px;">$phone</td>
            </tr>
        </table>

        <h3 style="color: #333; margin-top: 24px;">Message</h3>
        <div style="background: #f9f9f9; padding: 16px; border-left: 4px solid #333; border-radius: 4px; white-space: pre-wrap;">$message</div>

        <p style="color: #888; font-size: 12px; margin-top: 24px;">
            This message was sent via the contact form on carlalfarero.com
        </p>
    </div>
    HTML;
}

function buildPlainBody(string $firstName, string $lastName, string $email, string $phone, string $message): string
{
    $phone = $phone ?: 'Not provided';
    return <<<TEXT
    New Contact Form Submission
    ===========================
    Name:    $firstName $lastName
    Email:   $email
    Phone:   $phone

    Message:
    $message

    ---
    Sent via the contact form on carlalfarero.com
    TEXT;
}
