<?php
include("header.php");
include("forms.php");
session_start();

// Session validation
if (empty($_SESSION['Session.userID'])) {
    echo '<script>alert("' . $errPage . '");window.location.href="index.php";</script>';
    exit;
}

// Get request parameters
$action = $_REQUEST['action'] ?? '';
$pk = $_REQUEST['pk'] ?? '';
$strURL = $_REQUEST['strURL'] ?? '';
$SubmitForm = $_POST['SubmitForm'] ?? '';

// Form fields
$collectDate = $_POST['collectDate'] ?? '';
$atmID = $_POST['atmID'] ?? '';
$vendorID = $_POST['vendorID'] ?? '';
$collectPic = $_POST['collectPic'] ?? '';
$collectPic1 = $_FILES['collectPic1']['name'] ?? '';
$docNo = $_POST['docNo'] ?? '';

$sFileName = "collectAddUpdate.php";
$sActionFileName = "collect.php";
$Title = "COLLECTION CDM/CICO";

$path = dirname(__FILE__);

$delDoc = $_POST['delDoc'] ?? '';

// Date handling
$dd = $_POST['dd'] ?? date("d");
$mm = $_POST['mm'] ?? date("m");
$yy = $_POST['yy'] ?? date("Y");

$dmy = $dd . '/' . $mm . '/' . $yy;
if (ValidDate($dmy) == "N") {
    echo '<script>alert("Pilihan tarikh tidak sah - ' . $dmy . '");</script>';
    $dd = 1;
}
$ymd = $yy . '-' . $mm . '-' . $dd;
$yyyymmdd = $yy . $mm . $dd;

// Physical denomination quantities
$qty100 = intval($_POST['qty100'] ?? 0);
$qty50 = intval($_POST['qty50'] ?? 0);
$qty20 = intval($_POST['qty20'] ?? 0);
$qty10 = intval($_POST['qty10'] ?? 0);
$qty5 = intval($_POST['qty5'] ?? 0);
$qty2 = intval($_POST['qty2'] ?? 0);
$qty1 = intval($_POST['qty1'] ?? 0);

// Calculate values
$v100 = $qty100 * 100;
$v50 = $qty50 * 50;
$v20 = $qty20 * 20;
$v10 = $qty10 * 10;
$v5 = $qty5 * 5;
$v2 = $qty2 * 2;
$v1 = $qty1 * 1;
$tvalue = $v100 + $v50 + $v20 + $v10 + $v5 + $v2 + $v1;

// Initialize display variables
$collectID = '';
$atmNo = '';
$status = '0';
$createdBy = '';
$createdDate = '';
$updatedBy = '';
$updatedDate = '';
$approvedBy = '';
$approvedDate = '';
$collectDate1 = '';
$collectDate2 = '';
$err1 = '';
$err2 = '';
$err3 = '';

$strErrMsg = [];

