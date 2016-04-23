<?php
if(!isset($_SESSION)){ session_start(); } 
require_once('../syslibs/sysdbconfig.php');
require('../../../php/PEAR/fpdf/fpdf.php');

$myconn = new ustconfig;
$myconn->ust_dbconn();
$user = $_SESSION['session_user'];//'AYRA LIZA M. MAGALLANES';
$datetime = date('Y-m-d h:i:s', time() + 28800);
$soano = urldecode(str_replace('\\','',$_GET['soano']));
$from = date('m/d/Y',strtotime(urldecode($_GET['from'])));
$to = date('m/d/Y',strtotime(urldecode($_GET['to'])));

$strSQLCompany = "SELECT DISTINCT compcode, compname 
					FROM view_coupons
					WHERE soano = " . $soano;
$qrySQLCompany = mssql_query($strSQLCompany);


class PDF extends FPDF
{
	var $data;
	var $result_company;
	var $date_from;
	var $date_to;
	var $soa_no;
	
	function Main($arrH)
	{
		while($company = mssql_fetch_array($this->result_company))
		{	
			$y = 60;
			$total_stub = 0;
			$total_amount = 0;
			$this->addPage();
			$qryResult = $this->getDetails($this->soa_no, $company['compcode']);
			$dueAmt = $this->getDueAmt($this->soa_no, $company['compcode']);
			
			$this->SetFont('Arial','B',15);
			$this->Cell(98,10, $company['compname'],0,0,'L');
			$this->SetFont('Arial','IB',8);
			$this->Cell(97,5, '3/F Tabacalera Bldg. 600 D. Romualdez Sr. Street, Ermita, Manila',0,0,'R');
			$this->SetFont('Arial','B',14);
			$this->Ln(8);
			$this->Cell(98,5, 'Statement of Account',0,0,'L');
			
			$this->SetFont('Arial','B',8);
			$this->Rect(10,26,90,17);
			$this->Rect(103,26,98,17);
			$this->SetXY(12,32);
			$this->MultiCell(90,4,"UNIVERSITY OF STO. TOMAS FACULTY UNION \r\n 301 Health Service Bldg. UST, España, Sampaloc, Manila",0);
			
			$soano = str_pad($this->soa_no, 9, "0", STR_PAD_LEFT);
			$this->SetXY(105,29);
			$this->MultiCell(150,4,"Statement Account Number",0);
			$this->SetXY(144,29);
			$this->MultiCell(150,4,' : ' . $soano,0);
			
			$this->SetXY(105,33);
			$this->MultiCell(150,4,"Due Date",0);
			$this->SetXY(144,33);
			$this->MultiCell(150,4," : 21st working days upon receipt of SOA",0);
			
			$this->SetXY(105,37);
			$this->MultiCell(150,4,"Due Amount",0);			
			
			$this->SetXY(144,37);
			$this->MultiCell(150,4," : Php ". number_format($dueAmt,2,'.',','),0);

			$this->Rect(10,48,191,5);
			$this->Rect(10,55,191,200);
		
			$i = 0;
			$x = 12;
			$this->SetFont('Arial','B',9);
			while($i < count($arrH))
			{
				$this->SetXY($x, 49);
				$this->Cell(50,4,$arrH[$i],0);
				$i++;
				$x+=45;
			}
			
			$ctr = 0;
			while($row = mssql_fetch_array($qryResult))
			{	
				if($ctr > 35)
				{
					$this->addPage();
					$this->Rect(10,25,191,230);
					$y = 30;
					$ctr = 0;
				}
				
				$this->SetFont('Arial','',8);
				$this->SetXY(12, $y);
				$this->Cell(50,4, $row['soa_daterange'],0);
				$this->SetXY(67, $y);
				$this->Cell(50,4, $row['noOfStubs'],0);
				$this->SetXY(102, $y);
				$this->Cell(50,4, $row['iLocationID'] . "-" . $row['cStoreName'],0);
				$this->SetXY(160, $y);
				$this->Cell(50,4, number_format($row['totalAmt'],2,'.',','),0);
				
				$y+=5; 
				$total_stub = $total_stub + $row['noOfStubs'];
				// $total_amount = $total_amount + $row['totalAmt'];
				$ctr++;
			}
			
			// $this->SetXY(144,37);
			// $this->MultiCell(150,4," : Php ". number_format($total_amount,2,'.',','),0);
			
			$this->SetFont('Arial','',8);
			$this->SetXY(12, 235);
			$this->Cell(100,4,'Total No. of Ust Stub Used: ' . $total_stub,0);
			$this->Footer_();
		}
	}
	
	function getDetails($soano, $compcode)
	{
		$strSQL = " SELECT noOfStubs, compcode, compname, soano, iLocationID, cStoreName, totalAmt, soa_daterange
					FROM view_coupons
					WHERE soano = ". $soano . " AND compcode = ".$compcode;
		$qrySQL = mssql_query($strSQL);
		return $qrySQL;
	}
	
	function getDueAmt($soano, $compcode)
	{
		$strSQL = " SELECT SUM(totalAmt) AS totalDueAmt
					FROM view_coupons
					WHERE soano = ". $soano . " AND compcode = ".$compcode;
		$qrySQL = mssql_query($strSQL);
		$result = mssql_fetch_array($qrySQL);
		return $result['totalDueAmt'];
	}

	function Footer_()
	{	
		$this->SetXY(20, -33);
		$this->SetFont('Arial','',9);
		$this->MultiCell(65,4,"Processed By : \r\n\r\n".$this->data,0);
		$this->SetXY(100, -33);
		$this->MultiCell(65,4,"Noted By :",0);
		$this->SetXY(160, -33);
		$this->MultiCell(65,4,"Received By :",0);
		
		//$this->Cell(98,10,'DATE/TIME : '.$datetime,0,0,'R');
	}
}
$pdf = new PDF();
$pdf->FPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->data = $user;
$pdf->date_from = $from;
$pdf->date_to = $to;
$pdf->result_company = $qrySQLCompany;
$pdf->soa_no = $soano;
$header = array('Transaction Date', 'No. of UST# Used', 'Store Purchases', 'Total UST Coupon Purchases');
$pdf->Main($header);
$pdf->Output();
?>