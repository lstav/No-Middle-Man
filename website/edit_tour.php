<?php
include_once("dbConnect.php");
$form = array();
if (session_status() == PHP_SESSION_NONE) 
{
	session_start();
}

//var_dump($_GET);
$hourList = array();
$smondayf = '';
$smondayl = '';
$stuesdayf = '';
$stuesdayl = '';
$swednesdayf = '';
$swednesdayl = '';
$sthursdayf = '';
$sthursdayl = '';
$sfridayf = '';
$sfridayl = '';
$ssaturdayf = '';
$ssaturdayl = '';
$ssundayf = '';
$ssundayl = '';

$hourList['mondayf'] =  '';
$hourList['tuesdayf'] =  '';
$hourList['wednesdayf'] =  '';
$hourList['thursdayf'] =  '';
$hourList['fridayf'] =  '';
$hourList['saturdayf'] =  '';
$hourList['sundayf'] =  '';
$hourList['mondayl'] =  '';
$hourList['tuesdayl'] =  '';
$hourList['wednesdayl'] =  '';
$hourList['thursdayl'] =  '';
$hourList['fridayl'] =  '';
$hourList['saturdayl'] =  '';
$hourList['sundayl'] =  '';

$checkMark = array();
$checkMark['monday'] = '';
$checkMark['tuesday'] = '';
$checkMark['wednesday'] = '';
$checkMark['thursday'] = '';
$checkMark['friday'] = '';
$checkMark['saturday'] = '';
$checkMark['sunday'] = '';

$isInDB = array();
$isInDB['monday'] = '';
$isInDB['tuesday'] = '';
$isInDB['wednesday'] = '';
$isInDB['thursday'] = '';
$isInDB['friday'] = '';
$isInDB['saturday'] = '';
$isInDB['sunday'] = '';



