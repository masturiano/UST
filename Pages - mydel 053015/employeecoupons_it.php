<?php 
	session_start(); 
	require_once('../syslibs/sysdbconfig.php');
	require('../syslibs/sysfunction.php');

	$myconn = new ustconfig;
	$myconn->ust_dbconn();

	$aMySearch = array();
	$aMySearch[] = "CSTUBNOxOxSTUB NO";
	$aMySearch[] = "CEMPLOYEENAMExOxFACULTY NAME";

	$perpage = 30;
	$i = 0;
	$numrec = 0;
	
	if ($_GET['pos'] <> '') 
	{
		$goToPagego = $_GET['pos'];
	} 
	elseif ($goToPagego == '' or $goToPagego == 0) 
	{
		$goToPagego = 0;
	}
	
	$SelectSQL = "CSTUBNO, CEMPLOYEENAME, CDEPARTMENT, MAMOUNT, CSTATUS, DCLAIMED ";
	
	$mposition = '';
	
	if (isset($gosearch) and $searchwhat <> '' and $mysearch <> '')
	{
		$searchwhat = trim($searchwhat,' ');
		$goToPagego = 0;
		
		$strSQL = "SELECT COUNT(*) AS MTOTAL FROM VIEWCOUPONSTATUS";
		
		if($mposition <> '')
		{
			$strSQL .= " where (" . $mposition . ")";
		}
		else
		{
			$strSQL .= " where $mysearch like '%".strtoupper($searchwhat)."%'";
		}
		
		$query = mssql_query($strSQL);
		$rsquery = mssql_fetch_assoc($query);
		$numrec = $rsquery['MTOTAL'];
		mssql_free_result($query);
		
		$strSQL = "SELECT " . $SelectSQL . " FROM VIEWCOUPONSTATUS";
		
		if($mposition <> '')
		{
			$strSQL .= " WHERE (" . $mposition . ")";
		}
		else
		{
			$strSQL .= " WHERE $mysearch LIKE '%$searchwhat%'";
		}
		$strSQL .= " ORDER BY CSTUBNO ASC";
		
		$query = mssql_query($strSQL);
		$rsquery = mssql_fetch_assoc($query);
	}
	
	else 
	{
		$mysearch = "CEMPLOYEENAME";
		$strSQL = "SELECT COUNT(*) AS MTOTAL FROM VIEWCOUPONSTATUS";

		$query = mssql_query($strSQL);
		$rsquery = mssql_fetch_assoc($query);
		$numrec = $rsquery['MTOTAL'];
		mssql_free_result($query);
	
		
		$strSQL = "SELECT TOP 100 " .$SelectSQL . " FROM VIEWCOUPONSTATUS ORDER BY CSTUBNO ASC";
	
		$query = mssql_query($strSQL);
		$rsquery = mssql_fetch_assoc($query);
	}

	($numrec <= 0) ? $lpage = ' disabled="disabled" ' : $lpage = ' ';
	do 
	{
		$i = $i + 1;
		$empdata[] = $rsquery['CSTUBNO'] . 'xOx' .
			strtoupper($rsquery['CEMPLOYEENAME']) . 'xOx' . 
			strtoupper($rsquery['CDEPARTMENT']) . 'xOx' .
			strtoupper($rsquery['MAMOUNT']) . 'xOx' .
			strtoupper($rsquery['CSTATUS'])  . 'xOx' . 
			strtoupper($rsquery['DCLAIMED']);
	} while($rsquery = mssql_fetch_assoc($query));
	
?>

