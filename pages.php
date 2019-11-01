<?php

// Wake the sleeping giant

include("inc/functions.php");
include("inc/functions_template.php");

$links = getlinks();

// **********************************************************************
// We do all our prepwork here
// **********************************************************************

$page = $_GET["page"];
$page = preg_replace("/[^a-zA-Z0-9s]/", "", $page);
$page = secure($page);

if($page != ""){

$pagecontent = getsitecontent($page);
$article_title = $pagecontent[title];
$article_content = "<p>".$pagecontent[content]."</p>";

$article_content = nl2br($article_content);

}
else{

$article_title = "No Page Selected";
$article_content = "This page is designed to pull up a page based on a page name supplied.  Please specify a page name in the URL and I'll try and fetch that page for you.";

}


$loginstatus = logincheck();
$isloggedin = $loginstatus[loginstatus];
$loggedinname = $loginstatus[username];

// Grab our settings...

$browsertitle = grabanysetting("browsertitle");
$sitename = grabanysetting("sitename");
$slogan = grabanysetting("slogan");
$showlogin = grabanysetting("showlogin");

// Grab our ads...

$ads = getads("any","cms");


// **********************************************************************
// End Prepwork - Output the page to the user
// **********************************************************************

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
