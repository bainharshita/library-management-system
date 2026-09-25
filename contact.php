
<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json');

function respond($success, $message, $status = 200) {
    http_response_code($status);
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Invalid request method.', 405);
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    respond(false, 'Please fill in all fields.', 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Please enter a valid email address.', 400);
}

if (
    strlen($name) > 100 ||
    strlen($email) > 254 ||
    strlen($message) > 5000
) {
    respond(false, 'One or more fields are too long.', 400);
}

$mail = new PHPMailer(true);

try {
    // Gmail SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    // Replace with your Gmail address
    $mail->Username = 'harshitab25805@gmail.com';

    // Replace with your Gmail App Password
    $mail->Password = 'ifjqdfrtcgbfhhye';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->CharSet = 'UTF-8';

    // Sender and recipient
    $mail->setFrom(
        'harshitab25805@gmail.com',
        'GreenShelf Contact Form'
    );

    $mail->addAddress('harshitab25805@gmail.com');

    // Visitor's email, so you can reply
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'New GreenShelf Contact Message';

    $mail->Body =
        '<h2>New Contact Message</h2>' .
        '<p><strong>Name:</strong> ' .
        htmlspecialchars($name, ENT_QUOTES, 'UTF-8') .
        '</p>' .
        '<p><strong>Email:</strong> ' .
        htmlspecialchars($email, ENT_QUOTES, 'UTF-8') .
        '</p>' .
        '<p><strong>Message:</strong><br>' .
        nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) .
        '</p>';

    $mail->AltBody =
        "Name: $name\n" .
        "Email: $email\n\n" .
        "Message:\n$message";

    $mail->send();

    respond(true, 'Your message has been sent successfully!');

} catch (Exception $e) {
    error_log('GreenShelf email error: ' . $mail->ErrorInfo);

    respond(
        false,
        'Sorry, your message could not be sent. Please try again later.',
        500
    );
}