<?php
	if (!empty($myselection))
	{
		$MySel = array();
		$MySel = split("xOx",$myselection);		
		switch ($MySel[0]) 
		{
			case 'post':
				$strSQL = "SELECT * FROM TBLCOUPONS WHERE CSTUBNO = '" . $MySel[1] . "' AND BCLAIMED = 1";
				$qrySQL = mssql_query($strSQL);
				$NumRows = mssql_num_rows($qrySQL);
				if ($NumRows == 1)
				{
					showmess("Warning: Christmas Coupon [" . $MySel[1]. "] already claimed");
					break;
				}
				else
				{
					$strUpdate = "UPDATE TBLCOUPONS SET CUSER = '" . $_SESSION['session_id'] . "', BCLAIMED = 1, DCLAIMED = '" . date("m-d-Y g:i:s A",strtotime("+8 Hours")) . "'";
					$strUpdate .= ", ILOCATIONID = " . $_SESSION['session_code'] . " WHERE CSTUBNO = '" . $MySel[1] . "'";
					mssql_query($strUpdate);
					showmess("PG-UST Coupon for this UST Faculty member successfully posted");
					
					echo "<script type='text/javascript'>
					window.location = 'employeecoupons.php';
					</script>";
				}
		}
		$myselection = '';
	}
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
<style type="text/css">
<!--
.style6 {font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif; }
.style7 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;}
-->
</style>
</head>
<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" style="font-family:Arial, Helvetica, sans-serif">
<form method="post" name="myform">
<input name="myselection"  value="<?php echo $myselection;?>"  type="hidden"/>
	<div>
      <div class="style6" style="background-color:#6AB5FF; font-size:11px; font-weight:bold; height:23px; width:1040px; margin:10px 0px 0px 3px; position">
      	<div style="float:left; margin-top:5px">
      		LIST OF UNIVERSITY OF STO. TOMAS FACULTY MEMBERS [Redemption Period : 11/30/2013 - 03/31/2014]
      	</div>
      </div>
      <div class="style6" style="background-color:#DEEDD1; font-size:11px; font-weight:bold; height:23px;; width:1040px; margin:2px 0px 0px 3px">
		<div style="float:left; margin-top:1px">
      		Search by: 
			<?php populatelist($aMySearch,$mysearch,'mysearch',' class="style6" ');  ?> 
    	    <input type="text" name="searchwhat" size="40" value="<?php echo $searchwhat; ?>"  class="style6" />
			<input type="submit" name="gosearch" value="Go Search" class="style7" />
            <?php
				if ($searchwhat == '')
				{
					echo "<blink><font color='red'>Search criteria is required</font></blink>";
				}
				else
				{
				}
			?>
		</div>      
      </div>
      <div class="style6" style="background-color:#DEEDD1; font-size:11px; font-weight:bold; height:23px;; width:1040px; margin:2px 0px 0px 3px">
		<div style="float:left; margin-top:5px">
      		[ <?php echo $numrec; ?> Record(s) found... ] Page: <?php echo my_pageselector(' onchange="javascipt:ako(this);" class="style6" ' . $lpage,$numrec,$pageNow,$totpage); ?>
            <input type="hidden" name="numrec" value="<?php echo $numrec; ?>"  />
		</div>      
      </div>
   </div>
  
	<table width="1045" border="0" style="font-family:Arial, Helvetica, sans-serif" >
      <tr nowrap="wrap" bgcolor="#D9DFDB">
        <th width="86" height="23" nowrap="nowrap"><div align="center" class="style6 style7">Stub No </div></th>
        <th height="23" colspan="1"><div align="center" class="style6">Action</div></th>
        <th width="273" height="23"><div align="center" class="style6">Employee Name </div></th>
        <th width="273" height="23"><div align="center" class="style6">Department</div></th>
        <th width="95" height="23" ><div align="center" class="style6">Stub Amount</div></th>
        <th width="90" height="23"><div align="center" class="style6">Stub Status</div></th>
        <th width="148" height="23"><div align="center" class="style6">Store/Redeem Date</div></th>
      </tr>
    </table>
    <div style="overflow:auto; height:350px; width:1044px">
	  <?php
		$i = 0;
		$nStart = 0;
		while($nStart < count($empdata)) {			
			
		$mData = split('xOx',$empdata[$nStart]);
		
		$bgcolor = ($i++ % 2) ? "#F2FEFF" : "#EAEAEA";
		
		$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
				  . ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';

	  ?>
       
      <table width="1027" border="0" style="font-family:Arial, Helvetica, sans-serif" >
      <tr bgcolor="<?php echo $bgcolor; ?>" align="left" style="font-size:12px" <?php echo $on_mouse; ?> >
        <td width="86" height="22">
        	<span class="style6"><?php echo $mData[0]; ?></span>        </td>
        <td width="50" height="21" align="center">
        	<?php
				if ($mData[4] == "REDEEMED")
				{?>
				<input type="image" src="../Images/mydelete.gif" alt="Post Stub Number" />
				<?php }
				else
				{
				?>
				<input name="imgPost" type="image" onClick="javascript:procData('post','<?php echo $mData[0]; ?>');" src="../Images/s_f_okay.gif" alt="Post Stub Number" />
            <?php

				}
            ?>
        
        </td>
            
		<td width="273" height="21" align="left"><span class="style7"><?php echo $mData[1]; ?></span>
        <td width="273" height="21"><span class="style7"><?php echo $mData[2]; ?></span></td>
        <td width="95" height="21" align="right"><span class="style7"><?php echo $mData[3]; ?></span></td>
        <td width="90" height="21" nowrap="nowrap" align="left"><span class="style7"><?php echo $mData[4]; ?></span></td>
        <td width="130" height="21"  align="right"><span class="style7"><?php echo $mData[5]; ?></span></td>
      </tr>
	  <?php
	  	$nStart++;		
	  }  //end while statement
	  ?>
       </table>
      </div>
   	<div class="style6" style="background-color:#6AB5FF; font-size:11px; font-weight:bold; height:23px; width:1040px; margin:10px 0px 0px 3px"></div>
</form>
</body>
</html>
<?php
	if($lclean<>0) {
		mssql_free_result($qry_emp_mast);
	}

?>
<script language="javascript" type="text/javascript">
	window.document.getElementById('searchwhat').focus();
	function ako(selObj) 
	{
		var msrcby = document.myform.elements['mysearch'].value;
		var msrc = document.myform.elements['searchwhat'].value;
		eval("document.location.href = 'employeecoupons.php?pos=" + 
		selObj.options[selObj.selectedIndex].value + "&amp;psearch=" + msrcby +"&amp;ssearch=" + msrc + "'"	);
	}
	
	function procData(maction,mysel)
	{
		if (maction == "post")
		{
			var lPost = confirm("Are you sure you want to post this PG-UST Coupon [" + mysel +"]?");
			if (lPost == true)
			{
				document.myform.elements['myselection'].value = "postxOx" + mysel;
			}
		}
	}

</script>