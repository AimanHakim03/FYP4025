<?php

include("common.php");
// Session is already started in common.php, no need to start again!
include("setupinfo.php");
include("dhsbQuery.php");

if (!isset($CR281ed5ec223130fa7b5b181e176aaf67)) {
    $CR281ed5ec223130fa7b5b181e176aaf67 = tc4HqejcxVXtI($yVZcSz2OuGE5U,$T5ZZPpvAKXOsI);
}

// Cache session variables for easier access
$sessionGroupID = $_SESSION['Session.groupID'] ?? '';
$sessionUserName = $_SESSION['Session.userName'] ?? '';
$sessionFullName = $_SESSION['Session.fullName'] ?? '';
$sessionGroupName = $_SESSION['Session.groupName'] ?? '';
$sessionBranchName = $_SESSION['Session.branchName'] ?? '';

print '
<!DOCTYPE html>
<html lang="ms">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>' . $emaNetis . '</title>
	<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
	<meta name="keywords" content="'.$drowyeKatem.'">
	<meta name="description" content="'.$cseDatem.'">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="expires" content="0"> 
	<meta http-equiv="cache-control" content="no-cache">
	<link rel="stylesheet" href="images/zssb.css">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

	
<style type="text/css">
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

body {
	font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
	background: #f8fafc;
	color: #1e293b;
}

/* Top Header Bar */
.top-header {
	background: #ffffff;
	border-bottom: 1px solid #e2e8f0;
	padding: 1rem 2rem;
	display: flex;
	align-items: center;
	justify-content: space-between;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.logo-section {
	display: flex;
	align-items: center;
	gap: 1rem;
}

.logo-section img {
	height: 48px;
	width: auto;
}

.system-title {
	font-size: 1.25rem;
	font-weight: 700;
	color: #0f172a;
}

.header-right {
	display: flex;
	align-items: center;
	gap: 1rem;
	color: #64748b;
	font-size: 0.875rem;
}

.secure-badge {
	display: flex;
	align-items: center;
	gap: 0.5rem;
	padding: 0.5rem 0.75rem;
	background: #f1f5f9;
	border-radius: 6px;
}

.secure-badge i {
	color: #10b981;
}

/* User Info Bar */
.user-info-bar {
	background: #ffffff;
	border-bottom: 1px solid #e2e8f0;
	padding: 0.875rem 2rem;
	display: flex;
	align-items: center;
	justify-content: space-between;
}

.user-details {
	display: flex;
	align-items: center;
	gap: 0.75rem;
	font-size: 0.875rem;
	color: #475569;
}

.user-id {
	font-weight: 600;
	color: #0f172a;
}

.user-badge {
	display: inline-block;
	padding: 0.25rem 0.75rem;
	background: #dbeafe;
	color: #1e40af;
	border-radius: 12px;
	font-size: 0.75rem;
	font-weight: 600;
}

.user-badge.admin {
	background: #e0e7ff;
	color: #4338ca;
}

.user-badge.regular {
	background: #d1fae5;
	color: #065f46;
}

/* Navigation Bar */
.nav-bar {
	background: #ffffff;
	border-bottom: 1px solid #e2e8f0;
	padding: 0;
}

.nav-container {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 0.5rem;
	padding: 0.5rem 2rem;
}

.nav-item {
	display: flex;
	flex-direction: column;
	align-items: center;
	padding: 1rem 1.5rem;
	text-decoration: none;
	color: #64748b;
	transition: all 0.2s ease;
	border-radius: 8px;
	position: relative;
}

.nav-item:hover {
	background: #f8fafc;
	color: #2563eb;
}

.nav-item i {
	font-size: 1.5rem;
	margin-bottom: 0.5rem;
}

.nav-item img {
	width: 32px;
	height: 32px;
	margin-bottom: 0.5rem;
	opacity: 0.7;
	transition: opacity 0.2s ease;
}

.nav-item:hover img {
	opacity: 1;
}

.nav-item span {
	font-size: 0.75rem;
	font-weight: 500;
	text-align: center;
}

/* Dropdown Menu Styles */
#popitmenu {
	position: absolute;
	background: #ffffff;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
	font: normal 14px "Inter", sans-serif;
	line-height: 1.5;
	z-index: 100;
	visibility: hidden;
	min-width: 200px;
	padding: 0.5rem 0;
}

#popitmenu a {
	text-decoration: none;
	padding: 0.75rem 1rem;
	color: #475569;
	display: block;
	transition: all 0.15s ease;
	font-size: 0.875rem;
}

#popitmenu a:hover {
	background: #f1f5f9;
	color: #2563eb;
	padding-left: 1.25rem;
}

/* Content Area */
.content-wrapper {
	padding: 2rem;
	max-width: 1400px;
	margin: 0 auto;
}

/* Responsive Design */
@media (max-width: 1024px) {
	.nav-container {
		flex-wrap: wrap;
	}
	
	.nav-item {
		padding: 0.75rem 1rem;
	}
}

