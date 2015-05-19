<?php
session_start();
include_once("dbConnect.php");
$schedule = '';
$tours = '';
if($_SESSION['tgemail'])
{
	    $uemail = $_SESSION['tgemail'];
		$uid = $_SESSION['tgid'];
		$ufname = $_SESSION['tgfname'];
		$ulname = $_SESSION['tglname'];
		$upass = $_SESSION['tgpass'];
		$tgcompany = $_SESSION['tgcompany'];
		$errorMsg = '';
		
		$squery = pg_query($dbconn, "SELECT \"tour_key\", \"t_FName\",\"t_LName\",\"p_quantity\",\"City\", \"tour_Desc\", \"State-Province\", \"ts_key\", \"tour_Name\", \"extremeness\" , \"Price\", \"s_Time\",\"Payed\", \"s_isActive\",
		(\"Price\"*\"Payed\") as total
		FROM \"Upcoming Tours\" NATURAL JOIN \"Location\" NATURAL JOIN \"Tourist\"
		WHERE \"g_key\"=$uid ORDER BY \"s_Time\" ASC");
	while($row = pg_fetch_array($squery))
	{
		$tname = $row['tour_Name'];
		$tdescription = $row['tour_Desc'];
		$tid = $row['tour_key'];
		$quantity = $row['Payed'];
		$name = $row['t_FName'].' '.$row['t_LName'];
		$tcity = $row['City'];
		$tstate = $row['State-Province'];
		$reserved_time = date("F/d/Y g:i a" , strtotime(substr($row['s_Time'], 0, -3)));
	    $schedule .= '<article class="search-result row">
			<div class="col-xs-12 col-sm-12 col-md-3">
				<a title="Lorem ipsum" class="thumbnail"><img src="images/'.$tid.'/1.jpg" alt="Lorem ipsum"></a>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-7 excerpet">
				<h3><a title="">'.$tname.'</a></h3>
				<h5><strong>Reserved time: '.$reserved_time.'</strong></h5>	
				<h5>'.$name.'\'s party of: '.$quantity.'</h5>				
			</div>
			<span class="clearfix borda"></span>
		</article>';
	}
	
	$tquery = pg_query($dbconn, "SELECT * FROM \"Tour\" NATURAL JOIN \"Location\" WHERE \"g_key\"='$uid'");
	while($row = pg_fetch_array($tquery))
	{
		$tname = $row['tour_Name'];
		$tdescription = $row['tour_Desc'];
		$tid = $row['tour_key'];
		$tcity = $row['City'];
		$tstate = $row['State-Province'];
		$tprice = $row['Price'];
		
		$tours .= '<article class="search-result row">
			<div class="col-xs-12 col-sm-12 col-md-3">
				<a title="Lorem ipsum" class="thumbnail" href="guide_tour_page.php?tid='.$tid.'"><img src="images/'.$tid.'/1.jpg" alt="Lorem ipsum"></a>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-7 excerpet">
				<h3><a href="guide_tour_page.php?tid='.$tid.'">'.$tname.'</a></h3>
				<p>'.$tdescription.'</p>	
				<h7>'.$tcity.'</h7>
				<h7>'.$tstate.'</h7>
				<h5>'.$tprice.'</h5>
				<a style="" class="btn btn-default" href="edit_tour.php?tid='.$tid.'" type="button">Edit <span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>			
			</div>
			<span class="clearfix borda"></span>
		</article>';
		
	}
		
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name  = "viewport" content = "width=device-width, initial-scale = 1.0">
	<title>My site</title>
    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
	<link href="css/bootstrap.min.css" rel = "stylesheet">
	<link rel="stylesheet" href="css/nmmstyles.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script> 
	<script src="js/bootstrap.js"></script>
	<style type="text/css">
        @import "http://fonts.googleapis.com/css?family=Roboto:300,400,500,700";
    </style>
</head>

<body>
<?php include 'guide_navbar.php';?>
<div style = "margin-top: 10px;" class = "container">
<div class="container">
        <div class ="row">
          <div class="col-md-6">
         	<img id="item-display" src="images/business/<?php echo $uid?>.jpg" alt="" style="max-width:100%">   
          </div>
          <div class="col-md-6">
            <div><h3><strong><?php echo $tgcompany;?></strong></h3></div>
            <br />
            <div class="product-desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi pharetra congue commodo. Proin lacinia est at nulla scelerisque, commodo volutpat arcu egestas. Cras facilisis lectus ornare turpis varius, posuere ullamcorper felis sodales. Sed blandit magna nisl. Lorem ipsum dolor sit amet, consectetur adipisicing elit. <br /> <br />Voluptatem, exercitationem, suscipit, distinctio, qui sapiente aspernatur molestiae non corporis magni sit sequi iusto debitis delectus doloremque. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatem, exercitationem, suscipit, distinctio, qui sapiente aspernatur molestiae non corporis magni sit sequi iusto debitis delectus doloremque.</div>
          </div>
        </div>
      </div>

</div>
<div style = "margin-top: 10px;" class="container">
    <!-- /.col-lg-6 -->	
    <section class="col-xs-6 col-sm-3 col-md-6">
    <hgroup class="mb20">
    	
		<h1>My Tour Schedule</h1>
    								
	</hgroup>
		<?php echo $schedule; ?>	
	</section>
    <section class="col-xs-6 col-sm-3 col-md-6">
    <hgroup class="mb20">	
		<h1>Available Tours</h1>   							
	</hgroup>
		<?php echo $tours;?>
	</section>
    <a style="float:right" class="btn btn-success" href="add_tour.php" type="button">Add Tour <span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
</div>
</body>
</html>
