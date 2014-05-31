<?php
	$dbHost	= 'XXXXX';			//	database host
	$dbUser	= 'XXXXX';		//	database user
	$dbPass	= 'XXXXX';		//	database password
	$dbName	= 'XXXXX'; 		//	database name
	$dbTable = 'tweets';

	$connection = @mysql_connect($dbHost, $dbUser, $dbPass) or die("Couldn't connect.");
	$db = mysql_select_db($dbName, $connection) or die("Couldn't select database.");

	/*
mysql_query("update $dbTable set text=replace(text,'â€™','\'');");
	mysql_query("update $dbTable set text=replace(text,'â€¦','...');");
	mysql_query("update $dbTable set text=replace(text,'â€“','-');");
	mysql_query("update $dbTable set text=replace(text,'â€œ','\"');");
	mysql_query("update $dbTable set text=replace(text,'â€','\"');");
	mysql_query("update $dbTable set text=replace(text,'â€˜','\'');");
	mysql_query("update $dbTable set text=replace(text,'â€¢','-');");
	mysql_query("update $dbTable set text=replace(text,'â€¡','c');");
*/
	
	
	$sql = "SELECT tweetskey, username, text, date, time, retweets, followers FROM $dbTable";
	$result = @mysql_query($sql)	or die("Couldn't execute query:".mysql_error().''.mysql_errno());
	
	
	header('Content-Type: application/vnd.ms-excel');	//define header info for browser
	header('Content-Disposition: attachment; filename='.$dbTable.'-'.date('d-m-Y').'.xls');
	header('Pragma: no-cache');
	header('Expires: 0');

	for ($i = 0; $i < mysql_num_fields($result); $i++)	 // show column names as names of MySQL fields
		echo mysql_field_name($result, $i)."\t";
	print("\n");

	while($row = mysql_fetch_row($result))
	{
		//set_time_limit(60); // you can enable this if you have lot of data
		$output = '';
		for($j=0; $j < mysql_num_fields($result); $j++)
		{
			if(!isset($row[$j]))
			{
				$output .= "NULL\t";
			}
			else 
			{
				$temp = $row[$j];
				$t = str_replace('â€™','\'',str_replace('â€¦','...',str_replace('â€“','-',str_replace('â€œ','"',str_replace('â€','"',str_replace('â€˜','\'',str_replace('â€¢','-',str_replace('â€¡','c',$temp))))))));
				$output .= "$t\t";
			}
		}
		$output = preg_replace("/\r\n|\n\r|\n|\r/", ' ', $output);
		print(trim($output))."\t\n";
	}
?>