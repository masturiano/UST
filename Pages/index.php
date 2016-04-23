
<?php   if(!isset($_SESSION)){ session_start(); } 

if(isset($_GET['action']) && $_GET['action'] == 'logout')
{
	session_unset($_SESSION['session_id']);
	session_unset($_SESSION['session_user']);
	session_unset($_SESSION['session_fullname']);
	session_unset($_SESSION['session_code']);
	session_unset($_SESSION['session_location']);
	session_unset($_SESSION['session_level']);
	session_unset($_SESSION['session_dept_id']);
	session_unset($_SESSION['session_dept']);
	
	echo "window.location='../login.php'";
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
<script type="text/javascript" src="../jquery/js/jquery-1.6.2.js"></script>
<script type="text/javascript" src="../jquery/js/jquery-ui-1.9.0.custom.min.js"></script>

	<style type="text/css" title="currentStyle">
		.topLbl{
			margin-bottom:1px;
		}
        .dvTop {
            background-color:#3F6998;
        }
        .dvTopContent {
            height: 100px;
            width: 1250px;
            padding-left:5px;
			
        }
        .dvMenu {
            height: 700px;
            width: 200px;/*200*/
            float:left;
            border:1px solid #D6D4D4;
        }
        .dvBody {
            height:700px;
            border:1px solid #D6D4D4;
            margin-left: 202px; /*202*/
        }
      .dvFooter {
            height: 25px;
            width: 100%;
			border:1px solid #D6D4D4;
			font:12px 'Trebuchet MS';
			text-align:center;
			padding-top:5px;
        }
		.wrapper {
            width: 200px;
			height:100%;
      }
    </style>
<script type="text/javascript">    
	$(function(){
  		var int = self.setInterval(function() { clock() },1000);
		$("#acdnMenu").accordion();
	//	checkSession();
	});
	
	function checkSession() {
		$.ajax({
			url: "tzrs_conditions.php?action=check_session",
			type: "GET",
			success: function(Data){
					eval(Data);
				}				
		   });
		setTimeout("checkSession()",10000);   			
	}
	
	function clock()
    {
		var d = new Date();
		var t = d.toLocaleTimeString();
		date = '<?php echo date('D, M d Y'); ?>';
		$('#time').text(date + ' ' +t);
	}
			
	function menu(url)	{
		$("#bodyFrame").attr('src',url);
	}
	
	function confirmMsg(msg){
		$("#conf-msg-icon").addClass('ui-icon ui-icon-alert')
							.attr('style','float:left; margin-right:7px');
		$("#dialogConfMsg").html(msg);		
		$("#dialogConfirm").dialog({
			resizable: false,
			modal: true,
			closeOnEscape:false,
			draggable:false,
			buttons: {
				Ok: function() {
					$.ajax({
						url: "index.php?action=logout",
						type: "GET",
						success: function(Data){
								eval(Data);
							}				
					   });
				},
				Cancel:function(){
					$(this).dialog('close');	
				}
			}
		});	
	}
</script>
</head>

<body>
<div class="dvTop">
	<div class="dvTopContent">
        <div style="width:1200px; height:200px; margin-left:0px">
            <div style="float:left"><img src="../Images/ust_header.png" alt="PPCI" width="500" height="90" border="0" title="PPCI"  /></div>
            <div style="float:right; font:11px 'Trebuchet MS'; color:#FFFFFF"> 
			<b>
            	USER : <?php echo $_SESSION['session_fullname'];?><br>
                DATE/TIME : <label id="time"></label><br>
                STORE : <?php echo $_SESSION['session_location'];?><br />
                <a href="#" onClick="confirmMsg('Are you sure you want to log out?')">LOG OUT</a></p> 
            </b>
            </div>
        </div>
    </div>
</div>
<div class="dvMenu">
	<div id="acdnMenu" class="wrapper">
    	<h3>REDEMPTION</h3>
			<div>
				<?php
					if ($_SESSION['session_dept_id'] == 101)
					{
						echo '<p><a href="#" onClick="menu(\'employeecoupons_ws.php\')">Faculty Member Coupons</a></p>';	
					}
					else if ($_SESSION['session_dept_id'] == 102)
					{
						echo '<p><a href="#" onClick="menu(\'employeecoupons_trs.php\')">Faculty Member Coupons</a></p>';	
						echo '<p><a href="#" onClick="menu(\'couponstatus.php\')">Status Inquiry</a></p>';	
					}
					else if ($_SESSION['session_dept_id'] == 103)
					{
						echo '<p><a href="#" onClick="menu(\'employeecoupons_cc.php\')">Faculty Member Coupons</a></p>';	
						echo '<p><a href="#" onClick="menu(\'couponstatus.php\')">Status Inquiry</a></p>';
						echo '<p><a href="#" onClick="menu(\'create_soa.php\')">Create SOA</a></p>';
					}
					else if ($_SESSION['session_dept_id'] == 100)
					{
						echo '<p><a href="#" onClick="menu(\'employeecoupons_ws.php\')">Faculty Member Coupons (WS)</a></p>';	
						echo '<p><a href="#" onClick="menu(\'employeecoupons_trs.php\')">Faculty Member Coupons (TRS)</a></p>';	
						echo '<p><a href="#" onClick="menu(\'couponstatus.php\')">Status Inquiry</a></p>';
					}
					else
					{
						echo '<p><a href="#" onClick="menu(\'employeecoupons_ws.php\')">Faculty Member Coupons (WS)</a></p>';	
						echo '<p><a href="#" onClick="menu(\'employeecoupons_trs.php\')">Faculty Member Coupons (TRS)</a></p>';	
						echo '<p><a href="#" onClick="menu(\'employeecoupons_cc.php\')">Faculty Member Coupons (C&C)</a></p>';	
						echo '<p><a href="#" onClick="menu(\'couponstatus.php\')">Status Inquiry</a></p>';
						echo '<p><a href="#" onClick="menu(\'create_soa.php\')">Create SOA</a></p>';
					}
				?> 
			</div>
       
	   <?php
			if ($_SESSION['session_dept_id'] != 101)
			{
				echo '<h3>REPORT</h3>
					<div>
						<p><a href="#" onClick="menu(\'rpt_status_filter.php\')">Status Report</a></p>	
						<p><a href="#" onClick="window.open(\'rpt_exception_xls.php\')">Monthly Redemption Report</a></p>	
					</div>';
			}
		
		if ($_SESSION['session_dept_id'] == 100 || $_SESSION['session_dept_id'] == 999)
		{   
		?>
			<h3>SYSTEM</h3>
			<div>
		<!--	<p><a href="#" onClick="menu('tzrs_change_password.php')">Change Password</a></p> -->
				<p><a href="#" onClick="menu('users.php')">Users</a></p>
				<p><a href="#" onClick="menu('audit_trail.php')">Audit Trail</a></p>
			</div>
			
	<?php } ?>
	</div>
</div>

<div class="dvBody" id="dvBody">
	<iframe style="height:700px;border:1px solid #D6D4D4;" id="bodyFrame" width="100%" ></iframe>
</div>
<div id="dialog-container" style="display:none"></div>

<div class="ui-widget-header">
	<div class="dvFooter">PG-UST Coupon Redemption v.2.0 | Puregold Price Club Inc. &copy; 2014</div>
</div>

<div id='dialogConfirm' title='PG-UST Coupon Redemption'>
    <p>
        <span id="conf-msg-icon"></span>
        <label id='dialogConfMsg'></label>
    </p>
</div>

</body>
</html>

<?php 
	session_start(); 
	require_once('../syslibs/sysdbconfig.php');
?>
<!--
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>PG - UST Coupon Redemption</title>
</head>

    <frameset rows="135,*" cols="*" frameborder="yes" border="0" framespacing="0">
	    <frame src="mytop.php" name="topFrame" scrolling="No" noresize="noresize" id="topFrame" title="topFrame" />
    		<frameset rows="*" cols="250,*" framespacing="0" frameborder="yes" border="1">
		    <frame src="myleft.php?wdb=<?php // echo $cmydb; ?>" name="leftFrame" scrolling="Yes" id="leftFrame" title="leftFrame" />
		    <frame src="mybody.php" id="bodyFrame" name="mainFrame" id="mainFrame" title="mainFrame" />
	    </frameset>
    </frameset>
<noframes><body>
</body>
</noframes>
</html>
-->