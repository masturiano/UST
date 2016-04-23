<?php 
	session_start(); 
	require_once("../syslibs/sysdbconfig.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../css/brpexxall.css" type="text/css" rel="stylesheet" />
<link href="../css/brptheme.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../jquery/js/jquery-1.8.2.js"></script>
<script language="javascript" type="text/javascript"> 
	
	function showContent(cID) 
	{
		var content = cID + "_content";
		var clink = cID + "_link";
		if (window.document.getElementById(content).style.display == "none") 
		{
			window.document.getElementById(content).style.display = "";
		} 
		else 
		{
			window.document.getElementById(content).style.display = "none";
		}
	}

	function showAddinContent(cID) 
	{
		var content = cID + "_content";
		var clink = cID + "_link";
		if (window.document.getElementById(content).style.display == "none") 
		{
			window.document.getElementById(clink).src = "../images/s_b_insr.gif";
			window.document.getElementById(content).style.display = "";
		} 
		else 
		{
			window.document.getElementById(clink).src = "../images/s_b_srtd.gif";
			window.document.getElementById(content).style.display = "none";
		}
	}
	
	function menu(url)	{
		$("#bodyFrame").attr('src',url);
	}
</script>
</head>
<body>
    <table width="232" border="0" cellpadding="0" cellspacing="0" class="oatborder">
	    <tr height="22" class="oaheadtitle3">
    		<td colspan="2">
				<?php 	
					if ($_SESSION['session_user'] != '')
					{
						echo "WELCOME : " . $_SESSION['session_user'];
					}
				?>
			</td>
	    </tr>
	    <tr height="22" class="oaheadtitle3">
    		<td colspan="2">
				<?php 	
					if ($_SESSION['session_user'] != '')
					{
						echo "LOCATION : " . $_SESSION['session_location'];
					}
				?>
			</td>
	    </tr>
			
        <tr height="22" class="oaheadtitle3">
    		<td colspan="2">
				<?php 	
					if ($_SESSION['session_dept'] != '')
					{
						echo "DEPARTMENT : " . $_SESSION['session_dept'];
					}
				?>
			</td>
		</tr>
                 
<?php
	if ($_SESSION['session_user'] != '')
	{
?>
		<tr height="22" class="oaheadtitle3">
			<td>
				<img src="../Images/s_b_srtd.gif" alt="Applicant Manager" width="16" height="17" border="0" id="sdata_link" title="Applicant Manager" >
			</td>
			<td>
				<a href="javascript:showAddinContent('sdata');">Redemption</a>
			</td>
		</tr>
<?php
	}
?>
        <tr id="sdata_content" style="display:none">
			<td></td>
			<td>
				<table width="203" border="0">
					<tr class="oan">
						<td width="14"><img src="../images/s_b_exec.gif" alt="Employee Coupons" title="Applicant Profile" border="0" /></td>
						<td width="179">
						   <?php
							if ($_SESSION['session_dept_id'] == 101)
							{
								echo '<a href="employeecoupons_ws.php" target="mainFrame">Faculty Member Coupons</a>';	
							}
							else if ($_SESSION['session_dept_id'] == 102)
							{
								echo '<a href="employeecoupons_trs.php" target="mainFrame">Faculty Member Coupons</a>';	
							}
							else if ($_SESSION['session_dept_id'] == 103)
							{
								echo '<a href="employeecoupons_cc.php" target="mainFrame">Faculty Member Coupons</a>';	
							}
							else
							{
								echo '<a href="employeecoupons_ws.php" target="mainFrame">Faculty Member Coupons (WS)</a></td></tr>';	
								echo '<tr class="oan"><td width="14"><img src="../images/s_b_exec.gif" alt="Employee Coupons" title="Applicant Profile" border="0" /></td><td width="179"><a href="employeecoupons_trs.php" target="mainFrame">Faculty Member Coupons (TRS)</a></td></tr>';	
								echo '<tr class="oan"><td width="14"><img src="../images/s_b_exec.gif" alt="Employee Coupons" title="Applicant Profile" border="0" /></td><td width="179"><a href="employeecoupons_cc.php" target="mainFrame">Faculty Member Coupons (C&C)</a>';	
							}
							?> 
						</td>
					</tr>
					
					<tr id="fm_print_content" style="display:none">
						<td></td>
						<td class="oan">
							<table width="200" border="0">
								<tr></tr>
							</table>      
					</tr>
        
				</table>
        
			</td>
	    </tr>
<?php 
	if ($_SESSION['session_level'] == "STAFF")
	{
	}
	else if ($_SESSION['session_level'] == "ITSTAFF")
	{ 
	}
	else if ($_SESSION['session_level'] == "ADMIN")
	{ 
?>
		<tr height="22" class="oaheadtitle3">
			<td>
				<img src="../images/icon/group_key.png" border="0" />
			</td>

			<td>
				<a href="useradmin.php" target="mainFrame">User Administration</a>
			</td>
		</tr>
<?php 
	}
	
	if ($_SESSION['session_user'] != '')
	{
?>
		<tr height="22" class="oaheadtitle3">
			<td>
				<img src="../Images/docs.gif" alt="Applicant Manager" width="16" height="17" border="0" id="sdata2_link" title="Applicant Manager" >
			</td>
			<td>
				<a href="javascript:showAddinContent('sdata2');">Reports</a>
			</td>
		</tr>
<?php
	}
?>
        <tr id="sdata2_content" style="display:none">
			<td></td>
			<td>
				<table width="203" border="0">
					<tr class="oan">
						<td width="14"><img src="../images/folder-setting.gif" alt="Reports" title="Reports" border="0" /></td>
						<td width="179">
						   <?php
							if ($_SESSION['session_dept_id'] == 101)
							{
								echo '<a href="employeecoupons_ws.php" target="mainFrame">Faculty Member Coupons</a>';	
							}
							else if ($_SESSION['session_dept_id'] == 102)
							{
								echo '<a href="employeecoupons_trs.php" target="mainFrame">Faculty Member Coupons</a>';	
							}
							else if ($_SESSION['session_dept_id'] == 103)
							{
								echo '<a href="employeecoupons_cc.php" target="mainFrame">Faculty Member Coupons</a>';	
							}
							else
							{
								echo '<a href="#" onClick="menu(\'rpt_summary_pdf.php\')">Summary Report</a></td></tr>';	
								echo '<tr class="oan"><td width="14"><img src="../images/folder-setting.gif" alt="Employee Coupons" title="Applicant Profile" border="0" /></td><td width="179"><a href="employeecoupons_trs.php" target="mainFrame">Status Report</a>';
									
							//	echo '</td></tr>';<tr class="oan"><td width="14"><img src="../images/folder-setting.gif" alt="Employee Coupons" title="Applicant Profile" border="0" /></td><td width="179"><a href="employeecoupons_cc.php" target="mainFrame">Faculty Member Coupons (C&C)</a>';
							}
							?> 
						</td>
					</tr>
					
					<tr id="fm_print_content" style="display:none">
						<td></td>
						<td class="oan">
							<table width="200" border="0">
								<tr></tr>
							</table>      
					</tr>
        
				</table>
        
			</td>
	    </tr>
		
		<tr height="22" class="oaheadtitle3">
			<td><img src="../images/icon/lock_go.png" border="0" /></td>
			<td>&nbsp;<a href="logout.php" target="_top">Log Out</a></td>
		</tr>
		<tr>
			<td></td>
		</tr>
	</table>

</body>
</html>
<script language="javascript" type="text/javascript">
</script>