<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../phpmailer/vendor/autoload.php';
require_once '../conn.php';

function send_verification($email, $token)
{
    $mail = new PHPMailer(true); // Passing true enables exceptions
    global $conn; // Ensure the $conn variable is accessible within the function

    try {
        // Server settings
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'pjarahgalias27@gmail.com';         // SMTP username
        $mail->Password = 'lcvo sfjj xnkm thci';              // SMTP password (App Password)
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption
        $mail->Port = 587;                                    // TCP port to connect to

        // Recipients
        $mail->setFrom('pjarahgalias27@gmail.com', 'Cosmeticas Fraiche Naturale');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = "Email Verification";
        $mail->Body = "
            <div class='container' style='color: #ffffff; display: flex; justify-content: center; align-items: center; height: 100vh;'>
                <div class='row'>
                    <div class='col' style='background-color: #1F4529; padding: 20px; border-radius: 10px; text-align: center;'>
                        <h1 style='color: #ffffff;'>Verify your email</h1> 
                        <p style='color: #ffffff;'>Please Click on the button below to verify your account</p>
                        <p style='color: #ffffff;'>By clicking on the button below, you will verify $email</p>
                        <a href='http://localhost/CFN/Registration_Page/verify.php?email=$email&token=$token' 
                            style='
                            display: inline-block;
                            padding: 10px 20px;
                            font-size: 16px;
                            color: #ffffff;
                            background-color: #C0D171;
                            text-decoration: none;
                            border-radius: 5px;
                        '>Verify Email</a>
                        <p style='color: #ffffff;'>If you didn’t request this email verification, you can safely ignore it.</p>
                    </div>
                </div>
            </div>";

        $mail->send();
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Email Successfully Sent!',
                    showConfirmButton: true
                });
              </script>";
    } catch (Exception $e) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Message could not be sent.',
                    text: 'Mailer Error: {$mail->ErrorInfo}',
                    showConfirmButton: true
                });
              </script>";
    }
}


function pass_recov($email, $token)
{
    $mail = new PHPMailer(true); // Passing true enables exceptions
    global $conn; // Ensure the $conn variable is accessible within the function

    try {
        // Server settings
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'pjarahgalias27@gmail.com';         // SMTP username
        $mail->Password = 'lcvo sfjj xnkm thci';              // SMTP password (App Password)
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption
        $mail->Port = 587;                                    // TCP port to connect to

        // Recipients
        $mail->setFrom('pjarahgalias27@gmail.com', 'Cosmeticas Fraiche Naturale');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = "Email Verification";
        $mail->Body = "
            <div class='container' style='color: #ffffff; display: flex; justify-content: center; align-items: center; height: 100vh;'>
                <div class='row'>
                    <div class='col' style='background-color: #1F4529; padding: 20px; border-radius: 10px; text-align: center;'>
                        <h1 style='color: #ffffff;'>Verify your email</h1> 
                        <p style='color: #ffffff;'>Please Click on the button below to verify your account</p>
                        <p style='color: #ffffff;'>By clicking on the button below, you will verify $email</p>
                        <a href='http://localhost/CFN/Registration_Page/verify_pass_recov.php?email=$email&token=$token' 
                            style='
                            display: inline-block;
                            padding: 10px 20px;
                            font-size: 16px;
                            color: #ffffff;
                            background-color: #C0D171;
                            text-decoration: none;
                            border-radius: 5px;
                        '>Verify Email</a>
                        <p style='color: #ffffff;'>If you didn’t request this email verification, you can safely ignore it.</p>
                    </div>
                </div>
            </div>";

        $mail->send();
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Email Successfully Sent!',
                    showConfirmButton: true
                });
              </script>";
    } catch (Exception $e) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Message could not be sent.',
                    text: 'Mailer Error: {$mail->ErrorInfo}',
                    showConfirmButton: true
                });
              </script>";
    }
}
?>