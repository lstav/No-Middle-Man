<?php
/*function test_input($data)
{
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}*/
session_start();
include_once("dbConnect.php");
$errorMsg = "";
$lerrorMsg = "";
$uemail = '';
$emailNotification = '';

if(!empty($_POST['uemail'])&&!empty($_POST['password']))
{
	$uemail = strip_tags($_POST["uemail"]);
	$paswd = strip_tags($_POST["password"]);
	$salt = '6e663cc2478ebdc49cbce5609ba0305b60d10844';
	$paswd = $paswd.$salt; //.$t_Email;
	$paswd = sha1($paswd);
	$query = pg_query($dbconn, "SELECT * FROM \"Tourist\" WHERE \"t_Email\" = '$uemail' AND \"t_isActive\" = TRUE");
	$count = pg_num_rows($query);
	
	if($count > 0)
	{
		$row = pg_fetch_array($query);
		$pass = $row['t_password'];
		if($paswd == $pass) {
			$_SESSION['uemail'] = $row['t_Email'];
			$_SESSION['uid'] = $row['t_key'];
			$_SESSION['ufname'] = $row['t_FName'];
			$_SESSION['ulname'] = $row['t_LName'];
			$_SESSION['upass'] = $row['t_password'];
			$_SESSION['isadmin'] = $row['isAdmin'];
			header("Location: index.php");
		} else {
			$lerrorMsg = "<h5> Oops that email or password combination was incorrect.
			<br /> Please try again. </h5>";
		}
		

	}
	else
	{
		$lerrorMsg = "<h5> Oops that email or password combination was incorrect.
			<br /> Please try again. </h5>";
	}
}
else if(!empty($_POST['new-uemail'])||!empty($_POST['new-ufname'])||!empty($_POST['new-ulname'])||!empty($_POST['new-upass'])||isset($_POST['terms']))
{
	if(!empty($_POST['new-uemail'])&&!empty($_POST['new-ufname'])&&!empty($_POST['new-ulname'])&&!empty($_POST['new-upass'])&&isset($_POST['terms'])&&isset($_POST['address'])&&isset($_POST['phone']))
	{
				$newuemail =  $_POST['new-uemail'];
				$newufname = $_POST["new-ufname"];
				$newulname = $_POST["new-ulname"];
				$newupass = $_POST['new-upass'];
				$checkpass = $_POST['new-upass'];
				$address = $_POST['address'];
				$phone = $_POST['phone'];
				$salt = '6e663cc2478ebdc49cbce5609ba0305b60d10844';
				$newupass = $newupass.$salt;//.$t_Email;
				$newupass = sha1($newupass);
				
				$verifCode = substr( md5(rand()), 0, 8);
				$verif = $verifCode;
				$verifCode = $verifCode.$salt;//.$t_Email;
				$verifCode = sha1($verifCode);
				
				if (!filter_var($newuemail, FILTER_VALIDATE_EMAIL)) 
				{
  					$errorMsg .= "<a style=\"color:red\">Invalid email format</a><br>"; 
				}
				else if (!preg_match("/[0-9]/",$phone)) 
				{
				  $errorMsg .= "<a style=\"color:red\">Only numbers in phone are allowed</a>"; 
				}
				else if(strlen($checkpass)<8)
				{
					$errorMsg .= "<a style=\"color:red\">Password not long enough</a>";
				}
				else
				{
					/*$uemail = $_SESSION['uemail'] = $newuemail;
					$ufname = $_SESSION['ufname'] = $newufname;
					$ulname = $_SESSION['ulname'] = $newulname;
					$upass = $_SESSION['upass'] = $newupass;*/
					$query = pg_query($dbconn, "INSERT INTO \"Tourist\" 
					(\"t_Email\", \"t_FName\", \"t_LName\", \"isAdmin\",
					\"t_isActive\", \"t_isSuspended\", \"t_password\", \"t_telephone\", 
					\"t_Address\", \"verification\") VALUES('$newuemail', '$newufname', '$newulname', FALSE, FALSE, 
					FALSE, '$newupass', '$phone', '$address', '$verifCode') RETURNING \"t_key\"");
					if($query)
					{
						$to      = $newuemail;
						$subject = 'Verify Email for No Middle Man';
						$message = "Please follow this link and use this code <b>".$verif."</b> to verify your account in No Middle Man<br><a href='http://kiwiteam.ece.uprm.edu/NoMiddleMan/website/verifyForm.php'>Verify email</a><br>If you are unable to click on the link, copy and paste it on the address bar.";
						$headers = 'From: luis.tavarez@outlook.com' . "\r\n" .
						'Reply-To: luis.tavarez@outlook.com' . "\r\n" .
						'X-Mailer: PHP/' . phpversion().' MIME-Version: 1.0'."\r\n".
						'Content-type: text/html; charset=utf-8' . "\r\n";
						mail($to, $subject, $message, $headers);
						/*$row = pg_fetch_array($query);
						$_SESSION['uid'] = $row['t_key'];*/
						header("Location: emailNotification.php");
						
						$emailNotification = "*A verification email will be sent to you on Sign Up. Please follow link to verify account.";
					}
					else
					{
						$errorMsg = "Could not create account. Email already exists";
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
<head><?php include 'header.php';?></head>
<body>
<?php include 'navbar.php';?>
<div class="container">
    <div class="row-fluid">
            <div class="col-md-6">
                <div class="area">
                    <form action = "login.php" method = "post" enctype ="multipart/form-data" class="form-horizontal">
                        <div class="heading">
                            <h4 class="form-heading">Sign In</h4>
                           <div><font color="red"><?php echo $lerrorMsg; ?></font></div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputUsername">Email</label>

                            <div class="controls">
                                <input name = "uemail" id="inputUsername" placeholder="E.g. ashwinhegde" type="text">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputPassword">Password</label>

                            <div class="controls">
                                <input name = "password" id="inputPassword" placeholder="Min. 8 Characters" type="password">
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <!--<label class="checkbox"><input type="checkbox">
                                Keep me signed in ¦ <a class="btn btn-link" href="#">Forgot my password</a></label>-->
                                <button class="btn btn-success" name = "Submit" type="submit" style="margin-top: 10px;">Sign In</button> <!--<button class="btn" type="button">Help</button>-->
                            </div>
                        </div>

                        
                    </form>
                    <a href="requestPasswordPage.php">Lost password?</a>
                </div>
                <br />
                <a href = "guide_login.php"> Tour guide? Sign in here.</a>
                <img src="images/NOMITransparentGIMP.png" width="25%" height="25%" style="
    width: 25%;
    height: 25%;
">
            </div>

            <div class="col-md-6">
                <div class="area">
                    <form class="form-horizontal" method = "post" action = "login.php">
                        <div class="heading">
                            <h4 class="form-heading">Sign Up</h4>
							<div><font color="red"><?php echo $errorMsg; ?></font></div>
                            <div><font color="blue">* Please fill out all fields</font></div>
                            <div><font color="blue">* A verification email will be sent to you on Sign Up. Please follow link to verify account.</font></div>
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
                            <label class="control-label" for="inputEmail">Address</label>

                            <div class="controls">
                                <input id="inputEmail" name = "address" placeholder="Carr 2.3" type="text">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="inputEmail">Phone </label>

                            <div class="controls">
                                <input id="inputEmail" maxlength="10" name = "phone" placeholder="787123456" type="text">
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