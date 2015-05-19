<?php 
session_start();
// Moved here
$tskey = '';
$tourName = '';
$errorMsg = '';
include_once("dbConnect.php");
if(!empty($_POST['report'])||!empty($_POST['g_key']))
{
  if(!empty($_POST['report'])&&!empty($_POST['g_key']))
  {
	  	  $g_key = $_POST['g_key'];
		  $report = $_POST['report'];
		  $query = pg_query("SELECT \"ReportFromGuide\"($g_key, '$report')");
		  if($query)
		  header("Location: guide_account.php");
		  else
		  	echo "Could not generate Report";
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