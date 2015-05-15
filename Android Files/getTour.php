<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_POST['tour_key'])) {
		$tour_key = $_POST['tour_key'];
		
		/*$result = pg_query($conn, "select \"tour_key\" as key, \"tour_Name\" as name, \"tour_Desc\" as description,
		\"Facebook\" as facebook, \"Youtube\" as youtube, \"Instagram\" as instagram, \"Twitter\" as twitter, 
		\"Price\" as price, \"tour_quantity\" as quantity, \"extremeness\" as extremeness, \"tour_photo\" as photo, 
		\"g_Email\" as gemail, \"g_FName\" as gfname, \"g_LName\" as glname, \"g_License\" as license, \"Company\" as company,
		\"g_telephone\" as telephone, \"avg\" as averagerate, \"count\" as ratecount, \"tour_address\" as address,
		\"State-Province\" as state, \"City\" as city, \"Country\" as country
		from \"Tour Info\" where \"tour_key\"=$tour_key");*/
		
		$result = pg_query($conn, "SELECT \"Tour Guide\".g_telephone as telephone, 
		\"Tour Guide\".g_key, \"Tour Guide\".g_desc, \"Tour Guide\".\"Company\" as company, \"Tour Guide\".\"g_Email\" gemail, 
		\"Tour Guide\".\"g_FName\" as gfname, \"Tour Guide\".\"g_LName\" as glname, \"Tour Guide\".\"g_License\" as license, \"Tour\".tour_photo as photo, 
		\"Tour\".extremeness as extremeness, 
		\"Tour\".tour_address as address, \"Tour\".tour_quantity as quantity, \"Tour\".\"L_key\", \"Tour\".\"Price\" as price, 
		\"Tour\".\"Twitter\" as twitter, \"Tour\".\"Youtube\" as youtube, \"Tour\".\"Facebook\" as facebook, \"Tour\".tour_key as key, \"Tour\".\"Instagram\" as instagram,
		\"Tour\".\"tour_Desc\" as description,
		\"Tour\".\"tour_Name\" as name, \"Location\".\"State-Province\" as state, \"Location\".\"City\" as city, \"Location\".\"Country\" as country, \"Rating\".avg as averagerate,
		\"Rating\".count as ratecount
		FROM \"Tour Guide\"
		JOIN \"Tour\" ON \"Tour Guide\".g_key = \"Tour\".g_key
		JOIN \"Location\" ON \"Tour\".\"L_key\" = \"Location\".\"L_key\"
		NATURAL JOIN \"Rating\"
		WHERE \"tour_key\"=$tour_key");
		
		if(!empty($result)) {
			if(pg_num_rows($result) > 0) {
				$row = pg_fetch_array($result);
				
				$response['tour'] = array();
				
				$tour = array();
				$tour['key'] = $row['key'];
				$tour['name'] = $row['name'];
				$tour['description'] = $row['description'];
				$tour['facebook'] = $row['facebook'];
				$tour['youtube'] = $row['youtube'];
				$tour['instagram'] = $row['instagram'];
				$tour['twitter'] = $row['twitter'];
				$tour['price'] = $row['price'];				
				$tour['extremeness'] = $row['extremeness'];
				$tour['photo'] = $row['photo'];
				$tour['address'] = $row['address']."".$row['city'].", ".$row['state'].", ".$row['country'];
				$tour['gemail'] = $row['gemail'];
				$tour['gname'] = $row['gfname']." ".$row['glname'];
				$tour['license'] = $row['license'];
				$tour['company'] = $row['company'];
				$tour['telephone'] = $row['telephone'];
				$tour['averagerate'] = $row['averagerate'];
				$tour['ratecount'] = $row['ratecount'];
				
				$response['success'] = 1;
				
				array_push($response['tour'], $tour);
				
				//echo json_encode($response);
			} else {
				$response['success'] = 0;
				$response['message'] = "No missing tour information";
				
				echo json_encode($response);
			}
		} else {
				$response['success'] = 0;
				$response['message'] = "No tour found";
				
				echo json_encode($response);
			}
	} else {
		$response['success'] = 0;
		$response['message'] = "Required field(s) is missing";
		
		echo json_encode($response);
	}
	
	if(isset($_POST['tour_key'])) {
		$tour_key = $_POST['tour_key'];
		
		
		
		$result = pg_query($conn, "select \"ts_key\" as tskey, \"s_Time\" as time, \"Availability\" as availability
		from \"Tour Session\" where \"tour_key\" = $tour_key and \"s_isActive\" = true and \"s_Time\" > (now()-interval '4 hour') and \"Availability\" > 0
		Order by (\"s_Time\") ASC");
		
		if(!empty($result)) {
			if(pg_num_rows($result) > 0) {
				$response['sessions'] = array();
				
				while($row = pg_fetch_array($result)) {
				
					$tour = array();
					$tour['tskey'] = $row['tskey'];
					$tour['time'] = date("g:i A" , strtotime(substr($row['time'], 0, -3)));
					$tour['date'] = date("M-d-Y", strtotime($row['time']));
					$tour['availability'] = $row['availability'];
					array_push($response['sessions'], $tour);
				}
				$response['success'] = 1;
				
				//echo json_encode($response);
			} else {
				$response['success'] = 0;
				$response['message'] = "No tours found";
				
				echo json_encode($response);
			}
		} else {
				$response['success'] = 0;
				$response['message'] = "No tourist found";
				
				echo json_encode($response);
			}
	} else {
		$response['success'] = 0;
		$response['message'] = "Required field(s) is missing";
		
		echo json_encode($response);
	}
	
	if(isset($_POST['tour_key'])) {
		$tour_key = $_POST['tour_key'];
		
		
		
		$result = pg_query($conn, "Select \"tour_key\", \"t_key\", \"ts_key\", \"Text\" as review, 
		\"Rate\" as rating, \"Date\" as date From \"All Reviews\" Where \"tour_key\" = $tour_key 
		Order by (\"Date\") DESC");
		
		if(!empty($result)) {
			if(pg_num_rows($result) > 0) {
				
				$response['reviews'] = array();
				
				while($row = pg_fetch_array($result)) {
					$tour = array();
					$tour['tour_key'] = $row['tour_key'];
					$tour['t_key'] = $row['t_key'];
					$tour['ts_key'] = $row['ts_key'];
					$tour['review'] = $row['review'];
					$tour['rating'] = $row['rating'];
					$tour['time'] = date("g:i:s A" , strtotime($row['date']))." ".date("M-d-Y", strtotime($row['date']));
					array_push($response['reviews'], $tour);
				}
				
				$response['success'] = 1;		
				
				echo json_encode($response);
			} else {
				$response['success'] = 0;
				$response['message'] = "No tours found";
				
				echo json_encode($response);
			}
		} else {
				$response['success'] = 0;
				$response['message'] = "No tourist found";
				
				echo json_encode($response);
			}
	} else {
		$response['success'] = 0;
		$response['message'] = "Required field(s) is missing";
		
		echo json_encode($response);
	}
	
	pg_close($conn);
?>