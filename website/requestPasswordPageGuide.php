<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include 'header.php';?>
</head>
<body>
<?php include 'guide_navbar_login.php';?>
<script>
$(document).ready(function(){
	$("#language").attr("style", "");
});
</script>
<div class="container">
	<h2>Enter your tour guide email to recover password.</h2>
	<h4>An email will be sent with new password.</h4>
    <form action="requestPasswordChangeGuide.php" method="post">
 <div class="input-group" style="
    width: 400px;
">
      <input type="text" class="form-control" name="g_Email" placeholder="Email">
      <span class="input-group-btn">
        <button class="btn btn-default" type="submit">Submit</button>
      </span>
    </div><!-- /input-group --></form>
</div>
</body>
</html>
		