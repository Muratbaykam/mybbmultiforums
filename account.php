<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////
// BRANDON'S MYBB MULTIFORUMS MOD VERSION 3.0.0
// REDISTRIBUTION PROHIBITED WITHOUT WRITTEN PERMISSION
////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Wake the sleeping giant
// Add our file includes so that we can work with the rest of the script

include("inc/functions.php");
include("inc/functions_template.php");
include("inc/config.php");

// Pull the site links from the database.
// We can also add more links at a per page level here.

$links = getlinks();


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

if($isloggedin == "yes"){

$article_title = "My Account and Hosted Forums:";
$article_content = "<p>Welcome back to your account ".$loggedinname.".  Below are all of the forums that you have signed up for and added to your account.</p>";

// Fetch the forums...

$query = "SELECT * FROM site_hosted_forums WHERE assocaccount = '".$loggedinname."' ORDER BY id DESC";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

	
	if($num > 0){

		while ($i < $num) {

		$name=@mysql_result($result,$i,"forumname");
		$created=@mysql_result($result,$i,"created");

		$spit = $spit."<img src='templates/icons/next.gif' class='borderless' alt='Arrow'/> <a href='http://".$name.".".$domain."'>".$name.".".$domain."</a> :: <a href='http://".$name.".".$domain."/admin/'>Admin CP</a> - Created On: ".$created."<br/>";

		$i++;
		}

	$spit = $spit."<br/>";
	$article_content = $article_content."".$spit;

	}
	else{

	$article_content = $article_content."<p>You have not added any forums to your account.  <a href='create.php'>Click here</a> to create a new forum.  Make sure and check the <b>add this forum to my account</b> checkbox when creating the forum so that it is displayed here.</p>";	


	}

}
else{

$article_title = "Guest Access Forbidden";
$article_content = "Guests cannot access this page.  Please <a href='login.php'>log in</a> to view this page.";

}


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
