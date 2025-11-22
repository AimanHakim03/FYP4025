<?php
/
print '
<script>
	function dispName(field) {
		e = document.MyForm;
	  	for(c=0; c<e.elements.length; c++) {
	    	if(e.elements[c].name==field) {
				pk = e.elements[c].value;
				alert (pk);
				e.display.value = pk;
			}
		}		
	}	
</script>
';

function FormEntry($strFormName, $strFormElement, $strFormType, $strFormValue, $strFormData, $strFormDataValue, $strFormSize, $strFormLength)
{
//	if (($strFormType == "number"))
	if ($strFormType == "number")
		print '<input name='.$strFormElement.' type="text" size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'"  style="text-align:right;" >';

//		print '<input name='.$strFormElement.' type='.$strFormType.' size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'">';

	//--- text & password ---
	if (($strFormType == "text") || ($strFormType == "password"))
		print '<input name='.$strFormElement.' type='.$strFormType.' size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'">';

//	if (($strFormType == "textAuto"))
//		print '<input name='.$strFormElement.' type="text" size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'" onchange="document.MyDHSB.submit();">';
		
	if (($strFormType == "textAuto")) {
		print '<input name='.$strFormElement.' type="text" size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'" onblur="document.MyDHSB.submit();">';		
		if ($strFormDataValue <> '') {
			print '&nbsp;&nbsp;&nbsp;<b>'.$strFormData[array_search($strFormValue,$strFormDataValue)].'</b>';
		}
	}
	if ($strFormType == "textUcase") {
		print '<input name='.$strFormElement.' type='.$strFormType.' size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'" onKeyup="this.value=this.value.toUpperCase();">';		
	}
	//--- displayonly ---
	if ($strFormType == "displayonly") {
		if ($strFormData == "") { 
			print ' <input class="inputDisable" name='.$strFormElement.' type="text" size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'" onfocus="this.blur()">';
		} else {
			print ' <input class="inputDisable" name='.$strFormElement.' type="text" size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'" onfocus="this.blur()"  onselect="dispName(\''.$strFormElement.'\');">';
			print ' <input class="inputDisable" name="display" type="text" size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'" onfocus="this.blur()">';
//			print ' <input class="inputDisable" name="display" type="text" size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'" onfocus="this.blur()">';
//			print '<b>'.$strFormData[array_search($strFormValue,$strFormDataValue)].'</b>';
		}
	}
		
	//--- hidden & file ---
//	if (($strFormType == "hidden") || ($strFormType == "file")) {
	if ($strFormType == "hidden") {
		print '<input name='.$strFormElement.' type='.$strFormType.' value="'.$strFormValue.'">';
		if ($strFormData == "") { 
			print '<b>'.$strFormValue.'</b>';
		} else {
//			if ($strFormValue <> "" AND $strFormValue <> '0') {
			if ($strFormValue <> "" ) {
				print '<b>'.$strFormData[array_search($strFormValue,$strFormDataValue)].'</b>';
			}
		}
	}
	
	if ($strFormType == "file") {
		print '<input name="'.$strFormElement.'" type="'.$strFormType.'" value="'.$strFormValue.'">';
	}	
		
	//--- checkbox ---
	if ($strFormType == "checkbox"){
//		print '<input name="'.$strFormElement.'" type="'.$strFormType.'" value="'.$strFormValue.'" ';
		print '<input name="'.$strFormElement.'" type="'.$strFormType.'" value="1" ';
		if ($strFormValue == 1) print 'checked';
		print '>';
	}

	if ($strFormType == "hiddencheckbox"){
		print '<input name="'.$strFormElement.'" type="hidden" value="'.$strFormValue.'" >&nbsp;';
		if ($strFormValue == 1) print '<b>Ya</b>'; else print '<b>Tidak</b>';
	}
	
	//--- readonly - especially for password display ---
	if ($strFormType == "readonly"){
		print '<input name='.$strFormElement.' type="hidden" value='.$strFormValue.'>&nbsp;';
		print '<b>******************</b>';
	}
	
	//--- Hidden Date ---
	if ($strFormType == "hiddenDate"){
		if (strlen($strFormValue) <> 0)
//			$strDate = substr($strFormValue,8,2).'/'.substr($strFormValue,5,2).'/'.substr($strFormValue,0,4);
			$strDate = toDate("d/m/Y",$strFormValue);
		else
			$strDate = $strFormValue;
		print '<input name='.$strFormElement.' type="hidden" value='.$strDate.'>';
		print '<b>'.$strDate.'</b>';
	}	
	//--- Hidden Date Time---
	if ($strFormType == "hiddenDateTime"){
		if (strlen($strFormValue) <> 0)
//			$strDate = substr($strFormValue,8,2).'/'.substr($strFormValue,5,2).'/'.substr($strFormValue,0,4).' '.substr($strFormValue,11,8);
			$strDate = toDate("d/m/Y H:i:s A",$strFormValue);
		else
			$strDate = $strFormValue;
		print '<input name='.$strFormElement.' type="hidden" value='.$strDate.'>';
		print '<b>'.$strDate.'</b>';
	}	
	//--- Date ---
	if ($strFormType == "date"){
/*		if (strlen($strFormValue) <> 0)
			$strDate = substr($strFormValue,8,2).'/'.substr($strFormValue,5,2).'/'.substr($strFormValue,0,4);
		else*/
		$strDate = $strFormValue;
		print '<input name="'.$strFormElement.'" type="text" size="12" maxlength="10" value="'.$strDate.'" onClick="popup(this.name);" >';
	}

	//--- TextArea ---
	if ($strFormType == "textarea")
		print '<textarea class="textFont" cols='.$strFormSize.' rows='.$strFormLength.' wrap="hard" name='.$strFormElement.' class="textFont">'.$strFormValue.'</textarea>';

	if ($strFormType == "textarea255")
		print '<textarea class="textFont" cols='.$strFormSize.' rows='.$strFormLength.' wrap="hard" name='.$strFormElement.' maxlength="255" onkeyup="return ismaxlength(this)" class="textFont">'.$strFormValue.'</textarea>';

	if ($strFormType == "textarea100")
		print '<textarea class="textFont" cols='.$strFormSize.' rows='.$strFormLength.' wrap="hard" name='.$strFormElement.' maxlength="255" onkeyup="return ismaxlength(this)" class="textFont">'.$strFormValue.'</textarea>';
		
	//--- Radio ---
	if ($strFormType == "radio" || $strFormType == "hiddenRadio" || ($strFormType == "radioAuto")){
		for ($cnt = 0; $cnt < count($strFormData); $cnt++) {
			print '<input type="radio" name='.$strFormElement.' value="'.$strFormDataValue[$cnt].'" ';
			if ($strFormValue == $strFormDataValue[$cnt]) print ' checked ';
			if ($strFormType == "hiddenRadio") print ' disabled ';
			if ($strFormType == "radioAuto") print ' onclick="document.MyDHSB.submit();" ';
			print '><font class="textFont">&nbsp;'.$strFormData[$cnt].'</font> '; 
		}
	}
	
	//--- Select Box ---
	if (($strFormType == "select") || ($strFormType == "hiddenSelect")  || ($strFormType == "selectAuto")) {
		if ($strFormType == "hiddenSelect") {
			print '<select name="'.$strFormElement.'" class="inputDisable">
					<option value="">';
		} else {
			print '<select name="'.$strFormElement.'" class="textFont" ';
			if ($strFormType == "selectAuto") {
				print ' onchange="document.MyDHSB.submit();" ';
			}
			if ($strFormName <>  "") {
				print  '><option value="">- Select '.$strFormName.' -';
			} else {
				print  '><option value="">--';
			}
		}
		for ($cnt = 0; $cnt < count($strFormData); $cnt++) {
			print '<option value="'.$strFormDataValue[$cnt].'" ';
			if ($strFormValue == $strFormDataValue[$cnt]) print 'selected';
			print '>'.$strFormData[$cnt];
		}
		print '</select>';
	}
		
}

function FormValidation($strFormName, $strFormElement, $strFormValue, $strFormValidMethod, $errCount)
{
    global $strErrMsg;
    $errMsg = '';

    if ($strFormValue == "") {
        if ($strFormValidMethod == "CheckBlank") {
            array_push($strErrMsg, $strFormElement);
            $errMsg .= '<font class=redText>- '.$strFormName.' cannot be blank.</font><br>';
        }
        if ($strFormValidMethod == "CheckRadio") {
            array_push($strErrMsg, $strFormElement);
            $errMsg .= '<font class=redText>- Please tick '.$strFormName.'.</font><br>';
        }
        if ($strFormValidMethod == "CheckSelect") {
            array_push($strErrMsg, $strFormElement);
            $errMsg .= '<font class=redText>- Please select '.$strFormName.'.</font><br>';
        }
    } else {
        if ($strFormValidMethod == "CheckNumeric") {
            // check for non-numeric (allow digits, comma, dot)
            if (preg_match("/[^0-9,.]/", $strFormValue)) {
                array_push($strErrMsg, $strFormElement);
                $errMsg .= '<font class=redText>- '.$strFormName.' must be digit.</font><br>';
            }
        }
        if ($strFormValidMethod == "CheckEmailAddress") {
            // modern email regex
            if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\.)+[a-z]{2,}$/i", $strFormValue)) {
                array_push($strErrMsg, $strFormElement);
                $errMsg .= '<font class=redText>- Invalid email address.</font><br>';
            }
        }
        if ($strFormValidMethod == "CheckDate") {
            if (ValidDate($strFormValue) == "N") {
                array_push($strErrMsg, $strFormElement);
                $errMsg .= '<font class=redText>- Date format '.$strFormName.' not valid (dd/mm/yyyy) OR  date incorrect.</font><br>';
            }
        }
    }
    return $errMsg;
}

