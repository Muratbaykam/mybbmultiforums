<?php

// Wake the sleeping giant

include("inc/functions.php");
include("inc/functions_template.php");
include("inc/config.php");

if($scriptpath != ""){

	$scriptpath = $scriptpath."/";

}

$path = $scriptpath."".$ourmybbpath."/inc/functions_user.php";
//require_once($path);

$links = getlinks();

// **********************************************************************
// We do all our prepwork here
// **********************************************************************


// Grab our settings...

$regurl = grabanysetting("regurl");

// Grab our ads...

$ads = getads("login","cms");

$loginstatus = logincheck();
$isloggedin = $loginstatus[loginstatus];
$loggedinname = $loginstatus[username];

$username = $_POST["username"];
$password = $_POST["password"];
$remember = $_POST["remember"];

$username = secure($username);
$password = secure($password);
$remember = secure($remember);

if ($isloggedin == "yes"){

$article_title = "You're already logged in!";
$article_content = "<p>Hey ".$loggedinname."!  You're already logged in!  <a href='logout.php'>Click Here</a> to log out.</p>";


}
else{

//User is not logged in

if($loggedinname == "" and $password == ""){
// User is viewing login form
$article_title = "Member Login:";

$regurl = grabanysetting("regurl");

$loginform = "<p>Here you can log in to manage your forum settings, make backups, purchase ad removal and more!  Don't have an account?  <a href='".$regurl."'>Register Free!</a></p>

<form id='login' method='post' action='login.php'>
<div id='loginform'>
  <p>Username: 
    <input name='username' type='text' id='username'/>
</p>
  <p>Password: 
    <input name='password' type='password' id='password'/>
</p>
<p><input name='remember' type='checkbox' id='remember' value='yes'/> Remember Me
</p>
  <p>
    <input type='submit' name='Submit' value='Submit'/>
  </p>
  <p>Don't have an account?<br/>
  <a href='".$regurl."'>Register Free</a>  </p>

</div>
  
</form>";
$article_content = $loginform;
}
else if(($username != "" and $password == "") or ($username == "" and $password != "") ){

//Something was left blank
$article_title = "Login Error:";
$article_content = "Something was left blank.  Please try logging in again.<br/><br/><form id='login' method='post' action='login.php'>
<div id='loginform'>
  <p>Username: 
    <input name='username' type='text' id='username'/>
</p>
  <p>Password: 
    <input name='password' type='password' id='password'/>
</p>
  <p>
    <input type='submit' name='Submit' value='Submit'/>
  </p>
  <p>Don't have an account?<br/>
  <a href='register.php'>Register Free</a>  </p>
</div>
</form>";

}
else if($username != "" and $password != ""){

$loginprefix = grabanysetting('uforumprefix');

$query = "SELECT * FROM ".$loginprefix."users WHERE username = '$username' LIMIT 1";
$result = @mysql_query($query);
$num = @mysql_num_rows($result);

	$luser=@mysql_result($result,$i,"username");
	$lpass=@mysql_result($result,$i,"password");
	$salt=@mysql_result($result,$i,"salt");

$password = md5($password);
$password = salt_password($password, $salt);


if($username == $luser and $password == $lpass){
$article_title = "Login Successful!";
$article_content = "<p>Welcome back ".$username.".  You are now logged in.  <a href='account.php'>Click Here to view or edit your account.</a></p>";
$isloggedin = "yes";

// Set the session...

 	$_SESSION['username'] = $username;
        $_SESSION['is_logged_in'] = TRUE;
	$_SESSION['unique'] = $rsession;	// We set this so there are no conflicts with other installs on the same server...

if($remember == "yes"){

// Set the login cookies...

$Month = 2592000 + time();
setcookie("rusername",$username,$Month);
setcookie("rpass",$password,$Month);

}

}
else{
$article_title = "Login Failed!";
$article_content = "<p>Sorry, we could not log you on with the details specified.  You can <a href='login.php'>try again</a> if you would like.</p>";
$fail = 1;
}


}
else{
die("You shouldn't be seeing this.  An unknown error has occurred.");
}
}

// **********************************************************************
// Begin Template Definition
// **********************************************************************

//Define our current theme
$file = $themeurl;

// Format the variables and send them to the template function;

$templatevars = "";
$templatevars[articletitle] = $article_title;
$templatevars[articlecontent] = $article_content;
$templatevars[links] = $links;
$templatevars[ads] = $ads;

// This variable is used for external JavaScript code in the HEAD tag
// This will usually be blank, unless we need JavaScript / JQuery / AJAX on the page

$templatevars[customheadtag] = "<script type='text/javascript' src='js/validate.js'></script>
<script type='text/javascript' src='js/validatelogin.js'></script>"; //  

// Now we send all this, plus the page name / type to the template system...
// Valid page types are: generic, admin, noads

$template = spittemplate("generic",$templatevars,$isloggedin);

echo $template;

// **********************************************************************
// End Template Definition
// **********************************************************************

?>
