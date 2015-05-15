<?php
include_once("dbConnect.php");
$emailList = '';
$email = '';
$business = '';
$msg = '';
if(isset($_POST['business']))
{
	$business = $_POST['business'];
	$query = pg_query($dbconn,"SELECT * FROM \"Tour Guide\" WHERE \"g_Email\" = '$business'");
	while($row = pg_fetch_array($query))
	{
		$email = $row['g_Email'];
		$name = $row['g_FName'].' '.$row['g_LName'];
		$emailList .= '<h2>Search results for: '.$business.' </h2> <div class="list-group"> <a class="list-group-item">Email: '.$email.' | Name: '.$name.'</a><a style="" class="btn btn-default" href="search_business.php?email='.$email.'&op=activate" type="button">Activate<span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a><a style="" class="btn btn-default" href="search_business.php?email='.$email.'&op=suspend" type="button">Suspend<span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>';
	}
}
else if(isset($_GET))
{
	$op = $_GET['op'];
	$email = $_GET['email'];
	//var_dump($op);
	//var_dump($email);
	$gquery = pg_query($dbconn,"Select g_key From \"Tour Guide\" WHERE \"g_Email\" = '$email'");
	$rg = pg_fetch_array($gquery);
	$g_key = $rg['g_key'];
	
	if($op == 'activate')
	{
		$query = pg_query($dbconn,"UPDATE \"Tour Guide\" as t SET \"g_isActive\" = True WHERE t.\"g_Email\" = '$email'");
		if($query)
			$msg =  "Operation successful";
		else
			$msg =  "Operation not completed!";
	}
	else if($op == 'suspend')
	{
		//$query = pg_query($dbconn,"UPDATE \"Tour Guide\" as t SET \"g_isActive\" = False WHERE t.\"g_Email\" = '$email'");
		$query = pg_query($dbconn,"Select \"suspend_TourGuide\"($g_key)");
		if($query)
			$msg =  "Operation successful";
		else
			$msg =  "Operation not completed!";
	}
}

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><?php include 'header.php';?></head>
<body>
<?php include 'navbar.php';?>
<h3><?php echo $msg; ?></h3>
<?php echo $emailList; ?>
</body>
</html>