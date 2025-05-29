<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer
session_start();
include('doctor/includes/dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $mobnum = trim($_POST['phone'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $appdate = $_POST['date'] ?? '';
    $aaptime = $_POST['time'] ?? '';
    $specialization = $_POST['specialization'] ?? '';
    $doctorlist = $_POST['doctorlist'] ?? '';
    $message = trim($_POST['message'] ?? '');
    $aptnumber = mt_rand(100000000, 999999999);
    $cdate = date('Y-m-d');

    $specializationId = $_POST['specialization'];
    $doctorId = $_POST['doctorlist'];

    // Get Specialization Name
    $sqlSpec = "SELECT Specialization FROM tblspecialization WHERE ID = :specid";
    $querySpec = $dbh->prepare($sqlSpec);
    $querySpec->bindParam(':specid', $specializationId, PDO::PARAM_INT);
    $querySpec->execute();
    $specResult = $querySpec->fetch(PDO::FETCH_ASSOC);
    $specialization1 = $specResult ? $specResult['Specialization'] : 'N/A';

    // Get Doctor Full Name and Email
    $sqlDoc = "SELECT FullName, Email FROM tbldoctor WHERE ID = :docid";
    $queryDoc = $dbh->prepare($sqlDoc);
    $queryDoc->bindParam(':docid', $doctorId, PDO::PARAM_INT);
    $queryDoc->execute();
    $docResult = $queryDoc->fetch(PDO::FETCH_ASSOC);
    $doctorName = $docResult ? $docResult['FullName'] : 'N/A';
    $doctorEmail = $docResult ? $docResult['Email'] : 'N/A';


    $formattedDate = date("F j, Y", strtotime($appdate));
    $formattedTime = date("h:i a", strtotime($aaptime));

    /// Basic validation
    if (empty($name) || empty($mobnum) || empty($email) || empty($appdate) || empty($aaptime) || empty($specialization) || empty($doctorlist)) {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Missing Fields</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: "error",
                    title: "Missing Fields",
                    text: "Please fill in all required fields.",
                    confirmButtonColor: "#d33"
                }).then(() => {
                    window.location.href = "booking.php";
                });
            });
        </script>
        </body>
        </html>';
        exit;
    }

    if (!preg_match('/^0\d{10}$/', $mobnum)) {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Invalid Phone</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: "error",
                    title: "Invalid Phone Number",
                    text: "Phone number must be 11 digits and start with 0.",
                    confirmButtonColor: "#d33"
                }).then(() => {
                    window.location.href = "booking.php";
                });
            });
        </script>
        </body>
        </html>';
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Invalid Email</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: "error",
                    title: "Invalid Email",
                    text: "Please enter a valid email address.",
                    confirmButtonColor: "#d33"
                }).then(() => {
                    window.location.href = "booking.php";
                });
            });
        </script>
        </body>
        </html>';
        exit;
    }


    if ($appdate < $cdate) {
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Invalid Appointment Date</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "error",
                title: "Invalid Date",
                text: "Please select a date from today or later.",
                confirmButtonColor: "#d33"
            }).then(() => {
                window.location.href = "booking.php" // Go back to the previous page
            });
        });
    </script>
    </body>
    </html>';
    exit;
}


    // Insert into DB
    $sql = "INSERT INTO tblappointment(AppointmentNumber, Name, MobileNumber, Email, AppointmentDate, AppointmentTime, Specialization, Doctor, Message)
            VALUES (:aptnumber, :name, :mobnum, :email, :appdate, :aaptime, :specialization, :doctorlist, :message)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':aptnumber', $aptnumber);
    $query->bindParam(':name', $name);
    $query->bindParam(':mobnum', $mobnum);
    $query->bindParam(':email', $email);
    $query->bindParam(':appdate', $appdate);
    $query->bindParam(':aaptime', $aaptime);
    $query->bindParam(':specialization', $specialization);
    $query->bindParam(':doctorlist', $doctorlist);
    $query->bindParam(':message', $message);

    if ($query->execute()) {
        $LastInsertId = $dbh->lastInsertId();

        // Email credentials (store these in env/config file ideally)
        $smtpHost = 'mail.achillesdrill.com';
        $smtpUser = 'admin@achillesdrill.com';
        $smtpPass = 'Drills250889#';
        $smtpFrom = 'admin@achillesdrill.com';
        $adminEmail = 'afoglad@gmail.com';
        $adminName = 'Admin';

        // Send confirmation to user
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom($smtpFrom, 'Sebiotimo Medical and Diagnostics Center');
            $mail->addAddress($email, $name);
            $mail->addReplyTo($smtpFrom, 'Sebiotimo Center');
            $mail->isHTML(true);
            $mail->Subject = "Appointment Request Received";

            $mail->Body = file_get_contents('templates/appointment_user_email.html');
            $mail->Body = str_replace(
                ['{{name}}', '{{date}}', '{{time}}', '{{doctor}}', '{{specialization}}'],
                [htmlspecialchars($name), htmlspecialchars($formattedDate), htmlspecialchars($formattedTime), htmlspecialchars($doctorName), htmlspecialchars($specialization1)],
                $mail->Body
            );

            $mail->send();
        } catch (Exception $e) {
            error_log("User email failed: {$mail->ErrorInfo}");
        }

        // Send alert to admin
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom($smtpFrom, 'Sebiotimo Medical and Diagnostics Center');
            $mail->addAddress($adminEmail, $adminName);
            $mail->addReplyTo($smtpFrom, 'Sebiotimo Center');
            $mail->isHTML(true);
            $mail->Subject = "New Appointment Booking by $name";

            $approveLink = "https://test.achillesdrill.com/approve.php?id=$LastInsertId";
            $cancelLink = "https://test.achillesdrill.com/cancel.php?id=$LastInsertId";

            $mail->Body = file_get_contents('templates/appointment_admin_email.html');
            $mail->Body = str_replace(
                ['{{name}}', '{{email}}', '{{phone}}', '{{specialization}}', '{{doctor}}', '{{date}}', '{{time}}', '{{message}}', '{{approve_link}}', '{{cancel_link}}'],
                [htmlspecialchars($name), htmlspecialchars($email), htmlspecialchars($mobnum), htmlspecialchars($specialization1), htmlspecialchars($doctorName), htmlspecialchars($formattedDate), htmlspecialchars($formattedTime), nl2br(htmlspecialchars($message)), $approveLink, $cancelLink],
                $mail->Body
            );

            $mail->send();
        } catch (Exception $e) {
            error_log("Admin email failed: {$mail->ErrorInfo}");
        }

        // Send alert to Doctor
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom($smtpFrom, 'Sebiotimo Medical and Diagnostics Center');
            $mail->addAddress($doctorEmail, $doctorName);
            $mail->addReplyTo($smtpFrom, 'Sebiotimo Medical Center');
            $mail->isHTML(true);
            $mail->Subject = "New Appointment Booking by $name";

            $approveLink = "https://test.achillesdrill.com/approve.php?id=$LastInsertId";
            $cancelLink = "https://test.achillesdrill.com/cancel.php?id=$LastInsertId";

            $mail->Body = file_get_contents('templates/appointment_admin_email.html');
            $mail->Body = str_replace(
                ['{{name}}', '{{email}}', '{{phone}}', '{{specialization}}', '{{doctor}}', '{{date}}', '{{time}}', '{{message}}', '{{approve_link}}', '{{cancel_link}}'],
                [htmlspecialchars($name), htmlspecialchars($email), htmlspecialchars($mobnum), htmlspecialchars($specialization1), htmlspecialchars($doctorName), htmlspecialchars($formattedDate), htmlspecialchars($formattedTime), nl2br(htmlspecialchars($message)), $approveLink, $cancelLink],
                $mail->Body
            );

            $mail->send();
        } catch (Exception $e) {
            error_log("Admin email failed: {$mail->ErrorInfo}");
        }

        // Final success alert and redirect
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Appointment Sent',
                    text: 'Your appointment request has been sent. We will contact you soon.'
                }).then(() => {
                    window.location.href = 'booking.php';
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Failed',
                    text: 'Something went wrong. Please try again.'
                });
            });
        </script>";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- SEO Meta Tags -->
  <title>Sebiotimo Medical and Diagnostics Center | Healthcare in Ondo</title>
  <meta name="description" content="Sebiotimo Medical and Diagnostics Center in Ondo offers high-quality diagnostic, medical consultation, lab tests, cardiology, and imaging services. Book your appointment today.">
  <meta name="keywords" content="Sebiotimo, Medical Center Ondo, Diagnostics, Healthcare, Lab Test, Cardiology, Ultrasound, Doctor Ondo, Hospital Ondo, Make Appointment">
  <meta name="author" content="Sebiotimo Medical and Diagnostics Center">

  <!-- Open Graph for Social Media -->
  <meta property="og:title" content="Sebiotimo Medical and Diagnostics Center" />
  <meta property="og:description" content="High-quality diagnostic and medical services in Ondo. Book appointments, access lab tests, and more." />
  <meta property="og:image" content="image/logo.png" />
  <meta property="og:url" content="https://test.achillesdrill.com/" />
  <meta property="og:type" content="website" />

  <!-- Favicon -->
  <link rel="icon" href="image/logo.png" type="image/png" />

  <!-- CSS and Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.1.0/uicons-thin-rounded/css/uicons-thin-rounded.css">
  <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
  <link rel="stylesheet" href="style.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-jQGwYdfKqsl7qP4NHCPpQJQ4ZKwzGlOUZg8/9ZPZExNLPl8RJgaVChWnYg0obN9Kv7tbLdUbcOPUMQZl7nCqFA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: #f1f5f9;
    }
    .main-contact {
      padding: 60px 20px;
      max-width: 800px;
      margin: auto;
    }

    .contact-heading {
      text-align: center;
      margin-bottom: 40px;
    }

    .contact-heading h2 {
      font-size: 32px;
      color: #152265;
    }

    .contact-heading p {
      color: #152265;
      font-size: 16px;
      max-width: 600px;
      margin: 10px auto 0;
    }

    .contact-content {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      justify-content: center;
    }

    .contact-card {
      flex: 1 1 350px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.07);
      padding: 30px;
    }

    .contact-card h3 {
      margin-bottom: 20px;
      color: #152265;
      font-size: 22px;
    }

    .contact-info p {
      margin: 10px 0;
      font-size: 15px;
      color: #152265;
    }

    .contact-info i {
      color: #0081cf;
      margin-right: 10px;
    }

    .contact-form input,
    .contact-form select,
    .contact-form textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      background: #fdfdfd;
      transition: 0.3s;
    }

    .contact-form input:focus,
    .contact-form textarea:focus {
      border-color: #0081cf;
      outline: none;
    }

    .contact-form button {
      width: 100%;
      background: #0081cf;
      color: white;
      border: none;
      padding: 12px;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
    }

    .contact-form button:hover {
      background: #006fb3;
    }

    .contact-map {
      margin-top: 60px;
    }

    .contact-map iframe {
      width: 100%;
      height: 400px;
      border: none;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
 .main-footer {
    background-color: #152265;
    padding: 40px 20px;
    color: white;
    font-family: Arial, sans-serif;
  }

  .footer-inner {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
    align-items: flex-start;
  }

  .footer-content {
    flex: 1 1 200px; /* Flex-grow, flex-shrink, min-width */
    min-width: 200px;
    max-width: 400px;
  }

  .footer-content h4 {
    font-size: 20px;
    margin-bottom: 15px;
    color: white;
  }

  .link {
    list-style: none;
    padding: 0;
  }

  .link li {
    margin-bottom: 10px;
    font-size: 16px;
    color: white;
    word-wrap: break-word;
  }

  .link a {
    color: white;
    text-decoration: none;
  }

  .link a:hover {
    text-decoration: none;
  }

  .footer-bottom {
    margin-top: 30px;
    text-align: center;
    font-size: 16px;
    color: white;
  }

  .social i {
    margin-right: 8px;
  }

  @media (max-width: 768px) {
    .footer-inner {
      flex-direction: column;
      align-items: flex-start;
    }

    .footer-content {
      width: 100%;
    }
     .link li {
   
    font-size: 12px;

  }
  }
    @media (max-width: 768px) {
      .contact-content {
        flex-direction: column;
      }
    }
    /* Floating Button */
#whatsapp-fab {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #25D366;
    color: white;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    font-size: 30px;
    text-align: center;
    line-height: 60px;
    cursor: pointer;
    z-index: 9999;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease;
}
#whatsapp-fab:hover {
    transform: scale(1.1);
}

