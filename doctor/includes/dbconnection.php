<?php 
// DB credentials.
define('DB_HOST','www.test.achillesdrill.com');
define('DB_USER','achilles_admin');
define('DB_PASS','Drills250889#');
define('DB_NAME','achilles_test');
// Establish database connection.
try
{
$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e)
{
exit("Error: " . $e->getMessage());
}
?>