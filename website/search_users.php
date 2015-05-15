<?php
include_once("dbConnect.php");
$emailList = '';
$email = '';
$tourist = '';
$msg = '';
if(isset($_POST['tourist']))
{
	$tourist = $_POST['tourist'];
	$query = pg_query($dbconn,"SELECT * FROM \"Tourist\" WHERE \"t_Email\" = '$tourist'");
	while($row = pg_fetch_array($query))
	{
		$email = $row['t_Email'];
		$name = $row['t_FName'].' '.$row['t_LName'];
		$emailList .= '<h2>Search results for: '.$tourist.' </h2> <div class="list-group"> <a class="list-group-item">Email: '.$email.' | Name: '.$name.'</a><a style="" class="btn btn-default" href="search_users.php?email='.$email.'&op=activate" type="button">Activate<span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a><a style="" class="btn btn-default" href="search_users.php?email='.$email.'&op=suspend" type="button">Suspend<span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a><a style="" class="btn btn-default" href="search_users.php?email='.$email.'&op=admin" type="button">Make Admin<span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>';
	}
}
else if(isset($_GET))
{
	$op = $_GET['op'];
	$email = $_GET['email'];
	//var_dump($op);
	//var_dump($email);
	if($op == 'activate')
	{
		$query = pg_query($dbconn,"UPDATE \"Tourist\" as t SET \"t_isActive\" = True WHERE t.\"t_Email\" = '$email'");
		if($query)
			$msg =  "Operation successful";
		else
			$msg =  "Operation not completed!";
	}
	else if($op == 'suspend')
	{
		$query = pg_query($dbconn,"UPDATE \"Tourist\" as t SET \"t_isActive\" = False WHERE t.\"t_Email\" = '$email'");
		if($query)
			$msg =  "Operation successful";
		else
			$msg =  "Operation not completed!";
	}
	else if($op == 'admin')
	{
		//var_dump('IM in');
		$query = pg_query($dbconn,"UPDATE \"Tourist\" as t SET \"isAdmin\" = TRUE WHERE t.\"t_Email\" = '$email'");
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