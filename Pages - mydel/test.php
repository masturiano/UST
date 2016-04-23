<?php 
	session_start(); 
	require_once('../syslibs/sysdbconfig.php');
	require('../syslibs/sysfunction.php');

	$myconn = new ustconfig;
	$myconn->ust_dbconn();

	if(isset($_GET['action']) && $_GET['action'] == 'LoadCoupons')
	{
		$strSQL = "SELECT CSTUBNO, CEMPLOYEENAME, CDEPARTMENT, MAMOUNT, CSTATUS, DVERIFIED, VERIFIEDBY
					FROM VIEWCOUPONSTATUS_WS";
		$query = mssql_query($strSQL);
		
		echo "
		<table cellpadding='0' cellspacing='0'  border='0' class='display' id='tblCoupons' width='100%'>
			<thead> 
			  <tr>
		        <th width='7%'>STUB NO</th>
		        <th width='5%'>ACTION</th>
		        <th width='27%'>EMPLOYEE NAME</th>
				<th width='20%'>DEPARTMENT</th>
				<th width='7%'>STUB AMOUNT</th>
				<th width='8%'>STUB STATUS</th>
				<th width='13%'>STORE/ DATE VERIFIED</th>
				<th width='13%'>VERIFIED BY</th>
			  </tr>
			</thead>
			";	  
		
			while($row = mssql_fetch_array($query))
			{
				echo "<tr style='font: Verdana; font-size:11px; height:25px;' class='gradeU'  align='center'>
					<td>".$row['CSTUBNO']."</td>
					<td>";
					
					if ($row['CSTATUS'] == "VERIFIED")
					{
						echo '<span class="ui-icon ui-icon-circle-check" title="Verified"></span>';  
						echo '<span class="ui-icon ui-icon-cancel" style="cursor:pointer" title="Untag Verified" onclick="openDlg(\''.$row['CSTUBNO'].'\');"></span>';
					}
					else
					{//
						echo '<span class="ui-icon ui-icon-tag" style="cursor:pointer" title="Tag Verified" onclick="tagReceived(\''.$row['CSTUBNO'].'\');"></span>';     
					}
					
				echo "</td>	
					<td align=\"left\" >".$row['CEMPLOYEENAME']."</td>
					<td align=\"left\" >".$row['CDEPARTMENT']."</td>
					<td align=\"right\" >".$row['MAMOUNT']."</td>
					<td>".$row['CSTATUS']."</td>
					<td>".$row['DVERIFIED']."</td>
					<td>".$row['VERIFIEDBY']."</td>
					";
				echo "</tr>";
			}
			echo "</tbody>
				  </table>";
		exit();
	}
	else if(isset($_GET['action']) && $_GET['action'] == 'verify_stub')
	{
		$stub = $_POST['stub'];
			
		$strUpdate = "UPDATE TBLCOUPONS SET VERIFIEDBY = '" . $_SESSION['session_id'] . "', BVERIFIED = 1, DVERIFIED = '" . date("m-d-Y g:i:s A",strtotime("+8 Hours")) . "'";
		$strUpdate .= ", ILOCATIONID = " . $_SESSION['session_code'] . " WHERE CSTUBNO = '" . $stub . "'";
		$qryUpdate = mssql_query($strUpdate);
		if($qryUpdate)
		{				
			echo json_encode(array('success'=>true, 'msg'=>'PG-UST Coupon['.$_POST['stub'].'] has been verified.'));
		}
		else
		{	
			echo json_encode(array('errmsg'=>'Database error.'));
		}
		
		exit();
	}
	else if(isset($_GET['action']) && $_GET['action'] == 'untag_stubs')
	{
		$stub = $_POST['stub'];
			
		
		$strQry = "SELECT BVERIFIED, BREDEEMED, BRECEIVED FROM TBLCOUPONS WHERE CSTUBNO = '" . $stub . "'";
		$qryGet = mssql_query($strUpdate);
		$res = mssql_fetch_array($strUpdate);
		if($res['BREDEEMED'] == 1 || $res['BRECEIVED'] == 1)
		{
			echo json_encode(array('errmsg'=>'WARNING: PG_UST Coupon already redeemed or received, cannot be untag as verfied.'));
		}
		else
			{
			$strUpdate = "UPDATE TBLCOUPONS SET BVERIFIED = NULL";
			$strUpdate .= " WHERE CSTUBNO = '" . $stub . "'";
			$qryUpdate = mssql_query($strUpdate);
			if($qryUpdate)
			{				
				echo json_encode(array('success'=>true, 'msg'=>'PG-UST Coupon['.$_POST['stub'].'] successfully untagged as verified.'));
			}
			else
			{	
				echo json_encode(array('errmsg'=>'Database error.'));
			}
		}
		
		exit();
	}
	else if(isset($_GET['action']) && $_GET['action'] == 'validate_user')
	{
		$user = $_POST['user'];
		$pw = $_POST['pw'];
		$store = $_SESSION['session_code'];
			
		$strQry = "SELECT cUserID FROM tblusers WHERE cUserName='". $user ."' AND cPassword='". $pw ."' AND iLocationID=".$store." AND (cLevel='ADMIN' OR cLevel='MANAGER')";
		$qrySQL = mssql_query($strQry);
		$NumRows = mssql_num_rows($qrySQL);
		
		if ($NumRows > 0)
		{
			echo json_encode(array('success'=>true));
		}
		else
		{	
			echo json_encode(array('errmsg'=>'Invalid Username and Password.'));
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
	.ui-progressbar-value { background-image: url(../jquery/development-bundle/demos/images/pbar-ani.gif); }

	@import "../jquery/css/demo_page.css" "";
	@import "../jquery/css/demo_table_jui.css";
</style>
<script language="javascript" type="text/javascript">
	
	$(function() {
			LoadData('employeecoupons_ws.php','DataCoupons','tblCoupons','action=LoadCoupons','');
			
			$('#dvDialog').dialog({
				autoOpen: false,
				height : 150,
				show: 'slideDown',
				width : 500,
				resizable: false,
				modal: true,
				closeOnEscape:false,
				draggable:false,
				title : 'PG-UST Coupon Redemption',
				buttons: {
					'ENTER': function() {
						 untagVerified();
					}
				},
				close: function() {
					$("#txtUsername, #txtPassword").val("");
					$(this).dialog('close');				
				}
			});	
			
			
	});
	
	function LoadData(page,divData,gridID,params){
		time = setTimeout("LoadData()",1000);
		$.ajax({
			url: page,
			type: "GET",
			data: params,
			 beforeSend:function(){
					$("#progressbar").progressbar({
						//value:setTimeout("LoadData()",1000)
					});
				
			},
				success: function(Data){
				//clearTimeout(setTimeout("LoadData()",1000));
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
	
	function tagReceived(stub)
	{
		if(confirm("Verify PG-UST Coupon["+stub+"]?"))
		{
			$.ajax({
				url:'employeecoupons_ws.php?action=verify_stub',
				data:{stub:stub},
				method:'post',
				type:'POST',
				dataType:'json',
				success: function(result){
					if(result.success){
						alert(result.msg);
						LoadData('employeecoupons_ws.php','DataCoupons','tblCoupons','action=LoadCoupons');
					}else{
						alert(result.errmsg);
					}
				}	
			});
		}
	}

	function openDlg(stub)
	{
		$("#dvDialog").dialog('open');
		$("#hdnStubNo").val(stub);
	}
	
	function untagVerified()
	{
		stub = $("#hdnStubNo").val();
		user = $("#txtUsername").val();
		pw = $("#txtPassword").val();
		
		$.ajax({
			url:'employeecoupons_trs.php?action=validate_user',
			data:{user:user, pw:pw},
			method:'post',
			type:'POST',
			dataType:'json',
			success: function(result){
				if(result.success){
					$.ajax({
						url:'employeecoupons_ws.php?action=untag_stubs',
						data:{stub:stub},
						method:'post',
						type:'POST',
						dataType:'json',
						success: function(result){
							if(result.success){
								alert(result.msg);
								LoadData('employeecoupons_ws.php','DataCoupons','tblCoupons','action=LoadCoupons','');
								$("#txtUsername, #txtPassword").val("");
							}else{
								alert(result.errmsg);
							}
						}	
					});
				}else{
					alert(result.errmsg);
				}
			}	
		});
	}
</script>
</head>
<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" style="font-family:Arial, Helvetica, sans-serif">

<div id="DataCoupons"></div>
<!-- LIST OF UNIVERSITY OF STO. TOMAS FACULTY MEMBERS [Redemption Period : 11/30/2013 - 03/31/2014] -->
<div id="progressbar"></div>
<input type="hidden" id="hdnStubNo">
<div id="dvDialog">
	<fieldset>
	<legend>MANAGER</legend>
		<table>
			<tr>
				<td>USERNAME</td>
				<td><input type="text" id="txtUsername"></td>
				<td> &nbsp </td>
				<td>PASSWORD</td>
				<td><input type="password" id="txtPassword"></td>
			</tr>
		</table>
	</fieldset>
</div>
</body>
</html>
