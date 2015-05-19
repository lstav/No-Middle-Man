<?php
if(isset($_POST['myform']))
{
	var_dump("im in!");
				if (!file_exists("images/yeah")) {
    mkdir("images/yeah", 0777, true);
}
				$target_file = "images/yeah/1.jpg";
				$image_name = $_FILES["image"]["name"];
				$image_type = $_FILES["image"]["type"];
				$image_size = $_FILES["image"]["size"];
				$image_tmp_name = $_FILES['image']['tmp_name'];
				move_uploaded_file($image_tmp_name, $target_file);
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
	
	//else
	//{
		//$errorMsg = "Missing fields";
	//}
//}
}
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
        <form class="form-horizontal" name='myform' method = "post" action = "imagetest.php" enctype="multipart/form-data">
          <div class="heading">
            <h2 class="form-heading">Add a Tour</h2>
          </div>
          
          <div class="control-group">
            <label class="control-label" for="inputEmail">Image:</label>
            <div class="controls">
              <input type="file" name= "image">
            </div>
          </div>
        
          <div style = "margin-top:10px" class="control-group">
            <div class="controls">
              </label>
              <button class="btn btn-success" type="submit">Add Tour</button>
              <!--<button class="btn" type="button">Help</button>--> 
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>