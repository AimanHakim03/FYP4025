<?php

// Start session first thing!
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// For migration testing - show all errors temporarily
// Change this back after fixing everything
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// For production after fixing:
 error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

putenv('TZ=Asia/Kuala_Lumpur');

$e4iQwRsWtQ		= Array(84,104,101,32,112,97,103,101,32,99,117,114,114,101,110,116,108,121,32,114,117,110,110,105,
						110,103,32,105,115,32,100,105,115,97,98,108,101,100,32,100,117,101,32,116,111,32,110,111,32,
						60,98,62,83,101,116,117,112,32,73,110,102,111,114,109,97,116,105,111,110,60,47,98,62,46,32,
						80,108,101,97,115,101,32,99,111,110,116,97,99,116,32,90,73,32,83,111,102,116,101,99,104);
$JmXvyYaTY6f7g	= Array(82,101,103,105,115,116,101,114,32,73,68,32,105,115,32,110,111,116,32,118,97,108,105,100,32,
						40,86,101,114,105,102,121,32,119,105,116,104,32,90,73,32,83,111,102,116,101,99,104,41);
$P4tbDvtInbt	= Array(76,105,99,101,110,115,101,32,75,101,121,32,105,115,32,110,111,116,32,118,97,108,105,100,32,
						40,86,101,114,105,102,121,32,119,105,116,104,32,90,73,32,83,111,102,116,101,99,104,41);
$diresu			= 'E5D670ED39DDFA6F31B536C424899B72';
$drowssap		= '7E5B61EA0AF55213A53652842A10B0DD';
$errPage		= 'Anda tiada autoriti untuk mengakses ATAU sesi telah tamat..!!!';
$setLevel		= 5;

include_once('adodb5/adodb.inc.php');
include_once('setupDB.php');

ob_start();

$conn = ADONewConnection($DB_dbtype);
$conn->PConnect($DB_hostname, $DB_username, $DB_password, $DB_dbname);

// IMPORTANT: Set fetch mode for case-insensitive field names
$conn->SetFetchMode(ADODB_FETCH_ASSOC);
// Make all field names lowercase to avoid case sensitivity issues
global $ADODB_ASSOC_CASE;
$ADODB_ASSOC_CASE = ADODB_ASSOC_CASE_LOWER;

// REMOVED: The dangerous foreach loop that creates variables from $_REQUEST
// If you need specific request parameters, access them directly:
// Example: $someVar = $_REQUEST['someVar'] ?? '';

function tohtml($strValue)
{
  return htmlspecialchars($strValue);
}

function toYN($strValue)
{
  $YN = "Ya";	
  if ($strValue == "" OR $strValue == "0") $YN = "Tidak";
  return $YN;
}

function tourl($strValue)
{
  return urlencode($strValue);
}

function get_param($ParamName)
{
  // Updated to use $_POST and $_GET directly (PHP 8 compatible)
  $ParamValue = "";
  if(isset($_POST[$ParamName]))
    $ParamValue = $_POST[$ParamName];
  else if(isset($_GET[$ParamName]))
    $ParamValue = $_GET[$ParamName];

  return $ParamValue;
}

function is_number($string_value)
{
  if(is_numeric($string_value) || !strlen($string_value))
    return true;
  else 
    return false;
}

function is_param($param_value)
{
  if($param_value)
    return 1;
  else
    return 0;
}

function tosql($value, $type = "Text")
{
    if ($value === "" || $value === null) {
        return "NULL";
    }

    if ($type == "Number") {
        return doubleval($value);
    } else {
        // Sanitize for SQL - escape single quotes
        $value = str_replace("'", "''", $value);
        // Remove backslash escaping since magic quotes are gone
        return "'" . $value . "'";
    }
}

function strip($value)
{
    // Magic quotes are completely removed in PHP 8
    return $value;
}

function get_checkbox_value($sVal, $CheckedValue, $UnCheckedValue)
{
  if(!strlen($sVal))
    return tosql($UnCheckedValue);
  else
    return tosql($CheckedValue);
}

function dlookup($Table, $fName, $sWhere)
{
  global $conn;
  $sSQL = "";
  
  $sSQL = "SELECT " . $fName . " FROM " . $Table . " WHERE " . $sWhere;
  $rs2 = $conn->Execute($sSQL);
  
  if ($rs2 && !$rs2->EOF) {
    // Try different case variations to handle SQL Server's inconsistent casing
    $fieldNameLower = strtolower($fName);
    $fieldNameUpper = strtoupper($fName);
    
    // Try exact match first
    if (isset($rs2->fields[$fName])) {
      return $rs2->fields[$fName];
    }
    // Try lowercase
    if (isset($rs2->fields[$fieldNameLower])) {
      return $rs2->fields[$fieldNameLower];
    }
    // Try uppercase
    if (isset($rs2->fields[$fieldNameUpper])) {
      return $rs2->fields[$fieldNameUpper];
    }
    
    // Last resort: loop through all fields case-insensitively
    foreach ($rs2->fields as $key => $value) {
      if (strcasecmp($key, $fName) === 0) {
        return $value;
      }
    }
    
    // If still not found, return empty
    return "";
  } else {
    return "";
  }
}