/* check if a date in the format "DD/MM/YYYY" is valid. returns "Y" if valid, "N" if invalid */ 
function ValidDate($value) {
    $parts = explode("/", $value);
    if (count($parts) !== 3) {
        return "N";
    }

    list($tmpday, $tmpmonth, $tmpyear) = $parts;

    // make sure all are numbers and year has 4 digits
    if (!preg_match("/^\d+$/", $tmpday) || !preg_match("/^\d+$/", $tmpmonth) || !preg_match("/^\d{4}$/", $tmpyear)) {
        return "N";
    }

    // validate real date
    if (checkdate((int)$tmpmonth, (int)$tmpday, (int)$tmpyear)) {
        return "Y";
    } else {
        return "N";
    }
}


/* display time - $strTime : variable , $timeDB : time capture from database */
function selectTime($strTime, $timeDB) {
	print ' Time ';
	print '<select name="'.$strTime.'" class=data>"';
	print '	<option value="12:00:00 AM">12:00 AM</option>';	
	print '	<option value="12:30:00 AM">12:30 AM</option>';	
	print '	<option value="1:00:00 AM">1:00 AM</option>';	
	print '	<option value="1:30:00 AM">1:30 AM</option>';	
	print '	<option value="2:00:00 AM">2:00 AM</option>';	
	print '	<option value="2:30:00 AM">2:30 AM</option>';	
	print '	<option value="3:00:00 AM">3:00 AM</option>';	
	print '	<option value="3:30:00 AM">3:30 AM</option>';	
	print '	<option value="4:00:00 AM">4:00 AM</option>';	
	print '	<option value="4:30:00 AM">4:30 AM</option>';	
	print '	<option value="5:00:00 AM">5:00 AM</option>';	
	print '	<option value="5:30:00 AM">5:30 AM</option>';	
	print '	<option value="6:00:00 AM">6:00 AM</option>';	
	print '	<option value="6:30:00 AM">6:30 AM</option>';	
	print '	<option value="7:00:00 AM">7:00 AM</option>';	
	print '	<option value="7:30:00 AM">7:30 AM</option>';		
	print '	<option value="8:00:00 AM">8:00 AM</option>';
	print '	<option value="8:30:00 AM">8:30 AM</option>';
	print '	<option value="8:00:00 AM">9:00 AM</option>';
	print '	<option value="9:30:00 AM">9:30 AM</option>';
	print '	<option value="10:00:00 AM">10:00 AM</option>';	
	print '	<option value="10:30:00 AM">10:30 AM</option>';	
	print '	<option value="11:00:00 AM">11:00 AM</option>';	
	print '	<option value="11:30:00 AM">11:30 AM</option>';	
	print '	<option value="12:00:00 PM">12:00 PM</option>';	
	print '	<option value="12:30:00 PM">12:30 PM</option>';	
	print '	<option value="1:00:00 PM">1:00 PM</option>';	
	print '	<option value="1:30:00 PM">1:30 PM</option>';	
	print '	<option value="2:00:00 PM">2:00 PM</option>';	
	print '	<option value="2:30:00 PM">2:30 PM</option>';	
	print '	<option value="3:00:00 PM">3:00 PM</option>';	
	print '	<option value="3:30:00 PM">3:30 PM</option>';	
	print '	<option value="4:00:00 PM">4:00 PM</option>';	
	print '	<option value="4:30:00 PM">4:30 PM</option>';	
	print '	<option value="5:00:00 PM">5:00 PM</option>';	
	print '	<option value="5:30:00 PM">5:30 PM</option>';	
	print '	<option value="6:00:00 PM">6:00 PM</option>';	
	print '	<option value="6:30:00 PM">6:30 PM</option>';	
	print '	<option value="7:00:00 PM">7:00 PM</option>';	
	print '	<option value="7:30:00 PM">7:30 PM</option>';		
	print '	<option value="8:00:00 PM">8:00 PM</option>';
	print '	<option value="8:30:00 PM">8:30 PM</option>';
	print '	<option value="8:00:00 PM">9:00 PM</option>';
	print '	<option value="9:30:00 PM">9:30 PM</option>';
	print '	<option value="10:00:00 PM">10:00 PM</option>';	
	print '	<option value="10:30:00 PM">10:30 PM</option>';	
	print '	<option value="11:00:00 PM">11:00 PM</option>';	
	print '	<option value="11:30:00 PM">11:30 PM</option>';	
	print '</select>';
}

print'
<script language="javascript">
	function popup(wname) {
		window.open("calendar.php?action=" + wname, "calendar","width=250,height=200,left=200,top=200");
	}

	function ismaxlength(obj){
		var mlength=obj.getAttribute? parseInt(obj.getAttribute("maxlength")) : ""
		if (obj.getAttribute && obj.value.length>mlength)
			obj.value=obj.value.substring(0,mlength)
	}
</script>';

?>