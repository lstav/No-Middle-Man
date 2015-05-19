<?php
include_once("dbConnect.php");
$output = '';
$dropdown = '';
$ratingList = '';
$searchq = '';
$query = pg_query($dbconn, "SELECT * FROM \"Tour Category\"");
while($row = pg_fetch_array($query))
{
	$category = $row['Category_Name'];
	$categoryList .= ' <li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$category.'">'.$category.'</a></li>';
}
if(isset($_GET['search'])||isset($_GET['tsort']))
{
	//var_dump($_GET);
	if(isset($_GET['search']))
	{
		$searchq = $_GET['search'];
		//$searchq = preg_replace("#[^0-9a-z]#i", "", $searchq);
		$query = pg_query($dbconn, "SELECT * FROM \"Tour Info\" NATURAL JOIN \"isCategory\" NATURAL JOIN \"Tour Category\" WHERE lower(\"tour_Name\") LIKE lower('%$searchq%') OR lower(\"Category_Name\") LIKE lower('%$searchq%') OR lower(\"City\") LIKE lower('%$searchq%') OR lower(\"State-Province\") LIKE lower('%$searchq%')");
	}
	if(isset($_GET['tsort']))
	{
			$searchq = $_GET['search'];
			//$searchq = preg_replace("#[^0-9a-z]#i", "", $searchq);
			//$tsort = substr($_GET['tsort'], 1, -1);
			$tsort = $_GET['tsort'];
			$order = $_GET['order'];
			//var_dump($tsort);
			$query = pg_query($dbconn, "SELECT * FROM \"Tour Info\" NATURAL JOIN \"isCategory\" NATURAL JOIN \"Tour Category\" WHERE lower(\"tour_Name\") LIKE lower('%$searchq%') OR lower(\"Category_Name\") LIKE lower('%$searchq%') OR lower(\"City\") LIKE lower('%$searchq%') OR lower(\"State-Province\") LIKE lower('%$searchq%') ORDER BY \"$tsort\" $order");
	}
	
	$count = pg_num_rows($query);
	if($count == 0)
	{
		$output = 'No extreme results';
	}
	else
	{
		$dropdown = '<li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$searchq.'&tsort=tour_Name">Name</a></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$searchq.'&tsort=Price&order=ASC">Price: Lowest to Highest</a>
	<li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$searchq.'&tsort=Price&order=DESC">Price: Highest to Lowest</a></li>';
		while($row = pg_fetch_array($query))
		{
			$tname = $row['tour_Name'];
			$tdescription = $row['tour_Desc'];
			$tid = $row['tour_key'];
			$tprice = $row['Price'];
			$tcity = $row['City'];
			$tstate = $row['State-Province'];
			$rquery = pg_query($dbconn, "SELECT AVG(\"Rate\"), COUNT(*) FROM \"Review\" NATURAL JOIN \"Tour Session\" WHERE \"tour_key\" = $tid");
			$ratingRow = pg_fetch_array($rquery);
			$trating = $ratingRow['avg'];
			$rcount = $ratingRow['count'];
			$trating = round($trating, 1);
			$ratingList .= '$("#rating'.$tid.'").raty({ readOnly: true, score:'.$trating.' });';
			$output .= '<article class="search-result row">
			<div class="col-xs-12 col-sm-12 col-md-3">
				<a title="Lorem ipsum" class="thumbnail" href="tour_page.php?tid='.$tid.'"><img src="images/'.$tid.'/1.jpg" alt="Lorem ipsum"></a>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-2">
				<ul class="meta-search">
					<li><span><h7>'.$tcity.'</h7></span></li>
					<li> <span>'.$tstate.'</span></li>
					<li><div style = "float:left" id="rating'.$tid.'"></div><a href = "#">('.$rcount.')</a></li> 
					
					
				</ul>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-7 excerpet">
				<h3><a href="tour_page.php?tid='.$tid.'" title="">'.$tname.'</a></h3>
				<p>'.$tdescription.'</p>						
                <span style="text-align : right"><h6> Starting at </h6>  <h4>'.$tprice.'</h4></span>
			</div>
			<span class="clearfix borda"></span>
		</article>';
		}
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><?php include 'header.php';?></head>
<body>
<?php include 'navbar.php';?>
<div style = "margin-top: 10px;" class="container">
  <div style = "margin-bottom: 10px" class="col-lg-6">
    <form action="search_results.php" method="get">
      <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Extreme search...">
        <span class="input-group-btn">
        <button name="submit" class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
        </span> </div>
      <!-- /input-group -->
    </form>
    <!-- /input-group --> 
  </div>
  <!-- /.col-lg-6 -->
  <hgroup class="mb20">
  <div class="dropdown" style="float:right">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
    Sort by:
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
   <?php echo $dropdown;?>
  </ul>
</div>
<div class="dropdown" style="float:right
;margin-right: 20px;">
                <button class="btn btn-default dropdown-toggle" type="button" id="year" data-toggle="dropdown" aria-expanded="false" style="
    margin-left: 0px;
    margin-top: 0px;
">Categories<span class="caret"></span> </button>
                <ul class="dropdown-menu" id="yearList" role="menu" aria-labelledby="dropdownMenu1">
                 <?php echo $categoryList; ?>
                </ul>
              </div>
    <h1>Search Results</h1>
    <h2 class="lead"><strong class="text-danger"><?php echo $count;?></strong> results were found for the search for <strong class="text-danger">
      <?php if(isset($searchq)) echo $searchq;?>
      </strong></h2>
  </hgroup>
  <section class="col-xs-12 col-sm-6 col-md-12">
    <?php 
		 if(isset($output))
		 echo $output ?>
  </section>
</div>
<script>$.fn.raty.defaults.path = 'images'; <?php echo $ratingList;?></script>
<script>$.fn.raty.defaults.path = 'images/extremeness'; <?php echo $ratingList;?></script>
</body>
</html>
