<?php 
// Moved here
function test_input($data)
{
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
include_once("dbConnect.php");
session_start();
$uorders = '';
$porders = '';
if($_SESSION['uemail'])
{
	    $uemail = $_SESSION['uemail'];
		$uid = $_SESSION['uid'];
		$ufname = $_SESSION['ufname'];
		$ulname = $_SESSION['ulname'];
		$upass = $_SESSION['upass'];
		$errorMsg = '';
		$addr = '';
		$telephone = '';
		
		$oldPass = '';
		$result = pg_query($dbconn, "SELECT * FROM \"Tourist\" as T WHERE T.\"t_key\" = $uid");

		if(!empty($result)) {
			if(pg_num_rows($result) > 0) {
				$row = pg_fetch_array($result);
				$uemail = trim($row['t_Email']);
				$ufname = trim($row['t_FName']);
				$ulname = trim($row['t_LName']);
				$upass = trim($row['t_password']);
				$addr = trim($row['t_Address']);
				$telephone = trim($row['t_telephone']);
				
			}
		}
		
		if(!empty($_POST['new-uemail'])||!empty($_POST['new-ufname'])||!empty($_POST['new-ulname'])
			||!empty($_POST['old-upass'])||!empty($_POST['new-upass'])||!empty($_POST['con-new-upass']))
		{
			if(!empty($_POST['new-uemail']))
			{
				$newuemail =  test_input(strip_tags($_POST['new-uemail']));
				if (!filter_var($newuemail, FILTER_VALIDATE_EMAIL)) 
				{
  					$errorMsg = "Invalid email format"; 
				}
				else
				{
					$query = pg_query($dbconn, "UPDATE \"Tourist\" SET \"t_Email\" = '$newuemail' WHERE \"t_Email\" = '$uemail'");
					$uemail = $_SESSION['uemail'] = $newuemail;
				}
			}
			if(!empty($_POST['new-ufname']))
			{
				
				$newufname = test_input(strip_tags($_POST["new-ufname"]));
				if (!preg_match("/^[a-zA-Z ]*$/",$newufname)) 
				{
				  $errorMsg = "Only letters and white space allowed"; 
				}
				else
				{
					$query = pg_query($dbconn, "UPDATE \"Tourist\" SET \"t_FName\" = '$newufname' WHERE \"t_Email\" = '$uemail' AND \"t_FName\" = '$ufname'");
					$ufname = $_SESSION['ufname'] = $newufname;
				}
			}
			if(!empty($_POST['new-ulname']))
			{
				$newulname = test_input(strip_tags($_POST["new-ulname"]));
				if (!preg_match("/^[a-zA-Z ]*$/",$newulname)) 
				{
				  $errorMsg = "Only letters and white space allowed"; 
				}
				else
				{
					$query = pg_query($dbconn, "UPDATE \"Tourist\" SET \"t_LName\" = '$newulname' WHERE \"t_Email\" = '$uemail' AND \"t_LName\" = '$ulname'");
					$ulname = $_SESSION['ulname'] = $newulemail;
				}
			}
			if(!empty($_POST['new-addr']))
			{
				$newuaddr = test_input(strip_tags($_POST["new-addr"]));
				$query = pg_query($dbconn, "UPDATE \"Tourist\" SET \"t_Address" = '$newuaddr' WHERE \"t_Email\" = '$uemail' AND \"t_LName\" = '$ulname'");
			}
			if(!empty($_POST['new-telephone']))
			{
				$newutelephone = test_input(strip_tags($_POST["new-telephone"]));
				$query = pg_query($dbconn, "UPDATE \"Tourist\" SET \"t_telephone" = '$newutelephone' WHERE \"t_Email\" = '$uemail' AND \"t_LName\" = '$ulname'");
			}
			if(!empty($_POST['new-upass']) && !empty($_POST['old-upass']) && !empty($_POST['con-new-upass']))
			{
				$oldupass = strip_tags($_POST['old-upass']);
				$salt = '6e663cc2478ebdc49cbce5609ba0305b60d10844';
				$oldupass = $oldupass.$salt; //.$t_Email;
				$oldupass = sha1($oldupass);
				
				$newupass = strip_tags($_POST['new-upass']);
				$salt = '6e663cc2478ebdc49cbce5609ba0305b60d10844';
				$newupass = $newupass.$salt; //.$t_Email;
				$newupass = sha1($newupass);
				
				$connewupass = strip_tags($_POST['con-new-upass']);
				$salt = '6e663cc2478ebdc49cbce5609ba0305b60d10844';
				$connewupass = $connewupass.$salt; //.$t_Email;
				$connewupass = sha1($connewupass);
				
				if($oldupass == $upass)
				{
					if($newupass == $connewupass) {
						$_SESSION['upass'] = $newupass;
						$query = pg_query($dbconn, "UPDATE \"Tourist\" SET \"t_password\" = '$newupass' WHERE \"t_Email\" = '$uemail'");
					} else {
						$errorMsg = "Passwords do not match";
					}
				}
				else
				{
					$errorMsg = "Invalid password";
					//echo "<h2> Oops that email or password combination was incorrect.<br /> Please try again. </h2>";
					//$upass = $_SESSION['upass'] = $newupass;
				}
			}
		}
					$output = '<div class="control-group">
									<label class="control-label" for="inputEmail">Email</label>
		
									<div class="controls">
										<input id="inputEmail" name = "new-uemail" placeholder="'.$uemail.'" type="text">
									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="inputFirst">First
									Name</label>
		
									<div class="controls">
										<input id="inputFirst" name = "new-ufname" placeholder="'.$ufname.'">
									</div>
								</div>
		
								<div class="control-group">
									<label class="control-label" for="inputLast">Last
									Name</label>
		
									<div class="controls">
										<input id="inputLast" name = "new-ulname" placeholder="'.$ulname.'" type="text">
									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="inputLast">Address</label>
		
									<div class="controls">
										<input id="inputLast" name = "new-addr" placeholder="'.$addr.'" type="text">
									</div>
								</div>
		
								<div class="control-group">
									<label class="control-label" for="inputEmail">Telephone</label>
		
									<div class="controls">
										<input id="inputEmail" name = "new-telephone" placeholder="'.$telephone.'" type="text">
									</div>
								</div>';
								
			$uquery = pg_query($dbconn, "SELECT \"tour_key\", \"City\", \"tour_Desc\", \"State-Province\", \"ts_key\", \"tour_Name\", \"extremeness\" , \"Price\", \"s_Time\",\"Payed\", \"s_isActive\",
		(\"Price\"*\"Payed\") as total
		FROM \"Upcoming Tours\" NATURAL JOIN \"Location\"
		WHERE \"t_key\"=$uid");
			$ucount = pg_num_rows($uquery);
			while($row = pg_fetch_array($uquery))
			{
			  $tid = $row['tour_key'];
			  $tcity = $row['City'];
			  $tstate = $row['State-Province'];
			  $tname = $row['tour_Name'];
			  $tskey = $row['ts_key'];
			  $total = $row['total'];
			 // $tstime = $row['s_Time'];
			  $reserved_time = date("F/d/Y g:i a" , strtotime(substr($row['s_Time'], 0, -3)));
			  $payed = $row['Payed'];
			  $rquery = pg_query($dbconn, "SELECT * FROM \"Review\" WHERE \"ts_key\"='$tskey' AND \"t_key\"='$uid'");
			  $rbutton = '';
			  if(pg_num_rows($rquery) == 0)
			  {
				   $rbutton= '<a style="" class="btn btn-default" href="write_review.php?tid='.$tid.'&tskey='.$tskey.'" type="button">Write Review <span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>';
			  }
			  $tdescription = $row['tour_Desc'];
			  $tprice = $row['Price'];
			  $uorders .= '<article class="search-result row">
			  <div class="col-xs-12 col-sm-12 col-md-3">
				  <a title="Lorem ipsum" class="thumbnail" href="tour_page.php?tid='.$tid.'"><img src="images/'.$tid.'/1.jpg" alt="Lorem ipsum"></a>
			  </div>
			  <div class="col-xs-12 col-sm-12 col-md-2">
				  <ul class="meta-search">
					  <li><span><h7>'.$tcity.'</h7></span></li>
					  <li> <span>'.$tstate.'</span></li>
				  </ul>
			  </div>
			  <div class="col-xs-12 col-sm-12 col-md-7 excerpet">
				  <h3><a title="" href="tour_page.php?tid='.$tid.'">'.$tname.'</a></h3>
				  <p>'.$tdescription.'</p>	
				  <h5>Price per tourist: '.$tprice.'</h5>
				  <h5>Total amount paid: '.$total.'</h5>	
				  <h5>Party of: '.$payed.'</h5>
				  <h5>Reserved time: '.$reserved_time.'</h5>
			  </div>
			  <span class="clearfix borda"></span>
		  </article>';
			}
			$pquery = pg_query($dbconn, "SELECT \"tour_key\", \"City\", \"State-Province\", \"tour_Desc\", \"ts_key\", \"tour_Name\", \"extremeness\" , \"Price\", \"s_Time\",\"Payed\", \"s_isActive\",
		(\"Price\"*\"Payed\") as total
		FROM \"Past Tour\" NATURAL JOIN \"Location\"
		Where \"t_key\"=$uid");
			$pcount = pg_num_rows($pquery);
			while($row = pg_fetch_array($pquery))
			{
			  $rbutton = '';
			  $tid = $row['tour_key'];
			  $tskey = $row['ts_key'];
			  $tcity = $row['City'];
			  $tstate = $row['State-Province'];
			  $tname = $row['tour_Name'];
			  $tdescription = $row['tour_Desc'];
			  $tprice = $row['Price'];
			  $total = $row['total'];
			 // $tstime = $row['s_Time'];
			  $reserved_time = date("F/d/Y g:i a" , strtotime(substr($row['s_Time'], 0, -3)));
			  $payed = $row['Payed'];
			  $rpquery = pg_query($dbconn, "SELECT * FROM \"Review\" WHERE \"ts_key\"='$tskey' AND \"t_key\"='$uid'");
			  if(pg_num_rows($rpquery) == 0)
			  {
				   $rbutton= '<a style="" class="btn btn-default" href="write_review.php?tid='.$uid.'&tskey='.$tskey.'" type="button">Write Review <span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>';
			  }
			  $porders .= '<article class="search-result row">
			  <div class="col-xs-12 col-sm-12 col-md-3">
				  <a title="Lorem ipsum" class="thumbnail"><img src="images/'.$tid.'/1.jpg" alt="Lorem ipsum"></a>
			  </div>
			  <div class="col-xs-12 col-sm-12 col-md-2">
				  <ul class="meta-search">
					  <li><span><h7>'.$tcity.'</h7></span></li>
					  <li> <span>'.$tstate.'</span></li>
				  </ul>
			  </div>
			  <div class="col-xs-12 col-sm-12 col-md-7 excerpet">
				  <h3><a title="">'.$tname.'</a></h3>
				  <p>'.$tdescription.'</p>	
				  <h5>Price per tourist: '.$tprice.'</h5>
				  <h5>Total amount paid: '.$total.'</h5>	
				  <h5>Party of: '.$payed.'</h5>
				  <h5>Reserved time: '.$reserved_time.'</h5>
				  '.$rbutton.'	
			  </div>
			  <span class="clearfix borda"></span>
		  </article>';
			}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include 'header.php';?>
<script>
$(document).ready(function(){
	$("#pass-btn").click(function(){
        $("#password-field").toggle();
    });
});
</script>
</head>
<body>
<?php include 'navbar.php';?>
<div class="container-fluid" style="margin-top: 10px;">
<h1>My Account</h1>
  <div role="tabpanel"> 
    
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="true">Profile</a></li>
      <li role="presentation" class=""><a href="#upcoming-orders" aria-controls="upcoming-orders" role="tab" data-toggle="tab" aria-expanded="false">Upcoming Tours</a></li>
      <li role="presentation" class=""><a href="#past-orders" aria-controls="past-orders" role="tab" data-toggle="tab" aria-expanded="false">Past Tours</a></li>
    </ul>
    
    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="profile">
        <div class="area">
          <form class="form-horizontal" accept-charset="utf-8" method = "post" action="tourist_account.php">
            <div><font color="red"><?php echo $errorMsg; ?></font></div>
            <?php echo $output;?>
            <button type = "button" id = "pass-btn" class="btn btn-default btn-sm" style="margin-top: 5px;">Change Password</button>
            <div id = "password-field" style="display: none;">
              <div class="control-group">
                <label class="control-label" for="inputPassword">Old Password</label>
                <div class="controls">
                  <input id="inputPassword" name="old-upass" placeholder="" type="password">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="inputPassword">New Password</label>
                <div class="controls">
                  <input id="inputPassword" name = "new-upass" placeholder="" type="password">
                </div>
              </div>
			  <div class="control-group">
                <label class="control-label" for="inputPassword">Confirm New Password</label>
                <div class="controls">
                  <input id="inputPassword" name = "con-new-upass" placeholder="" type="password">
                </div>
              </div>
            </div>
            <div class="control-group">
              <div class="controls">
                <button class="btn btn-success" type="submit" style="margin-top: 5px;">Update</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div role="tabpanel" class="tab-pane" id="upcoming-orders">
        <div style="margin-top: 10px;" class="area">
          <?php echo $uorders; ?>
        </div>
      </div>
      <div role="tabpanel" class="tab-pane" id="past-orders">
        <div style="margin-top: 10px;" class="area">
          <?php echo $porders; ?>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>