function createLog($user, $event, $type) {
	global $conn;
	$dateEvent = date("Y-m-d H:i:s");         
	$terminal = $_SERVER["REMOTE_ADDR"] ?? 'unknown';

  	$sSQL = "";
	if ($type <> "Super User") {
		$sSQL	= "INSERT INTO logs (" . 
		          "userName," . 				
		          "eventText," . 
		          "dateEvent," . 
		          "groupName," . 
		          "terminal)" . 
		          " VALUES (" . 
		          tosql($user, "Text") . "," .
		          tosql($event, "Text") . "," .
		          tosql($dateEvent, "Text") . "," .
				  tosql($type, "Text") . ",".
				  tosql($terminal, "Text") . ")";
		$rs2 = $conn->Execute($sSQL);
	}
}

function tc4HqejcxVXtI($yVZcSz2OuGE5U,$T5ZZPpvAKXOsI){
	global $JmXvyYaTY6f7g;
	$yVZcSz2OuGE5U = str_replace(" ","",$yVZcSz2OuGE5U);
	$a = strlen($yVZcSz2OuGE5U);
	$sum = 0;
	for ($i=0; $i < $a; $i++){
		$sum = $sum + hexdec(ord($yVZcSz2OuGE5U[$i]));
	}	
	$key = strtoupper(md5($sum * hexdec(ord("TZWEB"))));

	if ($key <> $T5ZZPpvAKXOsI){
		for ($i=0; $i < count($JmXvyYaTY6f7g); $i++)	{
			print chr($JmXvyYaTY6f7g[$i]);
		}
		exit;
	}
	return $key;
}

function QwoBqcU3ac49k($gsURgLGTUOAMI) {
	global $P4tbDvtInbt;
	$serverName = $_SERVER["SERVER_NAME"] ?? '';
	$a = strlen($serverName);
	$sum = 0;
	for ($i=0; $i < $a; $i++){
		$sum = $sum + hexdec(ord($serverName[$i]));
	}
	$key = strtoupper(md5($sum * hexdec(ord("TZWEB"))));
	
	if ($key <> $gsURgLGTUOAMI){
		for ($i=0; $i < count($P4tbDvtInbt); $i++)	{
			print chr($P4tbDvtInbt[$i]);
		}
		exit;
	}
	return $key;
}

function toDate($dateformatstring, $mysqlstring) { 
	if ($mysqlstring <> "" && $mysqlstring !== null) {
		// Extract date components and convert to integers
		$year = (int)substr($mysqlstring, 0, 4);
		$month = (int)substr($mysqlstring, 5, 2);
		$day = (int)substr($mysqlstring, 8, 2);
		
		// Extract time components (default to 0 if not present)
		$hour = strlen($mysqlstring) > 11 ? (int)substr($mysqlstring, 11, 2) : 0;
		$minute = strlen($mysqlstring) > 14 ? (int)substr($mysqlstring, 14, 2) : 0;
		$second = strlen($mysqlstring) > 17 ? (int)substr($mysqlstring, 17, 2) : 0;
		
		return date($dateformatstring, mktime($hour, $minute, $second, $month, $day, $year));
	}
	return '';
}  

function penggenapan ($value) {
   $tmpval = str_replace(".","''",$value);
   $pval = $value - $tmpval;
   $pval = number_format($pval,2);
   $pval = substr($pval,3,1);
   switch ($pval)
     {
	   case 1 : {$g = -0.01; break;}
	   case 2 : {$g = -0.02;  break;}
	   case 3 : {$g = +0.02; break;}
	   case 4 : {$g = +0.01; break;}
	   case 6 : {$g = -0.01; break;}
	   case 7 : {$g = -0.02; break;}
	   case 8 : {$g = +0.02; break;}
	   case 9 : {$g = +0.01; break;}
	   default : {$g = 0; break;}
	  }
   return $g;
}

function datediff($interval, $datefrom, $dateto, $using_timestamps = false)
{
    if (!$using_timestamps) {
        $datefrom = strtotime($datefrom);
        $dateto = strtotime($dateto);
    }

    if ($dateto < $datefrom) {
        // swap if reversed
        list($datefrom, $dateto) = [$dateto, $datefrom];
    }

    $difference = $dateto - $datefrom; // in seconds

    switch ($interval) {
        case 'yyyy': // Number of full years
            $years_difference = floor($difference / 31536000);
            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom),
                date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {
                $years_difference--;
            }
            $datediff = $years_difference;
            break;

        case 'q': // Number of full quarters
            $quarters_difference = 0;
            while (strtotime("+".(($quarters_difference + 1) * 3)." months", $datefrom) <= $dateto) {
                $quarters_difference++;
            }
            $datediff = $quarters_difference;
            break;

        case 'm': // Number of full months
            $months_difference = 0;
            while (strtotime("+".($months_difference + 1)." month", $datefrom) <= $dateto) {
                $months_difference++;
            }
            $datediff = $months_difference;
            break;

        case 'y': // Difference between day numbers (within the year)
            $datediff = date("z", $dateto) - date("z", $datefrom);
            break;

        case 'd': // Full days
            $datediff = floor($difference / 86400);
            break;

        case 'w': // Full weekdays
            $days_difference = floor($difference / 86400);
            $weeks_difference = floor($days_difference / 7);
            $first_day = date("w", $datefrom);
            $days_remainder = $days_difference % 7;
            $odd_days = $first_day + $days_remainder;
            if ($odd_days > 7) $days_remainder--;
            if ($odd_days > 6) $days_remainder--;
            $datediff = ($weeks_difference * 5) + $days_remainder;
            break;

        case 'ww': // Full weeks
            $datediff = floor($difference / 604800);
            break;

        case 'h': // Full hours
            $datediff = floor($difference / 3600);
            break;

        case 'n': // Full minutes
            $datediff = floor($difference / 60);
            break;

        default: // Full seconds
            $datediff = $difference;
            break;
    }

    return $datediff;
}

?>