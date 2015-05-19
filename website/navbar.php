<?php
include_once("dbConnect.php");
if(!isset($_SESSION)) {
     session_start();
}
$loginOuput = '';
$cartCount = '';
$adminButtons = '';
if(isset($_SESSION['uid']))
{
	$uid = $_SESSION['uid'];
	$cartCount = '';
	$uemail = $_SESSION['uemail'];
	$ufname = $_SESSION['ufname'];
	$isadmin = $_SESSION['isadmin'];
	$navLink = "tourist_account.php";
	$loginOuput = '<li class = "dropdown"><a class = "dropdown-toggle" data-toggle = "dropdown"> Hello  '.$ufname.'! <b class = "caret"></b></a>
          <ul class = "dropdown-menu">
            <li><a href = "'.$navLink.'">My Account</a></li>';
            
	if($isadmin=="t") {
		$loginOuput = $loginOuput.'<li><a href = "reports.php">Reports</a></li>';
	}
	$loginOuput = $loginOuput.'<li><a href = "sign_out.php">Sign Out</a></li>
          </ul></li>';
	
	$cquery = pg_query($dbconn, "SELECT * FROM \"Shopping Cart\" WHERE \"t_key\" = '$uid' and \"p_quantity\" > 0;");
	if(pg_num_rows($cquery) > 0)
	{
		$cartCount = pg_num_rows($cquery);
	}
	if($isadmin=="t")
	{
		$adminButtons = '<div class="container">
    <div class="row-fluid">
            <div class="col-md-6">
               <form action="search_users.php" method="post"> 
    <div class="input-group">
      
      <input type="text" name="tourist" class="form-control" placeholder="Search tourists by email">
      <span class="input-group-btn">
        <button name="submit" class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
      </span>
      
    </div><!-- /input-group -->
    </form> 
                    
            </div>

            <div class="col-md-6">
                <form action="search_business.php" method="post"> 
    <div class="input-group">
      
      <input type="text" name="business" class="form-control" placeholder="Search tour guides by email">
      <span class="input-group-btn">
        <button name="submit1" class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
      </span>
      
    </div><!-- /input-group -->
    </form>
            </div>
    </div>
</div>';
	}
		
	
	
}
else
{
	$loginOuput = '<li class = "active"> <a href = "login.php"> Login/Register </a></li>';
}
?>

<div class = "navbar navbar-inverse navbar-static-top" style="background-color: #8CC739;border-bottom-width: 0px; margin-bottom: 0px; color:#21BEDE">
  <div class = "container"><a href ="index.php" class = "navbar-brand">NoMiddleMan</a><img style="width: 5%;height: 5%;padding-top: 8px;padding-bottom: 0px;" class = "navbar-brand" src=images/FaceOnly.png>
    <button class = "navbar-toggle" data-toggle = "collapse" data-target = ".navHeaderCollapse"> <span class = "icon-bar"></span> <span class = "icon-bar"></span> <span class = "icon-bar"></span> </button>
    <div class = "collapse navbar-collapse navHeaderCollapse">
      <ul class = "nav navbar-nav navbar-right">
        <?php echo $loginOuput?>
        <li class  = "dropdown"> 
        </li>
        <li> <div id="google_translate_element" style="padding-top: 12px; margin-left: 10px;"></div><script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', includedLanguages: 'en,es', layout: google.translate.TranslateElement.InlineLayout.SIMPLE, autoDisplay: false}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
        </li>
        <!--<li class  = "dropdown"> <a id = "language" style = "padding-top:20px" href = "#" class = "dropdown-toggle" data-toggle = "dropdown"><img src = images/us.png><b class = "caret"></b></a>
          <ul class = "dropdown-menu">
            <li><a href = "#">Español</a></li>
          </ul>
        </li>-->
        <li><a href="cart.php"> Cart <span class="glyphicon glyphicon-shopping-cart"></span> <?php echo $cartCount;?></a></li>
      </ul>
    </div>
  </div>
</div>
<?php echo $adminButtons;?>