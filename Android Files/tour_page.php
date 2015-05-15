<?php 
session_start();
$noSessions = '';
if(isset($_GET['tid']))
{  
  include_once("dbConnect.php");
  $tid = (int) $_GET['tid'];
  $query = pg_query($dbconn, "SELECT * FROM \"Tour\" NATURAL JOIN \"Location\" NATURAL JOIN \"Tour Guide\" WHERE \"tour_key\" = '$tid'");
  $rquery = pg_query($dbconn, "SELECT AVG(\"Rate\"), COUNT(*) FROM \"Review\" NATURAL JOIN \"Tour Session\" WHERE \"tour_key\" = $tid");
  $count = pg_num_rows($query);
  $ratingRow = pg_fetch_array($rquery);
  $trating = $ratingRow['avg'];
			$rcount = $ratingRow['count'];
			$trating = round($trating, 1);
			$tourRating = '$("#rating'.$tid.'").raty({ readOnly: true, score:'.$trating.' });';
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
	  $youtube = $row['Youtube'];
	  $facebook = $row['Facebook'];
	  $tour_photo = trim($row['tour_photo']);
	  $ratingRow = pg_fetch_array($rquery);
	  $trating = $ratingRow['avg'];
	  $tid = $row['tour_key'];
	  $about = $row['g_desc'];
	  $g_Email = $row['g_Email'];
	  $g_FName = $row['g_FName'];
	  $g_telephone = $row['g_telephone'];
	  $company = $row['Company'];
	  $tprice = $row['Price'];
	  $extremeness = $row['extremeness'];
	  $tcity = $row['City'];
	  $tstate = $row['State-Province'];
	  $tduration = $row['Duration'];
	  $taddress = $row['tour_address'];
	  $squery = pg_query($dbconn, "SELECT \"ts_key\", \"s_Time\", \"Availability\" FROM \"Tour Session\" Where \"tour_key\" = $tid and \"s_isActive\"  = TRUE and \"Availability\" > 0 and \"s_Time\" > (now() - interval '4 hour')
	  ORDER BY (\"s_Time\") ASC");
	  $i = 0;
	  if(pg_num_rows($squery) == 0)
	  {
		  $noSessions = "true";
	  }
	  else
	  {
		  $noSessions = "false";
	  }
	  
	  $ratingListE = '$("#ratingE'.$tid.'").raty({ readOnly: true, score:'.$extremeness.' });';
	  while($row = pg_fetch_array($squery))
	  {
		  $tskey = $row['ts_key'];
		  $sdate = $row['s_Time'];
		  $av = (int)$row['Availability'];
		  $datetime = explode(" ",$sdate);
		  $date = explode("-", $datetime[0]);
		  $query = pg_query($dbconn,"SELECT \"QuantityPerDate\"($tid, '$datetime[0]'::date)");
		  $result = pg_fetch_array($query);
		  $largeAv = (int)$result[0];
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
		  $sessionMap[$i]['largeAv'] = $largeAv;
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
	  $rquery = pg_query($dbconn, "SELECT \"t_key\", \"ts_key\", \"Rate\", \"Text\", \"t_FName\" FROM \"Review\" NATURAL JOIN \"Tour Session\" NATURAL JOIN \"Tour\" NATURAL JOIN \"Tourist\"  WHERE \"tour_key\" = $tid AND \"r_isActive\" = TRUE");
	  
	  while($reviewRow = pg_fetch_array($rquery))
	  {
		  $tsid = $reviewRow['ts_key'].$reviewRow['t_key'] ;
		  $text = $reviewRow['Text'];
		  $rating = $reviewRow['Rate'];
		  $name = $reviewRow['t_FName'];
		  $ratingList .= '$("#rating'.$tsid.'").raty({ readOnly: true, score:'.$rating.' });';
		  $reviewList .= '<div id = "rating'.$tsid.'"></div><h4>'.$name.' says:</h4><p>'.$text.'</p>';
		  $isadmin = $_SESSION['isadmin'];
		  if($isadmin=="t")
		  {
			  $reviewList .= '<a style="" class="btn btn-default" href="suspend_review.php?ts_key='.$reviewRow['ts_key'].'&t_key='.$reviewRow['t_key'].'&tid='.$tid.'" type="button">Suspend Review <span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a><hr>';
		  }
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
          <div class="col-md-6"> <img id="item-display" src="<?php echo $tour_photo;?>/1.jpg" alt="" style="max-width:100%"> </div>
          <div class="col-md-6">
            <div class="product-title"><?php echo $tourName;?></div>
             <div style = "float:left" id="rating<?php echo $tid?>"></div><a href = "#">(<?php echo $rcount?>)</a>
            <div class="product-desc"><?php echo $tdescription.'<br><strong>Estimated Duration: '.$tduration.' minutes </strong><br>'.$taddress .'<br>'.$tcity.', '. $tstate?> <div class="product-price"><div style = "" id="ratingE<?php echo $tid?>"><h4 style="
    margin-bottom: 0px;
" strong>Extremeness:</h4></div><?php echo $tprice; ?></div>
			           </div><hr>
           

            
            
           <a title="Lorem ipsum" href="<?php echo $youtube;?>"><img src="images/YouTube-social-squircle_red_48px.png" alt="Lorem ipsum"></a>
           <a title="Lorem ipsum" href="<?php echo $facebook;?>"><img src="images/fb-48px.png" alt="Lorem ipsum"></a>
            <hr>
            
           <div id = "pickedDate">
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
             
	     <div style="display:inline-block">
         <h4>How many in your party?:</h4>
              <div class="dropdown" style = "display:inline-block">
                <button class="btn btn-default dropdown-toggle" type="button" id="av" data-toggle="dropdown" aria-expanded="true"> 1<span class="caret"></span> </button>
                <ul class="dropdown-menu" id="avList" role="menu" aria-labelledby="dropdownMenu1">
                  
                </ul>
              </div>
              <div class="btn-group cart" style = "display:inline-block">
              <button type="button" class="btn btn-success" id ="cartButton"> Add to cart </button>
              <form name = "cartForm" method = "post">
              <input type="hidden" id = "tid" name="tid" value="English">
               <input type="hidden" id = "tdatetime" name="tdatetime" value="English">
                <input type="hidden" id = "tskey" name="tskey" value="English">
                 <input type="hidden" id = "quantity" name="quantity" value="English">
              </form>
            </div>
            </div>
         
         	<br>
            
            <div style="display:inline-block;margin-top: 20px;">
         	<h4>Large group? Pick a whole day. Your party will be distributed among different sessions</h4>
              <div style="display:inline-block">
              <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="largeYear" data-toggle="dropdown" aria-expanded="true"> <?php echo $dDate['year'];?> <span class="caret"></span> </button>
                <ul class="dropdown-menu" id="largeYearList" role="menu" aria-labelledby="dropdownMenu1">
                 <?php echo $yearList; ?>
                </ul>
              </div>
            </div>
                <div style="display:inline-block">
              <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="largeMonth" data-toggle="dropdown" aria-expanded="true"> <?php echo $dDate['month'];?> <span class="caret"></span> </button>
                <ul class="dropdown-menu" id="largeMonthList" role="menu" aria-labelledby="dropdownMenu1">
                 
                </ul>
              </div>
            </div>
            <div style="display:inline-block">
              <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="largeDay" data-toggle="dropdown" aria-expanded="true"> <?php echo $dDate['day'];?> <span class="caret"></span> </button>
                <ul class="dropdown-menu" id="largeDayList" role="menu" aria-labelledby="dropdownMenu1">
                  <?php //echo $dayList;?>
                </ul>
              </div>
            </div>
            
            <div class="dropdown" style = "display:inline-block">
            Quantity:
                <button class="btn btn-default dropdown-toggle" type="button" id="largeAv" data-toggle="dropdown" aria-expanded="true"> 1<span class="caret"></span> </button>         
                
                <ul class="dropdown-menu" id="largeAvList" role="menu" aria-labelledby="dropdownMenu1">
                </ul>
              </div>
              <div class="btn-group cart" style = "display:inline-block">
              <button type="button" class="btn btn-success" id ="largeCartButton" style="margin-top: 10px;"> Add group to cart </button>
              <form name = "largeCartForm" method = "post">
              	<input type="hidden" id = "largetid" name="largetid" value="English">
                <input type="hidden" id = "largeDate" name="largeDate" value="English">
              	<input type="hidden" id = "largeQuantity" name="largeQuantity" value="English">
              </form>
            </div>
            </div>
            
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
              <h3>About Tour Guide <?php echo $g_FName;?>:</h3>
             Email address: <?php echo $g_Email;?>
             <br>
             Phone: <?php echo $g_telephone;?>
			 <br>
			 <?php echo $about;?>
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
 var noSessions = <?php echo json_encode($noSessions);?>;
	if(noSessions == 'true')
	{
		$('#pickedDate').html("No sessions available");
	}
});

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
	$("body").on("click", ".dropdown-menu li a", function(e){ 
	e.preventDefault();
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
   if(clicked == "year")
   {
	   $("#monthList").empty();
	   $("#dayList").empty();
	   $("#timeList").empty();
	   $("#avList").empty();
	   for(var key in sessionMap)
	   {
	   	   if(sessionMap.hasOwnProperty(key))
		   {
			   if(sessionMap[key]['year']===selText)
			   {
				    if(!isSet['month'])
					{
							month = sessionMap[key]['month'];
							$("#month").html(sessionMap[key]['month'] + ' <span class="caret"></span>');
							isSet['month'] = true;
					}
					if(!$("#monthList li:contains("+sessionMap[key]['month']+")").length > 0)
					{
						$("#monthList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+sessionMap[key]['month']+'</a></li>');
					}
			    	if(sessionMap[key]['month']===month)
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
   
   }
   
   else if(clicked === "month")
   {
	   $("#dayList").empty();
	   $("#timeList").empty();
	   $("#avList").empty();
	   for(var key in sessionMap)
       {
		  if(sessionMap.hasOwnProperty(key))
	      {
			  if(sessionMap[key]['year']===year)
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
   }
   else if(clicked === "day")
   {
	$("#timeList").empty();
	$("#avList").empty();
	for(var key in sessionMap)
    {
		if(sessionMap.hasOwnProperty(key))
	    {
			if(sessionMap[key]['year']==year)
			{
				if(sessionMap[key]['month']==month)
				{
					if(sessionMap[key]['day'].trim()===selText)
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
							//console.log(sessionMap[key]['time']);
						//}
					}
				}
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
			if(sessionMap[key]['year']==year)
			{
				if(sessionMap[key]['month']==month)
				{
					if(sessionMap[key]['day']==day)
					{
					  if(sessionMap[key]['time']===selText)
					  {  
						  //if(!isSet['quantity'])
						  //{
							  //isSet['quantity'] = true;
							  var av = Number(sessionMap[key]['av']);
							  var i = 0;
							  for(i = 0; i < av; i++)
							  {
								  $("#avList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+(i+1)+'</a></li>');
							  }
						 // }
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


//LARGE CART FUNCTIONS
$(document).ready(function() {
   var sessionMap = <?php echo json_encode($sessionMap);?>;
   //console.log(sessionMap);
   var year = $("#largeYear").text().trim();
   var month = $("#largeMonth").text().trim();
   var day = $("#largeDay").text().trim();
   var sid = 0;
   console.log(year);
   console.log(month);
   console.log(day);
   for (var key in sessionMap) 
   {
		if (sessionMap.hasOwnProperty(key)) 
		{	
			if(sessionMap[key]['year']===year)
			{
				//console.log(sessionMap[key]['month']);
				//console.log($("#monthList li:contains("+sessionMap[key]['month']+")").length > 0);
				if(!$("#largeMonthList li:contains("+sessionMap[key]['month']+")").length > 0)
				{
					$("#largeMonthList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+sessionMap[key]['month']+'</a></li>');
				}
				if(sessionMap[key]['month']===month)
				{
					if(!$("#largeDayList li:contains("+sessionMap[key]['day']+")").length > 0)
					{
						$("#largeDayList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+sessionMap[key]['day']+'</a></li>');
					}
					if(sessionMap[key]['day']===day)
					{
							var av = Number(sessionMap[key]['largeAv']);
							
							$("#largeAvList").empty();
							var i = 0;
							for(i = 0; i < av; i++)
							{
								console.log(av);
								$("#largeAvList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+(i+1)+'</a></li>');
							}
					}
				}
			}
		}
   }
});

$(document).ready(function() {
	$("body").on("click", ".dropdown-menu li a", function(){ 
  var selText = $(this).text().trim();
  var clicked = $(this).parents('.dropdown').find('.dropdown-toggle').attr("id");
  console.log(clicked);
  $(this).parents('.dropdown').find('.dropdown-toggle').html(selText+' <span class="caret"></span>');
  var sessionMap = <?php echo json_encode($sessionMap);?>;
   var year = $("#largeYear").text().trim();
   var month = $("#largeMonth").text().trim();
   var day = $("#largeDay").text().trim();
   var sid = 0;
   var isSet = {month:false, day:false, time:false, quantity:false};
   //$("#monthList").empty();
   
  // $("#timeList").empty();
  // $("#avList").empty();
   if(clicked == "largeYear")
   {
	   $("#largeMonthList").empty();
	   $("#largeDayList").empty();
	   $("#largeAvList").empty();
	   for(var key in sessionMap)
	   {
	   	   if(sessionMap.hasOwnProperty(key))
		   {
			   if(sessionMap[key]['year']===selText)
			   {
				    if(!isSet['month'])
					{
							month = sessionMap[key]['month'];
							$("#largeMonth").html(sessionMap[key]['month'] + ' <span class="caret"></span>');
							isSet['month'] = true;
					}
					if(!$("#largeMonthList li:contains("+sessionMap[key]['month']+")").length > 0)
					{
						$("#largeMonthList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+sessionMap[key]['month']+'</a></li>');
					}
			    	if(sessionMap[key]['month']===month)
					{  
						if(!isSet['day'])
						{
							day = sessionMap[key]['day'];
							$("#largeDay").html(sessionMap[key]['day'] + ' <span class="caret"></span>');
							isSet['day'] = true;
						}
						if(!$("#largeDayList li:contains("+sessionMap[key]['day']+")").length > 0)
						{
							$("#largeDayList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+sessionMap[key]['day']+'</a></li>');
						}
						if(sessionMap[key]['day']===day)
						{
							if(!isSet['time'])
							{
								var av = Number(sessionMap[key]['largeAv']);
								var i = 0;
								isSet['time'] = true;
								for(i = 0; i < av; i++)
								{
									$("#largeAvList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+(i+1)+'</a></li>');
								}
							}
						}
					}
			   }
	   		}
    	}
   }
	  
   
   
   else if(clicked === "largeMonth")
   {
	   $("#largeDayList").empty();
	   $("#largeAvList").empty();
	   for(var key in sessionMap)
       {
		  if(sessionMap.hasOwnProperty(key))
	      {
			  if(sessionMap[key]['year']===year)
			  {
				  	if(sessionMap[key]['month']===selText)
					{  
					if(!isSet['day'])
					{
						day = sessionMap[key]['day'];
						$("#largeDay").html(sessionMap[key]['day'] + ' <span class="caret"></span>');
						isSet['day'] = true;
					}
					if(!$("#largeDayList li:contains("+sessionMap[key]['day']+")").length > 0)
					{
						$("#largeDayList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+sessionMap[key]['day']+'</a></li>');
					}
					if(sessionMap[key]['day']===day)
					{
						if(!isSet['time'])
						{
							isSet['time'] = true;
							var av = Number(sessionMap[key]['largeAv']);
							var i = 0;
							for(i = 0; i < av; i++)
							{
								$("#largeAvList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+(i+1)+'</a></li>');
							}
						}
						
						//if(!$("#timeList li:contains("+sessionMap[key]['time']+")").length > 0)
						//{
						//}
					}
				}	
			  }
		   }
	   }
   }
   else if(clicked === "largeDay")
   {
	$("#largeAvList").empty();
	for(var key in sessionMap)
    {
		if(sessionMap.hasOwnProperty(key))
	    {
			if(sessionMap[key]['year']==year)
			{
				if(sessionMap[key]['month']==month)
				{
					if(sessionMap[key]['day'].trim()===selText)
					{  
						if(!isSet['time'])
						{
							isSet['time'] = true;
							var av = Number(sessionMap[key]['largeAv']);
							var i = 0;
							for(i = 0; i < av; i++)
							{
								$("#largeAvList").append(' <li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+(i+1)+'</a></li>');
							}
						}
					}
				}
			}
		 }
	   }
     }
   });
});
   
$('#largeCartButton').click(function(){
	var sessionMap = <?php echo json_encode($sessionMap);?>;
  		var largeYear = $("#largeYear").text().trim();
  		var largeMonth = $("#largeMonth").text().trim();
  		var largeDay = $("#largeDay").text().trim();
		var tourID = Number(<?php echo $tid;?>);
		var largeAv = $("#largeAv").text().trim();
		var rtime = '';
		for(var key in sessionMap)
		{
			if(sessionMap[key]['year']===largeYear&sessionMap[key]['month']===largeMonth&sessionMap[key]['day']===largeDay)
			{
				var rtime = sessionMap[key]['year']+ "-"+ sessionMap[key]['month']+"-"+sessionMap[key]['day'];
				//console.log(rtime);			
				$('#largetid').val(tourID);
				$('#largeDate').val(rtime);
				//$('#tskey').val(Number(sessionMap[key]['tskey']));
				$('#largeQuantity').val(largeAv);
  				$('form[name=largeCartForm]').attr('action','add_large_group.php');
 				$('form[name=largeCartForm]').submit();
				break;
			}
		}
});


$.fn.raty.defaults.path = 'images'; 
<?php echo $ratingList;?>
<?php echo $tourRating;?>
</script>
<script>$.fn.raty.defaults.path = 'images/extremeness'; <?php echo $ratingListE;?></script>
</html>