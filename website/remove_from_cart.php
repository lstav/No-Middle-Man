<?php
include_once("dbConnect.php");
$t_key  = (int)$_GET['t_key'];
$ts_key = (int)$_GET['ts_key'];
$query = pg_query($dbconn, "select deactivate_cart_item($t_key,$ts_key)");
header("Location: cart.php");
?>