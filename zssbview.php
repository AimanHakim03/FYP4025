<?php

include("common.php");
session_start();

//if if ($_SESSION['Session.groupID'] <> "9") {
//	print '<script>alert("'.$errPage.'");window.location.href="index.php";</script>';
//	exit;
//}

$sSQL = 'SELECT * FROM atm';

$rs = &$conn->Execute($sSQL);
if (!$rs) {
	echo 'ERROR: '.$conn->ErrorMsg(); 
} else {
	echo '<html><body><table><tr>';

	while (!$rs->EOF) { 
		$fld = $rs->FetchField(1); 
//		$fld = $rs->fields[0].'=='.$rs->fields[1]; 
        $type = $rs->MetaType($fld->type); 
		
		if ( $type == 'D' || $type == 'T')  
            print $rs->fields[0].' '.$rs->UserDate($recordSet->fields[1],'m/d/Y').'<BR>'; 
        else 
            print $rs->fields[0].' '.$rs->fields[1].'<BR>'; 

		
//		echo '<td>'.$fld.' ('.$type.')</td>';
		$rs->MoveNext(); 
	}

/*  Display record per line
	while ($arr = $rs->FetchRow()) { 
        print_r($arr); print "<br>"; 
    } 
*/

	echo '</tr>';
/*	
	while ( ($row = mssql_fetch_row($rs))) 
	{
		$count = count($row);
		$y = 0;
		echo '<tr>';
		while ($y < $count)
		{
			$c_row = current($row);
			echo '<td>' . $c_row . '</td>';
			next($row);
			$y = $y + 1;
		}
		echo '</tr>';
	}
*/
	$rs->Close(); 
    $conn->Close();  
	echo '</table></body></html>';
}


?>