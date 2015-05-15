<?php 
if(isset($_GET['tid']))
{  
  include_once("dbConnect.php");
  $tid = (int) $_GET['tid'];
  $query = pg_query($dbconn, "SELECT * FROM \"Tour\" NATURAL JOIN \"Location\" WHERE \"tour_key\" = '$tid'");
  $rquery = pg_query($dbconn, "SELECT AVG(\"Rate\"), COUNT(*) FROM \"Review\" NATURAL JOIN \"Tour Session\" WHERE \"tour_key\" = $tid");
  $count = pg_num_rows($query);
  $sessionList = array();
  $reviewList = '';
  $ratingList = '';
  $yearList = '';
  $monthList = '';
  $dayList = '';
  $timeList = '';
  $dDate = array(array());
  if($count > 0)
  {
	  $row = pg_fetch_array($query);
	  $tourName = $row['tour_Name'];
	  $tdescription = $row['tour_Desc'];
	  $ratingRow = pg_fetch_array($rquery);
	  $trating = $ratingRow['avg'];
	  $tid = $row['tour_key'];
	  $tprice = $row['Price'];
	  $tcity = $row['City'];
	  $tstate = $row['State-Province'];
	  $tduration = $row['Duration'];
	  $taddress = $row['tour_address'];
	  $squery = pg_query($dbconn, "SELECT \"ts_key\", \"s_Time\", \"Availability\" FROM \"Tour Session\" Where \"tour_key\" = $tid and \"s_isActive\"  = TRUE and \"Availability\" > 0 and \"s_Time\" > now() 
	  ORDER BY (\"s_Time\") ASC");
	  $i = 0;
	  while($row = pg_fetch_array($squery))
	  {
		  $tskey = $row['ts_key'];
		  $sdate = $row['s_Time'];
		  $av = (int)$row['Availability'];
		  $datetime = explode(" ",$sdate);
		  $date = explode("-", $datetime[0]);
		  $year = $date[0];
		  $monthNum  = $date[1];
		  $dateObj   = DateTime::createFromFormat('!m', $monthNum);
		  $month = $dateObj->format('F');
		  $day = $date[2] ;
		  $time = date("g:i a", strtotime(substr($datetime[1], 0, -3)));
		  $sessionMap[$i]['year'] = $year;
		  $sessionMap[$i]['month'] = $month;
		  $sessionMap[$i]['day'] = $day;
		  $sessionMap[$i]['time'] = $time;
		  $sessionMap[$i]['av'] = $av; 
		  $sessionMap[$i]['tskey'] = $tskey;
		  //$date = date("M-d-Y", strtotime($row['s_Time']));
		  //$time = date("g:i:s A" , strtotime($row['s_Time']));
		  if($i==0)
		  {
			  $dDate['year'] = $year; 
			  $dDate['month'] = $month;
			  $dDate['day'] = $day;
			  $dDate['time'] = $time;
		  }
		  if (strpos($yearList, $year) == false) 
		  {
			  $yearList .= ' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'.$year.'</a></li>';
		  }
		  if (strpos($monthList, $month) == false) 
		  {
			  $monthList .= ' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'.$month.'</a></li>';
		  }
		  if (strpos($dayList, $day) == false) 
		  {
			  $dayList .= ' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'.$day.'</a></li>';
		  }
		  if (strpos($timeList, $time) == false) 
		  {
			  $timeList .= ' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'.$time.'</a></li>';
		  }
		  $i++;
		  //$sessionList .= '<a href="add_to_cart.php?tid='.$tid.'&tskey='.$tskey.'" class="list-group-item">Date: '.$date.' | Time: '.$time.'</a>';
	  }
	  $rquery = pg_query($dbconn, "SELECT \"t_key\", \"ts_key\", \"Rate\", \"Text\", \"t_FName\" FROM \"Review\" NATURAL JOIN \"Tour Session\" NATURAL JOIN \"Tour\" NATURAL JOIN \"Tourist\"  WHERE \"tour_key\" = $tid");
	  
	  while($reviewRow = pg_fetch_array($rquery))
	  {
		  $tsid = $reviewRow['ts_key'].$reviewRow['t_key'] ;
		  $text = $reviewRow['Text'];
		  $rating = $reviewRow['Rate'];
		  $name = $reviewRow['t_FName'];
		  $ratingList .= '$("#rating'.$tsid.'").raty({ readOnly: true, score:'.$rating.' });';
		  $reviewList .= '<div id = "rating'.$tsid.'"></div><h4>'.$name.' says:</h4><p>'.$text.'</p><hr>';
	  }
  }
  else
  {
	  echo "Tour not found";
  }
}
else
{
	echo "Tour not found";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'header.php';?>
</head>
<body>
<?php include 'guide_navbar.php';?>
<script>
$(document).ready(function(){
	$("#language").attr("style", "");
});
</script>
<div class="container-fluid">
  <div class="content-wrapper">
    <div class="item-container">
      <div class="container">
        <div class ="row">
          <div class="col-md-6"> <img id="item-display" src="images/<?php echo $tid?>/1.jpg" alt="" style="max-width:100%"> </div>
          <div class="col-md-6">
            <div class="product-title"><?php echo $tourName;?></div>
            <div class="product-desc"><?php echo $tdescription.'<br><strong>Estimated Duration: '.$tduration.' minutes </strong><br>'.$taddress .'<br>'.$tcity.', '. $tstate?></div>
            <div class="product-rating"><i class="fa fa-star gold"></i> <i class="fa fa-star gold"></i> <i class="fa fa-star gold"></i> <i class="fa fa-star gold"></i> <i class="fa fa-star-o"></i> </div>
            <hr>
            <div class="product-price"><?php echo $tprice; ?></div>
            <div class="product-stock"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="col-md-12 product-info">
        <ul id="myTab" class="nav nav-tabs nav_tabs">
          <li class="active"><a href="#service-one" data-toggle="tab">Tour guide</a></li>
          <li><a href="#service-two" data-toggle="tab">Reviews</a></li>
        </ul>
        <div id="myTabContent" class="tab-content">
          <div class="tab-pane fade in active" id="service-one">
            <section class="container product-info">
              <h3>Tour Business:</h3>
              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi pharetra congue commodo. Proin lacinia est at nulla scelerisque, commodo volutpat arcu egestas. Cras facilisis lectus ornare turpis varius, posuere ullamcorper felis sodales. Sed blandit magna nisl.
              <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi pharetra congue commodo. Proin lacinia est at nulla scelerisque, commodo volutpat</li>
              <li>Arcu egestas. Cras facilisis lectus ornare turpis varius, posuere ullamcorper felis sodales. Sed blandit magna nisl.</li>
              <li>E commodo. Proin lacinia est at nulla scelerisque, commodo volutpat</li>
            </section>
          </div>
          <div class="tab-pane fade" id="service-two">
            <section class="container product-info"> <?php echo $reviewList;?></section>
          </div>
        </div>
        <hr>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<!--<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Available Sessions</h4>
      </div>
      <div class="modal-body">
        <div class="list-group"> <?php //echo $sessionList;?></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>-->
</body>
<script>$.fn.raty.defaults.path = 'images'; 
<?php echo $ratingList;?></script>
</html>