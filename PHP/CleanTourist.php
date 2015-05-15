<?php
	include_once("dbconnection.php");
	
	pg_query($conn, "DELETE FROM \"Tourist\" 
	Where \"MemberSince\" < (now()-4*interval'1 hour'-1*interval'1 day')::date 
	and \"t_isActive\"=false ");
	
?>