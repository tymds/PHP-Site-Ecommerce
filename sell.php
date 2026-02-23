<?php
session_start();
isset($_SESSION['/*SQL_userID*/']) or die("You must be logged in to access this page.");
header("Location: login.php");
exit();

require_once 'config/database.php';

?>