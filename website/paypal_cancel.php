<?php 
	session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'header.php';?>
<script>
$(document).ready(function(){
	$("#language").attr("style", "");
});
</script>
</head>
<body>
<?php include 'navbar.php';?>
<h2>We apologize <?php echo $_SESSION['ufname'];?></h2>
<h2> Your order was not placed</h2>
<h4> To retry click the Checkout option of your shopping cart page.</h4>
<div style="margin-right: 20px;margin-left: 20px;" class="list-group"> <?php echo $cartList;?></div>
</body>
</html>