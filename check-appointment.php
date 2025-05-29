<?php
session_start();
include('doctor/includes/dbconnection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>Sebiotimo Medical and Diagnostics Center | Healthcare in Ondo</title>
  <meta name="description" content="Sebiotimo Medical and Diagnostics Center in Ondo offers high-quality diagnostic, medical consultation, lab tests, cardiology, and imaging services.">
  <meta name="keywords" content="Sebiotimo, Medical Center Ondo, Diagnostics, Healthcare, Lab Test, Cardiology, Ultrasound, Doctor Ondo, Hospital Ondo, Make Appointment">
  <meta name="author" content="Sebiotimo Medical and Diagnostics Center">

  <!-- Open Graph -->
  <meta property="og:title" content="Sebiotimo Medical and Diagnostics Center" />
  <meta property="og:description" content="High-quality diagnostic and medical services in Ondo." />
  <meta property="og:image" content="image/logo.png" />
  <meta property="og:url" content="https://test.achillesdrill.com/" />
  <meta property="og:type" content="website" />

  <!-- Favicon -->
  <link rel="icon" href="image/logo.png" type="image/png" />

  <!-- CSS and Scripts -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.1.0/uicons-thin-rounded/css/uicons-thin-rounded.css">
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

  .main-contact1 {
    padding: 40px 10px;
    width: 100%;
    box-sizing: border-box;
    
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
    box-sizing: border-box;
  }

  .contact-card h3 {
    margin-bottom: 20px;
    color: #152265;
    font-size: 22px;
  }

  .contact-form input,
  .contact-form button {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 6px;
    font-size: 15px;
  }

  .contact-form input {
    border: 1px solid #ccc;
  }

  .contact-form input:focus {
    border-color: #0081cf;
    outline: none;
  }

  .contact-form button {
    background: #0081cf;
    color: white;
    border: none;
    cursor: pointer;
  }

  .contact-form button:hover {
    background: #006fb3;
  }

  .table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    display: block;
  }

  .table {
    width: 100%;
    border-collapse: collapse;
    min-width: 500px; /* Ensures scrolling */
    font-size: 16px;
  }

  .table th,
  .table td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: left;
  }

  .table th {
    background: #152265;
    color: white;
  }

  .table-responsive::after {
    content: '← Scroll right to see more →';
    display: block;
    font-size: 12px;
    color: #888;
    text-align: center;
    margin-top: 8px;
  }

  @media screen and (max-width: 768px) {
  .main-contact1 {
    
    padding: 20px; /* Includes 20px right padding */
    width: 100%;
    margin: 0 auto;
    box-sizing: border-box;
    font-size: small;
  }
    .contact-card {
      padding: 20px;
    }

    .contact-heading h2 {
      font-size: 22px;
      margin-top: 20px;
    }

    .table {
      font-size: 12px;
    }
  }
  @media screen and (max-width: 768px) {
  .table-responsive {
    max-width: 100vw;  /* no more than viewport width */
    margin-left: 0 !important;  /* remove any left margin */
    padding-left: 0 !important; /* remove any left padding */
    box-sizing: border-box; 
  
  }

  .table {
  max-width: 200px; /* Ensures scrolling */
    font-size: 12px;
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

  <section class="main-contact" id="contact">
    <div class="contact-heading"><br><br><br><br>
      <h2>Check Your Appointment</h2>
      <p>confirm if your appointment has been approved</p>
    </div>

    <div class="contact-content">
      <!-- Contact Form -->
      <form class="contact-card contact-form" method="POST">
        <h3>Search Appointment History</h3>
        <input type="text" name="searchdata" required placeholder="Email/Mobile No.">
        <button type="submit" name="search">Check</button>
      </form>
    </div>

  </section>

  
    <?php
    if (isset($_POST['search'])) {
      $sdata = $_POST['searchdata'];
      ?>
  <div class="contact-content" style="margin-bottom: 100px;">
  <div class="contact-card contact-info main-contact1">
    <h3>Search Results</h3>
    <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>S.No</th>
              <th>Appointment Number</th>
              <th>Patient Name</th>
              <th>Mobile Number</th>
              <th>Email</th>
              <th>Appointment Date</th>
              <th>Appointment Time</th>
              <th>Status</th>
              <th>Remark</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql = "SELECT * FROM tblappointment WHERE email LIKE :sdata OR MobileNumber LIKE :sdata";
            $query = $dbh->prepare($sql);
            $query->bindValue(':sdata', "$sdata%");
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);
            $cnt = 1;
            if ($query->rowCount() > 0) {
              foreach ($results as $row) {
                // Format the date and time
                $formattedDate = date("F j, Y", strtotime($row->AppointmentDate));
                $formattedTime = date("g:i a", strtotime($row->AppointmentTime));
                ?>
                <tr>
                  <td><?php echo htmlentities($cnt); ?></td>
                  <td><?php echo htmlentities($row->AppointmentNumber); ?></td>
                  <td><?php echo htmlentities($row->Name); ?></td>
                  <td><?php echo htmlentities($row->MobileNumber); ?></td>
                  <td><?php echo htmlentities($row->Email); ?></td>
                  <td><?php echo htmlentities($formattedDate); ?></td>
                  <td><?php echo htmlentities($formattedTime); ?></td>
                  <td><?php echo $row->Status ? htmlentities($row->Status) : "Not Updated Yet"; ?></td>
                  <td><?php echo $row->Remark ? htmlentities($row->Remark) : "Not Updated Yet"; ?></td>
                </tr>
                <?php
                $cnt++;
              }
            } else {
              echo '<tr><td colspan="9">No record found against this search</td></tr>';
            }
            ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
    <?php } ?>
  
  
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
        <li><a href="doctor/login.php">Admin Login</a></li>
      </ul>
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
        <input type="text" id="whatsapp-input" placeholder="Type your message..." value="Hello sir, I am a Sebiotimo Medical Center Website user."/>
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
<script>
  document.getElementById('menu-bars').addEventListener('click', () => {
    document.getElementById('nav-menu').classList.toggle('active');
  });
</script>

</body>
</html>