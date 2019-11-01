<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////
// BRANDON'S MYBB MULTIFORUMS MOD VERSION 3.0.0
// REDISTRIBUTION PROHIBITED WITHOUT WRITTEN PERMISSION
////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Wake the sleeping giant
// Add our file includes so that we can work with the rest of the script

include("inc/functions.php");
include("inc/functions_template.php");

// Pull the site links from the database.
// We can also add more links at a per page level here.

$links = getlinks();

// Pull down the page content from the database...

$pagecontent = getsitecontent("tos");
$article_title = $pagecontent[title];
$article_content = $pagecontent[content];

// Make new lines out of ENTER keystrokes entered into the editor

$article_content = nl2br($article_content);

// **********************************************************************
// We do all our prepwork here
// **********************************************************************

// Check if user is logged in...

$loginstatus = logincheck();
$isloggedin = $loginstatus[loginstatus];
$loggedinname = $loginstatus[username];

// Grab our ads for this page.
// The any page is generic for any standard page.
// We can also set the ads to be blank for special pages.

$ads = getads("any","cms");

// **********************************************************************
// End Prepwork - Output the page to the user
// **********************************************************************


// **********************************************************************
// Begin Template Definition
// See inc > functions_template.php for more on the template system.
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