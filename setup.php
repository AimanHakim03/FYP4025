<?php

include ('header.php');
include ('forms.php');
//$conn->debug=true;

$sFileName		= 'setup.php';
$sActionFileName= 'setup.php';
$Title 			= 'KONFIGURASI SISTEM';

if ($_SESSION['Session.groupID'] <> "9")	{
	print '<script>alert("'.$errPage.'");window.location.href="index.php";</script>';
}

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = Array();

$a = 1;
$FormLabel[$a]   	= "Site Header";
$FormElement[$a] 	= "siteName";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "100";
$FormLength[$a]  	= "100";

$a++;
$FormLabel[$a]   	= "Site Footer";
$FormElement[$a] 	= "siteFooter";
$FormType[$a]	  	= "textarea";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "80";
$FormLength[$a]  	= "4";

$a++;
$FormLabel[$a]   	= "Status Bar";
$FormElement[$a] 	= "statusBar";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "100";
$FormLength[$a]  	= "100";

$a++;
$FormLabel[$a]   	= "Kata Kunci Meta";
$FormElement[$a] 	= "dhsb_metaKeyword";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "100";
$FormLength[$a]  	= "255";

$a++;
$FormLabel[$a]   	= "Keterangan Meta";
$FormElement[$a] 	= "dhsb_metaDesc";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "100";
$FormLength[$a]  	= "255";

$a++;
$FormLabel[$a]   	= "Nama Pendaftar";
$FormElement[$a] 	= "dhsb_registerName";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "ID Pendaftar";
$FormElement[$a] 	= "dhsb_registerID";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Tarikh Daftar";
$FormElement[$a] 	= "dhsb_registerDate";
$FormType[$a]	  	= "hiddenDate";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "ID Lesen";
$FormElement[$a] 	= "dhsb_licenceKey";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Tarikh Tamat";
$FormElement[$a] 	= "dhsb_expiryDate";
$FormType[$a]	  	= "hiddenDate";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Nama Produk";
$FormElement[$a] 	= "dhsb_productName";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Nama Pemilik";
$FormElement[$a] 	= "ownerName";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Alamat";
$FormElement[$a] 	= "ownerAddress";
$FormType[$a]	  	= "textarea";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "40";
$FormLength[$a]  	= "5";

$a++;
$FormLabel[$a]   	= "No Talipon";
$FormElement[$a] 	= "ownerPhone";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "No Faksimili";
$FormElement[$a] 	= "ownerFax";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Email";
$FormElement[$a] 	= "ownerEmail";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank,CheckEmail);
$FormSize[$a]    	= "50";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Logo";
$FormElement[$a] 	= "ownerLogo";
$FormType[$a]	  	= "file";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "100";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Kod Bar Logo";
$FormElement[$a] 	= "barcodeLogo";
$FormType[$a]	  	= "file";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "100";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Banner";
$FormElement[$a] 	= "ownerBanner";
$FormType[$a]	  	= "file";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "100";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "Header";
$FormElement[$a] 	= "rptHeader";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "100";
$FormLength[$a]  	= "100";

$a++;
$FormLabel[$a]   	= "Footer";
$FormElement[$a] 	= "rptFooter";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "100";
$FormLength[$a]  	= "100";

$a++;
$FormLabel[$a]   	= "IP Address/Hostname";
$FormElement[$a] 	= "ldap";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Nama Domain";
$FormElement[$a] 	= "dn";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Email BCC";
$FormElement[$a] 	= "emailBCC";
$FormType[$a]	  	= "text";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "80";
$FormLength[$a]  	= "100";

$a++;
$FormLabel[$a]  	= "Tarikh Kemaskini";
$FormElement[$a] 	= "updatedDate";
$FormType[$a]	  	= "hiddenDateTime";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a] 		= "Dikemaskini Oleh";
$FormElement[$a]	= "updatedBy";
$FormType[$a]   	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]  	= array();
$FormSize[$a]  	 	= "1";
$FormLength[$a]	 	= "1";
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

