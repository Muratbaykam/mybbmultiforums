<?php

// This file performs all template related functions for the MyBB Multiforums Mod

function spittemplate($page, $templatearray,$loginstatus){

// This function handles formatting the template for return to the page calling this function...

// First we must grab the current template's location...

$themeurl = grabanysetting("templateurl");

// Now we read in the contents of the template HTML file...

$template = file_get_contents($themeurl);

// Now we need to grab information necessary to the template system from the database...

$browsertitle = grabanysetting("browsertitle");
$sitename = grabanysetting("sitename");
$slogan = grabanysetting("slogan");

// Now we see if we have to change the ads to show the ad div...

$siteads = $templatearray[ads]; // Load up the ad code for the page...

$showaddiv = grabanysetting("createaddiv");

if($showaddiv == "yes" and $siteads != ""){

// We are showing the ad div and the ad code is not blank...

$siteads = "<div id='adsbar'>".$siteads."</div>";

}

// Now we use that information to output the template...

// Article Title and Article Content

$template = replace(':ARTICLETITLE:',$templatearray[articletitle],$template);
$template = replace(':ARTICLECONTENT:',$templatearray[articlecontent],$template);

// The links and ads come next.  These are controlled at the individual page level, so some pages don't
// have ads.  They can also have special links, like ACP pages.

//Define our links
$template = replace(':LINKSBAR:',$templatearray[links],$template);

//Get the ad content...
$template = replace(':ADS:',$siteads,$template);

// The login bar at the top of the screen...
// This won't show the login bar if the returned value is blank...

$usinglogin = showlogin($loginstatus);

if($usinglogin != "DONTSHOW"){

$loginvar = $usinglogin;

}
else{

$loginvar = "";

}

$template = replace(':TOPLOGIN:',$loginvar,$template);

// Date and Time...

$date = date("F j, Y - g:i a");

$template = replace(':DATETIME:',$date,$template);

// Site and browser information...

$template = replace(':BROWSERTITLE:',$browsertitle,$template);
$template = replace(':SITENAME:',$sitename,$template);

//Get the slogan info
$template = replace(':SLOGAN:',$slogan,$template);

//Get the custom HEAD tag information
$template = replace(':HEAD:',$templatearray[customheadtag],$template);

// Now we return the template content so it can be echoed by the calling page

return $template;

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// SHOWLOGIN FUNCTION BELOW
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function showlogin($isloggedin){

// Show the login bar in the template, if enabled...

// Get the status of this option...

$showlogin = grabanysetting("showlogin");

if($showlogin == "yes"){

// Check if the user is logged in...

	if($isloggedin == "yes"){

	$loginvar = "<a href='account.php'>My Account</a> :: <a href='logout.php'>Log Out</a>";

	}
	else{

	// Get the registration url...

	$regurl = grabanysetting("regurl");

	$loginvar = "<a href='login.php'>Log In</a> :: <a href='".$regurl."'>Register</a>";

	}

return $loginvar;

}
else{

return "DONTSHOW";

}

}

?>