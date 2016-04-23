<?php if(!isset($_SESSION)) { session_start(); } 

require_once('../syslibs/sysdbconfig.php');
require('../syslibs/sysfunction.php');

$myconn = new ustconfig;
$myconn->ust_dbconn();

if(isset($_GET['action']) && $_GET['action'] == 'LoadLogs')
{

	$strSQL = "SELECT PROCESS_ID, CFULLNAME, PROCESS_DATE, PROCESS
				FROM VIEW_LOGS
				ORDER BY PROCESS_ID DESC";
	$query = mssql_query($strSQL);
	
	echo "
	<table cellpadding='0' cellspacing='0'  border='0' class='display' id='tblAuditTrail' width='100%'>
		<thead> 
		  <tr>
			<th width='10%'>PROCESS ID</th>
			<th width='30%'>NAME</th>
			<th width='10%'>DATE</th>
			<th width='50%'>PROCESS</th>
		  </tr>
		</thead>
		";	  
	//date('M n Y s:i A', strtotime($row['PROCESS_DATE']))
		while($row = mssql_fetch_array($query))
		{
			echo "<tr style='font: Verdana; font-size:11px; height:25px;' class='gradeU'  align='center'>
				<td>".$row['PROCESS_ID']."</td>
				<td align=\"left\" >".$row['CFULLNAME']."</td>
				<td>". $row['PROCESS_DATE']."</td> 
				<td>".$row['PROCESS']."</td>
				";
			echo "</tr>";
		}
		echo "</tbody>
			  </table>";
	exit();
}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PG-UST Coupon Redemption</title>
<link type="text/css" href="../jquery/css/cupertino/jquery-ui-1.9.0.custom.min.css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/pg-icon.png">
<link href="../jquery/development-bundle/demos/demos.css" rel="stylesheet" type="text/css">
<link type="text/css" href="../jqGrid/css/ui.jqgrid.css" rel="stylesheet" />
<script type="text/javascript" src="../jquery/js/jquery-1.6.2.js"></script>
<script type="text/javascript" src="../jquery/js/jquery-ui-1.9.0.custom.min.js"></script>
<script type="text/javascript" src="../jqGrid/js/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="../jqGrid/js/jquery.jqGrid.min.js"></script>
<script type="text/javascript" src="../jquery/js/jquery.dataTables.min.js"></script>

<style type="text/css" title="currentStyle">
	@import "../jquery/css/demo_page.css" "";
	@import "../jquery/css/demo_table_jui.css";
</style>
<style type="text/css">
	input
	{
		width:250px;
	}
	.container{
		padding:5% 5% 10% 5%;
	}
	.ui-state-error { padding: .3em; }

	.ui-dialog-titlebar-close{
		display: none;
	}
</style> 
<script language="javascript">
	$(function() {
		
		LoadData('audit_trail.php','DataTrail','tblAuditTrail','action=LoadLogs');
		
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
					"iDisplayLength": 10,
					"aLengthMenu": [10, 20, 30]
				});
			}				
		});			
	}
	
</script>
</head>

<body>
<div class="ui-state-active" style="padding:5px; margin:5% 10% 0% 10%"> AUDIT TRAIL </div>
    <div class="ui-widget-content" style="margin:0% 10% 0% 10%">
        <div class="container"><br />
			<div id="DataTrail"></div>
       </div>
   </div>

    <div class="ui-state-active" style="margin:0% 10% 10% 10%">
		<div class="dvFooter">&nbsp</div>
	</div>
    
</body>
</html>