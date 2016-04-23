<?php	
	session_start();
	require_once("syslibs/sysdbconfig.php");
	
	$myconn = new ustconfig;
	$myconn->ust_dbconn();

	$userid = strtoupper($_POST['userid']);
	$userpass = strtoupper($_POST['userpass']);

	$mymsg = '';
	if (empty($userid) or empty($userpass))
	{
		$mymsg = "*** Username and Password MUST not be empty ***";
	}
	else
	{
		$sqlstr = "SELECT * FROM VIEWUSERS WHERE CUSERNAME = ('$userid') AND CPASSWORD = ('$userpass')";
		$row = $myconn->sql_exec($sqlstr);

		$rowcount = mssql_fetch_row($row);
		if ($rowcount)
		{
			header("Location: pages/index.php");
			$VIEWSQL = "SELECT * FROM VIEWUSERS WHERE CUSERNAME = ('$userid') AND CPASSWORD = ('$userpass')";
	
			$query = mssql_query($VIEWSQL);
			$rsquery = mssql_fetch_array($query);
			
			if (mssql_num_rows($query) > 0)
			{
				$_SESSION['session_id'] = $rsquery['cUserID'];
				$_SESSION['session_user'] = $rsquery['cUserName'];
				$_SESSION['session_fullname'] = $rsquery['cFullName'];
				$_SESSION['session_code'] = $rsquery['iLocation'];
				$_SESSION['session_location'] = $rsquery['iLocation'] . " - " . $rsquery['cStoreName'];
				$_SESSION['session_level'] = $rsquery['cLevel'];
				$_SESSION['session_dept_id'] = $rsquery['cDeptID'];
				$_SESSION['session_dept'] = $rsquery['deptName'];
				
				$datetime = date('Y-m-d h:i:s', time() + 28800);
				$strLogAction = "INSERT INTO tblaudittrail(cUserID, process_date, process) VALUES('".$_SESSION['session_id']."','".$datetime."','LOG-IN') ";
				mssql_query($strLogAction);
				
			}	
			exit();
		}
		else
		{
			$mymsg = "*** Account does not exists ***";
		}
	}
?>
<script language="javascript" type="text/javascript">
	function LoadLogin() 
	{
		var mywindow1;
		var nwidth = screen.availWidth - 10;
		var nheight = screen.availHeight - 60;
		var nleft = parseInt((screen.availWidth/2) - (nwidth/2));
		var ntop = parseInt((screen.availHeight/2) - (nheight/2));
		
		var winattbr = "menubar=no,toolbar=no,location=no,scrollbars=yes,status=yes,width=" + nwidth + ",height=" + nheight + 
		",left=" + nleft + ",top=5" + ",screenX=" + nleft + ",screenY=" + ntop + ",alwaysRaised=yes	";
		
		mywindow1 = window.open("/hris/pages/index.php","MyApplicant",winattbr);
		mywindow1.opener = window;
		mywindow1.focus();
	}
</script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PUREGOLD Price Club, Inc. Log-In Page</title>
<link href="css/brpexxall.css" type="text/css" rel="stylesheet" />
<link href="css/brptheme.css" type="text/css" rel="stylesheet" />
</head>
<br />
<br />
<br />
<br />
<br />
<body background="Images/bgv1.png">
<center>
	<div style="width:500px; margin-top:80px;" >
	<div class="x-box-tl"  >
		<div class="x-box-tr">
			<div class="x-box-tc"></div>
		</div>
	</div>
    
	<div class="x-box-ml">
        <div class="x-box-mr">
			<div class="x-box-mc">
            	<br />
	            <h1 align="center" ><img src="Images/ust_header.png" width="380" height="88" /></h1>
				<br />
                <h2 align="center">PG-UST Longevity Coupon Redemption
                <form name="mylogin" method="post" enctype="application/x-www-form-urlencoded">
                    <table width="180" border="0">
                        <tr align="center" class="oab">
                            <td colspan="2">System Log-In Screen</td>
                            <br />
                        </tr>
                        <tr class="oab" align="left">
                        <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr class="oab" align="left">
                        <td width="92">User Name</td>
                        <td width="78"><input type="text" id="userid" name="userid" maxlength="20" size="11" class="oab" /></td>
                        </tr>
                        <tr class="oab" align="left">
                        <td height="21">Password</td>
                        <td><input type="password" name="userpass" maxlength="20" size="11" class="oab" /></td>
                        </tr>
                        <tr align="center">
                        <td colspan="2"><br /><input type="submit" name="golog" value="LogIn" class="oab" /></td>
                        </tr>
                  </table>
                        <br />
                </form>
			</h2>
			<?php 
			if ( $mymsg <> '') 
				{
			?>
				<h3 align="center"><font color="#FF0000" class="oab">
			<?php echo $mymsg; ?>
	            </font></h3>
			<?php
				}
			?>
			</div>
        </div>
	</div>
	<div class="x-box-bl"  >
		<div class="x-box-br">
			<div class="x-box-bc"></div>
		</div>
	</div>
</div>
</center>
</body>
</html>
<script language="javascript" type="text/javascript">
	document.getElementById('userid').focus();
</script>