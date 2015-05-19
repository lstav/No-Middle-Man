<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><?php include 'header.php';?></head>
<body>
<?php include 'navbar.php';?>
<div id="myCarousel" class="carousel slide" data-ride="carousel"> 
  <!-- Indicators -->
  <div class="col-lg-6-home-search">
    <form action="search_results.php" method="get"> 
    <div class="input-group">
      
      <input type="text" name="search" class="form-control" placeholder="Extreme search...">
      <span class="input-group-btn">
        <button name="submit" class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
      </span>
      
    </div><!-- /input-group -->
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
		<a class = "navbar-btn btn-danger btn pull-right"> About us</a>
    </div>
</div>
</body>
</html>
