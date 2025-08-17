<?php
session_start();

// if (!isset($_COOKIE['username'])) {
//   die("Unauthorized access");
// }

if (!isset($_COOKIE['username'])) {
    include 'login.php';
    exit();
}elseif($_SESSION['admin'] == true){
    header('Location: dashboard');
    exit();
}else{
    header('Location: newsletter');
    exit();

}


?>