/* Chat UI */
#whatsapp-chatbar {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 300px;
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    font-family: Arial, sans-serif;
    z-index: 9998;
    overflow: hidden;
    transform: scaleY(0);
    transform-origin: bottom;
    transition: transform 0.3s ease;
}

/* Show Chat */
#whatsapp-chatbar.active {
    transform: scaleY(1);
}

/* Header */
#whatsapp-chatbar-header {
    background-color: #25D366;
    color: white;
    padding: 12px 15px;
    font-weight: bold;
    font-size: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
#whatsapp-close {
    cursor: pointer;
    font-size: 18px;
    font-weight: normal;
}

/* Body */
#whatsapp-chatbar-body {
    padding: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Input */
#whatsapp-input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 20px;
    outline: none;
    font-size: 14px;
}

/* Send button */
#whatsapp-send {
    background-color: #25D366;
    border: none;
    color: white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
  </style>
</head>
<body>
<div class="main-home">
  <header>
    <div class="logo"><img src="image/new.png" alt="Sebiotimo Logo" /></div>
    <nav class="navbar" id="nav-menu">
      <a href="/">Home</a>
      <a href="/#about">About</a>
      <a href="/#services">Services</a>
      <a href="contact.html">Contact</a>
      <a href="check-appointment.php">Check Appointment</a>
    </nav>
    <div class="right-icons">
      <div id="menu-bars" class="fas fa-bars"></div>
      <div class="btn"><a href="booking.php" style="color: white; text-decoration: none;">Make Appointment</a></div>
    </div>
  </header>
  <br><br><br>

