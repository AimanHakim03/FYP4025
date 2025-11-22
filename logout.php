<?php

include ("common.php");

session_start();
//--- Begin : Create log 
$user  = $_SESSION['Session.userName'];
$event = "Log Keluar";
$type  = $_SESSION['Session.groupName'];
createLog($user , $event, $type);
//--- End   : Create log 

$_SESSION['Session.ad'] = "";
$_SESSION['Session.userID'] = "";
$_SESSION['Session.userName'] = "";
$_SESSION['Session.email'] = "";
$_SESSION['Session.fullName'] = "";
$_SESSION['Session.groupID'] = "";
$_SESSION['Session.groupName'] = "";
$_SESSION['Session.branchID'] = "";
$_SESSION['Session.branchName'] = "";
$_SESSION['Session.branchCode'] = "";
$_SESSION['Session.isUD'] = "";

 header("Location: index.php"); 
//print '<script>window.location.href="index.php";</script>';

?>
