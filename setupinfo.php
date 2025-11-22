<?php

if (!isset($_SERVER["SCRIPT_NAME"])) {
	$ServerDir = '';
} else {
	$ServerDir = $_SERVER["SCRIPT_NAME"];
}
$ServerDir = str_replace(basename($ServerDir), "", $ServerDir);
$ServerDir = "http://".$_SERVER["SERVER_NAME"].$ServerDir;

//$conn->debug=true;

//--- BEGIN : Set Application variables ------------------------------------------------------------
$sSQL = "SELECT * FROM setup WHERE setupID = 1";
$rs = $conn->Execute($sSQL);

//--- Begin : Set Application variables ------------------------------------------------------------
if (!$rs->EOF) {
	// Helper function to get field regardless of case
	function getField($recordset, $fieldName) {
		// Try exact match first
		if (isset($recordset->fields[$fieldName])) {
			return $recordset->fields[$fieldName];
		}
		// Try lowercase
		if (isset($recordset->fields[strtolower($fieldName)])) {
			return $recordset->fields[strtolower($fieldName)];
		}
		// Try uppercase
		if (isset($recordset->fields[strtoupper($fieldName)])) {
			return $recordset->fields[strtoupper($fieldName)];
		}
		// Try all fields to find case-insensitive match
		foreach ($recordset->fields as $key => $value) {
			if (strcasecmp($key, $fieldName) === 0) {
				return $value;
			}
		}
		return null;
	}

	$emaNetis							= getField($rs, 'siteName');
	$retooFetis							= getField($rs, 'siteFooter');
	$raBsutats							= getField($rs, 'statusBar');
	$yVZcSz2OuGE5U						= getField($rs, 'dhsb_registerName');
	$T5ZZPpvAKXOsI						= getField($rs, 'dhsb_registerID'); 			// Register Key
	$vC64qAlkI74Pw						= getField($rs, 'dhsb_registerDate');			// Script Expired
	$gsURgLGTUOAMI						= getField($rs, 'dhsb_licenseKey');				// License Key
	$drowyeKatem						= getField($rs, 'dhsb_metaKeyword');
	$cseDatem							= getField($rs, 'dhsb_metaDesc');		
	$CR281ed5ec223130fa7b5b181e176aaf67 = tc4HqejcxVXtI($yVZcSz2OuGE5U,$T5ZZPpvAKXOsI);     
//	$CLdd12c62d698c0f6be564fef9f128469f = QwoBqcU3ac49k($gsURgLGTUOAMI);
	$emaNrenwo							= getField($rs, 'ownerName');		
	$sserddArenwo 						= str_replace("<pre>","<pre><font class=contentD>".getField($rs, 'ownerName').'<br>' ,getField($rs, 'ownerAddress'));
	$sserddArenwo 						= str_replace("</pre>","</font></pre>",$sserddArenwo);	
	$enohPrenwo							= getField($rs, 'ownerPhone');		
	$xaFrenwo							= getField($rs, 'ownerFax');		
	$liamErenwo							= getField($rs, 'ownerEmail');		
	$ogoLrenwo							= getField($rs, 'ownerLogo');			
	$ogoLedocrab						= getField($rs, 'barcodeLogo');			
	$ogoLrenwo							= getField($rs, 'ownerLogo');			
	$rennaBrenwo						= getField($rs, 'ownerBanner');			
	$redaeHtpr							= getField($rs, 'rptHeader');
	$tamroFtpr							= getField($rs, 'rptFormat');
	$retooFtpr							= getField($rs, 'rptFooter');
	$gubeDsi							= getField($rs, 'isDebug');   
	$padl								= getField($rs, 'ldap');   
	$nd									= getField($rs, 'dn');   
	$CCBliame							= getField($rs, 'emailBCC');   
	
	if ($gubeDsi) $conn->debug=true; else $conn->debug=false;
}
//--- END   : Set Application variables ------------------------------------------------------------