<!-- Contact Section -->
<section class="main-contact" style="width: 100%;" id="contact">
  <div class="contact-heading"><br><br><br><br>
    <h2>Book an appointment</h2>
    <p>Ready to see a doctor? book an appointment now!</p>
  </div>

  <div class="contact-content">
    <!-- Contact Form -->
    <!-- Loader Overlay -->
<div id="loader-overlay">
  <div class="spinner"></div>
</div>

<!-- Contact Form -->
<form class="contact-card contact-form" action="" method="POST" onsubmit="showLoader()">
    <h3>Send a Message</h3>
    <input type="text" name="name" placeholder="Full name" required>
    <input type="email" name="email" placeholder="Email address" required>
    <input type="text" name="phone" pattern="^0\d{10}$" maxlength="11" required title="Phone number" placeholder="Phone number">
    <input type="date" name="date" required>
    <input type="time" name="time" required>
    <select name="specialization" id="specialization" onChange="getdoctors(this.value);" required>
      <option value="">Select specialization</option>
      <?php
      $sql = "SELECT * FROM tblspecialization";
      $stmt = $dbh->query($sql);
      $stmt->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $stmt->fetch()) {
          echo "<option value=\"{$row['ID']}\">{$row['Specialization']}</option>";
      }
      ?>
    </select>
    <select name="doctorlist" id="doctorlist" required>
      <option value="">Select doctor</option>
    </select>
    <textarea name="message" rows="5" placeholder="Message (optional)"></textarea>
    <button type="submit">Submit</button>
