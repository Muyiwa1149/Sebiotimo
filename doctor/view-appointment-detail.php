<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
require '../vendor/autoload.php'; // PHPMailer via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (strlen($_SESSION['damsid']) == 0) {
    header('location:logout.php');
    exit();
}

if (isset($_POST['submit'])) {
    $eid = $_GET['editid'];
    $status = $_POST['status'];
    $remark = $_POST['remark'];

    // Update appointment
    $sql = "UPDATE tblappointment SET Status=:status, Remark=:remark WHERE ID=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->bindParam(':remark', $remark, PDO::PARAM_STR);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->execute();

    // Fetch details for email
    $stmt = $dbh->prepare("SELECT a.*, s.Specialization, d.FullName 
                           FROM tblappointment a
                           LEFT JOIN tblspecialization s ON a.Specialization = s.ID
                           LEFT JOIN tbldoctor d ON a.Doctor = d.ID
                           WHERE a.ID = :id");
    $stmt->bindParam(':id', $eid, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $name = $result['Name'];
        $email = $result['Email'];
        $specialization = $result['Specialization'];
        $doctor = $result['FullName'];
        $date = date('F j, Y', strtotime($result['AppointmentDate']));
        $time = date('h:i A', strtotime($result['AppointmentTime']));

        // Email appearance
        $subject = ($status == 'Approved') ? 'Appointment Approved' : 'Appointment Canceled';
        $bgColor = ($status == 'Approved') ? ' #152265' : ' #cc0000';
        $statusText = ($status == 'Approved') ? 'has been <strong>approved</strong>' : 'has been <strong>canceled</strong>';
        $statusFoot = ($status == 'Approved') ? 'Thank you for choosing us' : 'Kindly reschedule your appointment to a later date.<br>Thank you for choosing us';
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'mail.achillesdrill.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'admin@achillesdrill.com';
            $mail->Password = 'Drills250889#';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('admin@achillesdrill.com', 'Sebiotimo Medical and Diagnostics Center');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = '
            <!DOCTYPE html>
            <html lang="en">
            <head><meta charset="UTF-8"><title>' . $subject . '</title></head>
            <body style="margin:0; padding:0; background-color:#f5f8fa;">
              <table cellpadding="0" cellspacing="0" width="100%" style="padding: 20px 0;">
                <tr>
                  <td align="center">
                    <table cellpadding="0" cellspacing="0" width="600" style="background-color:#ffffff; border-radius:8px;">
                      <tr>
                        <td style="padding: 20px; background-color:' . $bgColor . '; color:#fff; text-align:center; font-size:18px; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                          Sebiotimo Medical Center
                        </td>
                      </tr>
                      <tr>
                        <td style="padding:30px; font-size:16px; color:#333;">
                          <p>Dear ' . htmlspecialchars($name) . ',</p>
                          <p>Your appointment ' . $statusText . '. Below are the details:</p>
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
                          <p style="margin-top:30px;">' . $statusFoot . '.</p>
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
            error_log("Email error: " . $mail->ErrorInfo);
        }
    }
}
?>
<?php if (isset($_POST['submit'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
  title: 'Success!',
  text: 'Remark and status have been updated successfully.',
  icon: 'success',
  confirmButtonText: 'OK'
}).then(() => {
  window.location.href = 'all-appointment.php';
});
</script>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
	
	<title>Sebiotimo Medical Center|| View Appointment Detail</title>
	
	<link rel="stylesheet" href="libs/bower/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="libs/bower/material-design-iconic-font/dist/css/material-design-iconic-font.css">
	<!-- build:css assets/css/app.min.css -->
	<link rel="stylesheet" href="libs/bower/animate.css/animate.min.css">
	<link rel="stylesheet" href="libs/bower/fullcalendar/dist/fullcalendar.min.css">
	<link rel="stylesheet" href="libs/bower/perfect-scrollbar/css/perfect-scrollbar.css">
	<link rel="stylesheet" href="assets/css/bootstrap.css">
	<link rel="stylesheet" href="assets/css/core.css">
	<link rel="icon" href="../image/logo.png" type="image/png" />
	<link rel="stylesheet" href="assets/css/app.css">
	<!-- endbuild -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,900,300">
	<script src="libs/bower/breakpoints.js/dist/breakpoints.min.js"></script>
	<script>
		Breakpoints();
	</script>
	
</head>
	
<body class="menubar-left menubar-unfold menubar-light theme-primary">
<!--============= start main area -->



<?php include_once('includes/header.php');?>

<?php include_once('includes/sidebar.php');?>



<!-- APP MAIN ==========-->
<main id="app-main" class="app-main">
  <div class="wrap">
	<section class="app-content">
		<div class="row">
			<!-- DOM dataTable -->
			<div class="col-md-12">
				<div class="widget">
					<header class="widget-header">
						<h4 class="widget-title" style="color: blue">Appointment Details</h4>
					</header><!-- .widget-header -->
					<hr class="widget-separator">
					<div class="widget-body">
						<div class="table-responsive">
							<?php
                  $eid=$_GET['editid'];
$sql="SELECT * from tblappointment  where ID=:eid";
$query = $dbh -> prepare($sql);
$query-> bindParam(':eid', $eid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);

$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $row)
{               ?>
							<table border="1" class="table table-bordered mg-b-0">
                                            <tr>
    <th>Appointment Number</th>
    <td><?php  echo $aptno=($row->AppointmentNumber);?></td>
    <th>Patient Name</th>
    <td><?php  echo $row->Name;?></td>
  </tr>
  
  <tr>
    <th>Mobile Number</th>
    <td><?php  echo $row->MobileNumber;?></td>
    <th>Email</th>
    <td><?php  echo $row->Email;?></td>
  </tr>
   <tr>
    <th>Appointment Date</th>
    <td><?php  echo $row->AppointmentDate;?></td>
    <th>Appointment Time</th>
    <td><?php  echo $row->AppointmentTime;?></td>
  </tr>
   
  <tr>
    <th>Apply Date</th>
    <td><?php  echo $row->ApplyDate;?></td>
     <th>Appointment Final Status</th>

    <td colspan="4"> <?php  $status=$row->Status;
    
if($row->Status=="")
{
  echo "Not yet updated";
}

if($row->Status=="Approved")
{
 echo "Your appointment has been approved";
}


if($row->Status=="Cancelled")
{
  echo "Your appointment has been cancelled";
}



     ;?></td>
  </tr>
   <tr>
    
<th >Remark</th>
 <?php if($row->Remark==""){ ?>

                     <td colspan="3"><?php echo "Not Updated Yet"; ?></td>
<?php } else { ?>                  <td colspan="3"> <?php  echo htmlentities($row->Remark);?>
                  </td>
                  <?php } ?>
   
  </tr>
 
<?php $cnt=$cnt+1;}} ?>