// Form Validation and Processing
if ($SubmitForm != "") {
    // Validate collection date
    if ($collectDate == '') {
        array_push($strErrMsg, 'collectDate');
        $err1 = '<br><font class=redText>* Sila pilih tarikh.</font>';
    } else {
        $collectDate1 = substr($collectDate, 6, 4) . '-' . substr($collectDate, 3, 2) . '-' . substr($collectDate, 0, 2);
        $collectDate2 = substr($collectDate, 6, 4) . substr($collectDate, 3, 2) . substr($collectDate, 0, 2);
        if ($collectDate2 > $yyyymmdd) {
            array_push($strErrMsg, 'collectDate');
            $err1 = '<br><font class=redText>* Tarikh tidak boleh lebih dari tarikh semasa.</font>';
        }
    }

    // Validate ATM selection
    if ($atmID == '') {
        array_push($strErrMsg, 'atmID');
        $err2 = '<br><font class=redText>* Sila pilih mesin CDM/CICO.</font>';
    }

    // Check for duplicate records (only for new entries)
    if (count($strErrMsg) == 0 && $docNo == '') {
        $sSQL = "SELECT * FROM collect 
                 WHERE atmID = " . tosql($atmID, "Text") . " 
                 AND vendorID = " . tosql($vendorID, "Text") . "
                 AND collectDate = " . tosql($collectDate1, "Text");

        $rs = $conn->Execute($sSQL);
        if ($rs->RowCount() != 0) {
            array_push($strErrMsg, 'docNo');
            $err2 = '<br><font class=redText>* Rekod sudah wujud.</font>';
        }
    }

    // Process form if no errors
    if (count($strErrMsg) == 0) {
        // Generate document number for new records
        if ($docNo == '') {
            $vendorCode = dlookup("vendor", "vendorCode", "vendorID=" . tosql($vendorID, "Text"));
            $atmNo = dlookup("atm", "atmNo", "atmID=" . tosql($atmID, "Text"));
            $docNo = "C" . $atmNo . $collectDate2 . ' ' . $vendorCode;
        }

        $sSQL = "";
        switch (strtolower($SubmitForm)) {
            case "simpan":
                // Update existing record
                $sWhere = "collectID =" . tosql($pk, "Text");
                $sSQL = "UPDATE collect SET " .
                    "qty100 = " . tosql($qty100, "Number") . "," .
                    "qty50 = " . tosql($qty50, "Number") . "," .
                    "qty20 = " . tosql($qty20, "Number") . "," .
                    "qty10 = " . tosql($qty10, "Number") . "," .
                    "qty5 = " . tosql($qty5, "Number") . "," .
                    "qty2 = " . tosql($qty2, "Number") . "," .
                    "qty1 = " . tosql($qty1, "Number") . "," .
                    "value100 = " . tosql($v100, "Number") . "," .
                    "value50 = " . tosql($v50, "Number") . "," .
                    "value20 = " . tosql($v20, "Number") . "," .
                    "value10 = " . tosql($v10, "Number") . "," .
                    "value5 = " . tosql($v5, "Number") . "," .
                    "value2 = " . tosql($v2, "Number") . "," .
                    "value1 = " . tosql($v1, "Number") . "," .
                    "totalValue = " . tosql($tvalue, "Number") . "," .
                    "collectPic = " . tosql($collectPic, "Text") . "," .
                    "updatedDate = " . tosql(date("Y-m-d H:i:s"), "Text") . "," .
                    "updatedBy = " . tosql($_SESSION['Session.userName'], "Text");
                $sSQL .= " WHERE " . $sWhere;
                $rs = $conn->Execute($sSQL);
                $msg = "Rekod Collection CDM/CICO - collectID " . $pk . " telah dikemaskini";
                break;

            case "tambah":
                // Insert new record
                $sSQL = "INSERT INTO collect (" .
                    "collectDate, atmID, vendorID, " .
                    "qty100, qty50, qty20, qty10, qty5, qty2, qty1, " .
                    "value100, value50, value20, value10, value5, value2, value1, " .
                    "totalValue, collectPic, docNo, " .
                    "status, isDeleted, isApproved, " .
                    "createdDate, createdBy, updatedDate, updatedBy) " .
                    "VALUES (" .
                    tosql($collectDate1, "Text") . "," .
                    tosql($atmID, "Text") . "," .
                    tosql($vendorID, "Text") . "," .
                    tosql($qty100, "Number") . "," .
                    tosql($qty50, "Number") . "," .
                    tosql($qty20, "Number") . "," .
                    tosql($qty10, "Number") . "," .
                    tosql($qty5, "Number") . "," .
                    tosql($qty2, "Number") . "," .
                    tosql($qty1, "Number") . "," .
                    tosql($v100, "Number") . "," .
                    tosql($v50, "Number") . "," .
                    tosql($v20, "Number") . "," .
                    tosql($v10, "Number") . "," .
                    tosql($v5, "Number") . "," .
                    tosql($v2, "Number") . "," .
                    tosql($v1, "Number") . "," .
                    tosql($tvalue, "Number") . "," .
                    tosql($collectPic, "Text") . "," .
                    tosql($docNo, "Text") . "," .
                    "0, 0, 0," .
                    tosql(date("Y-m-d H:i:s"), "Text") . "," .
                    tosql($_SESSION['Session.userName'], "Text") . "," .
                    tosql(date("Y-m-d H:i:s"), "Text") . "," .
                    tosql($_SESSION['Session.userName'], "Text") . ")";
                $msg = "Rekod telah didaftarkan ke dalam sistem";
                $rs = $conn->Execute($sSQL);
                break;
        }

        if ($rs === false) {
            echo '<p align="center"><hr size="1" color="#999999">
            <b class="textFont">Rekod ' . $Title . ' : Daftar/Kemaskini - ralat : ' . $conn->ErrorMsg() . '<br>' . $sSQL . '<br>- Sila hubungi Pihak Pentadbir Sistem -<br></b>
            <hr size="1" color="#999999"></p>';
            exit;
        }

        // Create log
        $user = $_SESSION['Session.userName'];
        $type = $_SESSION['Session.groupName'];
        createLog($user, $msg, $type);

        echo '<script>
                alert("' . $msg . '");
                window.location.href="' . $sActionFileName . '?' . $strURL . '";
              </script>';
    }
}

