<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_POST['keyword']) && isset($_POST['order']) && isset($_POST['by'])) {
		$keyword = trim($_POST['keyword']);
		$order = trim($_POST['order']);
		$by = trim($_POST['by']);
		
		$inter = "\"Category_Name\"";
		
		if(isset($_POST['cat_refine'])) {
			$cat_refine = $_POST['cat_refine'];
			
			if($cat_refine != "All" && $cat_refine != "") {
			$inter = "'$cat_refine'";
			}
		}
		
		$result = pg_query($conn, "Select T.tour_key as Key, 
		upper(T.\"tour_Name\") as Name, 
		T.\"tour_Desc\" as Description, T.\"Price\" as Price, 
		T.\"extremeness\" as Extremeness, T.\"tour_photo\" as Photo, T.\"avg\" as avg 
		FROM \"Tour Info\" as T 
		Where lower(concat(T.\"tour_Name\",' ',T.\"tour_Desc\")) like lower('%$keyword%') 	
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