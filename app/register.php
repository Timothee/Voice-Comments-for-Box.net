<?php
session_start();
$_SESSION['auth_token'] = $_REQUEST['auth_token'];
header('Location: index.php');

?>