//--- Begin	:Get all the URL querystrings
$action = $HTTP_GET_VARS["action"]; 
$pk = $HTTP_GET_VARS["pk"];
//--- End	:Get all the URL querystrings

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($SubmitForm <> '') {
	//--- Begin : Call function FormValidation ---  
	for ($i = 1; $i <= count($FormLabel); $i++) {
		for($j=0 ; $j < count($FormCheck[$i]); $j++) {
			FormValidation ($FormLabel[$i], 
							$FormElement[$i], 
							$$FormElement[$i],
							$FormCheck[$i][$j],
							$i);
		}
	}	
	//--- End   : Call function FormValidation ---  
	if (count($strErrMsg) == '0') {
		if ($ownerLogo == ''){
			$ownerLogo_name = $oldFile;
		} else {
			$uploadFile =  $HTTP_POST_FILES['ownerLogo']['tmp_name'];
			$toFile = 'images\\'.$ownerLogo_name;
//			print $uploadFile.'<==>'.$toFile;
			if ($uploadFile <> "none"){
				if (!copy( $uploadFile, $toFile ) ) 
		             $msg =  'File cannot be upload into server'; 
			} else
				$msg = 'File cannot be empty';
			$ownerLogo = basename($uploadFile);
		}	

		if ($barcodeLogo == ''){
			$barcodeLogo_name = $oldBarcodeFile;
		} else {
			$uploadFile =  $HTTP_POST_FILES['barcodeLogo']['tmp_name'];
			$toFile = 'images\\'.$barcodeLogo_name;
//			print $uploadFile.'<==>'.$toFile;
			if ($uploadFile <> "none"){
				if (!copy( $uploadFile, $toFile ) ) 
		             $msg =  'File cannot be upload into server'; 
			} else
				$msg = 'File cannot be empty';
			$barcodeLogo = basename($uploadFile);
		}	

		if ($ownerBanner == ''){
			$ownerBanner_name = $oldBannerFile;
		} else {
			$uploadFile =  $HTTP_POST_FILES['ownerBanner']['tmp_name'];
			$toFile = 'images\\'.$ownerBanner_name;
//			print $uploadFile.'<==>'.$toFile;
			if ($uploadFile <> "none"){
				if (!copy( $uploadFile, $toFile ) ) 
		             $msg =  'File cannot be upload into server'; 
			} else
				$msg = 'File cannot be empty';
			$ownerBanner = basename($uploadFile);
		}	
		
		if ($ownerAddress <> "") $ownerAddress = '<pre>'.$ownerAddress.'</pre>';
		$updatedBy = $_SESSION['Session.userName'];
		$updatedDate = date("Y-m-d H:i:s"); 
       	$sSQL	= "	UPDATE	 setup SET " .
		          "	siteName		= ".tosql($siteName, "Text").
		          ",siteFooter		= ".tosql($siteFooter, "Text").
		          ",statusBar		= ".tosql($statusBar, "Text").
				  ",dhsb_metaKeyword= ".tosql($dhsb_metaKeyword, "Text") .
		          ",dhsb_metaDesc	= ".tosql($dhsb_metaDesc, "Text") .
		          ",ownerName		=" . tosql($ownerName, "Text") .				  
		          ",ownerAddress	=" . tosql($ownerAddress, "Text") .
		          ",ownerPhone		=" . tosql($ownerPhone, "Text") .				  
		          ",ownerFax		=" . tosql($ownerFax, "Text") .				  
		          ",ownerEmail		=" . tosql($ownerEmail, "Text") .
		          ",ownerLogo		=" . tosql($ownerLogo_name, "Text") .				  
		          ",barcodeLogo		=" . tosql($barcodeLogo_name, "Text") .				  
		          ",ownerBanner		=" . tosql($ownerBanner_name, "Text") .				  
		          ",rptHeader		=" . tosql($rptHeader, "Text") .				  
		          ",rptFooter		=" . tosql($rptFooter, "Text") .
				  ",updatedDate		= ".tosql($updatedDate, "Text") .
		          ",updatedBy		= ".tosql($updatedBy, "Text").
				  ",ldap			= ".tosql($ldap, "Text").
				  ",dn				= ".tosql($dn, "Text").
				  ",emailBCC		= ".tosql($emailBCC, "Text").
		          "	WHERE	setupID = '1' ";
		$msg = "Rekod telah dikemaskinikan...!";			
		$rs = &$conn->Execute($sSQL);

		print 	'	<script>
						alert ("' . $msg . '");     
					</script>';
	}
}			
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

$sSQL = 'SELECT * FROM setup where setupID = 1';
$rs = &$conn->Execute($sSQL);

print '
<form name="MyForm" action='.$sFileName.'?action='.$action.'&pk='.$pk.' enctype="multipart/form-data" method=post>
<table border=0 cellpadding=3 cellspacing=1 width=800 align="center">
	<tr>
		<td class="maroonText" colspan=2 valign="top" height="30">'.$Title.'</td>
	</tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
    if ($i == 4) print	'<tr><td class=header colspan=2>Informasi Meta</td></tr>';
    if ($i == 6) print	'<tr><td class=header colspan=2>Maklumat Pendaftar</td></tr>';
	if ($i == 12) print '<tr valign=top><td class=Header colspan="2">Maklumat Pemilik</td></tr>';
	if ($i == 20) print '<tr valign=top><td class=Header colspan="2">Format Laporan</td></tr>';	
	if ($i == 22) print '<tr valign=top><td class=Header colspan="2">Pelayan LDAP</td></tr>';
	if ($i == 25) print '<tr valign=top><td class=Header colspan="2">Audit Informasi</td></tr>';
	print 	'
	<tr valign=top>
		<td class=Label align=right>' . $FormLabel[$i] . ' :</td>';

	if (in_array($FormElement[$i], $strErrMsg))
	  	print 	'
		<td class=errdata>';
	else
	  	print 	'
		<td class=data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($rs->fields($FormElement[$i])); 
	if ($FormType[$i] == "textarea") {
		$strFormValue = str_replace("<pre>","",$rs->fields($FormElement[$i]));
		$strFormValue = str_replace("</pre>","",$strFormValue);
	}
	FormEntry($FormLabel[$i], 
			  $FormElement[$i], 
			  $FormType[$i],
			  $strFormValue,
			  $FormData[$i],
			  $FormDataValue[$i],
			  $FormSize[$i],
			  $FormLength[$i]);
			  
	if ($FormType[$i] == "file")
		if ($rs->fields($FormElement[$i]) <> "")
			print '&nbsp;'.$rs->fields($FormElement[$i]).'<br><img src="images/'.$rs->fields($FormElement[$i]).'" border="0" align="top">';
	//--- End   : Call function FormEntry ---------------------------------------------------------  
    print 	'&nbsp;
		</td>
	</tr>';
}
print	'
	<tr>
		<td colspan="2" class="Data" align="center">
		<input type=Submit class="but" name="SubmitForm" value="Kemaskini"></p>
		<input type=hidden name=oldFile value='.$rs->fields('ownerLogo').'>		
		<input type=hidden name=oldBarcodeFile value='.$rs->fields('barcodeLogo').'>		
		<input type=hidden name=oldBannerFile value='.$rs->fields('ownerBanner').'>		
		</td>
	</tr>
</table>
</form>';

include('footer.php');	

?>
