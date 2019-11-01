<?php

// Wake the sleeping giant

include("inc/functions.php");
include("inc/functions_template.php");

$links = getlinks();

// **********************************************************************
// We do all our prepwork here
// **********************************************************************

$userdata = logincheck();
$isloggedin = $userdata[loginstatus];
$loggedinname = $userdata[username];

// Grab our ads...

$ads = getads("any","cms");

// Destroy the user's session, once and for all...

$_SESSION = array();
session_destroy();

if (isset($_COOKIE['rusername']) and isset($_COOKIE['rpass'])){

$past = time() - 10; 
setcookie("rusername",$username,$past);
setcookie("rpass",$password,$past);
$isloggedin = "no";

}
else
{
//User is not logged in
$isloggedin = "no";
} 

// **********************************************************************
// End Prepwork - Output the page to the user
// **********************************************************************

$article_title = "Log Out Successful";
$article_content = "<p>You have been logged out successfully. <a href='login.php'>Click Here</a> to log in again.</p>";

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
