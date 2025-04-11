<?php
require_once '../conn.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer autoloader
require '../phpmailer/vendor/autoload.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

function send_email_change_notification($old_email, $new_email) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'pjarahgalias27@gmail.com';
        $mail->Password = 'lcvo sfjj xnkm thci'; // App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('pjarahgalias27@gmail.com', 'Cosmeticas Fraiche Naturale');
        $mail->addAddress($old_email); // Send to the old email

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Your Email Address Has Been Updated";
        $mail->Body = "
            <div class='container' style='color: #ffffff; display: flex; justify-content: center; align-items: center; height: 100vh;'>
                <div class='row'>
                    <div class='col' style='background-color: #1F4529; padding: 20px; border-radius: 10px; text-align: center;'>
                        <h1 style='color: #ffffff;'>Email Address Updated</h1>
                        <p style='color: #ffffff;'>Your email address for Cosmeticas Fraiche Naturale has been changed.</p>
                        <p style='color: #ffffff;'>Old Email: $old_email</p>
                        <p style='color: #ffffff;'>New Email: $new_email</p>
                        <p style='color: #ffffff;'>If you did not request this change, please contact our support team immediately.</p>
                        <a href='http://localhost/CFN/support' 
                            style='
                            display: inline-block;
                            padding: 10px 20px;
                            font-size: 16px;
                            color: #ffffff;
                            background-color: #C0D171;
                            text-decoration: none;
                            border-radius: 5px;
                        '>Contact Support</a>
                    </div>
                </div>
            </div>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error instead of echoing to avoid breaking JSON response
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    // Validate input
    if ($user_id <= 0) {
        $response['message'] = 'Invalid user ID.';
        echo json_encode($response);
        exit;
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email address.';
        echo json_encode($response);
        exit;
    }

    // Get current email to send notification
    $stmt = $conn->prepare("SELECT email FROM tb_user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $response['message'] = 'User not found.';
        echo json_encode($response);
        $stmt->close();
        exit;
    }
    $old_email = $result->fetch_assoc()['email'];
    $stmt->close();

    // Check if email already exists for another user
    $stmt = $conn->prepare("SELECT user_id FROM tb_user WHERE email = ? AND user_id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $response['message'] = 'This email is already in use by another user.';
        echo json_encode($response);
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Update email
    $stmt = $conn->prepare("UPDATE tb_user SET email = ? WHERE user_id = ?");
    $stmt->bind_param("si", $email, $user_id);

    if ($stmt->execute()) {
        // Send email notification
        if (send_email_change_notification($old_email, $email)) {
            $response['success'] = true;
        } else {
            $response['message'] = 'Email updated, but failed to send notification.';
            $response['success'] = true; // Still consider it a success since DB update worked
        }
    } else {
        $response['message'] = 'Failed to update email in the database.';
    }

    $stmt->close();
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
$conn->close();
?>