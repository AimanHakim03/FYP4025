
okay okay babe don't cry!! 😭💕 i got you!! lemme fix all 3 files for you right now!! just copy paste these exactly:

rpt_InstructE.php (FIXED)
Code 
okay babe!! 💕 i made all 3 files for you!! here's what i fixed:

Main changes:

✨ Changed function ExportExcel() to function __construct() - this is the proper PHP 8 constructor syntax!!
✨ Added if (ob_get_level()) ob_end_clean(); before ALL headers to clear any output
✨ Fixed the Content-Disposition header to properly quote the filename: filename="'.$this->filename.'"
✨ Changed print to echo and added exit; after outputting data
✨ Cleaned up the flow in rpt_InstructE.php to properly buffer and clean output
just copy paste these 3 files exactly and it should work!! the main issue was the old PHP4 constructor syntax not working with PHP 8.2 💀

try it now babe and let me know!! you got this!! 🙏✨









PHP Project

<?php
session_start();

$fn = $_GET['fn'].".xls";

if (ob_get_level()) {
    ob_end_clean();
}

include_once("c_export_excel.php");

$excel_obj = new ExportExcel($fn);

if ($_GET['fn']=="rptE1" || $_GET['fn']=="rptE" || $_GET['fn']=="rptDA" || $_GET['fn']=="rptMA" || $_GET['fn']=="rptF"
|| $_GET['fn']=="rptMGMTA1" || $_GET['fn']=="rptMGMTA" || $_GET['fn']=="rptMGMTB") {
	$excel_obj->setHeadersAndValues1($_SESSION['report_header0'], 
	                                $_SESSION['report_header1'],
									$_SESSION['report_header2'],
									$_SESSION['report_header3'],
									$_SESSION['report_header'],
									$_SESSION['report_values']); 
	if ($_GET['fn']=="rptMGMTA1") $excel_obj->GenerateExcelFile2();
	else $excel_obj->GenerateExcelFile1();
} else {
	$excel_obj->setHeadersAndValues($_SESSION['report_header0'],
	                                $_SESSION['report_header1'],
									$_SESSION['report_header'],
									$_SESSION['report_values']); 
	$excel_obj->GenerateExcelFile();
}

exit;