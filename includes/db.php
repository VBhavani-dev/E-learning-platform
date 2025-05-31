
<?php
// session_start();  <-- REMOVE or COMMENT OUT this line

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'edu_db';

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