// File upload handling
if ($delDoc != '') {
    $filePath = $path . "\\scanned\\" . $delDoc;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    $delDoc = '';
}

if ($collectPic1 != '') {
    $allowedExtension = array("pdf", "PDF", "jpg", "JPG");

    $fileName = $_FILES['collectPic1']['name'];
    $tmpName = $_FILES['collectPic1']['tmp_name'];
    $fileSize = $_FILES['collectPic1']['size'];

    $fileExt = explode('.', $fileName);
    if (!in_array($fileExt[1], $allowedExtension)) {
        $err3 = '<br><font class=redText>* Filename not allowed (' . $fileName . ') only pdf and jpg.</font>';
    } elseif ($fileSize > 3000000) {
        $err3 = '<br><font class=redText>* Document size must be less than 3MB.</font>';
    } else {
        $fp = @fopen($tmpName, "r");
        $buffer = "";
        if (!$fp) {
            $err3 = '<br><font color="#FF0000">* ERROR opening file on server.</font>';
        } else {
            while (!feof($fp)) {
                $buffer .= fgets($fp, 4096);
            }
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileName = $docNo . "." . $fileExt;

            file_put_contents($path . "\\scanned\\" . $fileName, $buffer);
            $collectPic = $fileName;
        }
    }
}

// Load existing record for update
if ($action == "update" && $pk != "") {
    $sSQL = "SELECT * FROM collect WHERE collectID = " . tosql($pk, "Text");
    $rs = $conn->Execute($sSQL);

    $collectID = $rs->fields('collectID');
    $collectDate = todate('d/m/Y', $rs->fields('collectDate'));
    $atmID = $rs->fields('atmID');
    $atmNo = dlookup("atm", "atmNo", "atmID=" . tosql($rs->fields('atmID'), "Text")) . " - " .
        dlookup("atm", "atmLoc", "atmID=" . tosql($rs->fields('atmID'), "Text"));
    $vendorID = $rs->fields('vendorID');
    $qty100 = $rs->fields('qty100');
    $v100 = $rs->fields('value100');
    $qty50 = $rs->fields('qty50');
    $v50 = $rs->fields('value50');
    $qty20 = $rs->fields('qty20');
    $v20 = $rs->fields('value20');
    $qty10 = $rs->fields('qty10');
    $v10 = $rs->fields('value10');
    $qty5 = $rs->fields('qty5');
    $v5 = $rs->fields('value5');
    $qty2 = $rs->fields('qty2');
    $v2 = $rs->fields('value2');
    $qty1 = $rs->fields('qty1');
    $v1 = $rs->fields('value1');
    $tvalue = $rs->fields('totalValue');
    $status = $rs->fields('status');
    $docNo = $rs->fields('docNo');
    if ($collectPic == '') $collectPic = $rs->fields('collectPic');

    $createdBy = $rs->fields('createdBy') . '-' . dlookup("users", "fullName", "userName=" . tosql($rs->fields('createdBy'), "Text"));
    $createdDate = toDate("d/m/Y H:i:s A", $rs->fields('createdDate'));
    $updatedBy = $rs->fields('updatedBy') . '-' . dlookup("users", "fullName", "userName=" . tosql($rs->fields('updatedBy'), "Text"));
    $updatedDate = toDate("d/m/Y H:i:s A", $rs->fields('updatedDate'));
    $approvedBy = $rs->fields('approvedBy') . '-' . dlookup("users", "fullName", "userName=" . tosql($rs->fields('approvedBy'), "Text"));
    $approvedDate = toDate("d/m/Y H:i:s A", $rs->fields('approvedDate'));
}

