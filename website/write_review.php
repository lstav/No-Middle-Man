<?php 
session_start();
// Moved here
$tskey = '';
$tourName = '';
$errorMsg = '';
include_once("dbConnect.php");
if(isset($_GET['tskey']))
{
	$tskey = $_GET['tskey'];
	$tid = (int)$_GET['tid'];
	$query = pg_query("SELECT \"tour_Name\" FROM \"Tour\" WHERE \"tour_key\"='$tid'");
	while($row = pg_fetch_array($query))
	{
		$tourName = $row['tour_Name'];
	}
	
}
if(!empty($_POST['new-tdesc'])||!empty($_POST['new-rating']))
{
  if(!empty($_POST['new-tdesc'])&&!empty($_POST['new-rating']))
  {
	  //var_dump($_POST);
		  $uid = $_SESSION['uid'];
		  $tskey = $_POST['tskey'];
		  $desc = $_POST['new-tdesc'];
		  $rating = (int)$_POST['new-rating'];
		  $query = pg_query("INSERT INTO \"Review\" (\"t_key\",\"ts_key\",\"Text\",\"Rate\")
									  Values($uid,$tskey,'$desc',$rating)");
		  header("Location: tourist_account.php");
  }
  else
  {
	  $errorMsg = 'Missing fields';
  }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include 'header.php';?>
</head>
<body>
<?php include 'navbar.php';?>
<div class="container-fluid" style="margin-top: 10px;">
<h1>Write a Review for: <?php echo $tourName; ?></h1>
<div><font color="red"><?php echo $errorMsg; ?></font></div>
<form method = "post" action = "write_review.php">
<div class="control-group">
                            <label class="control-label" for="inputLast">Tour
                            Rating: <div id='rating'></div></label> 
</div>
<input type="hidden" name="tskey" value="<?php echo $tskey;?>">
<input type="hidden" id = "new-rating" name="new-rating" value = 0 />
<div class="control-group">
                            <label class="control-label" for="inputLast">Tour
                            Description: </label>
							<textarea id="inputLast" class="form-control" rows="5" name = "new-tdesc" type="text"></textarea>
                        </div>  
                        <div style = "margin-top:10px" class="control-group">
                            <div class="controls">
                               </label> <button class="btn btn-success" type="submit">Submit Review</button> <!--<button class="btn" type="button">Help</button>-->
                            </div>
                        </div>
</form>
</div>
</body>
<script>$.fn.raty.defaults.path = 'images'; $('#rating').raty();
$("#rating").click(function(){
    $('#new-rating').val($('#rating').raty('score'));                             //record clicked
});
</script>
</html>