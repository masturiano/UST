<?php if(!isset($_SESSION)) { session_start(); } 

require_once('../syslibs/sysdbconfig.php');
require('../syslibs/sysfunction.php');

$myconn = new ustconfig;
$myconn->ust_dbconn();

$ArrLocation = getlocation();
$ArrDept = getdepartment();
$ArrLevel = array();
$ArrLevel[]	= "ADMINxOxADMINISTRATOR";
$ArrLevel[] = "MANAGERxOxMANAGER";
$ArrLevel[] = "STAFFxOxSTAFF";

if(isset($_GET['action']) && $_GET['action'] == 'LoadUsers')
{

	$strSQL = "SELECT CUSERID, CUSERNAME, CFULLNAME, CSTORENAME, CLEVEL, DEPTNAME
				FROM VIEWUSERS";
	$query = mssql_query($strSQL);
	
	echo "
	<table cellpadding='0' cellspacing='0'  border='0' class='display' id='tblUsers' width='100%'>
		<thead> 
		  <tr>
			<th width='7%'>USER ID</th>
			<th width='5%'>USERNAME</th>
			<th width='30%'>NAME</th>
			<th width='22%'>STORE</th>
			<th width='7%'>LEVEL</th>
			<th width='8%'>DEPARTMENT</th>
			<th width='4%'>ACTION</th>
		  </tr>
		</thead>
		";	  
	
		while($row = mssql_fetch_array($query))
		{
			echo "<tr style='font: Verdana; font-size:11px; height:25px;' class='gradeU'  align='center'>
				<td>".$row['CUSERID']."</td>
				<td align=\"left\" >".$row['CUSERNAME']."</td>
				<td align=\"left\" >".$row['CFULLNAME']."</td>
				<td align=\"right\" >".$row['CSTORENAME']."</td>
				<td>".$row['CLEVEL']."</td>
				<td>".$row['DEPTNAME']."</td>
				<td><span class='ui-icon ui-icon-trash' style='cursor:pointer' title='Delete' onclick='deleteUser(\"".$row['CUSERID']."\")'></span></td>
				";
			echo "</tr>";
		}
		echo "</tbody>
			  </table>";
	exit();
}		
else if(isset($_GET['action']) && $_GET['action'] == 'getStore')
{
	echo populatelist($ArrLocation,$cmbLocation,"dropDownStr", " id='cmbLocation' ");
	exit();
}
else if(isset($_GET['action']) && $_GET['action'] == 'getDept')
{
	echo populatelist($ArrDept,$cmbDept,"dropDownDept", " id='cmbDept' ");
	exit();
}
else if(isset($_GET['action']) && $_GET['action'] == 'getUserLevel')
{
	echo populatelist($ArrLevel,$cmbLevel,"dropDownUserLevel", " id='cmbLevel' ");
	exit();
}
else if(isset($_GET['action']) && $_GET['action'] == 'save_user')
{
	$store = $_POST['dropDownStr'];
	$uname = $_POST['txtUserName'];
	$pw = $_POST['txtUserPassword'];
	$userlvl = $_POST['dropDownUserLevel'];
	$dept = $_POST['dropDownDept'];
	$fullname = $_POST['txtUserFullName'];
	
	$strInsert = "INSERT INTO tblusers(ILOCATIONID, CUSERNAME, CPASSWORD, CLEVEL, CDEPTID, CFULLNAME)
					VALUES('".$store."', '".$uname."', '".$pw."', '".$userlvl."', '".$dept."', '".$fullname."')";
	$qryInsert = mssql_query($strInsert);
	
	if($qryInsert)
	{
		$datetime = date('Y-m-d h:i:s', time() + 28800);
		$strLogAction = "INSERT INTO tblaudittrail(cUserID, process_date, process) VALUES('".$_SESSION['session_id']."','".$datetime."','ADDED USER ".$fullname."') ";
		mssql_query($strLogAction);
		echo json_encode(array('success'=>true, 'msg' => 'User has been added.'));
	}
	else
	{
		echo json_encode(array('msg'=>'Some error occurred.'));
	}
	
	exit();
}
else if(isset($_GET['action']) && $_GET['action'] == 'delete_user')
{
	$user = $_POST['user'];
	
	$strSQL = "DELETE FROM tblusers WHERE CUSERID=".$user;
	$qryDelete = mssql_query($strSQL);
	
	if($qryDelete)
	{
		$datetime = date('Y-m-d h:i:s', time() + 28800);
		$strLogAction = "INSERT INTO tblaudittrail(cUserID, process_date, process) VALUES('".$_SESSION['session_id']."','".$datetime."','DELETED USER ".$user."') ";
		mssql_query($strLogAction);
		echo json_encode(array('success'=>true, 'msg' => 'User has been deleted.'));
	}
	else
	{
		echo json_encode(array('msg'=>'Error deleting user.'));
	}
	
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
		padding:5% 10% 10% 10%;
	}
	.ui-state-error { padding: .3em; }

	.ui-dialog-titlebar-close{
		display: none;
	}