$loginStyles = '
<style>
	.login-info-container {
		max-width: 1000px;
		margin: 20px auto;
		font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	}
	
	.contact-card {
		background: #f7fafc;
		border-radius: 8px;
		padding: 16px;
		margin-bottom: 24px;
		text-align: center;
	}
	
	.contact-title {
		font-size: 14px;
		font-weight: 600;
		margin-bottom: 8px;
		color: #1a202c;
	}
	
	.contact-item {
		font-size: 13px;
		color: #4a5568;
		margin: 4px 0;
	}
	
	.contact-link {
		color: #4299e1;
		text-decoration: none;
	}
	
	.contact-link:hover {
		text-decoration: underline;
	}
	
	.notice-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
		gap: 20px;
		margin-bottom: 20px;
	}
	
	.notice-card {
		background: white;
		border-radius: 8px;
		padding: 20px;
		box-shadow: 0 2px 8px rgba(0,0,0,0.08);
		border-left: 4px solid #ef4444;
	}
	
	.notice-card.important {
		border-left-color: #f59e0b;
	}
	
	.notice-title {
		display: flex;
		align-items: center;
		gap: 8px;
		font-size: 16px;
		font-weight: 600;
		color: #ef4444;
		margin-bottom: 12px;
	}
	
	.notice-title.important {
		color: #f59e0b;
	}
	
	.notice-content {
		font-size: 14px;
		color: #4a5568;
		line-height: 1.6;
	}
	
	.notice-list {
		margin: 8px 0;
		padding-left: 20px;
	}
	
	.notice-list li {
		margin: 6px 0;
	}
	
	.tips-card {
		background: white;
		border-radius: 8px;
		padding: 20px;
		box-shadow: 0 2px 8px rgba(0,0,0,0.08);
		border-left: 4px solid #3b82f6;
	}
	
	.tips-title {
		display: flex;
		align-items: center;
		gap: 8px;
		font-size: 16px;
		font-weight: 600;
		color: #3b82f6;
		margin-bottom: 12px;
	}
	
	.tips-content {
		font-size: 14px;
		color: #4a5568;
		line-height: 1.6;
	}
	
	.help-text {
		background: #f7fafc;
		border-radius: 8px;
		padding: 16px;
		text-align: center;
		font-size: 13px;
		color: #4a5568;
		margin-top: 20px;
	}
	
	@media (max-width: 768px) {
		.login-info-container {
			padding: 12px;
		}
		
		.notice-grid {
			grid-template-columns: 1fr;
		}
		
		.contact-card {
			padding: 20px;
		}
	}
</style>';

$registerInfo = $loginStyles . '
<div class="login-info-container">
	<div class="contact-card">
		<div class="contact-title">MAKLUMAT HUBUNGAN</div>
		<div class="contact-item">'.$sserddArenwo.'</div>
		<div class="contact-item">Tel: '.$enohPrenwo.'</div>
		<div class="contact-item">Fax: '.$xaFrenwo.'</div>
		<div class="contact-item">Email: <a href="mailto:'.$liamErenwo.'" class="contact-link">'.$liamErenwo.'</a></div>
	</div>
</div>';

$introInfo = $loginStyles . '
<div class="login-info-container">
	<div class="notice-grid">
		<div class="notice-card">
			<div class="notice-title">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
					<line x1="12" y1="9" x2="12" y2="13"/>
					<line x1="12" y1="17" x2="12.01" y2="17"/>
				</svg>
				PERINGATAN
			</div>
			<div class="notice-content">
				Anda tidak sepatutnya mendedahkan PIN anda dan/atau Kata Laluan kepada sesiapa.
			</div>
		</div>
		
		<div class="notice-card important">
			<div class="notice-title important">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<circle cx="12" cy="12" r="10"/>
					<line x1="12" y1="8" x2="12" y2="12"/>
					<line x1="12" y1="16" x2="12.01" y2="16"/>
				</svg>
				NOTIS PENTING
			</div>
			<div class="notice-content">
				<ul class="notice-list">
					<li>Jangan log masuk melalui pautan yang diberi daripada e-mel</li>
					<li>Sila maklumkan kami mana-mana laman web "phishing" Sistem Borang Pusat ATM</li>
				</ul>
			</div>
		</div>
	</div>
	
	<div class="tips-card">
		<div class="tips-title">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<circle cx="12" cy="12" r="10"/>
				<path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
				<line x1="12" y1="17" x2="12.01" y2="17"/>
			</svg>
			Petunjuk Berguna
		</div>
		<div class="tips-content">
			<ul class="notice-list">
				<li>Paparan terbaik menggunakan pelayar internet Mozilla Firefox, Chrome dan Internet Explorer 11 ke atas.</li>
			</ul>
		</div>
	</div>
	
	<div class="help-text">
		Sebarang bantuan, sila hubungi Pusat ATM Bank Rakyat seperti tertera diatas.
	</div>
