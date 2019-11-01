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

// Pull down the page content from the database...

$pagecontent = getsitecontent("index");
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

// THIS PAGE IS USED TO ACTIVATE A FORUM...

$forum = $_GET["forum"];
$forum = preg_replace("/[^a-zA-Z0-9s]/", "", $forum);
$forum = secure($forum);

$code = $_GET["code"];
$code = preg_replace("/[^a-zA-Z0-9s]/", "", $code);
$code = secure($code);

if($forum != ""){

// We are attempting to activate a forum...
	
	$query = "SELECT * FROM site_hosted_forums WHERE forumname='".$forum."'";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

	if($num > 0){

	while ($i < 1) {

	$actcode=@mysql_result($result,$i,"actcode");
	$actstatus=@mysql_result($result,$i,"actstatus");

	$i++;
	
	}

		if($actstatus > 0){

		$article_title = "Forum Already Activated";
		$article_content = "This forum has already been activated.  No further action is necessary on your part.";

		}
		else{

		// Attempt to activate the forum...

			if($code == ""){

			// Manual activation, no code specified

			$article_title = "Activate a Forum";
			$article_content = "Here you can activate your forum.  Simply enter your activation code into the box below to activate your forum.<br/><br/>
			<form id='actform' method='get' action='activate.php' onsubmit='return actvalid();'>
			<div id='avform'>
 			<input name='forum' type='hidden' id='forum' value='".$forum."'/> 
  			Activation Code: 
  			<input name='code' type='text' id='code' maxlength='20'/> 
  			<input type='submit' name='Submit' value='Submit'/> <span id='errorarea'></span>
			</div>
			</form>";

			}
			else if($code == "resend"){

			$article_title = "Resend Activation Code";
			$article_content = "";

			

			}
			else if($actcode == $code){

			// Activation code matches

			// Update the forum's activation status in the database...
			$query = "UPDATE site_hosted_forums SET actstatus=1 WHERE forumname='".$forum."'";
			mysql_query($query);

			$article_title = "<img src='templates/icons/yes.gif' alt='yes'/> Thank you for activating your forums";
			$article_content = "Thank you for activating your forums!  You may <a href='http://www.".$forum.".".$domain."'>click here</a> to return to your forums.";

			}
			else{

			$article_title = "<img src='templates/icons/delete.gif' alt='Red X'/> Incorrect Activation Code";
			$article_content = "The activation code you entered is incorrect.  Please double check the code and try again.  Activation codes are case sensitive.<br/><br/>
			<form id='actform' method='get' action='activate.php'>
			<div id='avform'>
 			<input name='forum' type='hidden' id='forum' value='".$forum."'/> 
  			Activation Code: 
  			<input name='code' type='text' id='code' maxlength='20'/>
  			<input type='submit' name='Submit' value='Submit'/>
			</div>
			</form>";


			}


		}


	}
	else{

	$article_title = "Forum Not Found";
	$article_content = "We could not find a forum named ".$forum." in our system.  If you believe this is in error, please contact the site administration.";

	}


}
else{

$article_title = "No Forum Specified";
$article_content = "No forum name to activate was specified.  Please double check your activation link.";

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

$templatevars[customheadtag] = "<script type='text/javascript' src='js/actvalid.js'></script>"; //  

// Now we send all this, plus the page name / type to the template system...
// Valid page types are: generic, admin, noads

$template = spittemplate("generic",$templatevars,$isloggedin);

echo $template;

// **********************************************************************
// End Template Definition
// **********************************************************************

?>