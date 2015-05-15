<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_POST['country']) && isset($_POST['state']) && isset($_POST['city']) && isset($_POST['order']) && isset($_POST['by'])) {
		$country = $_POST['country'];
		$state = $_POST['state'];
		$city = $_POST['city'];
		$order = trim($_POST['order']);
		$by = trim($_POST['by']);
		
		
		if($country == "Any") {
			$country = "\"Country\"";
		} else {
			$temp = $country;
			$country = "'$temp'";
		}
		
		if($state == "Any") {
			$state = "\"State-Province\"";
		} else {
			$temp2 = $state;
			$state = "'$temp2'";
		}
		
		if($city == "Any") {
			$city = "\"City\"";
		} else {
			$temp3 = $city;
			$city = "'$temp3'";
		}
		
		$inter = "\"Category_Name\"";
		
		if(isset($_POST['cat_refine'])) {
			$cat_refine = $_POST['cat_refine'];
			
			if($cat_refine != "All" && $cat_refine != "") {
			$inter = "'$cat_refine'";
			}
		}
		
		$result = pg_query($conn, "Select T.tour_key as Key, upper(T.\"tour_Name\") as Name, 
									T.\"Price\" as Price, T.\"extremeness\" as Extremeness, 
									T.\"tour_photo\" as Photo, T.\"avg\" as avg
									From \"Tour Info\" as T
									Where \"Country\" = $country and 
									\"State-Province\" = $state and 
									\"City\" = $city
									and T.tour_key IN (Select C.tour_key as key	From \"TourAndCategory\" as C Where \"Category_Name\" = $inter)	
									Order By (T.\"$order\") $by");
		
		if(pg_num_rows($result) > 0) {
			$response['tours'] = array();
			$response['categories'] = array();
			
			while($row = pg_fetch_array($result)) {
				$tour = array();
				$tour['key'] = $row['key'];
				$tour_key = $row['key'];
				$tour['name'] = $row['name'];
				$tour['price'] = $row['price'];
				$tour['extremeness'] = $row['extremeness'];
				$tour['photo'] = $row['photo'];
				$tour['avg'] = $row['avg'];
				
				$getCategories = pg_query($conn, "Select \"Category_Name\"
				From \"Tour Category\"
				Natural Join \"isCategory\"
				Where \"tour_key\" = $tour_key");
				
				$category_name_row = pg_fetch_array($getCategories);
				
				$category_name['category_name'] = $category_name_row['Category_Name'];
				
				array_push($response['categories'], $category_name);
				array_push($response['tours'], $tour);
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
		$response['message'] = "Required field(s) is missing";
		
		echo json_encode($response);
	}
	pg_close($conn);
?>