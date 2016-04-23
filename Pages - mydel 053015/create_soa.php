<?php 
	session_start(); 
	require_once('../syslibs/sysdbconfig.php');
	require('../syslibs/sysfunction.php');

	$myconn = new ustconfig;
	$myconn->ust_dbconn();
	
	if(isset($_GET['action']) && $_GET['action'] == 'createandprint_soa')
	{
		$date_from = str_replace('\\','',$_POST['date_from']);
		$date_to = str_replace('\\','',$_POST['date_to']);
		$date_range = $date_from ."-".$date_to;
		$strGetSOA = "SELECT soano FROM tblsoa_number";
		$qryGetSOA = mssql_query($strGetSOA);
		$res_soano = mssql_fetch_array($qryGetSOA);
		
		$sqlView = "SELECT cStubNo FROM TBLCOUPONS WHERE CONVERT(VARCHAR,DRECEIVED,101) BETWEEN '" . $date_from . "' AND '". $date_to ."' AND BRECEIVED = 1 AND soano IS NULL";
		$qryView = mssql_query($sqlView);	
		$noOfRows = mssql_num_rows($qryView);
		
		if($noOfRows > 0)
		{
			$strUpdate = "UPDATE TBLCOUPONS SET SOANO = ".$res_soano['soano'].", SOA_DATERANGE='".$date_range."'";
			$strUpdate .= " WHERE CONVERT(VARCHAR,DRECEIVED,101) BETWEEN '" . $date_from . "' AND '". $date_to ."' AND BRECEIVED = 1 AND soano IS NULL";
			$qryUpdate = mssql_query($strUpdate);
			if($qryUpdate)
			{
				$next_soano = $res_soano['soano'] + 1;
				$strUpdateSOA = "UPDATE tblsoa_number SET soano=". $next_soano;
				$qryUpdateSOA = mssql_query($strUpdateSOA);
				if($qryUpdateSOA)
				{
					$datetime = date('Y-m-d h:i:s', time() + 28800);
					$strLogAction = "INSERT INTO tblaudittrail(cUserID, process_date, process) VALUES('".$_SESSION['session_id']."','".$datetime."','CREATED SOA NUMBER: ".$res_soano['soano']."') ";
					mssql_query($strLogAction);
					echo json_encode(array('success'=>true, 'msg'=>'SOA has been created.', 'soano'=>$res_soano['soano'],'dreceived_from'=>$date_from,'dreceived_to'=>$date_to));
				}
				else
				{
					echo json_encode(array('errmsg'=>'Database error.'));
				}
			}
			else
			{	
				echo json_encode(array('errmsg'=>'Database error.'));
			}
		}
		else
		{
			echo json_encode(array('errmsg'=>'No "PG-UST Coupon for SOA creation" in this particular date range.'));
		}
		exit();
	}
	else if(isset($_GET['action']) && $_GET['action'] == 'reprint_soa')
	{
		$soano = (int)$_POST['soano'];
		$strSQL = "SELECT cstubno FROM tblcoupons WHERE soano = ".$soano;
		$qryGet = mssql_query($strSQL);
		$rowCount = mssql_num_rows($qryGet);
		
		if($rowCount > 0)
		{
			$datetime = date('Y-m-d h:i:s', time() + 28800);
			$strLogAction = "INSERT INTO tblaudittrail(cUserID, process_date, process) VALUES('".$_SESSION['session_id']."','".$datetime."','REPRINTED SOA NUMBER: ".$soano."') ";
			mssql_query($strLogAction);
			echo json_encode(array('success'=>true));
		}
		else
		{
			echo json_encode(array('errmsg'=>'No existing SOA found in the database.'));
		}
		
		exit();
	}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
<link type="text/css" href="../jquery/css/cupertino/jquery-ui-1.9.0.custom.min.css" rel="stylesheet" />
<link href="../jquery/development-bundle/demos/demos.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../jquery/js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="../jquery/js/jquery-ui-1.9.0.custom.min.js"></script>
<script type="text/javascript" src="../jquery/js/jquery.dataTables.min.js"></script>

<style type="text/css" title="currentStyle">
	@import "../jquery/css/demo_page.css" "";
	@import "../jquery/css/demo_table_jui.css";
</style>

<script language="javascript" type="text/javascript">
	
	$(function() {
		
		var dates = $('#txtDateFrom, #txtDateTo').datepicker({
				maxDate: 0,
				defaultDate: "+1w",
				onSelect: function(selectedDate) {
					var option = this.id == "txtDateFrom" ? "minDate" : "maxDate";
					var instance = $(this).data("datepicker");
					var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
					dates.not(this).datepicker("option", option, date);
				}
			});
			
			$("#btnCreate").button({
				icons: {
					primary: 'ui-icon-tag'
				}
			});
			
			$("#btnReprint").button({
				icons: {
					primary: 'ui-icon-print'
				}
			});
	});
	
	function createSoa()
	{
		from = $("#txtDateFrom").val();
		to = $("#txtDateTo").val();
		
		if(from == "" || to == "")
		{
			alert("From Date and To Date fields are required.");
		}
		else
		{
			$.ajax({
				url:'create_soa.php?action=createandprint_soa',
				data:{date_from: from, date_to: to},
				method:'post',
				type:'POST',
				dataType:'json',
				success: function(result){
					if(result.success){
						alert(result.msg);
						window.open('soa_pdf.php?soano='+result.soano+'&from='+result.dreceived_from+'&to='+result.dreceived_to);
						window.open('rpt_summary_xls.php?soano='+result.soano+'&from='+result.dreceived_from+'&to='+result.dreceived_to);
					}else{
						alert(result.errmsg);
					}
				}	
			});
		}
	}

	function reprintSOA()
	{
		soano = $("#txtSOANo").val();
		if(soano == "")
		{
			alert("SOA No is required.");
		}
		else
		{
			$.ajax({
				url:'create_soa.php?action=reprint_soa',
				data:{soano: soano},
				method:'post',
				type:'POST',
				dataType:'json',
				success: function(result){
					if(result.success){
						window.open('soa_reprint_pdf.php?soano='+soano);
						window.open('rpt_summary_reprint_xls.php?soano='+soano);
					}else{
						alert(result.errmsg);
					}
				}	
			});
		}
	}
	
</script>
</head>
<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" style="font-family:Arial, Helvetica, sans-serif">
	<div class="ui-state-active"> LIST OF UNIVERSITY OF STO. TOMAS FACULTY MEMBERS [Redemption Period : 11/30/2013 - 03/31/2014] </div>
		<div class="ui-widget-content">
			<div>
				<fieldset>
				<legend>CREATE SOA</legend>
					<table width="100%">
						<tr>
							<td width="8%">From:</td>
							<td width="18%"> <input type="text" name="txtDateFrom" id="txtDateFrom" /> </td>
							<td width="2%">To:</td>
							<td> <input type="text" name="txtDateTo" id="txtDateTo" /> </td>
						</tr>
						
						<tr>
							<td></td>
							<td><button id="btnCreate" onclick="createSoa();">CREATE SOA</button></td>
							<td> &nbsp </td>
							<td> &nbsp </td>
						</tr>
					</table>
				</fieldset>
			</div>
			
			<div>
				<fieldset>
				<legend>REPRINT SOA</legend>
					<table>
						<tr>
							<td>SOA NO</td>
							<td><input type="text" id="txtSOANo"></td>
							<td><button id="btnReprint" onclick="reprintSOA();">REPRINT</button></td>
						</tr>
					</table>
				</fieldset>
			</div>
			<br>
		</div>
</body>
</html>
