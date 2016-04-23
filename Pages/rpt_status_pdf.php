<?php
if(!isset($_SESSION)){ session_start(); } 
require_once('../syslibs/sysdbconfig.php');
require('../../../php/PEAR/fpdf/fpdf.php');

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

class PDF extends FPDF
{
	var $data;
	var $result;
	var $datetime;
	
	function Header()
	{
		$this->SetFont('Arial','B',11);
		$this->Cell(195,5, 'UNIVERSITY OF STO. TOMAS FACULTY UNION',0,1,'C');
		$this->SetFont('Arial','B',8);
		$this->Cell(195,5, $this->sub_header,0,1,'C');
		
		$header = array('STORE', 'DATE', 'CUSTOMER NAME', 'STUB#', 'DENOMINATION');
		
		$i = 0;
		$x = 12;
		$this->SetFont('Arial','B',6);
		while($i < count($header))
		{
			$this->SetXY($x, 35);
			$this->Cell(50,4,$header[$i],0);
			$i++;
			$x+=43;
		}
	}
	
	function Main()
	{
		$this->addPage();
		
		$y = 43;
		$a = 0;
		$w = 1;
		$total_amount = 0;
		while($row = mssql_fetch_array($this->result))
		{		
			$this->SetFont('Arial','B',7);
			if($a == 0) 
			{
				$this->SetXY(12, 25);
				$this->Cell(50,4,"STATUS: ".$row['cStatus'] ,0);
			}
			
			$this->SetFont('Arial','',5);
			$this->SetXY(12, $y);
			$this->Cell(50,4, $row['iLocationID'],0);
			$this->SetXY(55, $y);
			$this->Cell(50,4, $row['date'],0);
			$this->SetXY(98, $y);
			$this->Cell(50,4, $row['cEmployeeName'],0);
			$this->SetXY(141, $y);
			$this->Cell(50,4, $row['cStubNo'],0);
			$this->SetXY(184, $y);
			$this->Cell(50,4, $row['mAmount'],0);
			
			$y+=3;
			$a++;
			
			if($w == 95){
				$this->addPage();
				$w = 0;
				$y = 43;
				$a = 0;
			}
			$w++;
			$total_amount = $total_amount + $row['mAmount'];
		}
		$this->SetXY(175, $y + 10);
		$this->SetFont('Arial','B',7);
		$this->Cell(50,4, 'TOTAL   : P '. number_format($total_amount,2,'.',','),0);
	}
	
	function Footer()
	{	
		$this->SetFont('Arial','',4);
		$this->SetXY(10, -15);
		$this->MultiCell(65,4,"PRINTED BY : ".$this->data,0,'L');
		$this->SetXY(140, -15);
		$this->MultiCell(65,4,"PRINT DATE/TIME :". $this->datetime,0,'R');
		$this->SetXY(75, -15);
		$this->MultiCell(65,4,"PAGE ".$this->PageNo(). " of {nb}",0,'C');
	}
}
$pdf = new PDF();
$pdf->FPDF('P', 'mm', 'LEGAL');
$pdf->AliasNbPages();
$pdf->data = $user;
$pdf->sub_header = $sub_hdr;
$pdf->result = $qrySQL;//, 'STORE NAME'
$pdf->datetime = $datetime;
$pdf->Main($header);
$pdf->Output();
?>