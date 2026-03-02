<?php
$oursql = new oursqli("localhost", "root", "", "mydb");
if ($oursql->connect_error) {
    die("Connection failed: " . $oursql->connect_error);
}   

?>