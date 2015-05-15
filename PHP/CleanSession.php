<?php
	include_once("dbconnection.php");
	
	pg_query($conn, "DELETE FROM \"Tour Session\" 
	Where \"ts_key\" 
	NOT IN(Select \"ts_key\" From \"Participants\") and \"s_Time\"<now()-interval '4 hour'  ");
	
	pg_query($conn,"DELETE FROM \"Tour Session\" 
	WHERE \"s_isActive\"=false and \"ts_key\" 
	NOT IN(SELECT \"ts_key\" FROM \"Participants\" NATURAL JOIN \"Tour Session\") ");
	
	pg_query($conn,"DELETE FROM \"Participants\" 
	WHERE \"p_isActive\"=false AND \"Payed\"=0 ")
?>