// Set vendor for new records
if ($action == "add") {
    if ($_SESSION['Session.groupID'] == "0" || $_SESSION['Session.groupID'] == "1") {
        $vendorID = $_SESSION['Session.branchID'];
    }
}

// Prepare CDM/CICO listing
$cdmList = [];
$cdmVal = [];

$sSQL = "SELECT atmID, atmNo, atmLoc FROM atm WHERE isDeleted = 0 AND [type] IN ('CDM','CICO')";
if ($vendorID != "") {
    $sSQL .= " AND vendorID = '" . $vendorID . "'";
}
$sSQL .= " ORDER BY atmNo";

$GetList = $conn->Execute($sSQL);
if ($GetList->RowCount() != 0) {
    while (!$GetList->EOF) {
        array_push($cdmVal, $GetList->fields('atmID'));
        array_push($cdmList, $GetList->fields('atmNo') . ' - ' . $GetList->fields('atmLoc'));
        $GetList->MoveNext();
    }
}

// Set form input types based on action and status
$inputSelect = "select";
$inputDate = "date";
$inputFile = "file";
$readonly = "";
$inputText = "text";

if ($action == "update") {
    if (($status == "0" && $_SESSION['Session.groupID'] == "0") || 
        ($status == "0" && $_SESSION['Session.groupID'] == "1")) {
        $inputText = "text";
        $inputDate = "hidden";
        $inputFile = "file";
        $inputSelect = "hidden";
        $readonly = "";
    } else {
        $inputText = "hidden";
        $inputDate = "hidden";
        $inputFile = "hidden";
        $inputSelect = "hidden";
        $readonly = "readonly";
    }
}
?>

<form name="MyDHSB" action="<?php echo $sFileName; ?>?action=<?php echo $action; ?><?php if($action=='update') echo '&pk='.$pk; ?>" 
      method="post" enctype="multipart/form-data" onSubmit="submitonce(this)">
    
<input type="hidden" name="strURL" value="<?php echo $strURL; ?>">
<input type="hidden" name="vendorID" value="<?php echo $vendorID; ?>">
<input type="hidden" name="delDoc" value="<?php echo $delDoc; ?>">
<input type="hidden" name="docNo" value="<?php echo $docNo; ?>">

