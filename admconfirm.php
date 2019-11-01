<?php
session_start();

// Wake the sleeping giant

include("inc/functions.php");
include("inc/config.php");
include("inc/functions_template.php");

$themeurl = grabanysetting("templateurl");
$links = getlinks();


// **********************************************************************
// We do all our prepwork here
// **********************************************************************

$loginstatus = logincheck();
$isloggedin = $loginstatus[loginstatus];
$loggedinname = $loginstatus[username];

$ads = "";

// **********************************************************************
// End Prepwork - Output the page to the user
// **********************************************************************

$password = $_POST['password']; 
$password = preg_replace("/[^a-zA-Z0-9s]/", "", $password); //Strip out all non alphanumeric chars
$password = secure($password);

$password = md5($password);
$admpass = md5($admpass);

if($isloggedin == "yes"){
$level = getacctstatus($loggedinname);

if($level == 4){

//Do our session related info...

if($password === $admpass){
//We've fully authenticated
//Set the session password

$_SESSION['password'] = $password;

//Output the rest of the page to the site admin.............
$article_title = "Admin Login Successful";
$article_content = "<p>Credentials Approved!  Welcome back ".$loggedinname.".  Please <a href='admconsole.php'>Click Here</a> to load the Admin CP.</p>";


}
else{
session_destroy();
$article_title = "Access Denied";
$article_content = "<p>Access Denied</p>";
}

}
else{
$article_title = "Access Denied";
$article_content = "<p>Access Denied</p>";
session_destroy();
}


}
else{
$article_title = "Access Denied";
$article_content = "<p>Access Denied</p>";
session_destroy();
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

$templatevars[customheadtag] = ""; //  

// Now we send all this, plus the page name / type to the template system...
// Valid page types are: generic, admin, noads

$template = spittemplate("generic",$templatevars,$isloggedin);

echo $template;

// **********************************************************************
// End Template Definition
// **********************************************************************

?>