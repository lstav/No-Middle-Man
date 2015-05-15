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
		  $day = $date[2];
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
<?php include 'navbar.php';?>
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
            <hr>
            
            <h4>Pick a date:</h4>
             <div style="display:inline-block">
              <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="year" data-toggle="dropdown" aria-expanded="true"> <?php echo $dDate['year'];?> <span class="caret"></span> </button>
                <ul class="dropdown-menu" id="yearList" role="menu" aria-labelledby="dropdownMenu1">
                 <?php echo $yearList; ?>
                </ul>
              </div>
            </div>
            <div style="display:inline-block">
              <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="month" data-toggle="dropdown" aria-expanded="true"> <?php echo $dDate['month'];?> <span class="caret"></span> </button>
                <ul class="dropdown-menu" id="monthList" role="menu" aria-labelledby="dropdownMenu1">
                 
                </ul>
              </div>
            </div>
            <div style="display:inline-block">
              <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="day" data-toggle="dropdown" aria-expanded="true"> <?php echo $dDate['day'];?> <span class="caret"></span> </button>
                <ul class="dropdown-menu" id="dayList" role="menu" aria-labelledby="dropdownMenu1">
                  <?php //echo $dayList;?>
                </ul>
              </div>
            </div>
            <div style="display:inline-block">
              <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="time" data-toggle="dropdown" aria-expanded="true">  <?php echo $dDate['time'];?> <span class="caret"></span> </button>
                <ul class="dropdown-menu" id="timeList" role="menu" aria-labelledby="dropdownMenu1">
                  <?php //echo $timeList;?>
                </ul>
              </div>
            </div>
            <br>
             <h4>How many in your party?:</h4>
	     <div style="display:inline-block">
              <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="av" data-toggle="dropdown" aria-expanded="true"> 1<span class="caret"></span> </button>
                <ul class="dropdown-menu" id="avList" role="menu" aria-labelledby="dropdownMenu1">
                  
                </ul>
              </div>
            </div>
            <div class="btn-group cart">
              <button type="button" class="btn btn-success" id ="cartButton" data-toggle="modal" data-target="#myModal"> Add to cart </button>
              <form name = "cartForm" method = "post">
              <input type="hidden" id = "tid" name="tid" value="English">
               <input type="hidden" id = "tdatetime" name="tdatetime" value="English">
                <input type="hidden" id = "tskey" name="tskey" value="English">
                 <input type="hidden" id = "quantity" name="quantity" value="English">
              </form>
            </div>
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
<script>
$(document).ready(function() {
   var sessionMap = <?php echo json_encode($sessionMap);?>;
   //console.log(sessionMap);
   var year = $("#year").text().trim();
   var month = $("#month").text().trim();
   var day = $("#day").text().trim();
   var time = $("#time").text().trim();
   var sid = 0;
   for (var key in sessionMap) 
   {
		if (sessionMap.hasOwnProperty(key)) 
		{	
			if(sessionMap[key]['year']===year)
			{
				//console.log(sessionMap[key]['month']);
				//console.log($("#monthList li:contains("+sessionMap[key]['month']+")").length > 0);
				if(!$("#monthList li:contains("+sessionMap[key]['month']+")").length > 0)
				{
					$("#monthList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+sessionMap[key]['month']+'</a></li>');
				}
				if(sessionMap[key]['month']===month)
				{
					if(!$("#dayList li:contains("+sessionMap[key]['day']+")").length > 0)
					{
						$("#dayList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+sessionMap[key]['day']+'</a></li>');
					}
					if(sessionMap[key]['day']===day)
					{
						//if(!$("#timeList li:contains("+sessionMap[key]['time']+")").length > 0)
						//{
							$("#timeList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+sessionMap[key]['time']+'</a></li>');
						//}
						
						if(sessionMap[key]['time']===time)
						{
							var av = Number(sessionMap[key]['av']);
							$("#avList").empty();
							var i = 0;
							for(i = 0; i < av; i++)
							{
								$("#avList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+(i+1)+'</a></li>');
							}
						}
					}
				}
			}
		}
  }
var usedNames = {};
$("#timeList li a").each(function () {
    if(usedNames[this.text]) {
        $(this).remove();
    } else {
        usedNames[this.text] = this.value;
    }});
});
$(document).ready(function() {
	$("body").on("click", ".dropdown-menu li a", function(){ 
  var selText = $(this).text().trim();
  var clicked = $(this).parents('.dropdown').find('.dropdown-toggle').attr("id");
  $(this).parents('.dropdown').find('.dropdown-toggle').html(selText+' <span class="caret"></span>');
  var sessionMap = <?php echo json_encode($sessionMap);?>;
   var year = $("#year").text().trim();
   var month = $("#month").text().trim();
   var day = $("#day").text().trim();
   var time = $("#time").text().trim();
   var sid = 0;
   var isSet = {month:false, day:false, time:false, quantity:false};
   //$("#monthList").empty();
   
  // $("#timeList").empty();
  // $("#avList").empty();
   
   if(clicked === "month")
   {
	   $("#dayList").empty();
	   $("#timeList").empty();
	   $("#avList").empty();
	   for(var key in sessionMap)
           {
		   if(sessionMap.hasOwnProperty(key))
	           {
			if(sessionMap[key]['month']===selText)
			{  
				if(!isSet['day'])
				{
					day = sessionMap[key]['day'];
					$("#day").html(sessionMap[key]['day'] + ' <span class="caret"></span>');
					isSet['day'] = true;
				}
				if(!$("#dayList li:contains("+sessionMap[key]['day']+")").length > 0)
				{
					$("#dayList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+sessionMap[key]['day']+'</a></li>');
				}
				if(sessionMap[key]['day']===day)
				{
					if(!isSet['time'])
					{
						time = sessionMap[key]['time'];
						$("#time").html(sessionMap[key]['time'] + ' <span class="caret"></span>');
						isSet['time'] = true;
						var av = Number(sessionMap[key]['av']);
						var i = 0;
						for(i = 0; i < av; i++)
						{
							$("#avList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+(i+1)+'</a></li>');
						}
					}
					//if(!$("#timeList li:contains("+sessionMap[key]['time']+")").length > 0)
					//{
						$("#timeList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+sessionMap[key]['time']+'</a></li>');
					//}
				}
			}		
		   }
	   }
   }
   else if(clicked === "day")
   {
	$("#timeList").empty();
	$("#avList").empty();
	for(var key in sessionMap)
    {
		if(sessionMap.hasOwnProperty(key))
	    {
			if(sessionMap[key]['day']===selText)
			{  
				if(!isSet['time'])
				{
					day = sessionMap[key]['time'];
					$("#time").html(sessionMap[key]['time'] + '<span class="caret"></span>');
					isSet['time'] = true;
					var av = Number(sessionMap[key]['av']);
					var i = 0;
					for(i = 0; i < av; i++)
					{
						$("#avList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+(i+1)+'</a></li>');
					}
				}
				//if(!$("#timeList li:contains("+sessionMap[key]['time']+")").length > 0)
				//{
					$("#timeList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+sessionMap[key]['time']+'</a></li>');
				//}
			}		
		 }
	   }
   }
   else if(clicked === "time")
   {
	$("#avList").empty();
	for(var key in sessionMap)
        {
		if(sessionMap.hasOwnProperty(key))
	        {
			if(sessionMap[key]['time']===selText)
			{  
				if(!isSet['quantity'])
				{
					isSet['quantity'] = true;
					var av = Number(sessionMap[key]['av']);
					var i = 0;
					for(i = 0; i < av; i++)
					{
						$("#avList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+(i+1)+'</a></li>');
					}
				}
			}		
		   }
	   }
   }
   var usedNames = {};
$("#timeList li a").each(function () {
    if(usedNames[this.text]) {
	    $(this).remove();
    } else {
        usedNames[this.text] = this.text;
    }
	});  
  });
});
$('#cartButton').click(function(){
	var sessionMap = <?php echo json_encode($sessionMap);?>;
  		var year = $("#year").text().trim();
  		var month = $("#month").text().trim();
  		var day = $("#day").text().trim();
   		var time = $("#time").text().trim();
		var av = $("#av").text().trim();
		var tourID = Number(<?php echo $tid;?>);
		
		for(var key in sessionMap)
		{
			if(sessionMap[key]['year']===year&sessionMap[key]['month']===month&sessionMap[key]['day']===day&sessionMap[key]['time']===time)
			{
				var rtime = sessionMap[key]['month']+" "+sessionMap[key]['day']+" "+sessionMap[key]['year']+" "+sessionMap[key]['time'];
				//console.log("called!");
				$('#tid').val(tourID);
				$('#tdatetime').val(rtime);
				$('#tskey').val(Number(sessionMap[key]['tskey']));
				$('#quantity').val(av);
  				$('form[name=cartForm]').attr('action','add_to_cart.php');
 				$('form[name=cartForm]').submit();
				//$.post( "add_to_cart.php", {tid: tourID, tdatetime: rtime, tskey: Number(sessionMap[key]['tskey']), quantity: av});
				break;
			}
		}
  
});
/*$(document).ready(function() {
	$("body").on("click", "#cartButton", function(){ 
	
		var sessionMap = <?php //echo json_encode($sessionMap);?>;
  		var year = $("#year").text().trim();
  		var month = $("#month").text().trim();
  		var day = $("#day").text().trim();
   		var time = $("#time").text().trim();
		var av = $("#av").text().trim();
		var tourID = Number(<?php //echo $tid;?>);
		
		for(var key in sessionMap)
		{
			if(sessionMap[key]['year']===year&sessionMap[key]['month']===month&sessionMap[key]['day']===day&sessionMap[key]['time']===time)
			{
				//console.log("called!");
				var rtime = sessionMap[key]['month']+" "+sessionMap[key]['day']+" "+sessionMap[key]['year']+" "+sessionMap[key]['time'];
				$.post( "add_to_cart.php", {tid: tourID, tdatetime: rtime, tskey: Number(sessionMap[key]['tskey']), quantity: av});
				break;
			}
		}
	});
});
*/
$.fn.raty.defaults.path = 'images'; 
<?php echo $ratingList;?>
</script>
</html>