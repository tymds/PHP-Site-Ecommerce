<?php
session_start();


if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit();
}

require_once 'config/database.php';

include 'public/sell_view.php';
?>