<?php
//var_dump($_SERVER['DOCUMENT_ROOT']);
if(!isset($_SESSION))
{
	session_start();
}
include_once("dbConnect.php");
$schedule = '';
$errorMsg = '';
if(!empty($_POST['tgemail'])&&!empty($_POST['tgpass']))
{

	//$tgpaswd = $paswd.$salt; //.$t_Email;
	//$tgpaswd = sha1($paswd);
	$tgemail = $_POST["tgemail"];
	$tgpaswd = $_POST["tgpass"];
	$salt = '6e663cc2478ebdc49cbce5609ba0305b60d10844';
	$tgpaswd = $tgpaswd.$salt; //.$t_Email;
	$tgpaswd = sha1($tgpaswd);
	$query = pg_query($dbconn, "SELECT * FROM \"Tour Guide\" WHERE \"g_Email\" = '$tgemail' AND \"g_isActive\" = TRUE");
	$count = pg_num_rows($query);
	
	if($count > 0)
	{
		$row = pg_fetch_array($query);
		$pass = $row['g_password'];
		if($tgpaswd == $pass) {
		//$row = pg_fetch_array($query);
		$_SESSION['tgemail'] = $row['g_Email'];
		$_SESSION['tgid'] = $row['g_key'];
		$_SESSION['tgfname'] = $row['g_FName'];
		$_SESSION['tglname'] = $row['g_LName'];
		$_SESSION['tgpass'] = $row['g_password'];
		$_SESSION['tgcompany'] = $row['Company'];
		$_SESSION['tgdesc'] = $row['g_desc'];
		header("Location: tour-guide-home.php");
		}
	}
	else
	{
		echo "<h2> Oops that email or password combination was incorrect.
		<br /> Please try again. </h2>";
	}
}
else if(!empty($_POST['new-uemail'])||!empty($_POST['new-ufname'])||!empty($_POST['new-ulname'])||!empty($_POST['new-upass'])||isset($_POST['terms']))
{
	if(!empty($_FILES["image"]["name"])&&!empty($_POST['new-uemail'])&&!empty($_POST['new-ufname'])&&!empty($_POST['new-ulname'])&&!empty($_POST['new-upass'])&&isset($_POST['terms']))
	{
				$newuemail =  $_POST['new-uemail'];
				$newufname = $_POST["new-ufname"];
				$newulname = $_POST["new-ulname"];
				$newupass = $_POST['new-upass'];
				$salt = '6e663cc2478ebdc49cbce5609ba0305b60d10844';
				$newupass = $newupass.$salt;//.$t_Email;
				$newupass = sha1($newupass);
				$desc = $_POST['desc'];
				$phone = $_POST['phone'];
				$company = $_POST['company'];
				$license = $_POST['license'];
				if (!filter_var($newuemail, FILTER_VALIDATE_EMAIL)) 
				{
  					$errorMsg .= "<a style=\"color:red\">Invalid email format</a><br>"; 
				}
				else if (!preg_match("/^[a-zA-Z ]*$/",$newufname)&&!preg_match("/^[a-zA-Z ]*$/",$newulname)) 
				{
				  $errorMsg .= "<a style=\"color:red\">Only letters and white space in name are allowed</a>"; 
				}
				else if (!preg_match("/[0-9]/",$phone)) 
				{
				  $errorMsg .= "<a style=\"color:red\">Only numbers in phone are allowed</a>"; 
				}
				else
				{
					$uemail = $newuemail;
					$ufname = $newufname;
					$ulname = $newulname;
					$upass = $newupass;
					$query = pg_query($dbconn, "INSERT INTO \"Tour Guide\" (\"g_Email\", \"g_FName\", \"g_LName\", \"g_License\", \"Company\", \"g_isActive\", \"g_isSuspended\", \"g_password\", \"g_telephone\", \"g_desc\", \"g_BDate\") VALUES('$uemail', '$ufname', '$ulname', '$license','$company', FALSE, FALSE, '$upass', '$phone', '$desc', '1991-01-01') RETURNING \"g_key\"");
					$row = pg_fetch_array($query);
					$g_key = $row['g_key']; 
					if($query)
					{
						//var_dump("Im in!!");
						//var_dump(!file_exists("/images/business/".$uid));
						$uid = $g_key;
						if (!file_exists("images/business/".$uid)) {
   							 mkdir("images/business/".$uid, 0777, true);
						}
					     $target_file = "images/business/".$uid."/1.jpg";
						 $image_name = $_FILES["image"]["name"];
				         $image_type = $_FILES["image"]["type"];
				         $image_size = $_FILES["image"]["size"];
				         $image_tmp_name = $_FILES['image']['tmp_name'];
				         move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
						$to      = $newuemail;
						$subject = 'Verify Email';
						$message = 'Please follow this link to verify your account
						"http://kiwiteam.ece.uprm.edu/NoMiddleMan/website/verifyFormGuide.html"';
						$headers = 'From: luis.tavarez@outlook.com' . "\r\n" .
						'Reply-To: luis.tavarez@outlook.com' . "\r\n" .
						'X-Mailer: PHP/' . phpversion();
						mail($to, $subject, $message, $headers);
						/*$row = pg_fetch_array($query);
						$_SESSION['uid'] = $row['t_key'];*/
						header("Location: index.php");
					}
					else
					{
						$errorMsg = "Could not create account!";
					}
				}
	}
	else if(!isset($_POST['terms']))
	{
		$errorMsg = "Please agree to Terms and Services";
	}
	else
	{
		$errorMsg = "<a style=\"color:red\">Missing fields</a>";
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include 'header.php';?>
</head>
<body>
<div class="container">
    <div class="row-fluid">
<div class = "col-md-6">
<div class="area">
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
</div>
<div class="col-md-6">
                <div class="area">
                    <form class="form-horizontal" method = "post" action = "guide_login.php" enctype="multipart/form-data">
                        <div class="heading">
                            <h4 class="form-heading">Tour Guide Sign Up</h4>
                            <div><font color="red"><?php echo $errorMsg; ?></font></div>
                            <div><font color="blue">A verification email will be sent to you on Sign Up. Please follow link to verify account.</font></div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputFirst">First
                            Name</label>

                            <div class="controls">
                                <input id="inputFirst" name = "new-ufname" placeholder="E.g. Ashwin" type="text">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputLast">Last
                            Name</label>

                            <div class="controls">
                                <input id="inputLast" name = "new-ulname"placeholder="E.g. Hegde" type="text">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputEmail">Email</label>

                            <div class="controls">
                                <input id="inputEmail" name = "new-uemail" placeholder="E.g. ashwinh@cybage.com" type="text">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputPassword">Password</label>

                            <div class="controls">
                                <input id="inputPassword" name = "new-upass" placeholder="Min. 8 Characters" type="password">
                            </div>
                        </div>
						<div class="control-group">
            <label class="control-label" for="inputLast">Company
              Description: </label>
            <textarea id="inputLast" class="form-control" rows="5" name = "desc" placeholder="E.g. Fly away with us..." type="text"></textarea>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputEmail">Image:</label>
            <div class="controls">
              <input type="file" name= "image">
            </div>
          </div>
                        <div class="control-group">
                            <label class="control-label" for="inputEmail">Phone</label>

                            <div class="controls">
                                <input id="inputEmail" maxlength="10" name = "phone" placeholder="787123456" type="text">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="inputEmail">Company name:</label>

                            <div class="controls">
                                <input id="inputEmail" name = "company" placeholder="E.g. Surf School" type="text">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="inputEmail">License Number</label>

                            <div class="controls">
                                <input id="inputEmail" maxlength="10" name = "license" placeholder="123456789" type="text">
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <label class="checkbox"><input type="checkbox" name = "terms" value = 'value1'>
                                I agree all your <a href="http://kiwiteam.ece.uprm.edu/NoMiddleMan/Terms%20and%20Conditions">Terms of
                                Services</a></label> <button class="btn btn-success" type="submit">Sign
                                Up</button> <!--<button class="btn" type="button">Help</button>-->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            </div>
            </div>
</body>
</html>