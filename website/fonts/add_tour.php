<?php
if (session_status() == PHP_SESSION_NONE) 
{
	session_start();
}
$hourList= '';
$i = (int)0;
$dateTail='';
$dhour = '12:00 am'; 
$dList = '';
//$time = date("g:i a", strtotime(substr($dhour, 0, -3)));
//$dhour = strtotime("$dhour + 30 mins");
$hourList .=  "<option>".$dhour."</option>";
//$hours[$time] = $dhour;
for($i = 0; $i<47;$i++)
{
	$dhour = date("g:i a", strtotime("$dhour + 30 mins"));
	//$dhour = strtotime("$dhour + 30 mins");
	$hourList .=  '<option>'.$dhour.'</option>';
	//$hours[$time] = $dhour;
}
$j = 0;
for($j = 0; $j < 24; $j++)
{
	for($i = 0; $i<4;$i++)
	{
		if($j!==0)
		$dList .= '<option>'.$j.' hour/s and '.$i*(15).' minutes</option>';
		else if($j==0 && $i==0)
		continue; 
		else
		$dList .= '<option>'.$i*(15).' minutes</option>';
	}
}
include_once("dbConnect.php");
if(!empty($_POST['name'])||!empty($_POST['desc'])||!empty($_POST['image'])||!empty($_POST['duration'])||!empty($_POST['price'])||!empty($_POST['address'])||!empty($_POST['city'])||!empty($_POST['state'])||!empty($_POST['country'])||!empty($_POST['facebook'])||!empty($_POST['youtube'])||!empty($_POST['instagram'])||!empty($_POST['twitter'])||!empty($_POST['extreme'])||!empty($_POST['quantity']))
{
	//var_dump("Here!");
	//if(!empty($_POST['name'])&&!empty($_POST['desc'])&&!empty($_POST['image'])&&!empty($_POST['duration'])&&!empty($_POST['price'])&&!empty($_POST['address'])&&!empty($_POST['city'])&&!empty($_POST['state'])&&!empty($_POST['country'])&&!empty($_POST['facebook'])&&!empty($_POST['youtube'])&&!empty($_POST['instagram'])&&!empty($_POST['twitter'])&&!empty($_POST['extreme'])&&!empty($_POST['quantity']))
	//{
				//$row = pg_fetch_array($query);
				$tourName = $_POST['name'];
				$tdescription = $_POST['desc'];
				//$tid = $row['tour_key'];
				$tprice = $_POST['price'];
				$tcity = $_POST['city'];
				$tstate = $_POST['state'];
				$tduration = $_POST['duration'];
				$uid = $_SESSION['tgid'];
				$category = $_POST['category'];
				$tduration = explode(" ", $tduration);
				if(isset($tduration[3]))
				{
					$tduration = ((int)$tduration[0])*60 + (int)($tduration[3]);
				}
				else
				{
					$tduration = (int)($tduration[0]);
				}
				$taddress = $_POST['address'];
				$tcountry = $_POST['country'];
				$facebook = $_POST['facebook'];
				$instagram = $_POST['instagram'];
				$youtube = $_POST['youtube'];
				$twitter = $_POST['twitter'];
				$extreme = $_POST['extreme'];
				$quantity = $_POST['quantity'];
				$mondayf = $_POST['mondayf'];
				$mondayf = date("H:i", strtotime($mondayf)).":00+00";
				$mondayl = $_POST['mondayl'];
				$mondayl = date("H:i", strtotime($mondayl)).":00+00";
				$tuesdayf = $_POST['tuesdayf'];
				$tuesdayf = date("H:i", strtotime($tuesdayf)).":00+00";
				$tuesdayl = $_POST['tuesdayl'];
				$tuesdayl = date("H:i", strtotime($tuesdayl)).":00+00";
				$wednesdayf = $_POST['wednesdayf'];
				$wednesdayf = date("H:i", strtotime($wednesdayf)).":00+00";
				$wednesdayl = $_POST['wednesdayl'];
				$wednesdayl = date("H:i", strtotime($wednesdayl)).":00+00";
				$thursdayf = $_POST['thursdayf'];
				$thursdayf = date("H:i", strtotime($thursdayf)).":00+00";
				$thursdayl = $_POST['thursdayl'];
				$thursdayl = date("H:i", strtotime($thursdayl)).":00+00";
				$fridayf = $_POST['fridayf'];
				$fridayf = date("H:i", strtotime($fridayf)).":00+00";
				$fridayl = $_POST['fridayl'];
				$fridayl = date("H:i", strtotime($fridayl)).":00+00";
				$saturdayf = $_POST['saturdayf'];
				$saturdayf = date("H:i", strtotime($saturdayf)).":00+00";
				$saturdayl = $_POST['saturdayl'];
				$saturdayl = date("H:i", strtotime($saturdayl)).":00+00";
				$sundayf = $_POST['sundayf'];
				$sundayf = date("H:i", strtotime($sundayf)).":00+00";
				$sundayl = $_POST['sundayl'];
				$sundayl = date("H:i", strtotime($sundayl)).":00+00";
				$lquery = pg_query($dbconn, "SELECT \"L_key\" FROM \"Location\" WHERE \"City\" = '$tcity' AND \"State-Province\"= '$tstate' AND \"Country\" = '$tcountry'");
				$lKey = '';
				if(pg_num_rows($lquery) > 0)
				{
					$row = pg_fetch_array($lquery);
					$lKey = $row['L_key'];
				}
				else
				{
					$lquery = pg_query($dbconn, "INSERT INTO \"Location\" (\"City\", \"State-Province\", \"Country\") VALUES (upper('$tcity'), upper('$tstate'), upper('$tcountry')) RETURNING \"L_key\"");
					$row = pg_fetch_array($lquery);
					$lKey = $row['L_key'];
				}
				
				$query = pg_query($dbconn, "INSERT INTO \"Tour\" (\"tour_Name\", \"tour_Desc\", \"Duration\", \"Price\", \"Facebook\", \"Youtube\", \"Instagram\", \"Twitter\", \"g_key\", \"tour_isActive\", \"tour_isSuspended\", \"L_key\", \"tour_quantity\", \"extremeness\", \"tour_address\", \"autoGen\", \"tour_photo\") VALUES('$tourName', '$tdescription', '$tduration', '$tprice', '$facebook', '$youtube', '$instagram', '$twitter', '$uid', TRUE, FALSE, $lKey, $quantity, $extreme, '$taddress', TRUE, RETURNING \"tour_key\"");
				$row = pg_fetch_array($query);
				$tour_key = $row['tour_key'];
				$query = pg_query($dbconn, "UPDATE \"Tour\" SET 'tour_photo' = 'http://kiwiteam.ece.uprm.edu/NoMiddleMan/website/images/$tour_key/' WHERE 'tour_key' = $tour_key"); 
				$cquery = pg_query($dbconn, "SELECT * FROM \"Tour Category\" WHERE upper(\"Category_Name\") = upper('$category')");
				$cKey = '';
				if(pg_num_rows($cquery) > 0)
				{
					$row = pg_fetch_array($cquery);
					$cKey = $row['cat_key'];
					$cquery = pg_query($dbconn, "INSERT INTO \"isCategory\" (\"cat_key\", \"tour_key\") VALUES ($cKey, $tour_key)");
				}
				else
				{
					$cquery = pg_query($dbconn, "SELECT \"Create_Category\"('$category')");
					$cquery = pg_query($dbconn, "SELECT \"Join_Category\"('$category', $tour_key)");
				}
				$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Monday', '$tour_key', '$mondayf', '$mondayl')");
				$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Tuesday', '$tour_key', '$tuesdayf', '$tuesdayl')");
				$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Wednesday', '$tour_key', '$wednesdayf', '$wednesdayl')");
				$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Thursday', '$tour_key', '$thursdayf', '$thursdayl')");
				$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Friday', '$tour_key', '$fridayf', '$fridayl')");
				$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Saturday', '$tour_key', '$saturdayf', '$saturdayl')");
				$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Sunday', '$tour_key', '$sundayf', '$sundayl')");
				
				$wquery = pg_query($dbconn, "SELECT \"TS_Generate\"($tour_key)");
				//$uploadOk = 1;
				if (!file_exists("images/".$tour_key)) {
    mkdir("images/".$tour_key, 0777, true);
}
				$target_file = "images/".$tour_key."/1.jpg";
				$image_name = $_FILES["image"]["name"];
				$image_type = $_FILES["image"]["type"];
				$image_size = $_FILES["image"]["size"];
				$image_tmp_name = $_FILES['image']['tmp_name'];
				move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
				/*// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" ) {
					echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					$uploadOk = 0;
				}
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					echo "Sorry, your file was not uploaded.";
				// if everything is ok, try to upload file
				} else {
					if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
						echo "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";
					} else {
						echo "Sorry, there was an error uploading your file.";
					}
				}*/
				//$query = pg_query($dbconn, "SELECT 't_key' FROM \"Tourist\"");
				//$_SESSION['uid'] = pg_num_rows($query);
				header("Location: tour-guide-home.php");
	}
	//else
	//{
		//$errorMsg = "Missing fields";
	//}
