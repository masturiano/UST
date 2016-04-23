<?php 
	session_start(); 
	require_once('../syslibs/sysdbconfig.php');
	require('../syslibs/sysfunction.php');

	$myconn = new ustconfig;
	$myconn->ust_dbconn();
	
	$branch = getlocation();
	
	if(isset($_GET['action']) && $_GET['action'] == 'LoadRedeemedCoupons')
	{
		$search = $_GET['search'];
		$store = $_GET['str'];
		$date_from = $_GET['dfrom'];
		$date_to = $_GET['dto'];
		$stub_from = $_GET['sfrom'];
		$stub_to = $_GET['sto'];

		$strSQL = "SELECT CSTUBNO, CEMPLOYEENAME, CDEPARTMENT, MAMOUNT, CSTATUS, DRECEIVED, ILOCATIONID, REDEEMDATE, RECVDATE, RECEIVEDBY 
					FROM VIEWCOUPONSTATUS_CC";
		
		if($search == 'y')
		{
			$strSQL .= " WHERE cStubNo BETWEEN '" . $stub_from . "' AND '" . $stub_to . "' 
							AND redeemDate BETWEEN '" . $date_from . "' AND '" . $date_to . "'
							AND iLocationID = ". $store;
		}
		$query = mssql_query($strSQL);
		
		echo "
		<table cellpadding='0' cellspacing='0'  border='0' class='display' id='tblCoupons' width='100%'>
			<thead> 
			  <tr>
		        <th width='7%'>STUB NO</th>
		        <th width='5%'><a href='#' onClick='checkAll()'>TAG ALL</a></th>
		        <th width='30%'>EMPLOYEE NAME</th>
				<th width='22%'>DEPARTMENT</th>
				<th width='7%'>STUB AMOUNT</th>
				<th width='8%'>STUB STATUS</th>
				<th width='8%'>REDEEM DATE</th>
				<th width='13%'>STORE/ RECEIVE DATE</th>
				<th width='13%'>RECEIVED BY</th>
			  </tr>
			</thead>
			";	  
		
			while($row = mssql_fetch_array($query))
			{
				echo "<tr style='font: Verdana; font-size:11px; height:25px;' class='gradeU'  align='center'>
					<td>".$row['CSTUBNO']."</td>
					<td>";
					
					if ($row['CSTATUS'] == "RECEIVED")
					{
						echo '<input type="image" src="../Images/mydelete.gif" alt="Post Stub Number" />';
					}
					else
					{
						echo '<input type="checkbox" name="chk[]" id = "chk" value='. $row['CSTUBNO'].'>';     
					}
					
				echo "</td>	
					<td align=\"left\" >".$row['CEMPLOYEENAME']."</td>
					<td align=\"left\" >".$row['CDEPARTMENT']."</td>
					<td align=\"right\" >".$row['MAMOUNT']."</td>
					<td>".$row['CSTATUS']."</td>
					<td>".$row['REDEEMDATE']."</td>
					<td>".$row['DRECEIVED']."</td>
					<td>".$row['RECEIVEDBY']."</td>
					";
				echo "</tr>";
			}
			echo "</tbody>
				  </table>";
		exit();
	}
	else if(isset($_GET['action']) && $_GET['action'] == 'receive_stubs')
	{
		$stub = str_replace('\\','',$_POST['stub']);
		$date_from = $_POST['date_from'];
		$date_to = $_POST['date_to'];
		$strSQL = "SELECT cStubNo FROM TBLCOUPONS WHERE CSTUBNO IN ('" . $stub . "') AND BRECEIVED = 1";
		
		$qrySQL = mssql_query($strSQL);
		$NumRows = mssql_num_rows($qrySQL);
		if ($NumRows > 0)
		{
			echo json_encode(array('errmsg'=>'Warning: PG-UST Coupons already received'));
		}
		else
		{
			$strGetSOA = "SELECT soano FROM tblsoa_number";
			$qryGetSOA = mssql_query($strGetSOA);
			$res_soano = mssql_fetch_array($qryGetSOA);
			
			$strUpdate = "UPDATE TBLCOUPONS SET receivedBY = '" . $_SESSION['session_id'] . "', BRECEIVED = 1, DRECEIVED = '" . date("m-d-Y g:i:s A",strtotime("+8 Hours")) . "'";
			$strUpdate .= ", SOANO=" . $res_soano['soano'] . " WHERE CSTUBNO IN  ('" . $stub . "')";
			$qryUpdate = mssql_query($strUpdate);
			if($qryUpdate)
			{
				$next_soano = $res_soano['soano'] + 1;
				$strUpdateSOA = "UPDATE tblsoa_number SET soano=". $next_soano;
				$qryUpdateSOA = mssql_query($strUpdateSOA);
				if($qryUpdateSOA)
				{
					echo json_encode(array('success'=>true, 'msg'=>'PG-UST Coupons successfully tagged as received.','soano'=>$res_soano['soano'],'dreceived_from'=>$date_from,'dreceived_to'=>$date_to));
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
		
		exit();
	}
	else if(isset($_GET['action']) && $_GET['action'] == 'untag_stubs')
	{
		$stub = str_replace('\\','',$_POST['stub']);
		$strSQL = "SELECT cStubNo FROM TBLCOUPONS WHERE CSTUBNO IN ('" . $stub . "') AND BRECEIVED = 1";
		
		$qrySQL = mssql_query($strSQL);
		$NumRows = mssql_num_rows($qrySQL);
		if ($NumRows > 0)
		{
			echo json_encode(array('errmsg'=>'Warning: PG-UST Coupons already received'));
		}
		else
		{		
			$strUpdate = "UPDATE TBLCOUPONS SET BREDEEMED = NULL, DREDEEMED = NULL, REDEEMEDBY = NULL";
			$strUpdate .= " WHERE CSTUBNO IN  ('" . $stub . "')";
			$qryUpdate = mssql_query($strUpdate);
			if($qryUpdate)
			{
				echo json_encode(array('success'=>true, 'msg'=>'PG-UST Coupons successfully untagged as redeemed.'));
			}
			else
			{	
				echo json_encode(array('errmsg'=>'Database error.'));
			}
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
			
			$("#btnReceive, #btnUntag").button({
				icons: {
					primary: 'ui-icon-tag'
				}
			});
			
			$("#btnView").button({
				icons: {
					primary: 'ui-icon-search'
				}
			});

			LoadData('employeecoupons_cc.php','DataCoupons','tblCoupons','action=LoadRedeemedCoupons&search=n&str=&dfrom=&dto=&sfrom=&sto=','');
	});
		
	
	
	function LoadData(page,divData,gridID,params){
		$.ajax({
			url: page,
			type: "GET",
			data: params,
				success: function(Data){
				$("#"+divData).html(Data);
				$('#'+gridID).dataTable({
					"bJQueryUI" : "true",
					"sPaginationType": "full_numbers",
					"iDisplayLength": 5,
					"aLengthMenu": [5, 10, 15]
				});
			}				
		});			
	}
	
	function getData()
	{
		store = $('#store').val();
		date_from = $('#txtDateFrom').val();
		date_to = $('#txtDateTo').val();
		stub_from = $('#txtSeriesFrom').val();
		stub_to = $('#txtSeriesTo').val();
		
		if(store=='' || date_from=='' || date_to=='' || stub_from=='' || stub_to=='')
		{
			alert('All fields are required.');
		}
		else
		{
			LoadData('employeecoupons_cc.php','DataCoupons','tblCoupons','action=LoadRedeemedCoupons&search=y&str='+store+'&dfrom='+date_from+'&dto='+date_to+'&sfrom='+stub_from+'&sto='+stub_to);
		}
	}
	
	function tagReceived()
	{
		 var selectedItems = new Array();
		$("input:checked").each(function() {
			selectedItems.push($(this).val());
		});

		from = $("#txtDateFrom").val();
		to = $("#txtDateTo").val();
		
		if(from == "" || to == "")
		{
			alert("From Date and To Date are required fields.");
		}
		else if (selectedItems.length == 0) 
		{
			 alert("Please choose the stub/s to receive.");
		} 
		else
		{
			$.ajax({
				url:'employeecoupons_cc.php?action=receive_stubs',
				data:{stub:selectedItems.join("','"), date_from: from, date_to: to},
				method:'post',
				type:'POST',
				dataType:'json',
				success: function(result){
					if(result.success){
						alert(result.msg);
						LoadData('employeecoupons_cc.php','DataCoupons','tblCoupons','action=LoadRedeemedCoupons&search=n&str=&dfrom=&dto=&sfrom=&sto=');
						window.open('soa_pdf.php?soano='+result.soano+'&from='+result.dreceived_from+'&to='+result.dreceived_to);
						window.open('rpt_summary_xls.php?soano='+result.soano+'&from='+result.dreceived_from+'&to='+result.dreceived_to);
					}else{
						alert(result.errmsg);
					}
				}	
			});
		}
	}
	
	function untagRedeemed()
	{
		var selectedItems = new Array();
		$("input:checked").each(function() {
			selectedItems.push($(this).val());
		});

		if (selectedItems.length == 0) 
		{
			 alert("Please choose the stub/s to untag.");
		} 
		else
		{
			$.ajax({
				url:'employeecoupons_cc.php?action=untag_stubs',
				data:{stub:selectedItems.join("','")},
				method:'post',
				type:'POST',
				dataType:'json',
				success: function(result){
					if(result.success){
						alert(result.msg);
						LoadData('employeecoupons_cc.php','DataCoupons','tblCoupons','action=LoadRedeemedCoupons&search=n&str=&dfrom=&dto=&sfrom=&sto=');
					}else{
						alert(result.errmsg);
					}
				}	
			});
		}
	}
	
	function checkAll()
	{
		$(':checkbox').attr('checked','checked');
	}

</script>
</head>
<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" style="font-family:Arial, Helvetica, sans-serif">
	<div class="ui-state-active"> LIST OF UNIVERSITY OF STO. TOMAS FACULTY MEMBERS [Redemption Period : 11/30/2013 - 03/31/2014] </div>
		<div class="ui-widget-content">
			<table width="100%">
				<tr>
					<td width="8%">Store:</td>
					<td width="15%"><?php populatelist($branch,$mybrnsearch,'store',' class="style6" ');  ?> </td>
					<td align="center" width="2%"> &nbsp </td>
					<td width="10%"> &nbsp </td>
					<td width="65%"> &nbsp </td>
				</tr>
				
				<tr>
					<td>From:</td>
					<td> <input type="text" name="txtDateFrom" id="txtDateFrom" /> </td>
					<td>To:</td>
					<td> <input type="text" name="txtDateTo" id="txtDateTo" /> </td>
					<td> &nbsp </td>
				</tr>
				
				<tr>
					<td>Series:</td>
					<td> <input type="text" name="txtSeriesFrom" id="txtSeriesFrom" /> </td>
					<td> - </td>
					<td> <input type="text" name="txtSeriesTo" id="txtSeriesTo" /> </td>
					<td> <button id="btnView" onclick="getData();return false;">VIEW</button></td>
				</tr>
				
				<tr>
					<td><button id="btnReceive" onclick="tagReceived();">RECEIVE</button></td>
					<td><button id="btnUntag" onclick="untagRedeemed();">UNTAG AS REDEEMED</button></td>
					<td> &nbsp </td>
					<td> &nbsp </td>
					<td> &nbsp </td>
				</tr>
			</table>
			<br>
			<div id="DataCoupons"></div>
		</div>
</body>
</html>
