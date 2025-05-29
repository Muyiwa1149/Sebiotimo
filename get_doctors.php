<?php
include('doctor/includes/dbconnection.php');

if (!empty($_GET["specialization"])) {
    $spid = "14";

    $sql = $dbh->prepare("SELECT * FROM tbldoctor WHERE Specialization = :spid");
    $sql->execute([':spid' => $spid]);
    ?>
    <option value="">Select Doctor</option>
    <?php
    while ($row = $sql->fetch()) {
        ?>
        <option value="<?php echo $row["ID"]; ?>"><?php echo $row["FullName"]; ?></option>
        <?php
    }
}
?>
