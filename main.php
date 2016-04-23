<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/ext-all.css">
<link rel="stylesheet" type="text/css" href="css/ytheme-vista.css">
<title>PUREGOLD Price Club, Inc.</title>
</head>
<body background="Images/bgv1.png">
<center>
<br />
<br />
<br />
<br />
<br />
<div align="center"><table width="680" height="106" border="0">
  <tr>
    <td height="155px" width="547px" background="Images/PG Logo.jpg"></td>
  </tr>
</table>
</div>
<center>
<div style="width:250px; margin-top:90px;margin-left:350px" >
	<div class="x-box-tl"  >
		<div class="x-box-tr">
			<div class="x-box-tc"></div>
		</div>
	</div>
	<div class="x-box-ml">
        <div class="x-box-mr">
			<div class="x-box-mc"><br />
				<h1 align="center" style="font-size:18px" ><a href="#" onclick="javascript:LoadLogin(1);">PG-UST Longevity Coupon Redemption</a></h1>
              <br />
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
<div style="height:150px; margin-top:50px;margin-left:0px" >
</div>
<div class="oab" align="center"><font style="font-size:10px" color="#FF0000">Developed By: Louie B. Datuin [IT Special Projects Group] 2009</font></div>
</center>
</body>
</html>
<script language="javascript" type="text/javascript">
	function LoadLogin(Nsys) 
	{
		var mywindow;
		var nwidth = screen.availWidth - 10;
		var nheight = screen.availHeight - 230;
		var nleft = parseInt((screen.availWidth/2) - (nwidth/2));
		var ntop = parseInt((screen.availHeight/5) - (nheight/2));
		
		var winattbr = "menubar=no,toolbar=no,location=no,scrollbars=yes,status=yes,width=" + nwidth + ",height=" + nheight + 
		",left=" + nleft + ",top=100" + ",screenX=" + nleft + ",screenY=" + ntop + ",alwaysRaised=yes";
		
		if (Nsys == 1) 
		{
			mywindow = window.open("login.php","MyRedemption",winattbr);	
			mywindow.opener = window;
			mywindow.focus();		
		} 
		if (Nsys == 2)
		{
			alert ("This site is under construction");
		}
	}
</script>