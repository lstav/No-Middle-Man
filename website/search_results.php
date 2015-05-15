<?php
include_once("dbConnect.php");
$output = '';
$dropdown = '';
$ratingList = '';
$ratingListE = '';
$searchq = '';
$categoryList = '';
$cityList = '';
//$query = pg_query($dbconn, "SELECT * FROM \"Tour Category\" Order By \"Category_Name\" Asc");
//while($row = pg_fetch_array($query))
//{
	//$category = $row['Category_Name'];
	//$categoryList .= ' <li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$category.'">'.$category.'</a></li>';
//}
$query = pg_query($dbconn, "SELECT DISTINCT \"City\" FROM \"Location\" Order By \"City\" Asc");
while($row = pg_fetch_array($query))
{
	$city = $row['City'];
	$cityList .= '<li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$city.'">'.$city.'</a></li>';
}
if(isset($_GET['search'])||isset($_GET['tsort']))
{
	//var_dump($_GET);
	if(isset($_GET['search']))
	{
		$searchq = $_GET['search'];
		$categoryList .= ' <li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$searchq.'&filter=ALL">ALL</a></li>';
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
	else if(isset($_GET['filter']))
	{
			$searchq = $_GET['search'];
			//$searchq = preg_replace("#[^0-9a-z]#i", "", $searchq);
			//$tsort = substr($_GET['tsort'], 1, -1);
			$tcategory = $_GET['filter'];
			if($tcategory == "ALL")
			{
				$query = pg_query($dbconn, "SELECT * FROM \"Tour Info\" NATURAL JOIN \"isCategory\" NATURAL JOIN \"Tour Category\" WHERE lower(\"tour_Name\") LIKE lower('%$searchq%') OR lower(\"Category_Name\") LIKE lower('%$searchq%') OR lower(\"City\") LIKE lower('%$searchq%') OR lower(\"State-Province\") LIKE lower('%$searchq%')");
			}
			else
			{
				$query = pg_query($dbconn, "SELECT * FROM \"Tour Info\" NATURAL JOIN \"isCategory\" NATURAL JOIN \"Tour Category\" WHERE (lower(\"tour_Name\") LIKE lower('%$searchq%') OR lower(\"Category_Name\") LIKE lower('%$searchq%') OR lower(\"City\") LIKE lower('%$searchq%') OR lower(\"State-Province\") LIKE lower('%$searchq%')) AND (lower(\"Category_Name\") = lower('$tcategory'))");
			}
	}
	$count = pg_num_rows($query);
	if($count == 0)
	{
		$output = 'No extreme results';
	}
	else
	{
		$dropdown = '<li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$searchq.'&tsort=tour_Name&order=ASC">A-Z</a></li>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$searchq.'&tsort=tour_Name&order=DESC">Z-A</a></li>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$searchq.'&tsort=Price&order=ASC">Price: Lowest to Highest</a>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$searchq.'&tsort=Price&order=DESC">Price: Highest to Lowest</a></li>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$searchq.'&tsort=extremeness&order=ASC">Extremeness: Lowest to Highest</a>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$searchq.'&tsort=extremeness&order=DESC">Extremeness: Highest to Lowest</a></li>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$searchq.'&tsort=avg&order=ASC">Rating: Lowest to Highest</a>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$searchq.'&tsort=avg&order=DESC">Rating: Highest to Lowest</a></li>';
		while($row = pg_fetch_array($query))
		{
			$tname = $row['tour_Name'];
			$tdescription = $row['tour_Desc'];
			$tour_photo = trim($row['tour_photo']);
			$tid = $row['tour_key'];
			$tprice = $row['Price'];
			$tcity = $row['City'];
			$tstate = $row['State-Province'];
			$extremeness = $row['extremeness'];
			$rquery = pg_query($dbconn, "SELECT AVG(\"Rate\"), COUNT(*) FROM \"Review\" NATURAL JOIN \"Tour Session\" WHERE \"tour_key\" = $tid");
			$ratingRow = pg_fetch_array($rquery);
			$trating = $ratingRow['avg'];
			$rcount = $ratingRow['count'];
			$trating = round($trating, 1);
			$ratingList .= '$("#rating'.$tid.'").raty({ readOnly: true, score:'.$trating.' });';
			$ratingListE .= '$("#ratingE'.$tid.'").raty({ readOnly: true, score:'.$extremeness.' });';
			
			$category = $row['Category_Name'];
			$categoryList .= ' <li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$searchq.'&filter='.$category.'">'.$category.'</a></li>';
			
			$city = $row['City'];
	$cityList .= '<li role="presentation"><a role="menuitem" tabindex="-1" href="search_results.php?search='.$city.'">'.$city.'</a></li>';
			
			
			$output .= '<article class="search-result row">
			<div class="col-xs-12 col-sm-12 col-md-3">
				<a title="Lorem ipsum" class="thumbnail" href="tour_page.php?tid='.$tid.'"><img src="'.$tour_photo.'1.jpg" alt="Lorem ipsum"></a>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-2">
				<ul class="meta-search">
					<li><span><h7>'.$tcity.'</h7></span></li>
					<li> <span>'.$tstate.'</span></li>
					<li><div style = "float:left" id="rating'.$tid.'"></div><a href = "#">('.$rcount.')</a></li> 
					<li><div style = "float:left" id="ratingE'.$tid.'"></div></li> 
					
					
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
                <button class="btn btn-default dropdown-toggle" type="button" id="categoryList" data-toggle="dropdown" aria-expanded="false" style="
    margin-left: 0px;
    margin-top: 0px;
">Categories<span class="caret"></span> </button>
                <ul class="dropdown-menu" id="categoryList" role="menu" aria-labelledby="dropdownMenu1">
                 <?php echo $categoryList; ?>
                </ul>
              </div>
              <!--<div class="dropdown" style="float:right
;margin-right: 20px;">
                <button class="btn btn-default dropdown-toggle" type="button" id="year" data-toggle="dropdown" aria-expanded="false" style="
    margin-left: 0px;
    margin-top: 0px;
">City<span class="caret"></span> </button>
                <ul class="dropdown-menu" id="yearList" role="menu" aria-labelledby="dropdownMenu1">
                 <?php //echo $cityList; ?>
                </ul>
              </div>-->
    <h1>Search Results</h1>
    <h2 class="lead"><strong class="text-danger"><?php echo $count;?></strong> results were found</h2>
  </hgroup>
  <section class="col-xs-12 col-sm-6 col-md-12">
    <?php 
		 if(isset($output))
		 echo $output ?>
  </section>
</div>
<script>
$.fn.raty.defaults.path = 'images'; <?php echo $ratingList;?>
</script>
<script>

$(document).ready(function() {
var usedNames = {};
$("#categoryList>li>a").each(function () {
    if(usedNames[this.text]) 
	{
		console.log("Entered!");
        $(this).remove();
    } else 
	{
		console.log(this.value);
        usedNames[this.text] = this.text;
}});});

$.fn.raty.defaults.path = 'images/extremeness'; 
<?php echo $ratingListE;?>

</script>
</body>
</html>
