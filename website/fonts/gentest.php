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
if(!empty($_POST['mondayf']))
{
				$tour_key = 9;
				$mondayf = $_POST['mondayf'];
				$mondayf = date("H:i", strtotime($mondayf)).":00+00";
				var_dump($mondayf);
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
				/*$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Monday', '$tour_key', '$mondayf', '$mondayl')");
				$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Tuesday', '$tour_key', '$tuesdayf', '$tuesdayl')");
				$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Wednesday', '$tour_key', '$wednesdayf', '$wednesdayl')");
				$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Thursday', '$tour_key', '$thursdayf', '$thursdayl')");
				$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Friday', '$tour_key', '$fridayf', '$fridayl')");
				$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Saturday', '$tour_key', '$saturdayf', '$saturdayl')");
				if($sundayf!==$sundayl)
				{
					$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\") VALUES('Sunday', '$tour_key', '$sundayf', '$sundayl')");
				}
				$wquery = pg_query($dbconn, "SELECT \"TS_Generate\"($tour_key)");*/
				echo "entered!";
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
        <form class="form-horizontal" method = "post" action = "gentest.php" enctype="multipart/form-data">
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