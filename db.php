<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mysqli = new mysqli("localhost", "root", "", "php_exam");
$mysqli->set_charset("utf8mb4");

echo "DB OK";