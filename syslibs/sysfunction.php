<?php
	require("../syslibs/sysdbconfig.php");

	$myconn = new ustconfig;
	$myconn->ust_dbconn();
	
	function getcompanies()
	{	
		$query = mssql_query("SELECT compcode, compname FROM tblcompanies") or die(mssql_get_last_message());
		$rs_query = mssql_fetch_array($query);
		$ArrCompany = array();
		do 
		{
			$ArrCompany[] = $rs_query[0] . 'xOx' .$rs_query[1];
		} 
		while ($rs_query = mssql_fetch_array($query));
		return $ArrCompany;
	}

	function getdepartment()
	{	
		$query = mssql_query("SELECT deptID, deptName FROM TBLDEPT") or die(mssql_get_last_message());
		$rs_query = mssql_fetch_array($query);
		$ArrDept = array();
		do 
		{
			$ArrDept[] = $rs_query[0] . 'xOx' .$rs_query[1];
		} 
		while ($rs_query = mssql_fetch_array($query));
		return $ArrDept;
	}
	
	function getposition()
	{	
		$query = mssql_query("SELECT * FROM VIEWPOSITION") or die(mssql_get_last_message());
		$rs_query = mssql_fetch_array($query);
		$ArrPosition = array();
		do 
		{
			$ArrPosition[] = $rs_query[0] . 'xOx' .$rs_query[1];
		} 
		while ($rs_query = mssql_fetch_array($query));
		return $ArrPosition;
	}
	
	function getlocation()
	{
		$query = mssql_query("SELECT * FROM TBLSTR") or die(mssql_get_last_message());
		$rs_query = mssql_fetch_array($query);
		$ArrLocation = array();
		do 
		{
			$ArrLocation[] = $rs_query[0] . 'xOx' . $rs_query[0] . " - " . $rs_query[2];
		} 
		while ($rs_query = mssql_fetch_array($query));
		return $ArrLocation;
	}
	
	function populatelist($myarray,$ccdata,$conname,$on_action='') 
	{
		$obj = '<select name="' . $conname . '" id="' . $conname . '"' . $on_action .'>';
		//echo $on_action;
		$ii=0; $nflag=0;
		
		while($ii < count($myarray)) 
		{
			$ddata = split("xOx",$myarray[$ii]);
			if($ddata[0] == $ccdata) 
			{
				$mselected = 'selected="selected" ';
				$nflag = 1;
			}
			else 
			{
				$mselected = '';
			}
			$obj .= '                <option' . $mselected . ' value="' . $ddata[0] . '">' . $ddata[1] . '</option>' . "\n";						
			$ii++;
		}
		if($nflag == 0) 
		{
			$obj .= '                <option selected="selected" value="' . $ccdata . '"></option>' . "\n";
		}
		$obj .=	'</select>';
		echo $obj;
	}
	
	function mymonths() 
	{
		$mymonths =array();
		$mymonths[]=1 . "xOx" . "JANUARY";
		$mymonths[]=2 . "xOx" . "FEBRUARY";
		$mymonths[]=3 . "xOx" . "MARCH";
		$mymonths[]=4 . "xOx" . "APRIL";	
		$mymonths[]=5 . "xOx" . "MAY";
		$mymonths[]=6 . "xOx" . "JUNE";
		$mymonths[]=7 . "xOx" . "JULY";
		$mymonths[]=8 . "xOx" . "AUGUST";	
		$mymonths[]=9 . "xOx" . "SEPTEMBER";
		$mymonths[]=10 . "xOx" . "OCTOBER";
		$mymonths[]=11 . "xOx" . "NOVEMBER";
		$mymonths[]=12 . "xOx" . "DECEMBER";	
		return $mymonths;
	}

	function daysofmonths() 
	{
		$mydays = array();
		$i = 1;
		for($i = 1; $i <= 31; $i++) 
		{
			$mydays[] = $i . "xOx" . str_pad($i,2,"0",STR_PAD_LEFT);
		}
		return $mydays;
	}

	function showmess($error='') 
	{
		$showerror = '<script language="javascript" type="text/javascript">';
		$showerror .= 'alert("' .  $error . '")';
		$showerror .= '</script>';
		echo $showerror;
	}
	
	function m_is_empty($cVar) 
	{
		$mystr ='';
		$mygo = 0;
		$mn = 0;
		for($mn=0; $mn <= strlen($cVar); $mn++) 
		{
			if((substr($cVar,$mn,1) == " ") or (substr($cVar,$mn,1) == "")) 
			{
			}
			else 
			{
				$mygo++; 
			}
		}
		return ($mygo > 0) ? 1 : 0;
	}
	
	function my_pageselector($onactions = '',$rows = 1, $pageNow = 1, $nbTotalPage = 1, $showAll = 200, $sliceStart = 5, $sliceEnd = 5, $percent = 20, $range = 10) 
	{
        $gotopage = '<select name="goToPagego" ' . $onactions .'>'. "\n";
		$perpahina = 30;
        if ($nbTotalPage < $showAll) 
		{
            $pages = range(1, $nbTotalPage);
        }
		else 
		{
            $pages = array();

            // Always show first X pages
            for ($i = 1; $i <= $sliceStart; $i++) 
			{
                $pages[] = $i;
            }

            // Always show last X pages
            for ($i = $nbTotalPage - $sliceEnd; $i <= $nbTotalPage; $i++) 
			{
                $pages[] = $i;
            }

            // garvin: Based on the number of results we add the specified $percent percentate to each page number,
            // so that we have a representing page number every now and then to immideately jump to specific pages.
            // As soon as we get near our currently chosen page ($pageNow - $range), every page number will be
            // shown.
            $i = $sliceStart;
            $x = $nbTotalPage - $sliceEnd;
            $met_boundary = false;
            while($i <= $x) {
                if ($i >= ($pageNow - $range) && $i <= ($pageNow + $range)) 
				{
                    // If our pageselector comes near the current page, we use 1 counter increments
                    $i++;
                    $met_boundary = true;
                } 
				else 
				{
                    // We add the percentate increment to our current page to hop to the next one in range
                    $i = $i + floor($nbTotalPage / $percent);

                    // Make sure that we do not cross our boundaries.
                    if ($i > ($pageNow - $range) && !$met_boundary) 
					{
                        $i = $pageNow - $range;
                    }
                }

                if ($i > 0 && $i <= $x) 
				{
                    $pages[] = $i;
                }
            }

            // Since because of ellipsing of the current page some numbers may be double,
            // we unify our array:
            sort($pages);
            $pages = array_unique($pages);
        }

        foreach($pages AS $i) 
		{
            if ($i == $pageNow) 
			{
                $selected = 'selected="selected" style="font-weight: bold"';
            } 
			else 
			{
                $selected = '';
            }
			$gotopage .= '                <option ' . $selected . ' value="' . (($i - 1) * $perpahina) . '">' . $i . '</option>' . "\n";
        }
        $gotopage .= ' </select>';
        return $gotopage;
    } // end function
	
?>