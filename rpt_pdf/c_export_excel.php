<?php
class ExportExcel
{
	var $titles0=array();
	var $titles1=array();
	var $titles2=array();
	var $titles3=array();
	var $titles=array();
	var $all_values=array();
	var $filename;
	
	function __construct($f_name)
	{
		$this->filename = $f_name;
	}
	
	function setHeadersAndValues($hdrs0,$hdrs1,$hdrs,$all_vals)
	{
	    $this->titles0=$hdrs0;	   
	    $this->titles1=$hdrs1;
		$this->titles=$hdrs;
		$this->all_values=$all_vals;
	}
	
	function setHeadersAndValues1($hdrs0,$hdrs1,$hdrs2,$hdrs3,$hdrs,$all_vals)
	{
	    $this->titles0=$hdrs0;	   
	    $this->titles1=$hdrs1;
		$this->titles2=$hdrs2;
		$this->titles3=$hdrs3;
		$this->titles=$hdrs;
		$this->all_values=$all_vals;
	}
	
	function GenerateExcelFile()
	{
		global $header, $header0, $header1, $data;
		
		$header = $header0 = $header1 = '';
		
		foreach ($this->titles0 as $title_val0) {
			$header0 .= $title_val0 . "\t";
		}
		
		foreach ($this->titles1 as $title_val1) {
			$header1 .= $title_val1 . "\t";
		}
		
		foreach ($this->titles as $title_val) {
			$header .= $title_val . "\t";
		}
		
		$data = '';
		
		if (is_array($this->all_values)) {
			for ($i = 0; $i < sizeof($this->all_values); $i++) {
				$line = '';
				foreach ($this->all_values[$i] as $value) {
					if ((!isset($value)) || ($value == "")) {
						$value = "\t";
					} else {
						$value = str_replace('"', '""', $value);
						$value = '"' . $value . '"' . "\t";
					}
					$line .= $value;
				}
				$data .= trim($line) . "\n";
			}
		} else {
			$data = "\n(0) Records Found!\n";
		}
		
		$data = str_replace("\r", "", $data);
		
		if ($data == "") {
			$data = "\n(0) Records Found!\n";
		}
		
		if (ob_get_level()) ob_end_clean();
		
		header("Content-type: application/vnd.ms-excel");
		header('Content-Disposition: attachment; filename="'.$this->filename.'"');
		header("Pragma: no-cache");
		header("Expires: 0");
		
		echo "$header0\n$header1\n$header\n$data";
		exit;
	}

	function GenerateExcelFile1()
	{
		$header2 = ''; 
		global $header, $header0, $header1, $data;
		
		foreach ($this->titles0 as $title_val0) { 
			$header0 .= $title_val0."\t"; 
		} 
		foreach ($this->titles1 as $title_val1) { 
			$header1 .= $title_val1."\t"; 
		} 
		foreach ($this->titles2 as $title_val2) { 
			$header2 .= $title_val2."\t"; 
		} 
		foreach ($this->titles as $title_val) { 
			$header .= $title_val."\t"; 
		} 
		
		for($i=0;$i<sizeof($this->all_values);$i++) { 
			$line = ''; 
			foreach($this->all_values[$i] as $value) { 
				if ((!isset($value)) OR ($value == "")) { 
					$value = "\t"; 
				} else { 
					$value = str_replace('"', '""', $value); 
					$value = '"' . $value . '"'."\t"; 
				}
				$line .= $value; 
			}
			$data .= trim($line)."\n"; 
		}
		
		$data = str_replace("\r", "", $data); 
		
		if ($data == "") { 
			$data = "\n(0) Records Found!\n"; 
		}
		
		if (ob_get_level()) ob_end_clean();
		
		header("Content-type: application/vnd.ms-excel"); 
		header('Content-Disposition: attachment; filename="'.$this->filename.'"');
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
		
		echo "$header0\n$header1\n$header2\n$header\n$data";
		exit;
	}
	
	function GenerateExcelFile2()
	{
		$header3 = ''; 
		global $header, $header0, $header1, $header2, $data;
		
		foreach ($this->titles0 as $title_val0) { 
			$header0 .= $title_val0."\t"; 
		} 
		foreach ($this->titles1 as $title_val1) { 
			$header1 .= $title_val1."\t"; 
		} 
		foreach ($this->titles2 as $title_val2) { 
			$header2 .= $title_val2."\t"; 
		} 
		foreach ($this->titles3 as $title_val3) { 
			$header3 .= $title_val3."\t"; 
		} 
		foreach ($this->titles as $title_val) { 
			$header .= $title_val."\t"; 
		} 
		
		for($i=0;$i<sizeof($this->all_values);$i++) { 
			$line = ''; 
			foreach($this->all_values[$i] as $value) { 
				if ((!isset($value)) OR ($value == "")) { 
					$value = "\t"; 
				} else { 
					$value = str_replace('"', '""', $value); 
					$value = '"' . $value . '"'."\t"; 
				}
				$line .= $value; 
			}
			$data .= trim($line)."\n"; 
		}
		
		$data = str_replace("\r", "", $data); 
		
		if ($data == "") { 
			$data = "\n(0) Records Found!\n"; 
		}
		
		if (ob_get_level()) ob_end_clean();
		
		header("Content-type: application/vnd.ms-excel"); 
		header('Content-Disposition: attachment; filename="'.$this->filename.'"');
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
		
		echo "$header0\n$header1\n$header2\n$header3\n$header\n$data";
		exit;
	}
}