</style> 
<script language="javascript">
	$(function() {
		
		$('#btnAdd').button({
			icons: {
				primary: 'ui-icon-plusthick'
			}
		});	
		
		$('#btnUpdate').button({
			icons: {
				primary: 'ui-icon-pencil'
			}
		});
		
		$('#btnRemove').button({
			icons: {
				primary: 'ui-icon-trash'
			}
		});
		
		$('#btnReset').button({
			icons: {
				primary: 'ui-icon-disk'
			}
		});
		
		
		$.ajax({
			url:'users.php?action=getStore',
			method:'post',
			type:'POST',
			success: function(data){
				$('#loading').dialog('close');
				$('#dvUserStr').html(data);
			},
		   error: function(XMLHttpRequest, textStatus, errorThrown) {     
			 $('#loading').dialog('close');
			 alertMsg("Refresh page.");
		  }
		});
		
		$.ajax({
			url:'users.php?action=getDept',
			method:'post',
			type:'POST',
			success: function(data){
				$('#loading').dialog('close');
				$('#dvDept').html(data);
			},
		   error: function(XMLHttpRequest, textStatus, errorThrown) {     
			 $('#loading').dialog('close');
			 alertMsg("Refresh page.");
		  }
		});
		
		$.ajax({
			url:'users.php?action=getUserLevel',
			method:'post',
			type:'POST',
			success: function(data){
				$('#loading').dialog('close');
				$('#dvUserLvl').html(data);
			},
		   error: function(XMLHttpRequest, textStatus, errorThrown) {     
			 $('#loading').dialog('close');
			 alertMsg("Refresh page.");
		  }
		});
		
		
		LoadData('users.php','DataUsers','tblUsers','action=LoadUsers');
		
		
		$('#loading').dialog({
			autoOpen : false,
			closeOnEscape : false,
			modal: true,
			draggable:false,
			resizable: false,
			dialogClass : "noclose"
		});
		
		$('#dlgAdd').dialog({
			autoOpen: false,
			height : 250,
			show: 'slideDown',
			width : 400,
			resizable: false,
			modal: true,
			closeOnEscape:false,
			draggable:false,
			dialogClass : "noClose",
			title : 'USERS',
			buttons: {
				'Save': function() {
					frm = $('#frmUser').serialize();

					$.ajax({
						url:'users.php?action=save_user',
						data: frm,
						method:'post',
						type:'POST',
						dataType:'json',
						beforeSend:function(){
							dlg('loading', 'open');
							loadMsg('Saving');
						},
						success: function(result){
							dlg('loading', 'close');
							if(result.success)
							{
								clearVal('#txtUserName, #txtUserPassword, #txtUserFullName, #dropDownStr, #dropDownDept, #dropDownUserLevel');
								LoadData('users.php','DataUsers','tblUsers','action=LoadUsers');
							}
							else
							{
								alertMsg(result.msg);
							}
						},
					   error: function(XMLHttpRequest, textStatus, errorThrown) {     
						 dlg('loading', 'close');
						 alertMsg("Refresh page.");
					  }
					});
				},
				'CANCEL': function() {
					clearVal('#txtUserName, #txtUserPassword, #txtUserFullName, #dropDownStr, #dropDownDept, #dropDownUserLevel');
					dlg('dlgAdd', 'close');
				}
			},
			close: function() {
				clearVal('#txtUserName, #txtUserPassword, #txtUserFullName, #dropDownStr, #dropDownDept, #dropDownUserLevel');
				dlg('dlgAdd', 'close');			
			}
		});
		
	});
	
	function user(url, id)
	{
		if(confirm("DELETE USER?"))
		{
			$.ajax({
				url: url,
				data:  {user: id},
				method:'post',
				type:'POST',
				dataType:'json',
				beforeSend:function(){
					dlg('loading', 'open');
					loadMsg('Processing');
				},
				success: function(result){
					dlg('loading', 'close');
					if(result.success)
					{
						LoadData('users.php','DataUsers','tblUsers','action=LoadUsers');
						infoMsg(result.msg);
					}
					else
					{
						alertMsg(result.msg);
					}
				},
			   error: function(XMLHttpRequest, textStatus, errorThrown) {     
				 dlg('loading', 'close');
				 alertMsg("Refresh page.");
			  }
			});
		}
	}
	
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
	
	function addUser()
	{
		dlg('dlgAdd', 'open');
	}
	
	function resetPW()
	{
		url = 'tzrs_conditions.php?action=reset_pw';
		user(url);
	}
	
	function deleteUser(id)
	{
		url = 'users.php?action=delete_user';
		user(url, id);
	}
	
	function dlg(frm, e)
	{
		$("#"+frm).dialog(e);
	}
	
	function loadMsg(msg)
	{
		$('#dvLoadingMsg').html("<p>" + msg + ". . .  Please Wait.</p>");
	}
	
	function clearVal(obj)
	{
		$(obj).val('');
	}
	
	function alertMsg(msg){
		$("#alert-msg-icon").addClass('ui-icon ui-icon-alert')
							.attr('style','float:left; margin-right:7px');
		$("#dialogMsg").html(msg);		
		$("#dialogAlert").dialog({
			resizable: false,
			modal: true,
			closeOnEscape:false,
			draggable:false,
			buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}
		});			
	}
	
	function infoMsg(infomsg){	
		$("#info-msg-icon").addClass('ui-icon ui-icon-info')
							.attr('style','float:left; margin-right:7px');
		$("#dialogInfoMsg").html(infomsg);		
		$("#dialogInfo").dialog({
			resizable: false,
			width : 700,
			modal: true,
			closeOnEscape:false,
			draggable:false,
			buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}
		});
	}
	
