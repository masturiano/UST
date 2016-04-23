<?php 
	session_start(); 
	require_once('../syslibs/sysdbconfig.php');
	require('../syslibs/sysfunction.php');

	$myconn = new ustconfig;
	$myconn->ust_dbconn();

	if(isset($_GET['action']) && $_GET['action'] == 'LoadVerifiedCoupons')
	{
		$strSQL = "SELECT CSTUBNO, CEMPLOYEENAME, CDEPARTMENT, MAMOUNT, CSTATUS, ILOCATIONID, DREDEEMED, REDEEMEDBY
					FROM VIEWCOUPONSTATUS_TRS";
					
		if($_SESSION['session_level'] != 'ADMIN' ||	$_SESSION['session_dept_id'] != 999)
		{
			$strSQL .= "  WHERE ILOCATIONID=".$_SESSION['session_code'];			
		}
					
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
				<th width='13%'>STORE/ DATE REDEEMED</th>
				<th width='13%'>REDEEMED BY</th>
			  </tr>
			</thead>
			";	  
		
			while($row = mssql_fetch_array($query))
			{
				echo "<tr style='font: Verdana; font-size:11px; height:25px;' class='gradeU'  align='center'>
					<td>".$row['CSTUBNO']."</td>
					<td>";
					
					if ($row['CSTATUS'] == "REDEEMED")
					{
						echo '<span class="ui-icon ui-icon-circle-check" title="Redeemed"></span>';  
					}
					else
					{
						echo '<span class="ui-icon ui-icon-tag" style="cursor:pointer" title="Tag Redeemed" onclick="tagRedeemDate(\''.$row['CSTUBNO'].'\',\''.$row['ILOCATIONID'].'\');"></span>';
						echo '<span class="ui-icon ui-icon-cancel" style="cursor:pointer" title="Untag Verified" onclick="confirm_action(\''.$row['CSTUBNO'].'\');"></span>';
					}
					
				echo "</td>	
					<td align=\"left\" >".$row['CEMPLOYEENAME']."</td>
					<td align=\"left\" >".$row['CDEPARTMENT']."</td>
					<td align=\"right\" >".$row['MAMOUNT']."</td>
					<td>".$row['CSTATUS']."</td>
					<td>".$row['DREDEEMED']."</td>
					<td>".$row['REDEEMEDBY']."</td>
					";
				echo "</tr>";
			}
			echo "</tbody>
				  </table>";
		exit();
	}
	else if(isset($_GET['action']) && $_GET['action'] == 'redeem_stub')
	{
		$stub = $_POST['stub'];
			
        //$strUpdate = "UPDATE TBLCOUPONS SET REDEEMEDBY = '" . $_SESSION['session_id'] . "', BREDEEMED = 1, DREDEEMED = '" . date("m-d-Y g:i:s A",strtotime("+8 Hours")) . "'";
		$strUpdate = "UPDATE TBLCOUPONS SET REDEEMEDBY = '" . $_SESSION['session_id'] . "', BREDEEMED = 1, DREDEEMED = '" . date("m-d-Y g:i:s A",strtotime($_GET['dateRedeem'])) . "'";
		$strUpdate .= ", ILOCATIONID = " . $_SESSION['session_code'] . " WHERE CSTUBNO = '" . $stub . "'";
		$qryUpdate = mssql_query($strUpdate);
		if($qryUpdate)
		{	
			$datetime = date('Y-m-d h:i:s', time() + 28800);
			$strLogAction = "INSERT INTO tblaudittrail(cUserID, process_date, process) VALUES('".$_SESSION['session_id']."','".$datetime."','TAGGED REDEEMED STUB NUMBER: ".$stub."') ";
			mssql_query($strLogAction);
			echo json_encode(array('success'=>true, 'msg'=>'PG-UST Coupon['.$_POST['stub'].'] has been redeemed.'));
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
			
		
		$strQry = "SELECT CSTUBNO, BVERIFIED, BREDEEMED, BRECEIVED FROM TBLCOUPONS WHERE CSTUBNO = '" . $stub . "'";
		$qryGet = mssql_query($strQry);
		$res = mssql_fetch_array($qryGet);
		if($res['BREDEEMED'] == 1 || $res['BRECEIVED'] == 1)
		{
			echo json_encode(array('errmsg'=>'WARNING: PG_UST Coupon already redeemed or received, cannot be untag as verfied.'));
		}
		else
		{
			$strUpdate = "UPDATE TBLCOUPONS SET BVERIFIED = NULL, DVERIFIED = NULL, VERIFIEDBY = NULL, ILOCATIONID = NULL";
			$strUpdate .= " WHERE CSTUBNO = '" . $stub . "'";
			$qryUpdate = mssql_query($strUpdate);
			if($qryUpdate)
			{				
				$datetime = date('Y-m-d h:i:s', time() + 28800);
				$strLogAction = "INSERT INTO tblaudittrail(cUserID, process_date, process) VALUES('".$_SESSION['session_id']."','".$datetime."','UNTAGGED VERIFIED STUB NUMBER: ".$stub."') ";
				mssql_query($strLogAction);
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
	@import "../jquery/css/demo_page.css" "";
	@import "../jquery/css/demo_table_jui.css";
</style>
<script language="javascript" type="text/javascript">
	
	$(function() {
		$("#btnUntag").button({
				icons: {
					primary: 'ui-icon-tag'
				}
			 })//.click(function(){
				// $("#dvDialog").dialog('open');
			// })
		;
			LoadData('employeecoupons_trs.php','DataCoupons','tblCoupons','action=LoadVerifiedCoupons','');
			
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
        
        $('#txtDateRedeem').datepicker({
            dateFormat : 'mm/dd/yy',
        });
        
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
    
    function tagRedeemDate(stub, str){
        $(function() {
            $('#dvDialogRedeemDate').dialog({ 
                height : 150,
                show: 'slideDown',
                width : 300,
                resizable: false,
                modal: true,
                closeOnEscape:false,
                title: 'TAG REDEEM',
                buttons: {
                    'TAG': 
                    function() {
                        var dateRedeem = $('#txtDateRedeem').val();
                        if(dateRedeem == ''){
                            alert('Please select redeem date');    
                        }else{
                            tagRedeemed(stub, str, dateRedeem);     
                        }  
                    }
                },
                close: function() {
                    $("#txtDateRedeem").val("");
                    $(this).dialog('close');                
                }
            });
        });
    }
	
	function tagRedeemed(stub, str, dateRedeem)
	{
		u_store = '<?php echo $_SESSION['session_code']; ?>';
		v_store = str;
		
		if(v_store == u_store)
		{
			if(confirm("Redeem PG-UST Coupon["+stub+"]?"))
			{
				$.ajax({
					url:'employeecoupons_trs.php?action=redeem_stub&dateRedeem='+dateRedeem,
					data:{stub:stub},
					method:'post',
					type:'POST',
					dataType:'json',
					success: function(result){
						if(result.success){
							alert(result.msg);
                            $("#txtDateRedeem").val("");
                            $('#dvDialogRedeemDate').dialog('close'); 
							LoadData('employeecoupons_trs.php','DataCoupons','tblCoupons','action=LoadVerifiedCoupons');
						}else{
							alert(result.errmsg);
						}
					}	
				});
			}
		}
		else
		{
			alert('PG-UST Coupon was verified in other store, cannot be redeemed in this store.');
		}
	}
	
	function confirm_action(stub)
	{
		if(confirm("Untag PG-UST Coupon["+ stub +"] Verified?"))
		{
			if(confirm("Are you sure?"))
			{	
				untagVerified(stub);
			}
		}
	}
	
	function openDlg(stub)
	{
		$("#dvDialog").dialog('open');
		$("#hdnStubNo").val(stub);
	}
	function untagVerified(stub)
	{
		$.ajax({
			url:'employeecoupons_trs.php?action=untag_stubs',
			data:{stub:stub},
			method:'post',
			type:'POST',
			dataType:'json',
			success: function(result){
				if(result.success){
					alert(result.msg);
					LoadData('employeecoupons_trs.php','DataCoupons','tblCoupons','action=LoadVerifiedCoupons&search=n&str=&dfrom=&dto=&sfrom=&sto=');
				}else{
					alert(result.errmsg);
				}
			}	
		});
	}
	
	// function untagVerified()
	// {
		// stub = $("#hdnStubNo").val();
		// user = $("#txtUsername").val();
		// pw = $("#txtPassword").val();
		
		// $.ajax({
			// url:'employeecoupons_trs.php?action=validate_user',
			// data:{user:user, pw:pw},
			// method:'post',
			// type:'POST',
			// dataType:'json',
			// success: function(result){
				// if(result.success){
					// $.ajax({
						// url:'employeecoupons_trs.php?action=untag_stubs',
						// data:{stub:stub},
						// method:'post',
						// type:'POST',
						// dataType:'json',
						// success: function(result){
							// if(result.success){
								// alert(result.msg);
								// LoadData('employeecoupons_trs.php','DataCoupons','tblCoupons','action=LoadVerifiedCoupons&search=n&str=&dfrom=&dto=&sfrom=&sto=');
								// $("#txtUsername, #txtPassword").val("");
							// }else{
								// alert(result.errmsg);
							// }
						// }	
					// });
				// }else{
					// alert(result.errmsg);
				// }
			// }	
		// });
	// }
	
</script>
</head>
<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" style="font-family:Arial, Helvetica, sans-serif">
<!--
<button id="btnUntag" onclick="untagVerified();">UNTAG AS VERIFIED</button>-->
<br>
<div id="DataCoupons"></div>
<!-- LIST OF UNIVERSITY OF STO. TOMAS FACULTY MEMBERS [Redemption Period : 11/30/2013 - 03/31/2014] -->
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

<div id="dvDialogRedeemDate" style="display:none;">
    <table>
            <tr>
                <td>Redeem Date</td>
                <td>:</td>
                <td><input type="text" name="txtDateRedeem" id="txtDateRedeem" /></td>
                <!-- <td><button id="btnTag" onclick="tagRedeemDate_____()" />TAG</button></td> -->
            </tr>
    </table>
    
</div>

</body>
</html>
