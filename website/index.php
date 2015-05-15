<?php 
include_once("dbConnect.php");
$cityList = '';
$categoryList = '';
$query = pg_query($dbconn, "SELECT * FROM \"Tour Category\" Order By \"Category_Name\" ASC");
while($row = pg_fetch_array($query))
{
	$category = $row['Category_Name'];
	$categoryList .= ' <li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search=&filter='.$category.'">'.$category.'</a></li>';
}
$query = pg_query($dbconn, "SELECT DISTINCT \"City\" FROM \"Location\" NATURAL JOIN \"Tour\" Order By \"City\" Asc");
while($row = pg_fetch_array($query))
{
	$city = $row['City'];
	$cityList .= '<li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$city.'">'.$city.'</a></li>';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><?php include 'header.php';?></head>
<body>
<?php include 'navbar.php';?>
<div id="myCarousel" class="carousel slide" data-ride="carousel"> 
  <!-- Indicators -->
  <div class="col-lg-6-home-search">
    <form action="search_results.php" enctype="multipart/form-data" method="get"> 
    <div class="input-group">
      
      <input type="text" name="search" class="form-control" placeholder="Extreme search by destination, tour category. . .">
      <span class="input-group-btn">
        <button name="submit" class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
      </span>
      
    </div><!-- /input-group -->
    <div class="dropdown" style="
    margin-top: 10px; display:inline-block
">
                <button class="btn btn-default dropdown-toggle" type="button" id="year" data-toggle="dropdown" aria-expanded="false" style="
    margin-left: 0px;
    margin-top: 0px;
">Or Choose Category<span class="caret"></span> </button>
                <ul class="dropdown-menu" id="yearList" role="menu" aria-labelledby="dropdownMenu1">
                 <?php echo $categoryList; ?>
                </ul>
              </div>
     <div class="dropdown" style="
    margin-top: 10px;  margin-left: 20px; display:inline-block
">
                <button class="btn btn-default dropdown-toggle" type="button" id="year" data-toggle="dropdown" aria-expanded="false" style="
    margin-left: 0px;
    margin-top: 0px;
">Or Choose City<span class="caret"></span> </button>
                <ul class="dropdown-menu" id="yearList" role="menu" aria-labelledby="dropdownMenu1">
                 <?php echo $cityList; ?>
                </ul>
              </div>
    </form>
  </div><!-- /.col-lg-6 -->
  <ol class="carousel-indicators">
    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
    <li data-target="#myCarousel" data-slide-to="1"></li>
    <li data-target="#myCarousel" data-slide-to="2"></li>
    <li data-target="#myCarousel" data-slide-to="3"></li>
  </ol>
  
  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <div class="item active"> <img src="images/slideshow/1.jpg" alt="Chania"> </div>
    <div class="item"> <img src="images/slideshow/2.jpg" class="img-responsive" alt="Aguadilla Skydiving"> </div>
    <div class="item"> <img src="images/slideshow/3.jpg" class="img-responsive" alt="Aguadilla Skydiving"> </div>
    <div class="item"> <img src="images/slideshow/4.jpg" class="img-responsive" alt="Aguadilla Skydiving"> </div>
  </div>
  <!-- Left and right controls --> 
  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> <span class="sr-only">Previous</span> </a> <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next"> <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> <span class="sr-only">Next</span> </a> </div>

&nbsp
&nbsp

<!--<div class="container">
  <div class="row">
    <div class="col-sm-4">
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales metus semper, consectetur dolor vel, sodales nibh. Vivamus sit amet magna nulla. Donec tristique faucibus nunc, a porttitor leo. Fusce ac cursus justo.  </p>
    </div>
    <div class="col-sm-4">
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales metus semper, consectetur dolor vel, sodales nibh. Vivamus sit amet magna nulla. Donec tristique faucibus nunc, a porttitor leo. Fusce ac cursus justo.  </p>
    </div>
    <div class="col-sm-4">
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales metus semper, consectetur dolor vel, sodales nibh. Vivamus sit amet magna nulla. Donec tristique faucibus nunc, a porttitor leo. Fusce ac cursus justo.  </p>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-4">
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales metus semper, consectetur dolor vel, sodales nibh. Vivamus sit amet magna nulla. Donec tristique faucibus nunc, a porttitor leo. Fusce ac cursus justo.  </p>
    </div>
    <div class="col-sm-4">
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales metus semper, consectetur dolor vel, sodales nibh. Vivamus sit amet magna nulla. Donec tristique faucibus nunc, a porttitor leo. Fusce ac cursus justo.  </p>
    </div>
    <div class="col-sm-4">
     <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales metus semper, consectetur dolor vel, sodales nibh. Vivamus sit amet magna nulla. Donec tristique faucibus nunc, a porttitor leo. Fusce ac cursus justo.  </p>
    </div>
  </div>
</div>-->

<div class = "navbar navbar-default navbar-static-bottom">
	<div class = "container">
    	<p class = "navbar-text pull-left">Happy Adventuring!</p>
		<a href = "about_us.php" class = "navbar-btn btn-danger btn pull-right"> About us</a>
    </div>
</div>
</body>
</html>
