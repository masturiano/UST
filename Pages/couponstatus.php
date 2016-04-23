<?php 
	session_start(); 
	require_once('../syslibs/sysdbconfig.php');
	require('../syslibs/sysfunction.php');

	$myconn = new ustconfig;
	$myconn->ust_dbconn();
	
	if(isset($_GET['action']) && $_GET['action'] == 'get_data')
	{
		$stub = urldecode($_GET['stub']);
		
		$strSQL = "SELECT cStubNo, cEmployeeName, cDepartment, mAmount, cStatus, date, tblusers.cfullname, soano FROM view_stat LEFT JOIN tblusers ON view_stat.cuser=tblusers.cuserid WHERE cStubNo = '". $stub ."' ";
		$qrySQL = mssql_query($strSQL);
		
		echo "<table width='100%' border=1>
				<th>STUB NO</th>
				<th>EMPLOYEE</th>
				<th>DEPARTMENT</th>
				<th>AMOUNT</th>
				<th>STATUS</th>
				<th>STORE/DATE</th>
				<th>USER</th>
				<th>SOA</th>
			";
			
			while($row = mssql_fetch_array($qrySQL))
			{
				$soano = ($row['soano'] == '') ? 'NO SOA' : str_pad($row['soano'], 9, "0", STR_PAD_LEFT);
				echo "	<tr>
							<td>".$row['cStubNo']."</td>
							<td>".$row['cEmployeeName']."</td>
							<td>".$row['cDepartment']."</td>
							<td>".$row['mAmount']."</td>
							<td>".$row['cStatus']."</td>
							<td>".$row['date']."</td>
							<td>".$row['cfullname']."</td>
							<td>". $soano ."</td>
						</tr>";
			}
			
		echo "</table>";
		
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
			
			$("#btnView").button({
				icons: {
					primary: 'ui-icon-search'
				}
			});
			
	});

	function getStubStat()
	{
		stub = $("#txtStub").val();
		if(stub == "")
		{
			alert("Stub no. is required.");
		}
		else
		{
			$.ajax({
				url:'couponstatus.php?action=get_data',
				data:{stub:stub},
				type: "GET",
				success: function(data){
					$("#dvResult").html(data);
					//alert(result);
				}	
			});
		}
	}
</script>
</head>
<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" style="font-family:Arial, Helvetica, sans-serif">
	<div class="ui-state-active"> STATUS INQUIRY </div>
		<div class="ui-widget-content">
		<br>
			<fieldset>
				<table>
					<tr>
					<td width="20%" align="right">STUB No: </td>
					<td width="50%" align="left"><input type="text" id="txtStub" name="txtStub"></td>
					<td width="30%" align="center"><button id="btnView" onclick="getStubStat()">VIEW STATUS</button></td>
				</tr>
				</table>
				
				<div id="dvResult"></div>
			</fieldset>
			
			<br>
			
		</div>
</body>
</html>