@media (max-width: 768px) {
	.top-header {
		flex-direction: column;
		gap: 1rem;
		text-align: center;
	}
	
	.system-title {
		font-size: 1rem;
	}
	
	.user-info-bar {
		flex-direction: column;
		gap: 0.5rem;
		text-align: center;
	}
	
	.nav-container {
		gap: 0.25rem;
	}
	
	.nav-item span {
		font-size: 0.65rem;
	}
}
</style>

<script type="text/javascript" src="dropdowncontent.js">
/***********************************************
* Drop Down/ Overlapping Content- © Dynamic Drive (www.dynamicdrive.com)
* This notice must stay intact for legal use.
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/
</script>

<script type="text/javascript">
/***********************************************
* Pop-it menu- © Dynamic Drive (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/
var defaultMenuWidth="200px" //set default menu width.

var linkset=new Array()
//SPECIFY MENU SETS AND THEIR LINKS. FOLLOW SYNTAX LAID OUT';

if ($sessionGroupID == "3" || $sessionGroupID == "4" || $sessionGroupID == "9") {		
	if ($sessionGroupID == "9") {
		print '
		linkset[0]=\'<a href="setup.php">Konfigurasi Sistem</a>\'
		linkset[0]+=\'<a href="users.php">Pengguna</a>\'';
	} 
	print '
		linkset[0]=\'<a href="users.php">Pengguna</a>\'
		linkset[0]+=\'<a href="terminal.php">Terminal ATM</a>\'
		linkset[0]+=\'<a href="vendor.php">Syarikat</a>\'';
	if ($sessionGroupID == "3" || $sessionGroupID == "9") {		
		print ' 
		linkset[0]+=\'<a href="logs.php">Audit Aktiviti</a>\'';
	}
}

print '
	linkset[1]=\'<a href="arahan.php">Senarai Arahan</a>\'';

if ($sessionGroupID == "3" || $sessionGroupID == "4" || $sessionGroupID == "5" || $sessionGroupID == "9") {	
	print '
		linkset[1]+=\'<a href="arahan_baru.php">Sedia Arahan Baru</a>\'';
}

if ($sessionGroupID == "3" || $sessionGroupID == "4" || $sessionGroupID == "9") {	
	print '
	linkset[2]=\'<a href="journalprocess.php">Penyediaan Jernal ATM</a>\'
	linkset[2]+=\'<a href="journalprocessCDM.php">Penyediaan Jernal CDM</a>\'
	linkset[2]+=\'<a href="journal.php">Senarai Jernal</a>\'';
}

if ($sessionGroupID == "3" || $sessionGroupID == "4" || $sessionGroupID == "9") {	
	print '
	linkset[3]=\'<a href="import.php">Import Data IBS</a>\'
	linkset[3]+=\'<a href="cash.php">Data IBS</a>\'';
}

print '
////No need to edit beyond here

var ie5=document.all && !window.opera
var ns6=document.getElementById

if (ie5||ns6)
document.write(\'<div id="popitmenu" onMouseover="clearhidemenu();" onMouseout="dynamichide(event)"></div>\')

function iecompattest(){
return (document.compatMode && document.compatMode.indexOf("CSS")!=-1)? document.documentElement : document.body
}

function showmenu(e, which, optWidth){
if (!document.all&&!document.getElementById)
return
clearhidemenu()
menuobj=ie5? document.all.popitmenu : document.getElementById("popitmenu")
menuobj.innerHTML=which
menuobj.style.width=(typeof optWidth!="undefined")? optWidth : defaultMenuWidth
menuobj.contentwidth=menuobj.offsetWidth
menuobj.contentheight=menuobj.offsetHeight
eventX=ie5? event.clientX : e.clientX
eventY=ie5? event.clientY : e.clientY
//Find out how close the mouse is to the corner of the window
var rightedge=ie5? iecompattest().clientWidth-eventX : window.innerWidth-eventX
var bottomedge=ie5? iecompattest().clientHeight-eventY : window.innerHeight-eventY
//if the horizontal distance isn\'t enough to accomodate the width of the context menu
if (rightedge<menuobj.contentwidth)
//move the horizontal position of the menu to the left by it\'s width
menuobj.style.left=ie5? iecompattest().scrollLeft+eventX-menuobj.contentwidth+"px" : window.pageXOffset+eventX-menuobj.contentwidth+"px"
else
//position the horizontal position of the menu where the mouse was clicked
menuobj.style.left=ie5? iecompattest().scrollLeft+eventX+"px" : window.pageXOffset+eventX+"px"
//same concept with the vertical position
if (bottomedge<menuobj.contentheight)
menuobj.style.top=ie5? iecompattest().scrollTop+eventY-menuobj.contentheight+"px" : window.pageYOffset+eventY-menuobj.contentheight+"px"
else
menuobj.style.top=ie5? iecompattest().scrollTop+event.clientY+"px" : window.pageYOffset+eventY+"px"
menuobj.style.visibility="visible"
return false
}

function contains_ns6(a, b) {
//Determines if 1 element in contained in another- by Brainjar.com
while (b.parentNode)
if ((b = b.parentNode) == a)
return true;
return false;
}

function hidemenu(){
if (window.menuobj)
menuobj.style.visibility="hidden"
}

function dynamichide(e){
if (ie5&&!menuobj.contains(e.toElement))
hidemenu()
else if (ns6&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget))
hidemenu()
}

function delayhidemenu(){
delayhide=setTimeout("hidemenu()",500)
}

function clearhidemenu(){
if (window.delayhide)
clearTimeout(delayhide)
}

if (ie5||ns6)
document.onclick=hidemenu

</script>
</head>
<body>

<!-- Top Header -->
<div class="top-header">
	<div class="logo-section">';
if (isset($ogoLrenwo) && $ogoLrenwo <> '') {
    print '<img src="images/'.$ogoLrenwo.'" alt="Logo">';
}
print '		<div class="system-title">'.$emaNetis.'</div>
	</div>
	<div class="header-right">
		<div class="secure-badge">
			<i class="fas fa-shield-alt"></i>
			<span>Secure Connection</span>
		</div>
		<div><i class="far fa-calendar"></i> ' . date("d M, Y") . '</div>
	</div>
</div>';

if ($sessionUserName !== "") {
	// Determine badge class based on group ID
	$badgeClass = 'user-badge';
	if ($sessionGroupID == "9" || $sessionGroupID == "3") {
		$badgeClass .= ' admin';
	} else {
		$badgeClass .= ' regular';
	}
	
	print '
<!-- User Info Bar -->
<div class="user-info-bar">
	<div class="user-details">
		<span class="user-id">ID PENGGUNA: '.$sessionUserName.'</span>
		<span>•</span>
		<span>'.$sessionFullName.'</span>
		<span class="'.$badgeClass.'">'.$sessionGroupName.'</span>';
	if ($sessionBranchName != '') {
		print '<span>•</span>
		<span>'.$sessionBranchName.'</span>';
	}
	print '
	</div>
</div>

<!-- Navigation Bar -->
<nav class="nav-bar">
	<div class="nav-container">
		<a href="home.php" class="nav-item">
			<i class="fa-solid fa-house"></i>
			<span>Laman Utama</span>
		</a>
		
		<a href="profile.php" class="nav-item">
			<i class="fa-solid fa-user"></i>
			<span>Profil</span>
		</a>';

	if ($sessionGroupID == "3" || $sessionGroupID == "4" || $sessionGroupID == "9") {	
		print '
		<a href="#" class="nav-item" onMouseover="showmenu(event,linkset[0], \'200px\')" onMouseout="delayhidemenu()">
			<i class="fa-solid fa-screwdriver-wrench"></i>
			<span>Pentadbir Sistem</span>
		</a>';
	}
	
/***********************************************	
	if ($sessionGroupID == "3" || $sessionGroupID == "4" || $sessionGroupID == "9") {	
		print '
		<a href="#" class="nav-item" onMouseover="showmenu(event,linkset[3], \'200px\')" onMouseout="delayhidemenu()">
			<i class="fa-solid fa-database"></i>
			<span>Data IBS</span>
		</a>';
	}
***********************************************/

	if ($sessionGroupID == "3" || $sessionGroupID == "4" || $sessionGroupID == "5" || $sessionGroupID == "9") {	
		print '
		<a href="#" class="nav-item" onMouseover="showmenu(event,linkset[1], \'200px\')" onMouseout="delayhidemenu()">
			<i class="fa-solid fa-file-signature"></i>
			<span>Arahan</span>
		</a>';
	} else {
		print '
		<a href="arahan.php" class="nav-item">
			<i class="fa-solid fa-file-signature"></i>
			<span>Senarai Arahan</span>
		</a>';
	}
	
	print '
		<a href="collect.php" class="nav-item">
			<i class="fa-solid fa-inbox"></i>
			<span>CDM</span>
		</a>';

	if ($sessionGroupID == "3" || $sessionGroupID == "4" || $sessionGroupID == "9") {	
		print '
		<a href="#" class="nav-item" onMouseover="showmenu(event,linkset[2], \'220px\')" onMouseout="delayhidemenu()">
			<i class="fa-solid fa-book"></i>
			<span>Jernal</span>
		</a>
		
		<a href="track.php" class="nav-item">
			<i class="fa-solid fa-desktop"></i>
			<span>Pantau Terminal</span>
		</a>';
	} elseif ($sessionGroupID == "6") {
		print '
		<a href="track.php" class="nav-item">
			<i class="fa-solid fa-desktop"></i>
			<span>Pantau Terminal</span>
		</a>';
	}
	
	print '
		<a href="reports.php" class="nav-item">
			<i class="fa-solid fa-chart-bar"></i>
			<span>Laporan</span>
		</a>
		
		<a href="logout.php" class="nav-item" onClick="if(!confirm(\'Keluar Sistem?\')) return false;">
			<i class="fa-solid fa-right-from-bracket"></i>
			<span>Keluar</span>
		</a>
	</div>
</nav>';
} 

print '
<div class="content-wrapper">';
?>