if(isset($_GET["tid"]))
{
	$tour_key = $_GET['tid'];
	$query = pg_query($dbconn, "SELECT * FROM \"Tour\" NATURAL JOIN \"Location\" NATURAL JOIN \"Tour Category\" NATURAL JOIN \"isCategory\" WHERE \"tour_key\"= '$tour_key'");
	$row = pg_fetch_array($query);
	$form['name'] = $row['tour_Name'];
	$form['desc'] = $row['tour_Desc'];
	$form['price'] = $row['Price'];
	$form['city'] = $row['City'];
	$form['state'] = $row['State-Province'];
	$form['country'] = $row['Country'];
	$form['duration'] = $row['Duration'];
	$form['address'] = $row['tour_address'];
	$form['category'] = $row['Category_Name'];
	$form['quantity'] = $row['tour_quantity'];
	$form['facebook'] = $row['Facebook'];
	$form['instagram'] = $row['Instagram'];
	$form['youtube'] = $row['Youtube'];
	$form['twitter'] = $row['Twitter'];
	$form['extremeness'] = $row['extremeness'];
	$query = pg_query($dbconn, "SELECT DISTINCT \"State-Province\" FROM \"Location\"");
	while($row = pg_fetch_array($query))
	{
		$state = $row['State-Province'];
		if($state == $form['state'])
			$stateList .= '<option selected>'.$state.'</option>';
		else
			$stateList .= '<option>'.$state.'</option>';
	}

	$query = pg_query($dbconn, "SELECT DISTINCT \"Country\" FROM \"Location\"");
	while($row = pg_fetch_array($query))
	{
		$country = $row['Country'];
		if($country == $form['country'])
			$countryList .= '<option selected>'.$country.'</option>';
		else
			$countryList .= '<option>'.$country.'</option>';
	}

	$query = pg_query($dbconn, "SELECT DISTINCT \"City\" FROM \"Location\"");
	while($row = pg_fetch_array($query))
	{
		$city = $row['City'];
		if($city == $form['city'])
			$cityList .= '<option selected>'.$city.'</option>';
		else
			$cityList .= '<option>'.$city.'</option>';
	}
	$query = pg_query($dbconn, "SELECT \"active\",\"dayname\", \"startTime\",\"endTime\" FROM \"Workdays\" WHERE \"tour_key\" = '$tour_key'");
	while($row = pg_fetch_array($query))
	{
		$dayname=trim($row['dayname']);
		if($dayname == 'Monday')
		{
			$isInDB['monday'] = "true";
			$smondayf = $row['startTime'];
			$smondayl = $row['endTime'];
			$active = $row['active'];
			if(($smondayf !== $smondayl) && $active == 't')
			{
				$checkMark['monday'] = "checked";
			} 
		}
		else if($dayname == 'Tuesday')
		{
			$isInDB['tuesday'] = "true";
			$stuesdayf = $row['startTime'];
			$stuesdayl = $row['endTime'];
			$active = $row['active'];
			if(($stuesdayf !== $stuesdayl)&& $active == 't')
			{
				$checkMark['tuesday'] = "checked";
			}
		}
		else if($dayname == 'Wednesday')
		{
			$isInDB['wednesday'] = "true";
			$swednesdayf = $row['startTime'];
			$swednesdayl = $row['endTime'];
			$active = $row['active'];
			if(($swednesdayf !== $swednesdayl)&& $active == 't')
			{
				$checkMark['wednesday'] = "checked";
			}
		}
		else if($dayname == 'Thursday')
		{
			$isInDB['thursday'] = "true";
			$sthursdayf = $row['startTime'];
			$sthursdayl = $row['endTime'];
			$active = $row['active'];
			if(($sthursdayf !== $sthursdayl)&& $active == 't')
			{
				$checkMark['thursday'] = "checked";
			}
		}
		else if($dayname == 'Friday')
		{
			$isInDB['friday'] = "true";
			$sfridayf = $row['startTime'];
			$sfridayl = $row['endTime'];
			$active = $row['active'];
			
			if(($sfridayf !== $sfridayl) && $active == 't')
			{
				$checkMark['friday'] = "checked";
			}
		}
		else if($dayname == 'Saturday')
		{
			$isInDB['saturday'] = "true";
			$ssaturdayf = $row['startTime'];
			$ssaturdayl = $row['endTime'];
			$active = $row['active'];
			if(($ssaturdayf !== $ssaturdayl)&&$active == 't')
			{
				$checkMark['saturday'] = "checked";
			}
		}
		else if($dayname == 'Sunday')
		{
			$isInDB['sunday'] = "true";
			$ssundayf = $row['startTime'];
			$ssundayl = $row['endTime'];
			$active = $row['active'];
			if(($ssundayf !== $ssundayl)&&$active == 't')
			{
				$checkMark['sunday'] = "checked";
			}
		}
	}	
}
else
{
	header("Location: tour-guide-home.php");
}
for($i = 0; $i < 5; $i++)
{
	//var_dump((int)$form['extremeness'] == ($i + 1));
	if((int)$form['extremeness'] == ($i + 1))
		$extremeList .= "<option selected>".($i+1)."</option>";
	else
		$extremeList .= "<option>".($i+1)."</option>";
}

$i = (int)0;
$dateTail='';
$dhour = '12:00 am'; 
$dList = '';
//$time = date("g:i a", strtotime(substr($dhour, 0, -3)));
//$dhour = strtotime("$dhour + 30 mins");
$hourList['mondayf'] .=  '<option>'.$dhour.'</option>';
	$hourList['tuesdayf'] .=  '<option>'.$dhour.'</option>';
	$hourList['wednesdayf'] .=  '<option>'.$dhour.'</option>';
	$hourList['thursdayf'] .=  '<option>'.$dhour.'</option>';
	$hourList['fridayf'] .=  '<option>'.$dhour.'</option>';
	$hourList['saturdayf'] .=  '<option>'.$dhour.'</option>';
	$hourList['sundayf'] .=  '<option>'.$dhour.'</option>';
	$hourList['mondayl'] .=  '<option>'.$dhour.'</option>';
	$hourList['tuesdayl'] .=  '<option>'.$dhour.'</option>';
	$hourList['wednesdayl'] .=  '<option>'.$dhour.'</option>';
	$hourList['thursdayl'] .=  '<option>'.$dhour.'</option>';
	$hourList['fridayl'] .=  '<option>'.$dhour.'</option>';
	$hourList['saturdayl'] .=  '<option>'.$dhour.'</option>';
	$hourList['sundayl'] .=  '<option>'.$dhour.'</option>';