<table border="0" cellpadding="3" cellspacing="1" width="700" align="center" class="lineBG">
    <tr class="Header">
        <td colspan="3" height="30" valign="middle"><?php echo $Title; ?></td>
    </tr>
    
    <tr>
        <td class="Label" valign="top" align="right" width="150"><b>Tarikh Collection:</b></td>
        <td class="Data" valign="top" colspan="2">
            <?php echo FormEntry("", "collectDate", $inputDate, $collectDate, "", "", "12", "10") . $err1; ?>
        </td>
    </tr>
    
    <tr>
        <td class="Label" valign="top" align="right"><b>Mesin CDM:</b></td>
        <td class="Data" valign="top" colspan="2">
            <?php echo FormEntry("", "atmID", $inputSelect, $atmID, $cdmList, $cdmVal, "1", "1") . $err2; ?>
        </td>
    </tr>
    
    <?php if ($action == "update") { ?>
    <tr>
        <td class="Label" valign="top" align="right"><b>No Dokumen:</b></td>
        <td class="Data" align="top" colspan="2"><b><?php echo $docNo; ?></b></td>
    </tr>
    <?php } ?>
    
    <tr class="Header">
        <td height="30" valign="middle" width="150">DENOMINASI</td>
        <td align="center" width="150">KEPING</td>
        <td align="center" width="150">JUMLAH (RM)</td>
    </tr>
    
    <tr class="label">
        <td height="20" valign="top" align="right">RM 100:</td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="qty100" value="<?php echo $qty100; ?>" size="10" maxlength="10" 
                   onchange="autoCalc();" style="text-align:right;" <?php echo $readonly; ?> tabindex="1">
        </td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="v100" value="<?php echo number_format($v100, 2); ?>" 
                   style="text-align:right; border:none;" class="Data" size="10" readonly>
        </td>
    </tr>
    
    <tr class="label">
        <td height="20" valign="top" align="right">RM 50:</td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="qty50" value="<?php echo $qty50; ?>" size="10" maxlength="10" 
                   onchange="autoCalc();" style="text-align:right;" <?php echo $readonly; ?> tabindex="2">
        </td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="v50" value="<?php echo number_format($v50, 2); ?>" 
                   style="text-align:right; border:none;" class="Data" size="10" readonly>
        </td>
    </tr>
    
    <tr class="label">
        <td height="20" valign="top" align="right">RM 20:</td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="qty20" value="<?php echo $qty20; ?>" size="10" maxlength="10" 
                   onchange="autoCalc();" style="text-align:right;" <?php echo $readonly; ?> tabindex="3">
        </td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="v20" value="<?php echo number_format($v20, 2); ?>" 
                   style="text-align:right; border:none;" class="Data" size="10" readonly>
        </td>
    </tr>
    
    <tr class="label">
        <td height="20" valign="top" align="right">RM 10:</td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="qty10" value="<?php echo $qty10; ?>" size="10" maxlength="10" 
                   onchange="autoCalc();" style="text-align:right;" <?php echo $readonly; ?> tabindex="4">
        </td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="v10" value="<?php echo number_format($v10, 2); ?>" 
                   style="text-align:right; border:none;" class="Data" size="10" readonly>
        </td>
    </tr>
    
    <tr class="label">
        <td height="20" valign="top" align="right">RM 5:</td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="qty5" value="<?php echo $qty5; ?>" size="10" maxlength="10" 
                   onchange="autoCalc();" style="text-align:right;" <?php echo $readonly; ?> tabindex="5">
        </td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="v5" value="<?php echo number_format($v5, 2); ?>" 
                   style="text-align:right; border:none;" class="Data" size="10" readonly>
        </td>
    </tr>
    
    <tr class="label">
        <td height="20" valign="top" align="right">RM 2:</td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="qty2" value="<?php echo $qty2; ?>" size="10" maxlength="10" 
                   onchange="autoCalc();" style="text-align:right;" <?php echo $readonly; ?> tabindex="6">
        </td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="v2" value="<?php echo number_format($v2, 2); ?>" 
                   style="text-align:right; border:none;" class="Data" size="10" readonly>
        </td>
    </tr>
    
    <tr class="label">
        <td height="20" valign="top" align="right">RM 1:</td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="qty1" value="<?php echo $qty1; ?>" size="10" maxlength="10" 
                   onchange="autoCalc();" style="text-align:right;" <?php echo $readonly; ?> tabindex="7">
        </td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="v1" value="<?php echo number_format($v1, 2); ?>" 
                   style="text-align:right; border:none;" class="Data" size="10" readonly>
        </td>
    </tr>
    
    <tr class="label">
        <td valign="top" align="right"><b>Jumlah</b></td>
        <td class="Data">&nbsp;</td>
        <td class="Data" valign="top" align="right">
            <input type="text" name="tvalue" value="<?php echo number_format($tvalue, 2); ?>" 
                   style="text-align:right; border:none; font-weight:bold;" class="Data" size="10" readonly>
        </td>
    </tr>
    
    <tr class="label">
        <td height="20" valign="top" align="right">Slip Collection&nbsp;</td>
        <td class="Data" valign="top" align="left" colspan="2">
            <?php
            $filename = $path . '\\scanned\\' . $collectPic;
            if (file_exists($filename) && $collectPic != '') {
                if (($status == '0' && $_SESSION['Session.groupID'] == "0") || 
                    ($status == '0' && $_SESSION['Session.groupID'] == "1")) {
                    echo '<img src="images/delete.gif" border="0" onclick="DeleteDoc(\'' . $collectPic . '\');" title="Delete File">';
                }
                echo '&nbsp;&nbsp;<a href="scanned/' . $collectPic . '" target="new"><b>' . $collectPic . '</b></a>';
                echo '<input type="hidden" name="collectPic" value="' . $collectPic . '">';
            } else {
                echo 'No File Attached';
            }
            ?>
            <br><br>
            <input type="<?php echo $inputFile; ?>" name="collectPic1" onchange="document.MyDHSB.submit();">
            <?php echo $err3; ?>
        </td>
    </tr>
    
    <tr>
        <td colspan="3" align="center" class="Data"><br>
            <input type="Button" class="but" value="Kembali" 
                   onclick="window.location.href='<?php echo $sActionFileName . '?' . $strURL; ?>';">
            
            <?php if ($action == "update") { ?>
                <input type="hidden" name="collectID" value="<?php echo $pk; ?>">
                <input type="hidden" name="docNo" value="<?php echo $docNo; ?>">
                <?php if ($status == 0 && ($_SESSION['Session.groupID'] == "0" || $_SESSION['Session.groupID'] == "1")) { ?>
                    <input type="Submit" name="SubmitForm" class="but" value="Simpan">
                <?php } ?>
            <?php } else { ?>
                <input type="Submit" name="SubmitForm" class="but" value="Tambah">
            <?php } ?>
        </td>
    </tr>
    
    <tr class="Header">
        <td colspan="3" class="Header">AUDIT INFORMASI</td>
    </tr>
    <tr>
        <td class="Label" valign="top" align="right" width="150">Status:</td>
        <td class="Data" valign="top" colspan="2">
            <?php echo $statusList[array_search($status, $statusVal)] ?? 'Pending'; ?>
        </td>
    </tr>
    <tr>
        <td class="Label" valign="top" align="right">Direkod Oleh:</td>
        <td class="Data" valign="top" colspan="2"><?php echo $createdBy . '&nbsp;&nbsp;&nbsp;' . $createdDate; ?></td>
    </tr>
    <tr>
        <td class="Label" valign="top" align="right">Kemaskini Oleh:</td>
        <td class="Data" valign="top" colspan="2"><?php echo $updatedBy . '&nbsp;&nbsp;&nbsp;' . $updatedDate; ?></td>
    </tr>
    <tr>
        <td class="Label" valign="top" align="right">Diluluskan Oleh:</td>
        <td class="Data" valign="top" colspan="2"><?php echo $approvedBy . '&nbsp;&nbsp;&nbsp;' . $approvedDate; ?></td>
    </tr>
