<?php 
	session_start(); 
	require_once('../syslibs/sysdbconfig.php');
	require('../syslibs/sysfunction.php');

	$myconn = new ustconfig;
	$myconn->ust_dbconn();
	
	$company = getcompanies();
	$branch = getlocation();
	$rpt_type = array('OPENxOxOPEN','VERIFIEDxOxVERIFIED','UNREDEEMEDxOxUNREDEEMED','REDEEMEDxOxREDEEMED','UNRECEIVEDxOxUNRECEIVED','RECEIVEDxOxRECEIVED');
	
	array_push($branch, '0' . 'xOx' . "ALL");
	
	if(isset($_GET['action']) && $_GET['action'] == 'count_records')
	{
		$status = $_POST['status'];
		$store = $_POST['store'];
		$date_from = $_POST['from'];
		$date_to = $_POST['to'];
		
		$strSQL = "SELECT cStubNo, cEmployeeName, cDepartment, mAmount, cStatus, date FROM view_couponstatus WHERE cStatus = '". $status ."' ";
		
		if($status != 'OPEN')
		{
			$strSQL .= " AND RIGHT(date,10) BETWEEN '".$date_from."' AND '".$date_to."'";
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
		$NumRows = mssql_num_rows($qrySQL);
		
		if ($NumRows > 0)
		{
			$datetime = date('Y-m-d h:i:s', time() + 28800);
			$strLogAction = "INSERT INTO tblaudittrail(cUserID, process_date, process) VALUES('".$_SESSION['session_id']."','".$datetime."','PRINTED STATUS REPORT') ";
			mssql_query($strLogAction);
			echo json_encode(array('success'=>true,'res'=>$strSQL));
		}
		else
		{
			
			echo json_encode(array('errmsg'=>'No record found.'));
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
	.hideTxt{
		display:none;
	}

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
			
			$("#btnPrint,#btnPrintxls").button({
				icons: {
					primary: 'ui-icon-print'
				}
			});
			
	});
	
	function print_rpt(doctype)
	{
		store = $("#store").val();
		report_type = $("#rpttype").val();
		from = $("#txtDateFrom").val();
		to = $("#txtDateTo").val();
		
		
		if(store == "" || report_type == "")
		{
			alert("All fields are required.");
		}
		else
		{
			$.ajax({
				url:'rpt_status_filter.php?action=count_records',
				data:{store:store, status:report_type, from:from, to:to},
				method:'post',
				type:'POST',
				dataType:'json',
				success: function(result){
					if(result.success){
						window.open('rpt_status_'+doctype+'.php?status='+report_type+'&store='+store+'&from='+from+'&to='+to);
					}else{
						alert(result.errmsg);
					}
				}	
			});
		}
	}
	
	function rpt_ext()
	{
		if($("#rpttype").val() == 'OPEN')
		{
			$('#fld_date').attr('class','hideTxt');
		}
		else
		{
			$('#fld_date').removeAttr('class');
		}
	}

</script>
</head>
<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" style="font-family:Arial, Helvetica, sans-serif">
	<div class="ui-state-active"> STATUS REPORT </div>
		<div class="ui-widget-content">
			<table width="100%">
				<tr>
					<td width="8%">Coupon Status:</td>
					<td width="15%"><?php populatelist($rpt_type,$myrpt_typesearch,'rpttype',' class="style6" onchange="rpt_ext()"');  ?> </td>
					<td align="center" width="2%"> &nbsp </td>
					<td width="10%"> &nbsp </td>
					<td width="65%"> &nbsp </td>
				</tr>
		<?php		
		if ($_SESSION['session_dept_id'] != 102)
		{
		?>
			<tr>
					<td width="8%">Branch:</td>
					<td width="15%"> <?php populatelist($branch,$mybrnsearch,'store',' class="style6" ');?>  </td>
					<td align="center" width="2%"> &nbsp </td>
					<td width="10%"> &nbsp </td>
					<td width="65%"> &nbsp </td>
				</tr>
		<?php
		}
		?>
				
				
				<tr id="fld_date" class="hideTxt">
					<td>FROM</td>
					<td><input type="text" name="txtDateFrom" id="txtDateFrom" /></td>
					<td>TO</td>
					<td><input type="text" name="txtDateTo" id="txtDateTo" /></td>
				</tr>
				
				<tr>
					<td width="8%"> &nbsp </td>
					<td width="15%"> <button id="btnPrint" onclick="print_rpt('pdf')">PRINT PDF</button><button id="btnPrintxls" onclick="print_rpt('xls')">PRINT EXCEL</button> </td>
					<td align="center" width="2%"> &nbsp </td>
					<td width="10%"> &nbsp </td>
					<td width="65%"> &nbsp </td>
				</tr>
				
				
			</table>
			<br>
			
		</div>
</body>
</html>