//}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include 'header.php';?>
</head>
<body>
<?php include 'guide_navbar.php' ?>
<div class="container">
  <div class="row-fluid">
    <div class="col-md-12">
      <div class="area">
        <form class="form-horizontal" method = "post" action = "add_tour.php" enctype="multipart/form-data">
          <div class="heading">
            <h2 class="form-heading">Add a Tour</h2>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputFirst">Tour
              Name</label>
            <div class="controls">
              <input id="inputFirst" name = "name" placeholder="E.g. Bungie Jumping" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputLast">Tour
              Description: </label>
            <textarea id="inputLast" class="form-control" rows="5" name = "desc" placeholder="E.g. Fly away with us..." type="text"></textarea>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputFirst">Category</label>
            <div class="controls">
              <input id="inputFirst" name = "category" placeholder="E.g. Ziplining" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputEmail">Image:</label>
            <div class="controls">
              <input type="file" name= "image">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputEmail">Activity duration:</label>
            <div class="controls">
              <select name = "duration" style = "width:20%" class="form-control">
                <?php echo $dList;?>
              </select>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Schedule:</label>
            <div class="controls">
            <p>*Sessions will be generated automatically between first and last tour session of the day</p> 
              <table class="table">
                <thead>
                  <tr>
                    <th>Day</th>
                    <th>First Session</th>
                    <th>Last Session</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Monday</td>
                    <td><select name = "mondayf" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                    <td><select name = "mondayl" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                  </tr>
                 <tr>
                    <td>Tuesday</td>
                    <td><select name = "tuesdayf" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                    <td><select name = "tuesdayl" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                  </tr>
                  <tr>
                    <td>Wednesday</td>
                    <td><select name = "wednesdayf" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                    <td><select name = "wednesdayl" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                  </tr>
                  <tr>
                    <td>Thursday</td>
                    <td><select name = "thursdayf" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                    <td><select name = "thursdayl" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                  </tr>
                  <tr>
                    <td>Friday</td>
                    <td><select name = "fridayf" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                    <td><select name = "fridayl" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                  </tr>
                  <tr>
                    <td>Saturday</td>
                    <td><select name = "saturdayf" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                    <td><select name = "saturdayl" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                  </tr>
                  <tr>
                    <td>Sunday</td>
                    <td><select name = "sundayf" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                    <td><select name = "sundayl" style = "display:inline" class="form-control">
                <?php echo $hourList;?>
              </select></td>
                  </tr>
                </tbody>
              </table>
              
              <!-- <input id="inputPassword" name = "duration" placeholder="E.g. 3" type="text">--> 
            </div>
            <!--<div style="display:inline-block">
                            <div class="dropdown">
                              <button class="btn btn-default dropdown-toggle" type="button" id="time" data-toggle="dropdown" aria-expanded="true"> 5:00pm <span class="caret"></span> </button>
                              <ul class="dropdown-menu" id="timeList" role="menu" aria-labelledby="dropdownMenu1">
                                <?php echo $hourList;?>
                              </ul>
                            </div>
                          </div>--> 
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Price:</label>
            <div class="controls">
              <input id="inputPassword" name = "price" placeholder="E.g. 300" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Max session quantity:</label>
            <div class="controls">
              <input id="inputPassword" name = "quantity" placeholder="E.g. 10" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Address:</label>
            <div class="controls">
              <input id="inputPassword" name = "address" placeholder="E.g. Carr 3 km. 4" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">City:</label>
            <div class="controls">
              <input id="inputPassword" name = "city" placeholder="E.g. Orlando" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">State/Providence:</label>
            <div class="controls">
              <input id="inputPassword" name = "state" placeholder="E.g. PR" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Country:</label>
            <div class="controls">
              <input id="inputPassword" name = "country" placeholder="Canada" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Facebook link:</label>
            <div class="controls">
              <input id="inputPassword" name = "facebook" placeholder="" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Instagram link:</label>
            <div class="controls">
              <input id="inputPassword" name = "instagram" placeholder="" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Youtube link:</label>
            <div class="controls">
              <input id="inputPassword" name = "youtube" placeholder="" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Twitter link:</label>
            <div class="controls">
              <input id="inputPassword" name = "twitter" placeholder="" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword"> Extremeness:</label><br />
            <select name = "extreme" style = "display:inline; width:10%" class="form-control">
                <option>1</option> <option>2</option><option>3</option><option>4</option>
                <option>5</option>
              </select>
          </div>
          <div style = "margin-top:10px" class="control-group">
            <div class="controls">
              </label>
              <button class="btn btn-success" type="submit">Add Tour</button>
              <!--<button class="btn" type="button">Help</button>--> 
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>