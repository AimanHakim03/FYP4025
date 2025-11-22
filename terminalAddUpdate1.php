<?php

include ("header.php");
include ("forms.php");
session_start(); 
//$conn->debug=true;

if ($_SESSION['Session.userID'] == "") {
	print '<script>alert("'.$errPage.'");window.location.href="index.php";</script>';
}

// FIX FOR UNDEFINED VARIABLES - GET ALL POST/GET DATA
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
$pk = isset($_GET['pk']) ? $_GET['pk'] : (isset($_POST['pk']) ? $_POST['pk'] : '');
$strURL = isset($_POST['strURL']) ? $_POST['strURL'] : (isset($_GET['strURL']) ? $_GET['strURL'] : '');
$SubmitForm = isset($_POST['SubmitForm']) ? $_POST['SubmitForm'] : '';
$oldATM = isset($_POST['oldATM']) ? $_POST['oldATM'] : '';

// Form fields
$atmNo = isset($_POST['atmNo']) ? $_POST['atmNo'] : '';
$atmLoc = isset($_POST['atmLoc']) ? $_POST['atmLoc'] : '';
$atmBranch = isset($_POST['atmBranch']) ? $_POST['atmBranch'] : '';
$glCode = isset($_POST['glCode']) ? $_POST['glCode'] : '';
$remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';
$glCode1 = isset($_POST['glCode1']) ? $_POST['glCode1'] : '';
$remarks1 = isset($_POST['remarks1']) ? $_POST['remarks1'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';
$vendorID = isset($_POST['vendorID']) ? $_POST['vendorID'] : '';

// Default values
if (!isset($isActive))	$isActive= 1; 
if (!isset($delItem1)) 	$delItem1 = "";  
if (!isset($itemCount)) $itemCount = "1";  

$qty100 = isset($_POST['qty100']) ? $_POST['qty100'] : 0;
$qty50 = isset($_POST['qty50']) ? $_POST['qty50'] : 0;
$qty20 = isset($_POST['qty20']) ? $_POST['qty20'] : 0;
$qty10 = isset($_POST['qty10']) ? $_POST['qty10'] : 0;
$mthPay = isset($_POST['mthPay']) ? $_POST['mthPay'] : 0;

// Calculate values for display
$value100 = 100 * $qty100;
$value50 = 50 * $qty50;
$value20 = 20 * $qty20;
$value10 = 10 * $qty10;
$totalValue = $value100 + $value50 + $value20 + $value10;

$sFileName		= "terminalAddUpdate.php";
$sActionFileName= "terminal.php";
$Title 			= "Terminal ATM";

//--- BEGIN : PREPARE DEPT/BRANCH LISTING ---------------------------------------------------------------------------------------
$vendorList = array();
$vendorVal = array();
$sSQL = " SELECT vendorID, vendorName FROM vendor WHERE isDeleted = '0' ORDER BY vendorCode ";
$GetList = $conn->Execute($sSQL);
if ($GetList->RowCount() <> 0){
	while (!$GetList->EOF) {
		array_push ($vendorList, $GetList->fields('vendorName'));
		array_push ($vendorVal, $GetList->fields('vendorID'));
		$GetList->MoveNext();
	}
}
//--- END   : PREPARE DEPT/BRANCH LISTING ---------------------------------------------------------------------------------------


//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = Array();

$i = 1;
$FormLabel[$i]   	= "* No ATM";
$FormElement[$i] 	= "atmNo";
if ($action == 'add') $FormType[$i]	  	= "textUcase"; else $FormType[$i]	  	= "hidden";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank");
$FormSize[$i]    	= "10";
$FormLength[$i]  	= "10";

$i++;
$FormLabel[$i]   	= "* Lokasi";
$FormElement[$i] 	= "atmLoc";
$FormType[$i]		= "textUcase";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank");
$FormSize[$i]    	= "70";
$FormLength[$i]  	= "100";

$i++;
$FormLabel[$i]   	= "Cawangan";
$FormElement[$i] 	= "atmBranch";
$FormType[$i]		= "textUcase";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "50";
$FormLength[$i]  	= "50";

$i++;
$FormLabel[$i]   	= "Kod GL";
$FormElement[$i] 	= "glCode";
$FormType[$i]		= "textUcase";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "10";
$FormLength[$i]  	= "10";

$i++;
$FormLabel[$i]   	= "Catatan";
$FormElement[$i] 	= "remarks";
$FormType[$i]		= "text";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "70";
$FormLength[$i]  	= "100";

$i++;
$FormLabel[$i]   	= "Kod GL 1";
$FormElement[$i] 	= "glCode1";
$FormType[$i]		= "textUcase";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "10";
$FormLength[$i]  	= "10";

$i++;
$FormLabel[$i]   	= "Catatan";
$FormElement[$i] 	= "remarks1";
$FormType[$i]		= "text";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "70";
$FormLength[$i]  	= "100";

$i++;
$FormLabel[$i]   	= "*Jenis";
$FormElement[$i] 	= "type";
$FormType[$i]		= "select";
$FormData[$i]   	= $atmList;
$FormDataValue[$i]	= $atmList;
$FormCheck[$i]   	= array("CheckBlank");
$FormSize[$i]    	= "5";
$FormLength[$i]  	= "5";

$i++;
$FormLabel[$i]   	= "* Jumlah Deno 100";
$FormElement[$i] 	= "qty100";
$FormType[$i]		= "text";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank","CheckNumeric");
$FormSize[$i]    	= "10";
$FormLength[$i]  	= "10";

$i++;
$FormLabel[$i]   	= "* Jumlah Deno 50";
$FormElement[$i] 	= "qty50";
$FormType[$i]		= "text";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank","CheckNumeric");
$FormSize[$i]    	= "10";
$FormLength[$i]  	= "10";

$i++;
$FormLabel[$i]   	= "* Jumlah Deno 20";
$FormElement[$i] 	= "qty20";
$FormType[$i]		= "text";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank","CheckNumeric");
$FormSize[$i]    	= "10";
$FormLength[$i]  	= "10";

$i++;
$FormLabel[$i]   	= "* Jumlah Deno 10";
$FormElement[$i] 	= "qty10";
$FormType[$i]		= "text";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank","CheckNumeric");
$FormSize[$i]    	= "10";
$FormLength[$i]  	= "10";

$i++;
$FormLabel[$i]   	= "Caj Tetap Bulanan";
$FormElement[$i] 	= "mthPay";
$FormType[$i]		= "number";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank","CheckNumeric");
$FormSize[$i]    	= "10";
$FormLength[$i]  	= "10";

$i++;
$FormLabel[$i]   	= "* Vendor";
$FormElement[$i] 	= "vendorID";
$FormType[$i]		= "select";
$FormData[$i]   	= $vendorList;
$FormDataValue[$i]	= $vendorVal;
$FormCheck[$i]   	= array("CheckBlank");
$FormSize[$i]    	= "50";
$FormLength[$i]  	= "50";

if ($action == "update") {
	$i++;
	$FormLabel[$i]  	= "Tarikh Daftar";
	$FormElement[$i] 	= "createdDate";
	$FormType[$i]	  	= "hiddenDateTime";
	$FormData[$i]    	= "";
	$FormDataValue[$i]	= "";
	$FormCheck[$i]   	= array();
	$FormSize[$i]    	= "1";
	$FormLength[$i]  	= "1";

	$i++;
	$FormLabel[$i]  	= "Daftar Oleh";
	$FormElement[$i] 	= "createdBy";
	$FormType[$i]	  	= "hidden";
	$FormData[$i]    	= "";
	$FormDataValue[$i]	= "";
	$FormCheck[$i]   	= array();
	$FormSize[$i]    	= "1";
	$FormLength[$i]  	= "1";
	
	$i++;
	$FormLabel[$i]  	= "Tarikh Kemaskini";
	$FormElement[$i] 	= "updatedDate";
	$FormType[$i]	  	= "hiddenDateTime";
	$FormData[$i]    	= "";
	$FormDataValue[$i]	= "";
	$FormCheck[$i]   	= array();
	$FormSize[$i]    	= "1";
	$FormLength[$i]  	= "1";
	
	$i++;
	$FormLabel[$i]  	= "Kemaskini Oleh";
	$FormElement[$i] 	= "updatedBy";
	$FormType[$i]	  	= "hidden";
	$FormData[$i]    	= "";
	$FormDataValue[$i]	= "";
	$FormCheck[$i]   	= array();
	$FormSize[$i]    	= "1";
	$FormLength[$i]  	= "1";
}
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($SubmitForm <> "") {
//	$conn->debug=true;
	//--- Begin : Call function FormValidation ---  
	for ($i = 1; $i <= count($FormLabel); $i++) {
		for($j=0 ; $j < count($FormCheck[$i]); $j++) {
			$fieldName = $FormElement[$i];
			$fieldValue = isset($$fieldName) ? $$fieldName : '';			
			FormValidation ($FormLabel[$i], 
							$FormElement[$i], 
							$fieldValue,
							$FormCheck[$i][$j],
							$i);
		}
	}	
	
	if ($oldATM <> $atmNo) {
		if (strtoupper(trim(dlookup("atm", "atmNo", "isDeleted='0' AND atmNo=" . tosql($atmNo, "Text")))) == strtoupper($atmNo)) {
			array_push ($strErrMsg, "atmNo");
			print '- <font class=redText>* No ATM <b>( '.$atmNo.' )</b> telah wujud.</font><br>';
		}
	}
	
	//--- End   : Call function FormValidation ---  
	if (count($strErrMsg) == "0") {

		$createdBy 	= $_SESSION['Session.userName'];
		$createdDate = date("Y-m-d H:i:s");             
		$updatedBy 	= $_SESSION['Session.userName'];
		$updatedDate = date("Y-m-d H:i:s");    
		
		$value100 = 100 * $qty100;	$value50 = 50 * $qty50;
		$value20 = 20 * $qty20;		$value10 = 10 * $qty10;
		
		$totalValue = $value100+$value50+$value20+$value10;
		
		$sSQL = "";
		switch(strtolower($SubmitForm)) 
		{
			case "daftar":

				$sSQL	= "INSERT INTO atm (" . 
				          "atmNo," . 
						  "atmLoc,".			
						  "atmBranch,".		
						  "glCode,".		
						  "remarks,".	
						  "glCode1,".			
						  "remarks1,".			
						  "[type],".			
						  "qty100,".			
						  "value100,".			
						  "qty50,".			
						  "value50,".			
						  "qty20,".			
						  "value20,".			
						  "qty10,".			
						  "value10,".			
						  "totalValue,".			
						  "mthPay,".			
				          "vendorID," . 
				          "createdDate," . 
				          "createdBy," . 
				          "updatedDate," . 
				          "updatedBy)" . 
				          " VALUES (" . 
				          tosql($atmNo, "Text") . "," .
				          tosql($atmLoc, "Text") . ",".
						  tosql($atmBranch, "Text") . ",".
				          tosql($glCode, "Text") . ",".
				          tosql($remarks, "Text") . ",".
				          tosql($glCode1, "Text") . ",".
				          tosql($remarks1, "Text") . ",".
				          tosql($type, "Text") . ",".
				          tosql($qty100, "Number") . ",".
				          $value100 . ",".
				          tosql($qty50, "Number") . ",".
				          $value50 . ",".
				          tosql($qty20, "Number") . ",".
				          $value20 . ",".
				          tosql($qty10, "Number") . ",".
				          $value10 . ",".
				          $totalValue . ",".
				          tosql($mthPay, "Number") . ",".
				          tosql($vendorID, "Text") . ",".
						  tosql($createdDate, "Text") . "," .
				          tosql($createdBy, "Text") . ",".
						  tosql($updatedDate, "Text") . "," .
				          tosql($updatedBy, "Text") . ")";
				$msg = "Rekod telah didaftarkan ke dalam sistem";
			break;
			case "kemaskini":
			
			    $sWhere = "atmID=" . tosql($pk, "Text");
	        	$sSQL	= "UPDATE	atm SET " .				          
				          "atmLoc		=" . tosql($atmLoc, "Text").
						  ",atmBranch	=" . tosql($atmBranch, "Text").
						  ",glCode		=" . tosql($glCode, "Text").
						  ",remarks		=" . tosql($remarks, "Text").
						  ",glCode1		=" . tosql($glCode1, "Text").
						  ",remarks1	=" . tosql($remarks1, "Text").
						  ",[type]		=" . tosql($type, "Text").
						  ",qty100		=" . tosql($qty100, "Text").
						  ",value100	=" . $value100.
						  ",qty50		=" . tosql($qty50, "Number").
						  ",value50		=" . $value50.
						  ",qty20		=" . tosql($qty20, "Text").
						  ",value20		=" . $value20.
						  ",qty10		=" . tosql($qty10, "Text").
						  ",value10		=" . $value10.
						  ",totalValue	=" . $totalValue .
						  ",mthPay		=" . tosql($mthPay, "Text").
						  ",vendorID	=" . tosql($vendorID, "Text").
						  ",updatedDate	=" . tosql($updatedDate, "Text") .
				          ",updatedBy	=" . tosql($updatedBy, "Text") ;
				$sSQL .= " where " . $sWhere;
				$msg = "Rekod dikemaskinikan";
			break;
		}
		$rs = $conn->Execute($sSQL);

		//--- Begin : Create log 
		$user  = $_SESSION['Session.userName'];
		if (strtolower($SubmitForm) == "daftar")
			$event = "Terminal ATM telah didaftarkan - ".$atmNo;
		else
			$event = "Terminal ATM telah dikemaskinikan - ".$atmNo;
		$type  = $_SESSION['Session.groupName'];
		createLog($user , $event, $type);
		//--- End   : Create log 				
		
		print '	<script>
					alert ("'.$msg.'");
					window.location.href = "'.$sActionFileName.'?'.$strURL.'";;
				</script>';
	}
}			
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

if ($action == "update") {
	if ($pk <> "") {
		//--- Begin : query database for information ---------------------------------------------
		$sSQL = "	SELECT 	* FROM  atm WHERE 	atmID = " . tosql($pk ,"Text");
		$rs = $conn->Execute($sSQL);
		//--- End   : query database for information ---------------------------------------------
	}
}

if ($action == "add") {
	print '<form name="MyDHSB" action='.$sFileName.'?action='.$action.' method=post>';
} else {
	print '<form name="MyDHSB" action='.$sFileName.'?action='.$action.'&pk='.$pk.' method=post>';
}
print '
<body onload="autoCalc();">
<input type="hidden" name="delItem1" value="'.$delItem1.'">
<input type="hidden" name="strURL" value="'.$strURL.'">
<table border=0 cellpadding=3 cellspacing=1 width="800" align="center" class="lineBG">
	<tr class=Header>
		<td colspan="2" height="30" valign="middle"><b>';
		
if ($action == "add") print 'Daftar '.$Title; else print 'Kemaskini '.$Title.' : '.$rs->fields('branchName');
print '</b></td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 15) print '<tr class="Header"><td colspan=2>Audit Informasi:</td></tr>';
	print '<tr valign=top><td class="Label" align=right width="200">'.$FormLabel[$i].' :</td>';
	if (in_array($FormElement[$i], $strErrMsg))
	  print '<td class=errdata>';
	else
	  print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	if ($action == "update") { 
		$strFormValue = tohtml($rs->fields($FormElement[$i])); 
	} else {
		$strFormValue = ${$FormElement[$i]};
	}
	if ($i <> 9 AND $i <> 10 AND $i <> 11 AND $i <> 12 ) {
		FormEntry($FormLabel[$i], 
				  $FormElement[$i], 
				  $FormType[$i],
				  $strFormValue,
				  $FormData[$i],
				  $FormDataValue[$i],
				  $FormSize[$i],
				  $FormLength[$i]);
	}
	//--- End   : Call function FormEntry ---------------------------------------------------------  
	if ($i==9) {
		print '
		<input type="text" name="'.$FormElement[$i].'" value="'.$strFormValue.'" size="10" maxlength="10" onchange="autoCalc();" style="text-align:right;">
		&nbsp;>>&nbsp;RM&nbsp;<input type="text" name="value100" value="'.number_format($value100,2).'" style="text-align:left; border:none;" class="Data" size="10" disabled>	';
	}
	if ($i==10) {
		print '
		<input type="text" name="'.$FormElement[$i].'" value="'.$strFormValue.'" size="10" maxlength="10" onchange="autoCalc();" style="text-align:right;">
		&nbsp;>>&nbsp;RM&nbsp;<input type="text" name="value50" value="'.number_format($value50,2).'" style="text-align:left; border:none;" class="Data" size="10" disabled>	';
	}
	if ($i==11) {
		print '
		<input type="text" name="'.$FormElement[$i].'" value="'.$strFormValue.'" size="10" maxlength="10" onchange="autoCalc();" style="text-align:right;">
		&nbsp;>>&nbsp;RM&nbsp;<input type="text" name="value20" value="'.number_format($value20,2).'" style="text-align:left; border:none;" class="Data" size="10" disabled>	';
	}
	if ($i==12) {
		print '
		<input type="text" name="'.$FormElement[$i].'" value="'.$strFormValue.'" size="10" maxlength="10" onchange="autoCalc();" style="text-align:right;">
		&nbsp;>>&nbsp;RM&nbsp;<input type="text" name="value10" value="'.number_format($value10,2).'" style="text-align:left; border:none;" class="Data" size="10" disabled>	';
	}
    print '&nbsp;</td></tr>';
	if ($i==12) {
		print '
		<tr valign=top>
			<td class="Label" align=right width="200">Jumlah :</td>
			<td class=Data><input type="text" name="totalValue" value="'.number_format($totalValue,2).'" style="text-align:left; border:none;" class="Data" size="10" disabled>&nbsp;</td>
		</tr>';
	}	
}
	