</div>';



//--- BEGIN : Set Form Variable --------------------------------------------------------------------	

$statusList	= array ('<b>BARU</b>',			// new instruction/collection
					 '<b>DISAHKAN</b>',		// validate instruction
					 '<b>DITERIMA</b>',		// accept instruction
					 '<b>DIKEMASKINI</b>',  // update data-entry
					 '<b>DILULUSKAN</b>');	// approved and send to BKRM
$statusVal	= array ("0","1","2","3","4");

$groupList	= array ('TELLER','SUPERVISOR','PENTADBIR SISTEM','PEGAWAI BKRM','STAFF BKRM','PENGURUSAN');
$groupVal	= array ("0","1","3","4","5","6");

// Fixed: Check if session variable exists first
if (isset($_SESSION['Session.groupID']) && $_SESSION['Session.groupID'] == "9") {
	array_push($groupList,"PENYELIA SISTEM");
	array_push($groupVal,"9");
}	

$timeList = array ('12:00 AM','12:30 AM','01:00 AM','01:30 AM','02:00 AM','02:30 AM','03:00 AM','03:30 AM','04:00 AM','04:30 AM',
				   '05:00 AM','05:30 AM','06:00 AM','06:30 AM','07:00 AM','07:30 AM','08:00 AM','08:30 AM','09:00 AM','09:30 AM',
				   '10:00 AM','10:30 AM','11:00 AM','11:30 AM',
				   '12:00 PM','12:30 PM','01:00 PM','01:30 PM','02:00 PM','02:30 PM','03:00 PM','03:30 PM','04:00 PM','04:30 PM',
				   '05:00 PM','05:30 PM','06:00 PM','06:30 PM','07:00 PM','07:30 PM','08:00 PM','08:30 PM','09:00 PM','09:30 PM',
				   '10:00 PM','10:30 PM','11:00 PM','11:30 PM');

$stateList	= array('Johor','Kedah','Kelantan','Melaka','Negeri Sembilan','Pahang','Pulau Pinang','Perak','Perlis','Selangor','Terengganu',
					'Sabah','Sarawak','Kuala Lumpur','Labuan','Putrajaya');
$stateVal	= array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16');	

$msList = array('Bujang','Berkahwin','Janda','Duda','Balu');
$msVal  = array('0','1','2','3','4');

$ATList = array('Tiada','Ada');
$ATVal  = array('0','1');

$atmList = array('ATM', 'CDM','CICO');

$dayList = array();
for ($i=1; $i <= 31; $i++)	{
	array_push($dayList, sprintf("%02d",$i));
}

$monthList = array ('Januari','Febuari','Mac','April','Mei','Jun','Julai','Ogos','September','Oktober','November','Disember');
$monthVal = array ('01','02','03','04','05','06','07','08','09','10','11','12');

$yrList=array();
for ($y=-1; $y <= 10; $y++) {
	array_push ($yrList, date('Y')-$y);
}

$mmyrList=array();
$mmyrVal=array();
for ($y = 0; $y < count($yrList); $y++) {
	for ($m = 0; $m < count($monthList); $m++) {
		array_push($mmyrList, $monthList[$m].', '.$yrList[$y]);
		array_push($mmyrVal, $monthVal[$m].$yrList[$y]);
	}
}
//--- END   : Set Form Variable --------------------------------------------------------------------	
?>