<?php 
	session_start(); 
	require("../syslibs/sysfunction.php");
	

	$ArrLocation = getlocation();
	$ArrDept = getdepartment();
	
	if (isset($_POST['btnSubmit']))
	{
		$sql = mssql_query("SELECT CUSERID FROM tblusers WHERE CUSERID='".strtoupper(trim($_POST['txtUserName']))."'");
		$row = mssql_num_rows($sql);
		if($row <= 0){
			$query = "INSERT INTO tblusers (ILOCATIONID, CUSERNAME, CFULLNAME, CPASSWORD, CLEVEL, CDEPTID)
			VALUES('".strtoupper($_POST['cmbLocation'])."', '".strtoupper($_POST['txtUserName'])."', '".strtoupper($_POST['txtFullName'])."', '".strtoupper($_POST['txtUserPass'])."',
				'$cmbLevel')";
		}else{
			$query = "UPDATE tblusers SET ILOCATIONID='".strtoupper($_POST['cmbLocation'])."', CUSERID='".strtoupper($_POST['txtUserName'])."', CUSERNAME='".strtoupper($_POST['txtFullName'])."', CPASSWORD='".strtoupper($_POST['txtUserPass'])."', CLEVEL='".$cmbLevel."' WHERE CUSERID='".strtoupper(trim($_POST['txtUserName']))."'";
		}
		
		mssql_query($query);
		showmess("<<< User successfully saved >>>");
		
		echo "<script type='text/javascript'>
			window.location = 'useradmin.php'
			</script>";
	}
	
	$ArrLevel=array();
	$ArrLevel[]	= "ADMINxOxADMINISTRATOR";
	$ArrLevel[] = "MANAGERxOxMANAGER";
	$ArrLevel[] = "STAFFxOxSTAFF";
	
	
	if ($_SESSION['session_level'] == "ADMIN")
	{
		$strSQL = "SELECT CUSERID, CUSERNAME, CFULLNAME, CLEVEL, CSTORENAME ";
		$strSQL .= "FROM VIEWUSERS ORDER BY CUSERID ASC";
		$query = mssql_query($strSQL);
		$rsquery = mssql_fetch_assoc($query);
		
		do 
		{
			$i = $i + 1;
			$appdata[] = $rsquery['CUSERID'] . 'xOx' .
				strtoupper($rsquery['CUSERNAME']) . 'xOx' .
				strtoupper($rsquery['CFULLNAME']) . 'xOx' .
				strtoupper($rsquery['CLEVEL']) . 'xOx' .
				strtoupper($rsquery['CSTORENAME']); 
				
				
		} while($rsquery = mssql_fetch_assoc($query));
		mssql_free_result($query);
		
		$strSQL = "SELECT COUNT(*) AS MTOTAL FROM tblusers";
		$query = mssql_query($strSQL);
		$rsquery = mssql_fetch_assoc($query);
		$numrec = $rsquery['MTOTAL'];
		mssql_free_result($query);	
	}
	else
	{
		$strSQL = "SELECT CUSERID, CUSERNAME, CFULLNAME, CLEVEL, CSTORENAME ";
		$strSQL .= "FROM VIEWUSERS WHERE ILOCATION = '" . trim(substr($_SESSION['session_location'],0,3)) . "' 
		ORDER BY CUSERID ASC";
		$query = mssql_query($strSQL);
		$rsquery = mssql_fetch_assoc($query);
		
		do 
		{
			$i = $i + 1;
			$appdata[] = $rsquery['CUSERID'] . 'xOx' .
				strtoupper($rsquery['CUSERNAME']) . 'xOx' .
				strtoupper($rsquery['CFULLNAME']) . 'xOx' .
				strtoupper($rsquery['CLEVEL']) . 'xOx' .
				strtoupper($rsquery['CSTORENAME']); 
				
		} while($rsquery = mssql_fetch_assoc($query));
		mssql_free_result($query);
		
		$strSQL = "SELECT COUNT(*) AS MTOTAL FROM tblusers WHERE ILOCATIONID = '" . trim(substr($_SESSION['session_location'],0,3)) . "'";
		$query = mssql_query($strSQL);
		$rsquery = mssql_fetch_assoc($query);
		$numrec = $rsquery['MTOTAL'];
		mssql_free_result($query);	
	}
	
	if (!empty($myselection))
	{
		$MyUser = array();
		$MyUser = split("xOx",$myselection);		
		switch ($MyUser[0]) 
		{
			case 'delete':
				$UserDelete = "DELETE FROM tblusers WHERE CUSERID = '" . $MyUser[1] . "'";
				mssql_query($UserDelete);
				
				echo "<script type='text/javascript'>
				window.location = 'useradmin.php';
				</script>";
				
				break;
		}	
		$myselection = '';
	}
	
	if($_GET['action']=='getUserInfo'){
		echo getUser($_GET['value']);
		exit();
	}
	function getUser($col)
	{
		$val = mssql_fetch_array(mssql_query("SELECT CUSERNAME, CPASSWORD, CUSERNAME, CFULLNAME, CLEVEL, CSTORENAME, iLocation FROM VIEWUSERS WHERE CUSERID= '" . $col . "'"));
		echo "$('#txtUserName').val('".$val['CUSERNAME']."');";
		echo "$('#txtUserPass').val('".strtoupper($val['CPASSWORD'])."');";
		echo "$('#txtFullName').val('".strtoupper($val['CFULLNAME'])."');";	
		echo "$('#cmbLevel').val('".strtoupper($val['CLEVEL'])."');";
		echo "$('#cmbLocation').val('".strtoupper($val['iLocation'])."');";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../css/ext-all.css">
<link rel="stylesheet" type="text/css" href="../css/ytheme-vista.css">
<script type="text/javascript" src="../syslibs/jquery-1.8.2.js"></script>
<title></title>
<style type="text/css">
<!--
	.style3 
	{
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 11px;
		font-weight: bold;
	}
	.styleme 
	{
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 11px;
	}
	.style6 
	{
		font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif; 
	}
	.style7 
	{
		font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;
	}
	.style3 
	{
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 11px;
		font-weight: bold;
	}
	
-->
</style>

<script language="javascript" type="text/jscript" src="../utils/mycalendar.js"></script>
<script language="javascript" type="text/jscript" src="../utils/ext-all.js"></script>
<script language="javascript" type="text/javascript">
	var usercpage = {
		init : function(){
				Ext.form.Field.prototype.msgTarget = 'under';
			var username = new Ext.form.TextField({
				width:300,
				allowBlank:false
			});
			username.applyTo('username');
			  
			var userpass = new Ext.form.TextField({
				width:300,
				allowBlank:false
			});
			userpass.applyTo('userpass');
		 
			
			 	
			 }
		}
		
	Ext.onReady(usercpage.init,usercpage, true);
</script>

</head>
<body bottommargin="0" topmargin="0" leftmargin="0" rightmargin="0">
<center>
<form name="frmUser" method="post">
<input name="myselection" type="hidden" />
 <div class="style6" style="background-color:#6AB5FF; font-size:11px; font-weight:bold; height:23px; width:892px; margin:10px 0px 0px 3px; position">
      	<div style="float:left; margin-top:5px">
      		<b>USER MAINTENANCE</b>
            <input type="hidden" name="numrec" value="<?php echo $numrec; ?>"  />
      	</div>
      </div>
<table width="896" border="0" style="font-family:Arial, Helvetica, sans-serif" class="style6">
<tr nowrap="wrap" bgcolor="#D9DFDB">

<th width="84" height="23" colspan="2"><div align="center" class="style6"><b>Action</b></div></th>
<th width="191" height="23"><div align="center" class="style6"><b>User ID</b></div></th>
<th width="191" height="23"><div align="center" class="style6"><b>User Name</b></div></th>
<th width="263" height="23"><div align="center" class="style6"><b>Full Name</b></div></th>
<th width="169" height="23" ><div align="center" class="style6"><b>Access Level</b></div></th>
<th width="189" height="23"><div align="center" class="style6"><b>Location</b></div></th>
</tr>
</table>
<div style="overflow:auto; height:175px; width:892px">
<?php
	$i = 0;
	$nStart = 0;
	while($nStart < count($appdata)) 
	{			
		
		$mData = split('xOx',$appdata[$nStart]);
		$bgcolor = ($i++ % 2) ? "#F2FEFF" : "#EAEAEA";
		$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
		. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
?>

<table width="875" border="0" style="font-family:Arial, Helvetica, sans-serif" class="style6">
<tr bgcolor="<?php echo $bgcolor; ?>" align="left" style="font-size:12px" onclick="javascript:editUser('<?php echo $mData[0];?>')" <?php echo $on_mouse;?> >
<!--<td width="32" height="21" align="center">
<a href="../pages/createemploymententry.php?maction=add&mrecid=<?php echo $mData[0]; ?>" target="mainFrame" class="style6"></a></td>-->
<td width="36" height="21" align="center">
	<input name="imgDelete" type="image" onClick="javascript:procData('delete','<?php echo $mData[0];?>');" src="../images/mydelete.gif" alt="Delete User Record" title="Delete User Record" />
<td width="191"><span class="style6"><?php echo $mData[0];?></span></td>
<td width="263"><span class="style6"><?php echo $mData[1];?></span></td>
<td width="263"><span class="style6"><?php echo $mData[2];?></span></td>
<td width="169"><span class="style6">
<?php 
	if ($mData[3] == "ADMIN")
	{
		echo "ADMINISTRATOR";
	}
	else if ($mData[3] == "STAFF")
	{
		echo "STAFF";
	}
	else
	{
		echo "STAFF";
	}
?></span></td>
<td width="169"><span class="style6">
<?php echo $mData[4];
?></span></td>
</tr>
<?php
		$nStart++;		
	}  //end while statement
?>
</table>
</div>
<table width="896" border="0" style="font-family:Arial, Helvetica, sans-serif" class="style6">
<th bgcolor="#DEEDD1" height="23" colspan="7" nowrap="nowrap" class="style6"><b> [ <?php echo $numrec; ?> Record(s) found... ]</b> 

<tr bgcolor="#6AB5FF">
<td height="21" colspan="14">
<span class="style3">
</span>
</td>
</tr>	  

</table>

</form>
</center>
<div style="height:2px; margin-top:0px;margin-left:0px" >
</div>
<center>
<div style="width:500px; margin-top:15px;margin-left:250px" >
	<div class="x-box-tl"  >
		<div class="x-box-tr">
			<div class="x-box-tc"></div>
		</div>
	</div>
	<div class="x-box-ml">
        <div class="x-box-mr">
			<div class="x-box-mc">
	            <h1 align="center" >&nbsp;</h1>
		    <br />
			  <h1 align="center">

<form name="userc" enctype="application/x-www-form-urlencoded" method="post">
<table width="353" border="0">
<tr align="left">
<td width="105">User Name</td>
<td width="238"><input name="txtUserName" type="text" id="txtUserName" size="20" class="oan" value="<?php echo $txtUserName; ?>" /></td>
</tr>
                      <tr align="left">
                        <td>Password</td>
                        <td><input name="txtUserPass" type="password" id="txtUserPass" size="20" class="oan" /></td>
                      </tr>
                      <tr align="left">
                        <td>Full Name</td>
                        <td><input name="txtFullName" type="text" id="txtFullName" size="40" class="oan" value="<?php echo $txtFullName; ?>" /></td>
                      </tr>
                      <tr align="left">
                        <td>Access Level</td>
                        <td><?php echo populatelist($ArrLevel,$cmbLevel,'cmbLevel',' class="styleme" id="cmbLevel" '); ?></td>
                      </tr>
                      
                      <tr align="left">
                        <td>Location</td>
                        <td><?php echo populatelist($ArrLocation,$cmbLocation,'cmbLocation',' class="styleme" id="cmbLocation" '); ?></td>
                </tr>
                <tr>
               	  <td></td>
                </tr>
                
          <tr align="left">
          <td colspan="2" align="center">
          <input type="submit" name="btnSubmit" id="savemo" value="Save" class="oan" onclick="return ValidateUsers();" />&nbsp;</td>
                </tr>
                </table>
                </form>
              </h1>
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
<div style="height:1px; margin-top:20px;margin-left:0px" >
</div>
<div class="oab" align="center"><font style="font-size:7px" color="#FF0000"></font></div>
</body>
</html>
<script language="javascript" type="text/javascript">
<!-- Hide me
	window.document.getElementById('txtUserName').focus();
	function ValidateUsers()
	{
		varUser = window.document.getElementById('txtUserName').value;
		if (varUser == "")
		{
			window.alert("<<< User Name is a required field >>>");
			window.document.getElementById('txtUserName').focus();
			return false;
		}
		varPass = window.document.getElementById('txtUserPass').value;
		if (varPass == "")
		{
			window.alert("<<< User Password is a required field >>>");
			window.document.getElementById('txtUserPass').focus();
			return false;
		}
		varName = window.document.getElementById('txtFullName').value;
		if (varName == "")
		{
			window.alert("<<< Full Name is a required field >>>");
			window.document.getElementById('txtFullName').focus();
			return false;
		}
		varLocation = window.document.getElementById('cmbLocation').value;
		if (varLocation == "")
		{
			window.alert("<<< Location is a required field >>>");
			window.document.getElementById('cmbLocation').focus();
			return false;
		}

	}
	
	function myDateValue(x)
	{
		var cal5 = new mycalendar(document.userc.elements[x]);
		cal5.popup();
		cal5.year_scroll = true;
		cal5.time_comp = false;
	}
// I end here -->

	function giveMeValue(midx) 
	{
		document.userc.elements['ccocd' + midx].value = midx;
	}

	function checkMeAll(mIdx) 
	{
		for (var i = 0; i < document.myform.elements.length; i++) 
		{
			document.userc.elements[i].checked = true;
		}
	
		for (var i = 1; i <= (mIdx + 0); i++)
		document.userc.elements['ccocd' + i].value = i;
	}

	function uncheckMeAll(mIdx) 
	{
		for (var i = 0; i < document.myform.elements.length; i++) 
		{
			document.userc.elements[i].checked = false;
		}
		
		for (var i = 1; i <= (mIdx + 0); i++) 
		{
			document.userc.elements['ccocd' + i].value = 0;
		}
	
	}
	
	function getCount(mIdx) 
	{
		var oa = 0
		for (var i = 1; i <= (mIdx + 0); i++) 
		{
			if(document.userc.elements['ccocd' + i].value > 0) 
			{
				document.userc.elements['meron'].value = "meron";
				oa = 1;
			} 
		}
		
		if( oa == 0) {
			document.userc.elements['meron'].value = "";
		}
	}

	function procData(maction,mysel)
	{
		if (maction == "delete")
		{
			var lDelete = confirm("Delete this User?");
			if (lDelete == true)
			{
				document.frmUser.elements['myselection'].value = "deletexOx" + mysel;
			}
		}
		
		if (maction == "edit")
		{
			document.frmUser.elements['myselection'].value = "editxOx" + mysel;
		}
	}
	
	function editUser(user)
	{
		$.ajax({
		  url: "useradmin.php",
		  cache: false,
		  type: "GET",
		  data: 'value='+user+"&action=getUserInfo",
		  success: function(Data){
			eval(Data);
		  }
		});
	}

</script>
