<?php
if(!isset($_SESSION))
{
	session_start();
}
include_once("dbConnect.php");
$schedule = '';
if($_POST['tgemail'])
{
	$tgemail = $_POST["tgemail"];
	$tgpaswd = $_POST["tgpass"];
	$query = pg_query($dbconn, "SELECT * FROM \"Tour Guide\" WHERE \"g_Email\" = '$tgemail' AND \"g_password\" = '$tgpaswd'");
	$count = pg_num_rows($query);
	
	if($count > 0)
	{
		$row = pg_fetch_array($query);
		$_SESSION['tgemail'] = $row['g_Email'];
		$_SESSION['tgid'] = $row['g_key'];
		$_SESSION['tgfname'] = $row['g_FName'];
		$_SESSION['tglname'] = $row['g_LName'];
		$_SESSION['tgpass'] = $row['g_password'];
		$_SESSION['tgcompany'] = $row['Company'];
		header("Location: tour-guide-home.php");
	}
	else
	{
		echo "<h2> Oops that email or password combination was incorrect.
		<br /> Please try again. </h2>";
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include 'header.php';?>
</head>
<body>
<div style="margin-left: 50px;" class="area">
  <form action = "guide_login.php" method = "post" enctype ="multipart/form-data" class="form-horizontal">
    <div class="heading">
      <h4 class="form-heading">Tour Guide Sign In</h4>
    </div>
    <div class="control-group">
      <label class="control-label" for="inputUsername">Email</label>
      <div class="controls">
        <input name = "tgemail" id="inputUsername" placeholder="E.g. ashwinhegde" type="text">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="inputPassword">Password</label>
      <div class="controls">
        <input name = "tgpass" id="inputPassword" placeholder="Min. 8 Characters" type="password">
      </div>
    </div>
    <div class="control-group">
      <div class="controls">
        <!--<label class="checkbox">
          <input type="checkbox">
          Keep me signed in ¦ <a class="btn btn-link" href="#">Forgot my password</a></label>-->
        <button class="btn btn-success" style="margin-top: 10px;" name = "Submit" type="submit">Sign In</button>
       <!-- <button class="btn" type="button">Help</button>-->
      </div>
    </div>
  </form>
</div>
</body>
</html>