print '<tr><td colspan=2 align=center class=Data>';
if ($action == "add") {
	print '
	<input type="hidden" name="oldNo" value="">
	<input type=Submit name=SubmitForm class="but" value="Daftar">';
} else { 
    print '
	<input type=hidden name=oldATM value='.$rs->fields('atmNo').'>
	<input type=Submit name=SubmitForm class="but" value="Kemaskini">';
}
print '	<input type="Button" value="Kembali" class="but" onclick="window.location.href=\''.$sActionFileName.'\';">';

print '	</td>
		</tr>
</table>
</form>
<script>
    function newLineList1() {
		e = document.MyDHSB;
        e.newItem1.value = "add";
		e.submit();
	}	
    function DeleteItem1(id) {
		e = document.MyDHSB;
        if (confirm(\'Hapuskan rekod ini?\')) {
			e.delItem1.value = id;
	        e.submit();
        }
	}		
	
	function autoCalc() {
		e = document.MyDHSB;	
		TV=0;
		for(c=0; c<e.elements.length; c++) {
			if (e.elements[c].name=="qty100") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.value100.value = (eval(e.elements[c].value) * 100).toFixed(2);
				TV += eval(e.value100.value);
		    }

			if (e.elements[c].name=="qty50") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.value50.value = (eval(e.elements[c].value) * 50).toFixed(2);
				TV += eval(e.value50.value);
		    }
			
			if (e.elements[c].name=="qty20") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.value20.value = (eval(e.elements[c].value) * 20).toFixed(2);
				TV += eval(e.value20.value);
		    }
			
			if (e.elements[c].name=="qty10") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.value10.value = (eval(e.elements[c].value) * 10).toFixed(2);
				TV += eval(e.value10.value);
		    }
		}
		e.totalValue.value = TV.toFixed(2);
	}	
</script>';
include("footer.php");	
?>