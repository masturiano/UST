<?php
ini_set('include_path','C:\wamp\php\PEAR');
if(!isset($_SESSION)) { session_start(); }
require_once 'Spreadsheet/Excel/Writer.php';
require_once('../syslibs/sysdbconfig.php');

$myconn = new ustconfig;
$myconn->ust_dbconn();
$user = $_SESSION['session_fullname'];
$datetime = date('m/d/Y h:i:s A', time() + 28800);

$soano = urldecode(str_replace('\\','',$_GET['soano']));
$from = date('m/d/Y',strtotime(urldecode($_GET['from'])));
$to = date('m/d/Y',strtotime(urldecode($_GET['to'])));

$strSQL = "SELECT DISTINCT compcode, compname FROM view_receive_summary WHERE soano = " . $soano;;
$result_company = mssql_query($strSQL);

	
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

$x = 1;
while($company = mssql_fetch_array($result_company))
{
	$worksheet = &$workbook->addWorksheet($company['compname']);

	$worksheet->setLandscape();

	$date = date('F d, Y');
	$time = date('h:i:s', time() + 28800);

	$worksheet->setColumn(0,0,15);
	$worksheet->setColumn(1,1,30);
	$worksheet->setColumn(2,2,30);
	$worksheet->setColumn(3,3,30);
	$worksheet->setColumn(4,4,25);
	$worksheet->setColumn(5,5,25);

	$i=0;

	$worksheet->write($i,0,$company['compname'],$headerFormat);
	$worksheet->mergeCells($i,0,$i,4);
	$i++;
	$worksheet->write($i,0,'UST PURCHASES',$headerFormat);
	$worksheet->mergeCells($i,0,$i,4);

	$strSQL = "SELECT iLocationID, cStoreDesc, CONVERT(varchar,dRedeemed,101) AS dRedeemed, cEmployeeName, cStubNo, mAmount, soa_daterange
			FROM view_receive_summary
			WHERE soano = ". $soano . " AND compcode = ".$company['compcode'];
	$qrySQL = mssql_query($strSQL);
	$ctr=0;
	while($result = mssql_fetch_array($qrySQL))
	{	
		if($ctr==0)
		{
			$i++;
			$worksheet->write($i,0,'COVERED PERIOD : ' . $result['soa_daterange'],$headerFormat);
			$worksheet->mergeCells($i,0,$i,4);

			$i+=2;
			$worksheet->write($i,0,'STORE CODE',$headerFormat);
			$worksheet->write($i,1,'STORE NAME',$headerFormat);
			$worksheet->write($i,2,'DATE',$headerFormat);
			$worksheet->write($i,3,'CUSTOMER NAME',$headerFormat);
			$worksheet->write($i,4,'CLAIM UST STUB#',$headerFormat);
			$worksheet->write($i,5,'DENOMINATION',$headerFormat);
		}
		
		$i++;
		$worksheet->write($i,0,$result['iLocationID'],$col_C);
		$worksheet->write($i,1,$result['cStoreDesc'],$col_L);
		$worksheet->write($i,2,$result['dRedeemed'],$col_C);
		$worksheet->write($i,3,$result['cEmployeeName'],$col_L);
		$worksheet->write($i,4,$result['cStubNo'],$col_C);
		$worksheet->write($i,5,$result['mAmount'],$col_R);
		$ctr++;
	}
 }

$workbook->close();
?>