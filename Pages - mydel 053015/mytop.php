<?php 
	require_once("../syslibs/sysdbconfig.php");

/*$dbm=oa_dbconfig::sys_dbm(0);

//$str = "select max(concat(upld_date,' ',upld_time)) as updates from {$dbm}.upldlog where upld_comp = 'GETZ'";
$str = "delete from {$dbm}.outlet_sales where (cycle is null or cycle = ' ')";
oa_dbconfig::oa_sqlexec($str,oa_dbconfig::$db_myconnection,"Error initializing db01: ");

$str = "select doc_date updates from {$dbm}.outlet_sales where comp = 'GETZ' order by doc_date desc limit 1";
$qgetz = oa_dbconfig::oa_sqlexec($str,oa_dbconfig::$db_myconnection,"Error fetching upload logs: ");
$rsgetz = mysql_fetch_assoc($qgetz);
$cgetz = $rsgetz[updates];
mysql_free_result($qgetz);

//$str = "select max(concat(upld_date,' ',upld_time)) as updates from {$dbm}.upldlog where upld_comp = 'MDC'";
$str = "select doc_date updates from {$dbm}.outlet_sales where comp = 'GETZ' order by doc_date desc limit 1";

$qmdc = oa_dbconfig::oa_sqlexec($str,oa_dbconfig::$db_myconnection,"Error fetching upload logs mdc: ");
$rsmdc = mysql_fetch_assoc($qmdc);
$cmdc = $rsmdc[updates];
mysql_free_result($qmdc);


//$str = "select max(concat(upld_date,' ',upld_time)) as updates from {$dbm}.upldlog where upld_comp = 'SDI' ";
$str = "select doc_date updates from {$dbm}.outlet_sales where comp = 'SDI' order by doc_date desc limit 1";

$qsdi = oa_dbconfig::oa_sqlexec($str,oa_dbconfig::$db_myconnection,"Error fetching upload logs mdc: ");
$rssdi = mysql_fetch_assoc($qsdi);
$csdi = $rssdi[updates];
mysql_free_result($qsdi);*/


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../css/brpexxall.css" type="text/css" rel="stylesheet" />
<link href="../css/brptheme.css" type="text/css" rel="stylesheet" />
<style type="text/css">
<!--
.style10 {color: #FFFFFF; font-weight: bold; font-size:12px }
a:link {
	text-decoration:none;
}
a:visited {
	text-decoration: none;
}
a:hover {
	text-decoration: none;
	color: #F7F9D0;
}
a:active {
	text-decoration:none;
	color: #99FF00;
}
-->
</style>

</head>
<body style="font-family:Arial, Helvetica, sans-serif" topmargin="0" leftmargin="0" bottommargin="0" background="../Images/bgv1.png">
<table border="0" width="100%" >
  <tr>
    <td height="105" rowspan="5"><span class="pgindexlogo">&nbsp <img src="../Images/ust_header.png" alt="PPCI" width="500" height="90" border="0" title="PPCI"  /></span></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="1683" height="30" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" width="168" height="30" bgcolor="#001671"><a href="menu.php" target="leftFrame" class="style10" ><font color="#FFFFFF">Redemption</font></a></td>
    <td align="center" width="57" bgcolor="#001671"><a href="../login.php" target="_parent" class="style10"><font color="#FFFFFF">Log Out</font></a></td>
    <td width="1291" bgcolor="#001671">&nbsp;</td>
  </tr>
</table>
</body>
</html>
