<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = "fyi_registration";
$conn = mysqli_connect($servername, $username, $password);
$connect= mysqli_select_db($conn, $db);
// if($connect){
//    echo "connected succefully";
// } else{
//     echo "connect unsuccefully";
// }
// ?>
