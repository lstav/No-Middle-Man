<?php
session_start();
include_once("dbConnect.php");
$schedule = '';
$tours = '';
$ref = '';
$totaltotal = '';
if($_SESSION['tgemail'])
{
	    $uemail = $_SESSION['tgemail'];
		$uid = $_SESSION['tgid'];
		$ufname = $_SESSION['tgfname'];
		$ulname = $_SESSION['tglname'];
		$upass = $_SESSION['tgpass'];
		$tgcompany = $_SESSION['tgcompany'];
		$tgdesc = $_SESSION['tgdesc'];
		$errorMsg = '';
		
		$target_file = "images/business/".$uid."/1.jpg";
		if(!file_exists($target_file))
		{
				$ref = "images/0/1.jpg";
		}
		else
		{
				$ref = $target_file;
		}
		
		$squery = pg_query($dbconn, "SELECT \"tour_key\", \"t_FName\",\"t_LName\",\"p_quantity\",
		\"City\", \"tour_Desc\", \"State-Province\", \"ts_key\", \"tour_Name\", \"extremeness\" , 
		\"Price\", \"s_Time\",\"Payed\", \"s_isActive\", (\"total\"*0.90) as total, \"tour_photo\"
		FROM \"Upcoming Tours\" NATURAL JOIN \"Location\" NATURAL JOIN \"Tourist\"
		WHERE \"g_key\"=$uid ORDER BY \"s_Time\" ASC");
		
		$earning = pg_query($dbconn, "Select \"getEarning\"($uid)");
		$rowtotal = pg_fetch_array($earning);
		$totaltotal = $rowtotal['getEarning'];
		
	while($row = pg_fetch_array($squery))
	{
		$tname = $row['tour_Name'];
		$tdescription = $row['tour_Desc'];
		$tid = $row['tour_key'];
		$quantity = $row['Payed'];
		$name = $row['t_FName'].' '.$row['t_LName'];
		$total = $row['total'];
		$tcity = $row['City'];
		$tstate = $row['State-Province'];
		$tourphoto = trim($row['tour_photo']);
		$reserved_time = date("F/d/Y g:i a" , strtotime(substr($row['s_Time'], 0, -3)));
	    $schedule .= '<article class="search-result row">
			<div class="col-xs-12 col-sm-12 col-md-3">
				<a title="Lorem ipsum" class="thumbnail"><img src="'.$tourphoto.'1.jpg" alt="Lorem ipsum"></a>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-7 excerpet">
				<h3><a title="">'.$tname.'</a></h3>
				<h5><strong>Reserved time: '.$reserved_time.'</strong></h5>	
				<h5>'.$name.'\'s party of: '.$quantity.'</h5>	
				<h5>Total Earnings: '.$total.'</h5>
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
		$tourphoto = trim($row['tour_photo']);
		$isActive = $row['tour_isActive'];
		
		$tours .= '<article class="search-result row">
			<div class="col-xs-12 col-sm-12 col-md-3">
				<a title="Lorem ipsum" class="thumbnail" href="guide_tour_page.php?tid='.$tid.'"><img src="'.$tourphoto.'1.jpg" alt="Lorem ipsum"></a>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-7 excerpet">
				<h3><a href="guide_tour_page.php?tid='.$tid.'">'.$tname.'</a></h3>';
				if($isActive == "f") {
					$tours.='<h5><font color="red">Tour is not active</font></h5>';
				}
				$tours.='<p>'.$tdescription.'</p>	
				<h7>'.$tcity.'</h7>
				<h7>'.$tstate.'</h7>
				<h5>'.$tprice.'</h5>
				<a style="" class="btn btn-default" href="edit_tour.php?tid='.$tid.'" type="button">Edit <span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>			
			</div>
			<span class="clearfix borda"></span>
		</article>';
		
	}
		
}
else
{
	header("Location: guide_login.php");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Guide Home</title>
	<?php include 'header.php' ?>
</head>

<body>
<?php include 'guide_navbar.php';?>
<div style = "margin-top: 10px;" class = "container">
<div class="container">
        <div class ="row">
          <div class="col-md-6">
         	<img id="item-display" src="<?php echo $ref?>" alt="" style="max-width:100%">   
          </div>
          <div class="col-md-6">
            <div><h3><strong><?php echo $tgcompany;?></strong></h3></div>
            <br />
            <div class="product-desc"><?php echo $tgdesc;?></div>
          </div>
		  <div class="col-md-6">
            <div><h4><strong>Total Earned: <?php echo $totaltotal;?></strong></h4></div>            
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
