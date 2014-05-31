<?php
//Created by Stuart Yamartino on 10/31/12
$db = new mysqli('XXXXX', 'XXXXX', 'XXXXX', 'XXXXX');
if(mysqli_connect_errno())
{
	echo "Connection Failed: " . mysqli_connect_errno();
	exit();
}
$stmt = $db->stmt_init();

require_once('usernames.php');


for($a=1;$a<=count($usernames);$a++)
{
	$username = $usernames[$a];
	$stmt->prepare("SELECT MAX(timestamp) FROM tweets WHERE username = ?");
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$stmt->bind_result($timestamp);
	while($stmt->fetch()) 
	{
		$timestamp = $timestamp;
	}
	
	if(is_null($timestamp))
	{
		$timestamp = 0;
	}
	
	$content = file_get_contents("http://api.twitter.com/1/statuses/user_timeline.json?screen_name=$username&count=20"); 
	$json = json_decode($content, TRUE);
	
	$i = 0;
	$newstamp = strtotime($json[$i]['created_at']);
	
	if($newstamp > strtotime('Nov 06 00:00:00 +0000 2012') && $newstamp < strtotime('Feb 30 00:00:00 +0000 2013'))
	{
		while($timestamp < $newstamp && $newstamp > strtotime('Nov 06 00:00:00 +0000 2012') && $newstamp < strtotime('Feb 30 00:00:00 +0000 2013'))
		{
			$i++;
			$newstamp = strtotime($json[$i]['created_at']);
		}
		
		for($j=$i-1; $j>=0; $j--)
		{
			$datetime = date_create_from_format('D M d H:i:s O Y', $json[$j]['created_at']);
			$date = date_format($datetime, 'm-d-y');
			$time = date_format($datetime, 'H:i');
			$stmt->prepare("INSERT INTO tweets (username, text, date, time, retweets, followers, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param('ssssiii', $username, $json[$j]['text'], $date, $time, $json[$j]['retweet_count'], $json[$j]['user']['followers_count'],strtotime($json[$j]['created_at']));
			$stmt->execute();
		}
	}
}
?>