//$hours[$time] = $dhour;
for($i = 0; $i<95;$i++)
{
	$dhour = date("g:i a", strtotime("$dhour + 15 mins"));
	$hourInStamp =  date("H:i", strtotime($dhour)).":00+00";
	//First time
	//var_dump($smonday);
	if($smondayf == $hourInStamp)
	$hourList['mondayf'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['mondayf'] .=  '<option>'.$dhour.'</option>';
	if($stuesdayf == $hourInStamp)
	$hourList['tuesdayf'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['tuesdayf'] .=  '<option>'.$dhour.'</option>';
	if($swednesdayf == $hourInStamp)
	$hourList['wednesdayf'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['wednesdayf'] .=  '<option>'.$dhour.'</option>';
	if($sthursdayf == $hourInStamp)
	$hourList['thursdayf'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['thursdayf'] .=  '<option>'.$dhour.'</option>';
	if($sfridayf == $hourInStamp)
	$hourList['fridayf'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['fridayf'] .=  '<option>'.$dhour.'</option>';
	if($ssaturdayf == $hourInStamp)
	$hourList['saturdayf'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['saturdayf'] .=  '<option>'.$dhour.'</option>';
	if($ssundayf == $hourInStamp)
	$hourList['sundayf'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['sundayf'] .=  '<option>'.$dhour.'</option>';
	
	//Last session end time
	if($smondayl == $hourInStamp)
	$hourList['mondayl'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['mondayl'] .=  '<option>'.$dhour.'</option>';
	if($stuesdayl == $hourInStamp)
	$hourList['tuesdayl'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['tuesdayl'] .=  '<option>'.$dhour.'</option>';
	if($swednesdayl == $hourInStamp)
	$hourList['wednesdayl'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['wednesdayl'] .=  '<option>'.$dhour.'</option>';
	if($sthursdayl == $hourInStamp)
	$hourList['thursdayl'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['thursdayl'] .=  '<option>'.$dhour.'</option>';
	if($sfridayl == $hourInStamp)
	$hourList['fridayl'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['fridayl'] .=  '<option>'.$dhour.'</option>';
	if($ssaturdayl == $hourInStamp)
	$hourList['saturdayl'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['saturdayl'] .=  '<option>'.$dhour.'</option>';
	if($ssundayl == $hourInStamp)
	$hourList['sundayl'] .=  '<option selected>'.$dhour.'</option>';
	else
	$hourList['sundayl'] .=  '<option>'.$dhour.'</option>';
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

if(!empty($_POST['name'])||!empty($_POST['desc'])||!empty($_POST['image'])||!empty($_POST['duration'])||!empty($_POST['price'])||!empty($_POST['address'])||!empty($_POST['city'])||!empty($_POST['state'])||!empty($_POST['country'])||!empty($_POST['facebook'])||!empty($_POST['youtube'])||!empty($_POST['instagram'])||!empty($_POST['twitter'])||!empty($_POST['extreme'])||!empty($_POST['quantity']))
{
	//var_dump($_POST);
	//var_dump("Here!");
	//if(!empty($_POST['name'])&&!empty($_POST['desc'])&&!empty($_POST['image'])&&!empty($_POST['duration'])&&!empty($_POST['price'])&&!empty($_POST['address'])&&!empty($_POST['city'])&&!empty($_POST['state'])&&!empty($_POST['country'])&&!empty($_POST['facebook'])&&!empty($_POST['youtube'])&&!empty($_POST['instagram'])&&!empty($_POST['twitter'])&&!empty($_POST['extreme'])&&!empty($_POST['quantity']))
	//{
				//$row = pg_fetch_array($query);
				$tour_key = $_POST['tour_key'];
				$tourName = $_POST['name'];
				//var_dump($tourName);
				$tdescription = $_POST['desc'];
				//$tid = $row['tour_key'];
				$tprice = $_POST['price'];
				$tcity = $_POST['city'];
				$tstate = $_POST['state'];
				$tduration = $_POST['duration'];
				$uid = $_SESSION['tgid'];
				$category = $_POST['category'];
				$tduration = explode(" ", $tduration);
				
				$query = pg_query($dbconn, "SELECT \"active\",\"dayname\", \"startTime\",\"endTime\" FROM \"Workdays\" WHERE \"tour_key\" = '$tour_key'");
	while($row = pg_fetch_array($query))
	{
		$dayname=trim($row['dayname']);
		if($dayname == 'Monday')
		{
			$isInDB['monday'] = "true";
			$smondayf = $row['startTime'];
			$smondayl = $row['endTime'];
			$active = $row['active'];
			if(($smondayf !== $smondayl) && $active == 't')
			{
				$checkMark['monday'] = "checked";
			} 
		}
		else if($dayname == 'Tuesday')
		{
			$isInDB['tuesday'] = "true";
			$stuesdayf = $row['startTime'];
			$stuesdayl = $row['endTime'];
			$active = $row['active'];
			if(($stuesdayf !== $stuesdayl)&& $active == 't')
			{
				$checkMark['tuesday'] = "checked";
			}
		}
		else if($dayname == 'Wednesday')
		{
			$isInDB['wednesday'] = "true";
			$swednesdayf = $row['startTime'];
			$swednesdayl = $row['endTime'];
			$active = $row['active'];
			if(($swednesdayf !== $swednesdayl)&& $active == 't')
			{
				$checkMark['wednesday'] = "checked";
			}
		}
		else if($dayname == 'Thursday')
		{
			$isInDB['thursday'] = "true";
			$sthursdayf = $row['startTime'];
			$sthursdayl = $row['endTime'];
			$active = $row['active'];
			if(($sthursdayf !== $sthursdayl)&& $active == 't')
			{
				$checkMark['thursday'] = "checked";
			}
		}
		else if($dayname == 'Friday')
		{
			$isInDB['friday'] = "true";
			$sfridayf = $row['startTime'];
			$sfridayl = $row['endTime'];
			$active = $row['active'];
			
			if(($sfridayf !== $sfridayl) && $active == 't')
			{
				$checkMark['friday'] = "checked";
			}
		}
		else if($dayname == 'Saturday')
		{
			$isInDB['saturday'] = "true";
			$ssaturdayf = $row['startTime'];
			$ssaturdayl = $row['endTime'];
			$active = $row['active'];
			if(($ssaturdayf !== $ssaturdayl)&&$active == 't')
			{
				$checkMark['saturday'] = "checked";
			}
		}
		else if($dayname == 'Sunday')
		{
			$isInDB['sunday'] = "true";
			$ssundayf = $row['startTime'];
			$ssundayl = $row['endTime'];
			$active = $row['active'];
			if(($ssundayf !== $ssundayl)&&$active == 't')
			{
				$checkMark['sunday'] = "checked";
			}
		}
	}
				
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
				
				$checkmonday = $_POST['checkmonday'];
				$checktuesday = $_POST['checktuesday'];
				$checkwednesday = $_POST['checkwednesday'];
				$checkthursday = $_POST['checkthursday'];
				$checkfriday = $_POST['checkfriday'];
				$checksaturday = $_POST['checksaturday'];
				$checksunday = $_POST['checksunday'];
				
				$lquery = pg_query($dbconn, "SELECT \"L_key\" FROM \"Location\" WHERE \"City\" = upper('$tcity') AND \"State-Province\"= upper('$tstate') AND \"Country\" = upper('$tcountry')");
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
				$tourName = pg_escape_string($tourName);
				$tdescription = str_replace(array("'", "\"", "&quot;"), "", $tdescription);
				$query = pg_query($dbconn, "UPDATE \"Tour\" SET (\"tour_Name\", \"tour_Desc\", \"Duration\", \"Price\", \"Facebook\", \"Youtube\", \"Instagram\", \"Twitter\", \"g_key\", \"tour_isActive\", \"tour_isSuspended\", \"L_key\", \"tour_quantity\", \"extremeness\", \"tour_address\", \"autoGen\") = ('$tourName', '$tdescription', '$tduration', '$tprice', '$facebook', '$youtube', '$instagram', '$twitter', '$uid', TRUE, FALSE, $lKey, $quantity, $extreme, '$taddress', TRUE) WHERE \"tour_key\"='$tour_key'");
				//$row = pg_fetch_array($query);
				//$tour_key = $row['tour_key'];
				
				/*$cquery = pg_query($dbconn, "SELECT * FROM \"Tour Category\" NATURAL JOIN \"isCategory\" WHERE upper(\"Category_Name\") = upper('$category') AND \"tour_key\" = '$tour_key'");
				$cKey = '';
				if(pg_num_rows($cquery) == 0)
				{
					$cquery = pg_query($dbconn, "SELECT \"Create_Category\"('$category')");
					$cquery = pg_query($dbconn, "SELECT \"Join_Category\"('$category', $tour_key)");
				}*/
				
				if($checkmonday) {
					if($checkMark['monday'] == "checked" || $isInDB['monday'] == "true")
					{
						$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") 
						= ('Monday', '$tour_key', '$mondayf', '$mondayl', TRUE) WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Monday'");
					}
					else
					{
						$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") 
						VALUES ('Monday', '$tour_key', '$mondayf', '$mondayl', TRUE)");
					}
				}
				else
				{
					$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET (\"active\") = (FALSE) WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Monday'");
				}
				
				if($checktuesday)
				{
					//var_dump($checkMark['tuesday'] == "checked" || $isInDB['tuesday'] == 'true');
					if($checkMark['tuesday'] == "checked" || $isInDB['tuesday'] == 'true')
					{
						$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") = ('Tuesday', '$tour_key', '$tuesdayf', '$tuesdayl', TRUE) WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Tuesday'");
					}
					else
					{
						$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") VALUES ('Tuesday', '$tour_key', '$tuesdayf', '$tuesdayl', TRUE)");
					}
					
				}
				else{
					$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET (\"active\") = (FALSE) WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Tuesday'");
				}
				
				if($checkwednesday)
				{
						if($checkMark['wednesday'] == "checked" || $isInDB['wednesday'] == 'true')
					{
						$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") = ('Wednesday', '$tour_key', '$wednesdayf', '$wednesdayl', TRUE) WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Wednesday'");
					}
					else
					{
						$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") VALUES ('Wednesday', '$tour_key', '$wednesdayf', '$wednesdayl', TRUE)");
					}
				}
				else {
					$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET (\"active\") = (FALSE) WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Wednesday'");
				}
				
				if($checkthursday)
				{
					if($checkMark['thursday'] == "checked" || $isInDB['thursday'] == 'true')
					{
						$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") = ('Thursday', '$tour_key', '$thursdayf', '$thursdayl', TRUE) WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Thursday'");
					}
					else
					{
						$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") VALUES ('Thursday', '$tour_key', '$thursdayf', '$thursdayl', TRUE)");
					}
				}
				else
				{
					$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET (\"active\") = (FALSE) WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Thursday'");
				}
				if($checkfriday)
				{
					if($checkMark['friday'] == "checked" || $isInDB['friday'] == 'true')
					{
						$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") = ('Friday', '$tour_key', '$fridayf', '$fridayl', TRUE) WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Friday'");
					}
					else
					{
						$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") VALUES ('Friday', '$tour_key', '$fridayf', '$fridayl', TRUE)");
					}
				}
				else
				{
					
				$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET \"active\" = FALSE WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Friday'");
				
				}
				
				
				if($checksaturday)
				{
					if($checkMark['saturday'] == "checked" || $isInDB['saturday'] == 'true')
					{
						$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") = ('Saturday', '$tour_key', '$saturdayf', '$saturdayl', TRUE) WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Saturday'");
					}
					else
					{
						$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") VALUES ('Saturday', '$tour_key', '$saturdayf', '$saturdayl', TRUE)");
					}
				}
				else
				{
					
				$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET (\"active\") = (FALSE) WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Saturday'");
				}
				if($checksunday)
				{
					if($checkMark['sunday'] == "checked" || $isInDB['sunday'] == 'true')
					{
						$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") = ('Sunday', '$tour_key', '$sundayf', '$sundayl', TRUE) WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Sunday'");
					}
					else
					{
						$wquery = pg_query($dbconn, "INSERT INTO \"Workdays\" (\"dayname\", \"tour_key\", \"startTime\", \"endTime\", \"active\") VALUES ('Sunday', '$tour_key', '$sundayf', '$sundayl', TRUE)");
					}
				}
				else
				{
					$wquery = pg_query($dbconn, "UPDATE \"Workdays\" SET (\"active\") = (FALSE) WHERE \"tour_key\" = '$tour_key' AND \"dayname\" = 'Sunday'");
				}
				
				$wquery = pg_query($dbconn, "SELECT \"TS_Generate\"($tour_key)");
				//$uploadOk = 1;
				if(is_uploaded_file($_FILES["image"]["tmp_name"]))
				{
					$query = pg_query($dbconn, "UPDATE \"Tour\" SET \"tour_photo\" = 'http://kiwiteam.ece.uprm.edu/NoMiddleMan/website/images/$tour_key/' WHERE \"tour_key\" = $tour_key"); 
					if (!file_exists("images/".$tour_key)) {
    mkdir("images/".$tour_key, 0777, true);
}						
					$target_file = "images/".$tour_key."/1.jpg";
					if(file_exists($target_file)&&is_uploaded_file($_FILES["image"]["tmp_name"])){
    				chmod($target_file,0777); //Change the file permissions if allowed
    				unlink($target_file); //remove the file
					}
					$image_name = $_FILES["image"]["name"];
					$image_type = $_FILES["image"]["type"];
					$image_size = $_FILES["image"]["size"];
					$image_tmp_name = $_FILES['image']['tmp_name'];
					move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
				}
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
        <form class="form-horizontal" method = "post" action = "edit_tour.php" enctype="multipart/form-data">
          <div class="heading">
            <h2 class="form-heading">Edit: <?php echo $form['name'];?></h2>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputFirst">Tour
              Name</label>
            <div class="controls">
              <input id="inputFirst" name = "name" value = "<?php echo $form['name'];?>" placeholder="<?php echo $form['name'];?>" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputLast">Tour
              Description: </label>
            <textarea id="inputLast" class="form-control" value = "<?php echo $form['desc'];?>" rows="5" name = "desc" placeholder="<?php echo $form['desc'];?>" type="text"><?php echo $form['desc'];?></textarea>
          </div>
          <!--<div class="control-group">
            <label class="control-label" for="inputFirst">Category</label>
            <div class="controls">
              <input id="inputFirst" name = "category" value = "<?php echo $form['category'];?>" placeholder="<?php echo $form['category'];?>" type="text">
            </div>
          </div>-->
          <div class="control-group">
            <label class="control-label" for="inputEmail">Image:</label>
            <div class="controls">
              <input type="file" name= "image">
            </div>
          </div>
          <!--<div class="control-group">
            <label class="control-label" for="inputEmail">Activity duration:</label>
            <div class="controls">
              <select name = "duration" style = "width:20%" class="form-control">
                <?php //echo $dList;?>
              </select>
            </div>
          </div>-->
          <div class="control-group">
            <label class="control-label" for="inputPassword">Schedule:</label>
            <div class="controls">
            <p>*Sessions will be generated automatically between first and last tour session of the day. If a tourist is already scheduled in a session the tour will not be canceled when you change </p> 
              <table class="table">
                <thead>
                  <tr>
					<th>Selected</th>
					<th>Day</th>
                    <th>First Session Start Time</th>
                    <th>Last Session End Time</th>
                     
                  </tr>
                </thead>
                <tbody>
                  <tr>
				  <td class="col-md-1" style="text-align: center"><input type="checkbox" name = "checkmonday" value = 'value1' <?php echo $checkMark['monday'];?>>
                       </td>
                    <td>Monday</td>
                    <td><select name = "mondayf" style = "display:inline" class="form-control">
                <?php echo $hourList['mondayf'];?>
              </select></td>
                    <td><select name = "mondayl" style = "display:inline" class="form-control">
                <?php echo $hourList['mondayl'];?>
              </select></td>

                  </tr>
                 <tr>
					<td class="col-md-1" style="text-align: center"><input type="checkbox" name = "checktuesday" value = 'value1' <?php echo $checkMark['tuesday'];?>>
                           </td>
                    <td>Tuesday</td>
                    <td><select name = "tuesdayf" style = "display:inline" class="form-control">
                <?php echo $hourList['tuesdayf'];?>
              </select></td>
                    <td><select name = "tuesdayl" style = "display:inline" class="form-control">
                <?php echo $hourList['tuesdayl'];?>
              </select></td>
                  </tr>
                  <tr>
					<td class="col-md-1" style="text-align: center"><input type="checkbox" name = "checkwednesday" value = 'value1' <?php echo $checkMark['wednesday'];?>>
                           </td>
                    <td>Wednesday</td>
                    <td><select name = "wednesdayf" style = "display:inline" class="form-control">
                <?php echo $hourList['wednesdayf'];?>
              </select></td>
                    <td><select name = "wednesdayl" style = "display:inline" class="form-control">
                <?php echo $hourList['wednesdayl'];?>
              </select></td>

                  </tr>
                  <tr>
				  <td class="col-md-1" style="text-align: center"><input type="checkbox" name = "checkthursday" value = 'value1' <?php echo $checkMark['thursday'];?>> 
                           </td>
                    <td>Thursday</td>
				
                    <td><select name = "thursdayf" style = "display:inline" class="form-control">
                <?php echo $hourList['thursdayf'];?>
              </select></td>
                    <td><select name = "thursdayl" style = "display:inline" class="form-control">
                <?php echo $hourList['thursdayl'];?>
              </select></td>

                  </tr>
                  <tr>
				  <td class="col-md-1" style="text-align: center"><input type="checkbox" name = "checkfriday" value = 'value1' <?php echo $checkMark['friday'];?>>
                           </td>
                    <td>Friday</td>
					
                    <td><select name = "fridayf" style = "display:inline" class="form-control">
                <?php echo $hourList['fridayf'];?>
              </select></td>
                    <td><select name = "fridayl" style = "display:inline" class="form-control">
                <?php echo $hourList['fridayl'];?>
              </select></td>

                  </tr>
                  <tr>
				  <td class="col-md-1" style="text-align: center"><input type="checkbox" name = "checksaturday" value = 'value1' <?php echo $checkMark['saturday'];?>>
                           </td>
                    <td>Saturday</td>
					
                    <td><select name = "saturdayf" style = "display:inline" class="form-control">
                <?php echo $hourList['saturdayf'];?>
              </select></td>
                    <td><select name = "saturdayl" style = "display:inline" class="form-control">
                <?php echo $hourList['saturdayl'];?>
              </select></td>

                  </tr>
                  <tr>
				  <td class="col-md-1" style="text-align: center"><input type="checkbox" name = "checksunday" value = 'value1' <?php echo $checkMark['sunday'];?>>
                           </td>
                    <td>Sunday</td>
					
                    <td><select name = "sundayf" style = "display:inline" class="form-control">
                <?php echo $hourList['sundayf'];?>
              </select></td>
                    <td><select name = "sundayl" style = "display:inline" class="form-control">
                <?php echo $hourList['sundayl'];?>
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
                                <?php //echo $hourList;?>
                              </ul>
                            </div>
                          </div>--> 
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Price:</label>
            <div class="controls">
              <input id="inputPassword" name = "price" value = "<?php echo $form['price'];?>" placeholder="<?php echo $form['price'];?>" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Max session quantity:</label>
            <div class="controls">
              <input id="inputPassword" name = "quantity" value = "<?php echo $form['quantity'];?>" placeholder="<?php echo $form['quantity'];?>" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Address:</label>
            <div class="controls">
              <input id="inputPassword" name = "address" value = "<?php echo $form['address'];?>" placeholder="<?php echo $form['address'];?>" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">City:</label>
            <div class="controls">
              <select name = "city" style = "width:20%;display:inline" class="form-control">
                <?php echo $cityList;?>
              </select>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">State/Providence:</label>
            <div class="controls">
              <select name = "state" style = "width:20%;display:inline" class="form-control">
                <?php echo $stateList;?>
              </select>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Country:</label>
           <div class = "controls">
            <select name = "country" style = "width:20%;display:inline" class="form-control">
                <?php echo $countryList;?>
              </select>
           </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Facebook link:</label>
            <div class="controls">
              <input id="inputPassword" name = "facebook" value = "<?php echo $form['facebook'];?>" placeholder="<?php echo $form['facebook'];?>" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Instagram link:</label>
            <div class="controls">
              <input id="inputPassword" name = "instagram" value = "<?php echo $form['instagram'];?>" placeholder="<?php echo $form['instagram'];?>" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Youtube link:</label>
            <div class="controls">
              <input id="inputPassword" name = "youtube" value = "<?php echo $form['youtube'];?>" placeholder="<?php echo $form['youtube'];?>" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Twitter link:</label>
            <div class="controls">
              <input id="inputPassword" name = "twitter" value = "<?php echo $form['twitter'];?>" placeholder="<?php echo $form['twitter'];?>" type="text">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputPassword"> Extremeness:</label><br />
            <select name = "extreme" style = "display:inline; width:10%" class="form-control">
                <?php echo $extremeList; ?>
              </select>
          </div>
          <div style = "margin-top:10px" class="control-group">
            <div class="controls">
              </label>
              <button class="btn btn-success" type="submit">Edit/Activate Tour</button>
              <!--<button class="btn" type="button">Help</button>--> 
            </div>
          </div>
          <div style = "margin-top:10px" class="control-group">
            <div class="controls">
              </label>
              <a class="btn btn-danger" data-toggle="modal" type = "button" data-target="#myModal">Deactivate Tour</a>
              <!--<button class="btn" type="button">Help</button>--> 
            </div>
          </div>
          <input type="hidden" name="tour_key" value="<?php echo $tour_key?>">
           <input type="hidden" name="duration" value="<?php echo $form['duration'];?>">
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Are you sure your wish to deactivate tour?</h4>
      </div>
      <div class="modal-body">
       The tour will not be visible to your tourists
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <a href = "delete_tour.php?tour_key=<?php echo $tour_key;?>"><button type="button" class="btn btn-primary">Deactivate Tour</button></a>
      </div>
    </div>
  </div>
</div>

</body>
</html>