</table>
</form>

<script>
function DeleteDoc(id) {
    e = document.MyDHSB;
    if (confirm('Delete file ' + id + '?')) {
        e.delDoc.value = id;
        e.submit();
    }
}

function autoCalc() {
    e = document.MyDHSB;
    let TV = 0;
    
    for (let c = 0; c < e.elements.length; c++) {
        if (e.elements[c].name == "qty100") {
            if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {
                alert("Masukkan digit/nombor sahaja...!");
                e.elements[c].value = 0;
            }
            e.v100.value = (eval(e.elements[c].value) * 100).toFixed(2);
            TV += eval(e.v100.value);
        }
        
        if (e.elements[c].name == "qty50") {
            if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {
                alert("Masukkan digit/nombor sahaja...!");
                e.elements[c].value = 0;
            }
            e.v50.value = (eval(e.elements[c].value) * 50).toFixed(2);
            TV += eval(e.v50.value);
        }
        
        if (e.elements[c].name == "qty20") {
            if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {
                alert("Masukkan digit/nombor sahaja...!");
                e.elements[c].value = 0;
            }
            e.v20.value = (eval(e.elements[c].value) * 20).toFixed(2);
            TV += eval(e.v20.value);
        }
        
        if (e.elements[c].name == "qty10") {
            if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {
                alert("Masukkan digit/nombor sahaja...!");
                e.elements[c].value = 0;
            }
            e.v10.value = (eval(e.elements[c].value) * 10).toFixed(2);
            TV += eval(e.v10.value);
        }
        
        if (e.elements[c].name == "qty5") {
            if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {
                alert("Masukkan digit/nombor sahaja...!");
                e.elements[c].value = 0;
            }
            e.v5.value = (eval(e.elements[c].value) * 5).toFixed(2);
            TV += eval(e.v5.value);
        }
        
        if (e.elements[c].name == "qty2") {
            if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {
                alert("Masukkan digit/nombor sahaja...!");
                e.elements[c].value = 0;
            }
            e.v2.value = (eval(e.elements[c].value) * 2).toFixed(2);
            TV += eval(e.v2.value);
        }
        
        if (e.elements[c].name == "qty1") {
            if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {
                alert("Masukkan digit/nombor sahaja...!");
                e.elements[c].value = 0;
            }
            e.v1.value = (eval(e.elements[c].value) * 1).toFixed(2);
            TV += eval(e.v1.value);
        }
    }
    
    e.tvalue.value = TV.toFixed(2);
}
</script>

<?php
include('footer.php');