</table> 
<br>

 
<?php 

if ($status=="" ){
?> 
<p align="center"  style="padding-top: 20px">                            
 <button class="btn btn-primary waves-effect waves-light w-lg" data-toggle="modal" data-target="#myModal">Take Action</button></p>  

<?php } ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
     <div class="modal-content">
      <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Take Action</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                <table class="table table-bordered table-hover data-tables">

                                 <form method="post" name="submit">

                                
                               
     <tr>
    <th>Remark :</th>
    <td>
    <textarea name="remark" placeholder="Remark" rows="12" cols="14" class="form-control wd-450" required="true"></textarea></td>
  </tr> 
     
  <tr>
    <th>Status :</th>
    <td>

   <select name="status" class="form-control wd-450" required="true" >
     <option value="Approved" selected="true">Approved</option>
     <option value="Cancelled">Cancelled</option>
     
   </select></td>
  </tr>
</table>
</div>
<div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
 <button type="submit" name="submit" class="btn btn-primary">Update</button>
  
  </form>
  

</div>

                      
                        </div>
                    </div>

						</div>

					</div><!-- .widget-body -->
					
   
				</div><!-- .widget -->
			</div><!-- END column -->
			
			
		</div><!-- .row -->
	</section><!-- .app-content -->
</div><!-- .wrap -->
  <!-- APP FOOTER -->
  <?php include_once('includes/footer.php');?>
  <!-- /#app-footer -->
</main>
<!--========== END app main -->

	<!-- APP CUSTOMIZER -->
<?php include_once('includes/customizer.php');?>

	
		<!-- build:js assets/js/core.min.js -->
	<script src="libs/bower/jquery/dist/jquery.js"></script>
	<script src="libs/bower/jquery-ui/jquery-ui.min.js"></script>
	<script src="libs/bower/jQuery-Storage-API/jquery.storageapi.min.js"></script>
	<script src="libs/bower/bootstrap-sass/assets/javascripts/bootstrap.js"></script>
	<script src="libs/bower/jquery-slimscroll/jquery.slimscroll.js"></script>
	<script src="libs/bower/perfect-scrollbar/js/perfect-scrollbar.jquery.js"></script>
	<script src="libs/bower/PACE/pace.min.js"></script>
	<!-- endbuild -->

	<!-- build:js assets/js/app.min.js -->
	<script src="assets/js/library.js"></script>
	<script src="assets/js/plugins.js"></script>
	<script src="assets/js/app.js"></script>
	<!-- endbuild -->
	<script src="libs/bower/moment/moment.js"></script>
	<script src="libs/bower/fullcalendar/dist/fullcalendar.min.js"></script>
	<script src="assets/js/fullcalendar.js"></script>
</body>
</html>