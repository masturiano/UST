<?php	
	session_start();

	if (!class_exists('ustconfig'))	
	{
		class ustconfig
		{
			public static $db_host = "192.168.200.231";//"192.168.200.37";
			public static $db_user = "sqluser";
			public static $db_pass = "sql@123";//"pw@123";
			public static $db_connection;
			public static $db_name = "dbPG_UST";
			
			public function ust_dbconn()
			{
				self::$db_connection = mssql_connect(self::$db_host, self::$db_user, self::$db_pass) or die(mssql_get_last_message());
				mssql_select_db("dbPG_UST") or die(mssql_get_last_message());
			}
			
			function sql_exec($sql_cmd)
			{
				$myqry = mssql_query($sql_cmd) or die(mssql_get_last_message());
				return $myqry;
			}
		}
	}
?>