</form>

<!-- Styles for Loader -->
<style>
  #loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    z-index: 9999;
    display: none;
    justify-content: center;
    align-items: center;
  }

  .spinner {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #0081cf;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
</style>

<!-- Show Loader Script -->
<script>
  function showLoader() {
    document.getElementById('loader-overlay').style.display = 'flex';
  }
</script>

  
  </div>

</section>
</div>
<!-- Footer -->
<footer class="main-footer">
  <div class="footer-inner">
    <div class="footer-content">
      <h4>Quick Links</h4>
      <ul class="link">
        <li><a href="#home">Home</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li><a href="booking.php">Make Appointment</a></li>
        <li><a href="doctor/login.php">Admin Login</a></li>      </ul>
    </div>

    <div class="footer-content">
      <h4>Contact</h4>
      <ul class="link">
        <li><a href="mailto:afoo@sebiotimomedicalcenter.com"><i class="fa fa-envelope"></i> afoo@sebiotimomedicalcenter.com</a></li>
        <li><a href="tel:+2347053231536"><i class="fa fa-phone"></i> +2347053231536</a></li>
        <li><a href="tel:+2347039514789"><i class="fa fa-phone"></i> +2347039514789</a></li>
      </ul>
    </div>

    <div class="footer-content">
      <h4>Address</h4>
      <ul class="link">
        <li>Headquarter: 1 Bagbe via Ondo-Ore road, Ondo</li><br>
        <li>Ayeyemi Branch: 144, New Garage, Ayeyemi Ondo</li>
      </ul>
    </div>

    <div class="footer-content">
      <h4>Follow Us</h4>
      <ul class="link social">
        <li><a href="#"><i class="fab fa-facebook-f"></i> Facebook</a></li>
        <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
        <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <p>&copy; 2025 Sebiotimo Medical and Diagnostics Center. All rights reserved.</p>
  </div>
</footer>

   <div id="whatsapp-fab" title="Chat with us">
    <i class="fab fa-whatsapp"></i>
</div>

<!-- Chat UI -->
<div id="whatsapp-chatbar">
    <div id="whatsapp-chatbar-header">
        Chat with us
        <span id="whatsapp-close">&times;</span>
    </div>
    <div id="whatsapp-chatbar-body">
        <input type="text" id="whatsapp-input" placeholder="Type your message..." value="Hello sir, I am a Sebiotimo Medical Center Website user." />
        <button id="whatsapp-send"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<script>
// Toggle chat bar
const fab = document.getElementById('whatsapp-fab');
const chatbar = document.getElementById('whatsapp-chatbar');
const closeBtn = document.getElementById('whatsapp-close');
const sendBtn = document.getElementById('whatsapp-send');

fab.addEventListener('click', () => {
    chatbar.classList.toggle('active');
});

closeBtn.addEventListener('click', () => {
    chatbar.classList.remove('active');
});

// Send to WhatsApp
sendBtn.addEventListener('click', () => {
    const message = document.getElementById('whatsapp-input').value.trim();
    if (message !== "") {
        const phone = '2347039514789'; // WhatsApp number
        const encoded = encodeURIComponent(message);
        const url = `https://wa.me/${phone}?text=${encoded}`;
        window.open(url, '_blank');
    }
});
</script> 
        
    <!-- footer ended -->
<script>
  const menuIcon = document.getElementById('menu-bars');
  const navMenu = document.getElementById('nav-menu');

  menuIcon.addEventListener('click', () => {
    navMenu.classList.toggle('active');
  });
</script>
<!-- Optional: AJAX Script to populate doctors based on specialization -->
<script>
function getdoctors(specializationId) {
  fetch('get_doctors.php?specialization=' + specializationId)
    .then(response => response.text())
    .then(data => {
      document.getElementById('doctorlist').innerHTML = data;
    });
}
</script>


<script src="dasm/js/jquery.min.js"></script>
        <script src="dasm/js/bootstrap.bundle.min.js"></script>
        <script src="dasm/js/owl.carousel.min.js"></script>
        <script src="dasm//scrollspy.min.js"></script>
        <script src="dasm/js/custom.js"></script>
    </body>
</body>
</html>
