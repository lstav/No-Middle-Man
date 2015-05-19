<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include 'header.php';?>
<style>
table, th, td {
    border: 1px solid black;
	cellpadding: 10;
}
</style>

</head>
<body>
<?php include 'navbar.php';?>


</body>
</html>


<?php
	
	$response = array();
	
	include_once("dbConnect.php");
	
		
		$result = pg_query($dbconn, "Select key,\"email\",\"text\",\"Date\",\"type\" from \"TouristReportList\" 
							union all
							Select \"key\",\"email\",\"text\",\"Date\",\"type\" from \"GuideReportList\"
							order by \"Date\" DESC");
		
		if(!empty($result)) {
			
			if(pg_num_rows($result) > 0) {
				echo '<div class = "container" ><table cellpadding="10"><tr><th>Email</th><th>Message</th><th>Date</th><th></th></tr>';
				
				while($row = pg_fetch_array($result)) {
				
					$tourist = array();
					$tourist['email'] = $row['email'];
					$tourist['text'] = $row['text'];
					$tourist['date'] = $row['Date'];
					$tourist['type'] = $row['type'];
					$tourist['key'] = $row['key'];
				
					$response['success'] = 1;
					$response['report'] = array();
				
					array_push($response['report'], $tourist);
					
					echo '<tr><td><a href="mailto:'.$tourist['email'].'">'.$tourist['email'].'</a></td><td>'.$tourist['text'].'</td><td>'.$tourist['date'].'</td>';
					
					if($tourist['type'] == "Tourist") {
						echo '<td><form action="readTourist.php" method="get"><input type="hidden" value="'.$tourist['key'].'" name="key">
						<input  type="submit" value="Mark as read"></form></td></tr>';
					} else {
						echo '<td><form action="readGuide.php" method="get"><input type="hidden" value="'.$tourist['key'].'" name="key">
						<input  type="submit" value="Mark as read"></form></td></tr>';
					}
					
				}
				echo "</table></div>";
			} else {
				
				
				echo "No reports found";
			}
		} else {
				echo "No reports found";
			}
	pg_close($dbconn);
?>