</script>
</head>

<body>
<div class="ui-state-active" style="padding:5px; margin:5% 10% 0% 10%"> USERS </div>
    <div class="ui-widget-content" style="margin:0% 10% 0% 10%">
        <div class="container">
        	<button id="btnAdd" onclick="addUser()">ADD</button>
            <br /><br />
			<div id="DataUsers"></div>
       </div>
   </div>

    <div class="ui-state-active" style="margin:0% 10% 10% 10%">
		<div class="dvFooter">&nbsp</div>
	</div>
    
    <div id='dialogAlert' title='PG-UST Coupon Redemption'>
        <p>
			<span id="alert-msg-icon"></span>
			<label id='dialogMsg'></label>
        </p>
    </div>
    
    <div id='dialogInfo' title='PG-UST Coupon Redemption'>
        <p>
			<span id="info-msg-icon"></span>
			<label id='dialogInfoMsg'></label>
        </p>
    </div>
    
    <div id="loading" title='PG-UST Coupon Redemption'>
        <div id="dvLoadingMsg" align="center"></div>
        <div id="dvLoadingTimer" align="center"></div>
        <div id="dvLoadingImg" align="center"> <img src="../images/loader.gif"></div>
    </div>
    
    <div id="dlgAdd">
    	<form id="frmUser">
        	 <table>
                <tr>
                    <td>USERNAME</td>
                    <td><input type="text" name="txtUserName" id="txtUserName" /></td>
                </tr>
                <tr>
                    <td>PASSWORD</td>
                    <td><input type="password" name="txtUserPassword" id="txtUserPassword" /></td>
                 </tr>
                <tr>
                    <td>NAME</td>
                    <td><input type="text" name="txtUserFullName" id="txtUserFullName" /></td>
                 </tr>
                 <tr>
                    <td>LEVEL</td>
                    <td><div id="dvUserLvl"></div></td>
                </tr>
                 <tr>
                    <td>STORE</td>
                    <td><div id="dvUserStr"></div></td>
                </tr>
				 <tr>
                    <td>DEPARTMENT</td>
                    <td><div id="dvDept"></div></td>
                </tr>
            </table>
        </form>
    </div>
    
</body>
</html>