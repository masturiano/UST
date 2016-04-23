<?php
ini_set('include_path','C:\wamp\php\PEAR');
if(!isset($_SESSION)) { session_start(); }
require_once 'Spreadsheet/Excel/Writer.php';
require_once('../syslibs/sysdbconfig.php');

$myconn = new ustconfig;
$myconn->ust_dbconn();
$user = $_SESSION['session_fullname'];
$datetime = date('m/d/Y h:i:s A', time() + 28800);

$strLogAction = "INSERT INTO tblaudittrail(cUserID, process_date, process) VALUES('".$_SESSION['session_id']."','".$datetime."','PRINTED MONTHLY REDEMPTION REPORT') ";
mssql_query($strLogAction);

function getMonYr()
{
	$strSQL = "SELECT DISTINCT YEAR(c.dRedeemed) AS exp_yr, MONTH(c.dRedeemed) AS exp_mon
			FROM tblcoupons c
			WHERE bRedeemed=1
			ORDER BY YEAR(c.dRedeemed) ASC, MONTH(c.dRedeemed) ASC";
	$qry_year = mssql_query($strSQL);
	return $qry_year;
}

function getStore()
{
	$strSQL = "SELECT DISTINCT c.iLocationID, s.cStoreDesc
			FROM tblcoupons c
			INNER JOIN tblstr s
				ON c.iLocationID = s.iLocationID
			WHERE bRedeemed=1
			ORDER BY c.iLocationID";
	$qry_store = mssql_query($strSQL);
//	$result = mssql_fetch_array($qry_store);
	return $qry_store;
}

function getAmt($store, $year, $month)
{
	$strSQL = "SELECT SUM(mAmount) AS exp_amt
			FROM tblcoupons
			WHERE bRedeemed=1 AND iLocationID=".$store." AND YEAR(dRedeemed)=".$year." AND MONTH(dRedeemed)=".$month;
	$qryAmount = mssql_query($strSQL);
	
	$result2 = mssql_fetch_array($qryAmount);
	return $result2['exp_amt'];
}

$arrMon = array(1 => 'JAN',
				2 => 'FEB',
				3 => 'MAR',
				4 => 'APR',
				5 => 'MAY',
				6 => 'JUN',
				7 => 'JUL',
				8 => 'AUG',
				9 => 'SEP',
				10 => 'OCT',
				11 => 'NOV',
				12 => 'DEC');

	
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

$filename = "ExceptionReportAsOf".date('Ymd').".xls";
$workbook->send($filename);
$worksheet = &$workbook->addWorksheet($filename);
$worksheet->setLandscape();

$date = date('F d, Y');
$time = date('h:i:s', time() + 28800);

$worksheet->setColumn(0,0,15);
$worksheet->setColumn(1,1,30);
$worksheet->setColumn(2,2,30);
$worksheet->setColumn(3,3,25);
$worksheet->setColumn(4,4,25);

$i=0;

$worksheet->write($i,0,'UNIVERSITY OF STO. TOMAS FACULTY UNION',$headerFormat);
$worksheet->mergeCells($i,0,$i,4);
$i++;
$worksheet->write($i,0,'MONTHLY REDEMPTION REPORT AS OF '.date('m/d/Y'),$headerFormat);
$worksheet->mergeCells($i,0,$i,4);

 $i+=2;

$worksheet->write($i,0,'STORE CODE',$headerFormat);
$worksheet->write($i,1,'STORE NAME',$headerFormat);
$col_ctr = 2;
$y = $i;
$arrDate = getMonYr();
$ctr = 0;

$base = $i;
$str_ctr = 0;
$total_amount = 0;
while($result_year = mssql_fetch_array($arrDate))
{
	
	$worksheet->write($y,$col_ctr, $arrMon[$result_year['exp_mon']] . " " . $result_year['exp_yr'],$headerFormat);
	$arrStore = getStore();
	
	
	$str_ctr = $base;
	while($result = mssql_fetch_array($arrStore))
	{
			if($ctr == 0)
			{
				$str_ctr++;
				$worksheet->write($str_ctr,0,$result['iLocationID'],$col_C);
				$worksheet->write($str_ctr,1,$result['cStoreDesc'],$col_L);
			}
			else
			{
				$str_ctr++; 
			}
			$Amt = getAmt($result['iLocationID'],$result_year['exp_yr'],$result_year['exp_mon']);
			$worksheet->write($str_ctr,$col_ctr, number_format($Amt,2,'.',','),$col_R);
			$total_amount = $total_amount + $Amt;
	}
	$str_ctr++;
	
	$worksheet->write($str_ctr,1,'TOTAL : ',$col_B_C);
	$worksheet->write($str_ctr,$col_ctr,number_format($total_amount,2,'.',','),$col_B_R);
	$col_ctr++;
	$ctr++;
}

$workbook->close();
?>