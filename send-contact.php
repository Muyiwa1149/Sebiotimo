<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Composer autoloader

// Fetch form data
$name    = $_POST['name'] ?? '';
$email   = $_POST['email'] ?? '';
$subject = $_POST['subject'] ?? 'New Contact Message';
$message = $_POST['message'] ?? '';

// Validate required fields
if (empty($name) || empty($email) || empty($message)) {
    echo "<script>
        alert('Please fill in all required fields.');
        window.history.back();
    </script>";
    exit;
}

$mail = new PHPMailer(true);

try {
    // SMTP settings (replace with your SMTP credentials)
    $mail->isSMTP();
    $mail->Host       = 'mail.achillesdrill.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'admin@achillesdrill.com';
    $mail->Password   = 'Drills250889#';
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    // SAFE SENDER
    $mail->setFrom('admin@achillesdrill.com', 'Sebiotimo Medical and Diagnostics Center');
    $mail->addReplyTo($email, $name); // Optional, but helpful
    $mail->addAddress('afoglad@gmail.com');

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <title>New Contact Message</title>
    </head>
    <body style="margin:0; padding:0; background-color:#f5f8fa; font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif;">
      <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f5f8fa; padding: 20px 0;">
        <tr>
          <td align="center">
            <table role="presentation" cellpadding="0" cellspacing="0" width="600" style="background-color:#ffffff; border-radius:8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
              <tr>
                <td style="padding: 20px; text-align:center; background-color:rgb(9, 92, 75); border-top-left-radius: 8px; border-top-right-radius: 8px;">
                  <h1 style="color:#ffffff; font-weight: 700; margin:0; font-size:18px;">Sebiotimo Medical Center</h1>
                </td>
              </tr>
              <tr>
                <td style="padding: 30px; color:#1a1a1a; font-size:16px; line-height:1.5;">
                  <p><strong>You received a new contact message:</strong></p>
                  <p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>
                  <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                  <p><strong>Subject:</strong> ' . htmlspecialchars($subject) . '</p>
                  <p><strong>Message:</strong></p>
                  <div style="background-color:#f1fdfb; border-left: 4px solid rgb(9, 92, 75); padding: 15px; font-size: 15px; color: #333333; border-radius: 4px; margin-top:10px;">
                    ' . nl2br(htmlspecialchars($message)) . '
                  </div>
                  <p style="margin-top:40px; font-size:14px; color:#666666;">Sent via Sebiotimo Medical Center website contact form</p>
                </td>
              </tr>
              <tr>
                <td style="background-color:#f0f4ff; text-align:center; padding: 15px; font-size:12px; color:#888888; border-bottom-left-radius:8px; border-bottom-right-radius:8px;">
                  &copy; ' . date("Y") . ' Sebiotimo Medical and Diagnostics Center. All rights reserved.
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </body>
    </html>';

    $mail->send();

    // SweetAlert2 success with redirect
    echo '
    <html>
      <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      </head>
      <body>
        <script>
          Swal.fire({
            icon: "success",
            title: "Message Sent!",
            text: "Thank you for reaching out. We will get back to you shortly.",
            confirmButtonText: "OK"
          }).then(() => {
            window.location.href = "contact.html";
          });
        </script>
      </body>
    </html>';
} catch (Exception $e) {
    echo "<script>
      alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');
      window.history.back();
    </script>";
}
