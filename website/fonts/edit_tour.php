<?php
function test_input($data)
{
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
session_start();
include_once("dbConnect.php");
if(!empty($_POST['new-tname'])||!empty($_POST['new-tdesc'])||!empty($_POST['new-timage'])||!empty($_POST['new-tduration'])||!empty($_POST['new-tprice'])||!empty($_POST['new-taddress'])||!empty($_POST['new-tcity'])||!empty($_POST['new-tstate'])||!empty($_POST['new-tcountry']))
{
	if(!empty($_POST['new-tname'])&&!empty($_POST['new-tdesc'])&&!empty($_POST['new-timage'])&&!empty($_POST['new-tduration'])&&!empty($_POST['new-tprice'])&&!empty($_POST['new-taddress'])&&!empty($_POST['new-tcity'])&&!empty($_POST['new-tstate'])&&!empty($_POST['new-tcountry']))
	{
				$row = pg_fetch_array($query);
				$tourName = $_POST['new-tname'];
				$tdescription = $_POST['new-tdesc'];
				//$tid = $row['tour_key'];
				$tprice = $_POST['new-tprice'];
				$tcity = $_POST['new-tcity'];
				$tstate = $_POST['new-tstate'];
				$tduration = $_POST['new-tduration'];
				$taddress = $_POST['new-taddress'];
				$tcountry = $_POST['new-tcountry'];
				$tgcompany = $_SESSION['tgcompany'];
				$query = pg_query($dbconn, "INSERT INTO \"Tour\" (\"tour_Name\", \"tour_Desc\", \"Duration\", \"Price\", \"address\", \"stateprovidence\", \"city\", \"country\", \"Company\") VALUES('$tourName', '$tdescription', '$tduration', '$tprice', $tstate, $tcity, $tcountry, '$tgcompany')");
				$query = pg_query($dbconn, "SELECT 't_key' FROM \"Tourist\"");
				$_SESSION['uid'] = pg_num_rows($query);
				header("Location: index.php");
	}
	else
	{
		$errorMsg = "Missing fields";
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><?php include 'header.php';?></head>
<body>
<?php include 'guide_navbar.php' ?>
<div class="container">
    <div class="row-fluid">
            <div class="col-md-12">
                <div class="area">
                    <form class="form-horizontal" method = "post" action = "add_tour.php">
                        <div class="heading">
                            <h2 class="form-heading">Edit Tour</h2>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputFirst">Tour
                            Name</label>
							
                            <div class="controls">
                                <input id="inputFirst" name = "new-tname" placeholder="E.g. Bungie Jumping" type="text">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputLast">Tour
                            Description: </label>
							<textarea id="inputLast" class="form-control" rows="5" name = "new-tdesc" placeholder="E.g. Fly away with us..." type="text"></textarea>
                        </div>
						
                        <div class="control-group">
                            <label class="control-label" for="inputEmail">Image:</label>

                            <div class="controls">
                                <input type="file" name= "new-timage">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputPassword">Duration:</label>

                            <div class="controls">
                                <input id="inputPassword" name = "new-tduration" placeholder="E.g. 3" type="text">
                            </div>
                        </div>
						
                        <div class="control-group">
                            <label class="control-label" for="inputPassword">Price:</label>

                            <div class="controls">
                                <input id="inputPassword" name = "new-tprice" placeholder="E.g. 300" type="text">
                            </div>
                        </div>
                                              
                        <div class="control-group">
                            <label class="control-label" for="inputPassword">Address:</label>

                            <div class="controls">
                                <input id="inputPassword" name = "new-taddress" placeholder="E.g. Carr 3 km. 4" type="text">
                            </div>
                        </div>
                        
                        <div class="control-group">
                            <label class="control-label" for="inputPassword">City:</label>

                            <div class="controls">
                                <input id="inputPassword" name = "new-tcity" placeholder="E.g. Orlando" type="text">
                            </div>
                        </div>
                        
                        <div class="control-group">
                            <label class="control-label" for="inputPassword">State/Providence:</label>

                            <div class="controls">
                                <input id="inputPassword" name = "new-tstate" placeholder="E.g. PR" type="text">
                            </div>
                        </div>
                        
                        <div class="control-group">
                            <label class="control-label" for="inputPassword">Country:</label>

                            <div class="controls">
                                <input id="inputPassword" name = "new-tcountry" placeholder="Canada" type="text">
                            </div>
                        </div>
                        
                        <div style = "margin-top:10px" class="control-group">
                            <div class="controls">
                               </label> <button class="btn btn-success" type="submit">Add Tour</button> <!--<button class="btn" type="button">Help</button>-->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</div>
</body>
</html>