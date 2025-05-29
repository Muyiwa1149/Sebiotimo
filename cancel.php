<?php
session_start();
include('includes/dbconnection.php');
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch appointment details
    $sql = "SELECT a.*, s.Specialization, d.FullName FROM tblappointment a
            LEFT JOIN tblspecialization s ON a.Specialization = s.ID
            LEFT JOIN tbldoctor d ON a.Doctor = d.ID
            WHERE a.ID = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $name = $result['Name'];
        $email = $result['Email'];
        $specialization = $result['Specialization'];
        $doctor = $result['FullName'];
        $date = date('F j, Y', strtotime($result['AppointmentDate']));
        $time = date('h:i A', strtotime($result['AppointmentTime']));

        // Update status
        $update = "UPDATE tblappointment SET Status='Canceled', Remark='Your appointment has been canceled.' WHERE ID=:id";
        $stmt = $dbh->prepare($update);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Send email
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'mail.achillesdrill.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'admin@achillesdrill.com';
            $mail->Password   = 'Drills250889#';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            $mail->setFrom('admin@achillesdrill.com', 'Sebiotimo Medical and Diagnostics Center');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = "Appointment Canceled - Sebiotimo Medical Center";

            $mail->Body = '
            <!DOCTYPE html>
            <html lang="en">
            <head><meta charset="UTF-8"><title>Appointment Canceled</title></head>
            <body style="margin:0; padding:0; background-color:#f5f8fa;">
              <table cellpadding="0" cellspacing="0" width="100%" style="padding: 20px 0;">
                <tr>
                  <td align="center">
                    <table cellpadding="0" cellspacing="0" width="600" style="background-color:#ffffff; border-radius:8px;">
                      <tr>
                        <td style="padding: 20px; background-color: #cc0000; color:#fff; text-align:center; font-size:18px; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                          Sebiotimo Medical Center
                        </td>
                      </tr>
                      <tr>
                        <td style="padding:30px; font-size:16px; color:#333;">
                          <p>Dear ' . htmlspecialchars($name) . ',</p>
                          <p>We regret to inform you that your appointment has been <strong>canceled</strong>. Below were your appointment details:</p>
                          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; margin-top: 20px;">
                            <tr>
                            <td style="padding: 8px; font-weight: bold; background-color: #f1f1f1; border: 1px solid #ddd;">Date:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($date) . '</td>
                            </tr>
                            <tr>
                            <td style="padding: 8px; font-weight: bold; background-color: #f1f1f1; border: 1px solid #ddd;">Time:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($time) . '</td>
                            </tr>
                            <tr>
                            <td style="padding: 8px; font-weight: bold; background-color: #f1f1f1; border: 1px solid #ddd;">Doctor:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($doctor) . '</td>
                            </tr>
                            <tr>
                            <td style="padding: 8px; font-weight: bold; background-color: #f1f1f1; border: 1px solid #ddd;">Specialization:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($specialization) . '</td>
                            </tr>
                        </table>
                          <p style="margin-top:30px;">Kindly re-schedule your appointment to a later date and time.</p>
                          <p style="margin-top:30px;">If you believe this was a mistake, kindly contact our support.</p>
                        </td>
                      </tr>
                      <tr>
                        <td style="background-color:#f0f4ff; text-align:center; padding: 15px; font-size:12px; color:#888; border-bottom-left-radius:8px; border-bottom-right-radius:8px;">
                          &copy; ' . date("Y") . ' Sebiotimo Medical and Diagnostics Center.
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </body>
            </html>';

            $mail->send();
        } catch (Exception $e) {
            error_log("Email Error: " . $mail->ErrorInfo);
        }


        // Send mail to admin

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'mail.achillesdrill.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'admin@achillesdrill.com';
            $mail->Password   = 'Drills250889#';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            $mail->setFrom('admin@achillesdrill.com', 'Sebiotimo Medical and Diagnostics Center');
            $mail->addAddress('afoglad@gmail.com', 'Admin');

            $mail->isHTML(true);
            $mail->Subject = "Appointment Canceled for $name - Sebiotimo Medical Center";

            $mail->Body = '
            <!DOCTYPE html>
            <html lang="en">
            <head><meta charset="UTF-8"><title>Appointment Canceled</title></head>
            <body style="margin:0; padding:0; background-color:#f5f8fa;">
              <table cellpadding="0" cellspacing="0" width="100%" style="padding: 20px 0;">
                <tr>
                  <td align="center">
                    <table cellpadding="0" cellspacing="0" width="600" style="background-color:#ffffff; border-radius:8px;">
                      <tr>
                        <td style="padding: 20px; background-color: #cc0000; color:#fff; text-align:center; font-size:18px; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                          Sebiotimo Medical Center
                        </td>
                      </tr>
                      <tr>
                        <td style="padding:30px; font-size:16px; color:#333;">
                          <p>Dear Admin,</p>
                          <p>A new appointment has been <strong>canceled</strong> for ' . htmlspecialchars($name) . '. Below were the appointment details:</p>
                          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; margin-top: 20px;">
                            <tr>
                            <td style="padding: 8px; font-weight: bold; background-color: #f1f1f1; border: 1px solid #ddd;">Date:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($date) . '</td>
                            </tr>
                            <tr>
                            <td style="padding: 8px; font-weight: bold; background-color: #f1f1f1; border: 1px solid #ddd;">Time:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($time) . '</td>
                            </tr>
                            <tr>
                            <td style="padding: 8px; font-weight: bold; background-color: #f1f1f1; border: 1px solid #ddd;">Doctor:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($doctor) . '</td>
                            </tr>
                            <tr>
                            <td style="padding: 8px; font-weight: bold; background-color: #f1f1f1; border: 1px solid #ddd;">Specialization:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($specialization) . '</td>
                            </tr>
                        </table>
                        </td>
                      </tr>
                      <tr>
                        <td style="background-color:#f0f4ff; text-align:center; padding: 15px; font-size:12px; color:#888; border-bottom-left-radius:8px; border-bottom-right-radius:8px;">
                          &copy; ' . date("Y") . ' Sebiotimo Medical and Diagnostics Center.
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </body>
            </html>';

            $mail->send();
        } catch (Exception $e) {
            error_log("Email Error: " . $mail->ErrorInfo);
        }

        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Appointment Approved</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
                Swal.fire({
                icon: "success",
                title: "Appointment Canceled",
                text: "The appointment has been canceled and the user has been notified via email."
                }).then(() => {
                window.location.href = "doctor/all-appointment.php";
                });
            </script>
        </body>
        </html>';
    }
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
