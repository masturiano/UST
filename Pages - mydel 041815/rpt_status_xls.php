<?php
ini_set('include_path','C:\wamp\php\PEAR');
if(!isset($_SESSION)) { session_start(); }
require_once 'Spreadsheet/Excel/Writer.php';
require_once('../syslibs/sysdbconfig.php');

$myconn = new ustconfig;
$myconn->ust_dbconn();
$user = $_SESSION['session_fullname'];
$datetime = date('m/d/Y h:i:s A', time() + 28800);

$status = urldecode($_GET['status']);
$store = urldecode($_GET['store']);
$date_from = $_GET['from'];
$date_to = $_GET['to'];

$strSQL = "SELECT cStubNo, cEmployeeName, cDepartment, mAmount, cStatus, date, iLocationID FROM view_couponstatus WHERE cStatus = '". $status ."' ";
$sub_hdr = 'STATUS REPORT AS OF '.date('m/d/Y');
if($status != 'OPEN')
{
	$strSQL .= " AND RIGHT(date,10) BETWEEN '".$date_from."' AND '".$date_to."'";
	$sub_hdr = 'STATUS REPORT FROM '.$date_from.' TO '.$date_to;
}

if ($_SESSION['session_dept_id'] == 102)
{
	$strSQL .= " AND ILOCATIONID = ".$_SESSION['session_code'];
}
else
{
	$where = ($store == 0) ? "" : " AND iLocationID = ".$store;
	$strSQL .= $where;
}

$qrySQL = mssql_query($strSQL);
	
$workbook = new Spreadsheet_Excel_Writer();
$headerFormat = $workbook->addFormat(array('Size' => 11, 'Color' => 'black', 'bold'=> 1, 'Align' => 'merge'));
$headerFormat->setFontFamily('Calibri'); 

$headerFormat_C = $workbook->addFormat(array('Size' => 10, 'Color' => 'black', 'Align' => 'center'));
$headerFormat_C->setFontFamily('Calibri');

$headerFormat_L = $workbook->addFormat(array('Size' => 11, 'Color' => 'black', 'bold'=> 1, 'Align' => 'left'));
$headerFormat_L->setFontFamily('Calibri');

$headerFormat_L_B = $workbook->addFormat(array('Size' => 10, 'Color' => 'black', 'bold'=> 1, 'Align' => 'left'));
$headerFormat_L_B->setFontFamily('Calibri');  

$headerFormat_C_B = $workbook->addFormat(array('Size' => 10, 'Color' => 'black', 'bold'=> 1, 'Align' => 'center'));
$headerFormat_C_B->setFontFamily('Calibri');  
								  
$col_I_L = $workbook->addFormat(array('Size' => 10, 'Color' => 'black', 'italic'=>1, 'Align' => 'left'));
$col_I_L->setFontFamily('Calibri'); 

$col_IB_C = $workbook->addFormat(array('Size' => 10, 'Color' => 'black', 'bold'=> 1, 'italic'=>1, 'Align' => 'center'));
$col_IB_C->setFontFamily('Calibri'); 

$col_B_C = $workbook->addFormat(array('Size' => 10, 'Color' => 'black', 'bold'=> 1, 'Align' => 'center'));
$col_B_C->setFontFamily('Calibri');

$col_B_R = $workbook->addFormat(array('Size' => 10, 'Color' => 'black', 'bold'=> 1, 'Align' => 'right'));
$col_B_R->setFontFamily('Calibri');

$col_C = $workbook->addFormat(array('Size' => 10, 'Color' => 'black', 'Align' => 'center'));
$col_C->setFontFamily('Calibri');

$col_R = $workbook->addFormat(array('Size' => 10, 'Color' => 'black', 'Align' => 'right'));
$col_R->setFontFamily('Calibri');

$col_L = $workbook->addFormat(array('Size' => 10, 'Color' => 'black', 'Align' => 'left'));
$col_L->setFontFamily('Calibri');

$format_undrln_C = $workbook->addFormat(array('bottom' => 1, 'size' => 10,'Align'=>'center'));
$format_undrln_C->setFontFamily('Calibri'); 

$format_undrln_R = $workbook->addFormat(array('top' => 1,'bottom' => 6, 'size' => 10,'Align'=>'right'));
$format_undrln_R->setFontFamily('Calibri'); 

$filename = "StatusReportAsOf".date('Ymd').".xls";
$workbook->send($filename);
$worksheet = &$workbook->addWorksheet($filename);
$worksheet->setLandscape();

$date = date('F d, Y');
$time = date('h:i:s', time() + 28800);

$worksheet->setColumn(0,0,15);
$worksheet->setColumn(1,1,20);
$worksheet->setColumn(2,2,30);
$worksheet->setColumn(3,3,25);
$worksheet->setColumn(4,4,25);

$i=0;

$worksheet->write($i,0,'UNIVERSITY OF STO. TOMAS FACULTY UNION',$headerFormat);
$worksheet->mergeCells($i,0,$i,4);
$i++;
$worksheet->write($i,0,$sub_hdr,$headerFormat);
$worksheet->mergeCells($i,0,$i,4);

$i+=2;
$worksheet->write($i,0,'STATUS : '.$status,$headerFormat_L);
$worksheet->mergeCells($i,0,$i,1);

$i+=2;
$worksheet->write($i,0,'STORE',$headerFormat);
$worksheet->write($i,1,'DATE',$headerFormat);
$worksheet->write($i,2,'CUSTOMER NAME',$headerFormat);
$worksheet->write($i,3,'STUB#',$headerFormat);
$worksheet->write($i,4,'DENOMINATION',$headerFormat);

$total_amount = 0;
while($result = mssql_fetch_array($qrySQL))
{	
		$i++;
		$worksheet->write($i,0,$result['iLocationID'],$col_C);
//		$worksheet->write($i,1,date('M d, Y',strtotime($result['date'])),$col_L);
		$worksheet->write($i,1,$result['date'],$col_L);
		$worksheet->write($i,2,$result['cEmployeeName'],$col_L);
		$worksheet->write($i,3,$result['cStubNo'],$col_C);
		$worksheet->write($i,4,number_format($result['mAmount'],2,'.',','),$col_R);
	//	$worksheet->write($i,4,$result['mAmount'],$col_R);
		$total_amount = $total_amount + $result['mAmount'];
}

$i+=2;
$worksheet->write($i,3,'TOTAL : ',$col_B_C);
$worksheet->write($i,4,number_format($total_amount,2,'.',','),$col_B_R);
$workbook->close();
?>