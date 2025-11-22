<?php

print '
<style>
<!--
.menuskin			{position:absolute; width:300px; background-color:#F7F7F7; border:1px solid black; 
					font:normal 8pt Trebuchet MS; line-height:20px; z-index:100; visibility:hidden; 
					left: 116px; top: 120px; color}
.menuskin a			{text-decoration:none; padding-left:10px; padding-right:10px; color:black;}
#mouseoverstyle		{background-color:#336699;}
#mouseoverstyle a	{color:white;}
.menu				{text-decoration:none; padding-left:10px; padding-right:10px; color:800000; background-color:#EDEDED;}
-->
</style>
<script language="JavaScript1.2">
//Pop-it menu- By Dynamic Drive
//For full source code and more DHTML scripts, visit http://www.dynamicdrive.com
//This credit MUST stay intact for use

var linkset=new Array()
//SPECIFY MENU SETS AND THEIR LINKS. FOLLOW SYNTAX LAID OUT

linkset[10]=\'<div class="menuitems"><b><a href="org.php">Organisasi</a></b></div>\'
linkset[10]+=\'<div class="menuitems"><b><a href="product.php">Produk</a></b></div>\'
////No need to edit beyond here

var ie4=document.all&&navigator.userAgent.indexOf("Opera")==-1
var ns6=document.getElementById&&!document.all
var ns4=document.layers

function showmenu(e,which){

if (!document.all&&!document.getElementById&&!document.layers)
return

clearhidemenu()

menuobj=ie4? document.all.popmenu : ns6? document.getElementById("popmenu") : ns4? document.popmenu : ""
menuobj.thestyle=(ie4||ns6)? menuobj.style : menuobj

if (ie4||ns6)
menuobj.innerHTML=which
else{
menuobj.document.write(\'<layer name=gui bgColor="#E6E6E6" width=165 onmouseover="clearhidemenu()" onmouseout="hidemenu()">\'+which+\'</layer>\')
menuobj.document.close()
}

menuobj.contentwidth=(ie4||ns6)? menuobj.offsetWidth : menuobj.document.gui.document.width
menuobj.contentheight=(ie4||ns6)? menuobj.offsetHeight : menuobj.document.gui.document.height
eventX=ie4? event.clientX : ns6? e.clientX : e.x
eventY=ie4? event.clientY : ns6? e.clientY : e.y

//Find out how close the mouse is to the corner of the window
var rightedge=ie4? document.body.clientWidth-eventX : window.innerWidth-eventX
var bottomedge=ie4? document.body.clientHeight-eventY : window.innerHeight-eventY

//if the horizontal distance isnt enough to accomodate the width of the context menu
if (rightedge<menuobj.contentwidth)
//move the horizontal position of the menu to the left by its width
menuobj.thestyle.left=ie4? document.body.scrollLeft+eventX-menuobj.contentwidth : ns6? window.pageXOffset+eventX-menuobj.contentwidth : eventX-menuobj.contentwidth
else
//position the horizontal position of the menu where the mouse was clicked
menuobj.thestyle.left=ie4? document.body.scrollLeft+eventX : ns6? window.pageXOffset+eventX : eventX

//same concept with the vertical position
if (bottomedge<menuobj.contentheight)
menuobj.thestyle.top=ie4? document.body.scrollTop+eventY-menuobj.contentheight : ns6? window.pageYOffset+eventY-menuobj.contentheight : eventY-menuobj.contentheight
else
menuobj.thestyle.top=ie4? document.body.scrollTop+event.clientY : ns6? window.pageYOffset+eventY : eventY
menuobj.thestyle.visibility="visible"
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
menuobj.thestyle.visibility=(ie4||ns6)? "hidden" : "hide"
}

function dynamichide(e){
if (ie4&&!menuobj.contains(e.toElement))
hidemenu()
else if (ns6&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget))
hidemenu()
}

function delayhidemenu(){
if (ie4||ns6||ns4)
delayhide=setTimeout("hidemenu()",500)
}

function clearhidemenu(){
if (window.delayhide)
clearTimeout(delayhide)
}

function highlightmenu(e,state){
if (document.all)
source_el=event.srcElement
else if (document.getElementById)
source_el=e.target
if (source_el.className=="menuitems"){
source_el.id=(state=="on")? "mouseoverstyle" : ""
}
else{
while(source_el.id!="popmenu"){
source_el=document.getElementById? source_el.parentNode : source_el.parentElement
if (source_el.className=="menuitems"){
source_el.id=(state=="on")? "mouseoverstyle" : ""
}
}
}
}

if (ie4||ns6)
document.onclick=hidemenu
</script>

<div id="popmenu" class="menuskin" onMouseover="clearhidemenu();highlightmenu(event,\'on\')" onMouseout="highlightmenu(event,\'off\');dynamichide(event)"></div>

<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center" class="bodyBG">
	<tr>
		<td valign="top">
			<form name="MyDHSBForm">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="left" class="bodyBG" height="50">
				<tr><td class="MenuBold" width="1%" valign="top"><a href="index.php"><font class="MenuBold">Home</font></a></td></tr>
			    <tr><td class="MenuBold" width="1%" valign="top"><a href="profile.php"><font class="MenuBold">Profile</font></a></td></tr>
				<tr><td align="left"><a href="logout.php" onClick="if(!confirm(\'Exit from system?\')) return false;"><font class="MenuBold">Logout</font></a></td>
				</tr>';
if ($_SESSION['Session.groupID'] == "8" OR $_SESSION['Session.groupID'] == "9" ) {		
	print '		<tr><td colspan="2" class="Header" height="30"><font class="yellowText">SYSTEM ADMINISTRATION</font></td></tr>';
	if ($_SESSION['Session.groupID'] == "9") {		
		print '	<tr><td class="MenuBold" width="1%" valign="top"><a href="setup.php"><font class="MenuBold">Setup Configuration</font></a></td></tr>
				<tr><td class="MenuBold" width="1%" valign="top"><a href="logs.php"><font class="MenuBold">Audit Logs</font></a></td></tr>';	
	}
	print '		<tr><td class="MenuBold" width="1%" valign="top"><a href="branch.php"><font class="MenuBold">Organization Structure</font></a></td></tr>
				<tr><td class="MenuBold" width="1%" valign="top"><a href="users.php"><font class="MenuBold">User Management</font></a></td></tr>
				<tr><td class="MenuBold" width="1%" valign="top"><a href="subject.php"><font class="MenuBold">Subjects</font></a></td></tr>';
}
	
if ($_SESSION['Session.groupID'] <> "0") { 			
	print '	<tr><td colspan="2" class="Header" height="30"><font class="yellowText">ITEM ADMINISTRATION</font></td></tr>
			<tr><td class="MenuBold" width="1%" valign="top"><a href="specify.php"><font class="MenuBold">Specification Matrix</font></a></td></tr>	
			<tr><td colspan="2" class="Header" height="30"><font class="yellowText">ITEM BANKING</font></td></tr>
			<tr><td class="MenuBold" width="1%" valign="top"><a href="itemmeta.php"><font class="MenuBold">Item Metadata</font></a></td></tr>
			<tr><td class="MenuBold" width="1%" valign="top"><a href="itemcreate.php"><font class="MenuBold">Item Creation</font></a></td></tr>
			<tr><td colspan="2" class="Header" height="30"><font class="yellowText">INSTRUMENT BANKING</font></td></tr>
			<tr><td class="MenuBold" width="1%" valign="top"><a href="instrument.php"><font class="MenuBold">Instrument Specification</font></a></td></tr>
			<tr><td colspan="2" class="Header" height="30"><font class="yellowText">ASSESSMENT</font></td></tr>
			<tr><td class="MenuBold" width="1%" valign="top"><a href="exam.php"><font class="MenuBold">Examination</font></a></td></tr>';
}		

print '		<tr><td colspan="2" class="Header" height="30"><font class="yellowText">REPORTS</font></td></tr>
			<tr><td class="MenuBold" width="1%"><a href="reports.php"><font class="MenuBold">Ad-Hoc Reports</font></a></td></tr>';
/*
if ($_SESSION['Session.groupID'] == "9" ) {					
	print '	<tr><td colspan="2" class="Header" height="30"><font class="yellowText">UTILITIES</font></td></tr>
   			<tr><td class="MenuBold" width="1%"><a href="sqlroot.php"><font class="MenuBold">SQL Query</font></a></td></tr>';
}*/
print '		</table>
			</form>
		</td>
	</tr>
</table>';
?>