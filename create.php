<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////
// BRANDON'S MYBB MULTIFORUMS MOD VERSION 3.0.0
// REDISTRIBUTION PROHIBITED WITHOUT WRITTEN PERMISSION
////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Wake the sleeping giant

include("inc/functions.php");
include("inc/config.php");
include("inc/functions_template.php");

$links = getlinks();

$pagecontent = getsitecontent("index");
$article_title = $pagecontent[title];
$article_content = $pagecontent[content];
$article_content = nl2br($article_content);

// Grab the site name just for this page...

$sitename = grabanysetting("sitename");

// **********************************************************************
// We do all our prepwork here
// **********************************************************************

// Check if user is logged in...

$loginstatus = logincheck();
$isloggedin = $loginstatus[loginstatus];
$loggedinname = $loginstatus[username];

// Grab our ads...

$ads = getads("any","cms");

// **********************************************************************
// End Prepwork - Output the page to the user
// **********************************************************************

// The purpose of this page is to check if the user's access name is
// available.  Then this page will take in the user's forum information
// and then forward that to another page that will make the actual
// forum for the user.

// First we want to take in a forum access name...

$fname = $_GET["fname"];
$fname = preg_replace("/[^a-zA-Z0-9s]/", "", $fname);
$fname = secure($fname);

$fname = strtolower($fname); // Convert the forum name to lower case...

// Check if the name is blank or not...

if($fname == ""){

// Prompt the user for a new forum name...

	$article_title = "Create a Free Forum";
	$article_content = "Welcome to ".$sitename." free forum hosting.  Let's get started creating your free forum!  Simply enter in your desired forum access name in the box below.
	Your access name will be the URL of your forum.  Valid access names can contain letters and numbers only and must be between 6 and 20 characters long.
	Once you enter your access name click the <i>Check Availability</i> button to see if it is available for your use.<br/><br/>


	<form id='form1' method='get' action='create.php' onsubmit='return Checker.instance.checkCanCreate();'>
	<div id='checkavailform'>
  	<input name='fname' type='text' id='fname' maxlength='20'/>.".$domain."  &nbsp;<span id='fnamecheck'></span><br/><br/> 
  	<input type='submit' name='Submit' value='Create My Forum'/> <input type='button' name='checkbutton' id='checkbutton' value='Check Availability' onclick='Checker.instance.checkName();'/>
	</div>
	</form>";


}
else{

// Check if the name is valid syntax...

$length = strlen($fname);

if($length < 6 or $length > 20){

	$article_title = "<img class='borderless' src='templates/icons/delete.gif' alt='Forum Name Not Available'/> Invalid forum access name";
	$article_content = "The access name you entered appears to be invalid.  Valid access names must be between 6 and 20 characters long.  Please try choosing a new access name using the form below.<br/><br/>
	
	<form id='form1' method='get' action='create.php'>
	<div id='checkavailform'>
  	<input name='fname' type='text' id='fname' maxlength='20'/>.".$domain."<br/><br/> 
  	<input type='submit' name='Submit' value='Check Availability'/>
	</div>
	</form>";

}
else{

// The forum name appears valid, continue...

// Check if the forum name is available...

	$available = "yes";
	$num = 0;

	$query = "SELECT * FROM site_hosted_forums WHERE forumname='".$fname."'";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

	if($num > 0){

		$available = "no";
	
	}

	$query = "SELECT * FROM reserved_access_names WHERE forumname='".$fname."'";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

	if($num > 0){

		$available = "no";
	
	}

	if($available == "yes"){

		// The forum name is available

		$article_title = "<img class='borderless' src='templates/icons/yes.gif' alt='Forum Name Available'/> Yay!  The access name ".$fname." is available!";
		$article_content = "The access name <b>".$fname."</b> is available!  To finish creating your free forum please use the form below.  Your free forum will be up and running in no time!<br/>
		<form id='form1' method='post' action='domake.php'>
		<div id='newforumform'>
  		<p>
    		<input name='fname' type='hidden' id='fname' value='".$fname."'/> 
		
		<div id='labelContainer' style='float: left; width: 190px; text-align: right; margin-right: 10px;'>
			<p style='padding-top: 4px;'><label for='uname'>Desired Admin Username:</label></p>
			<p style='padding-top: 6px;'><label for='pass1'>Desired Admin Password:</label></p>
			<p style='padding-top: 4px;'><label for='pass2'>Confirm Admin Password:</label></p>
			<p style='padding-top: 4px;'><label for='email'>Your Email Address:</label></p>
			<p style='padding-top: 7px;'><label for='forumname'>Forum Name:</label></p>
		</div>
		
		<div id='fieldContainer' style='float: left; width: 210px;'>
			<p><input name='uname' type='text' id='uname' maxlength='20' style='width: 200px;'/></p>
			<p><input name='pass1' type='password' id='pass1' maxlength='20' style='width: 200px;'/></p>
			<p><input name='pass2' type='password' id='pass2' maxlength='20' style='width: 200px;'/></p>
			<p><input name='email' type='text' id='email' style='width: 200px;'/></p>
			<p><input name='forumname' type='text' id='forumname' style='width: 200px;'/></p>
		</div>
		
		<div id='tipContainer' style='float: left; width: 16px;'>
		
			<p style='padding-top: 2px;'><img class='tips' src='templates/icons/blueinfo.gif' alt='information' title='Your admin username is the username you use to log in to your MyBB forum.  This may contain letters, numbers, spaces and special characters.'/></p>
			<p style='padding-top: 3px;'><img class='tips' src='templates/icons/blueinfo.gif' alt='information' title='Your admin password is used to log in to the admin account on your forum.  This password should be strong to prevent unauthorized access to your forum.  This may contain letters, numbers and special characters.'/></p>
			<p style='padding-top: 3px;'><img class='tips' src='templates/icons/blueinfo.gif' alt='information' title='For security reasons we make you enter in your new password twice.  Your passwords must match.'/></p>
			<p style='padding-top: 3px;'><img class='tips' src='templates/icons/blueinfo.gif' alt='information' title='You must enter in a valid email address for your forums.  You may have to validate your forums by clicking a link sent to the email address you specify here.'/></p>
			<p style='padding-top: 3px;'><img class='tips' src='templates/icons/blueinfo.gif' alt='information' title='Your forum name appears on all the pages of your forums.  You can change this later in your Admin CP.'/></p>
  		
		
		</div>
		
		<div class='clearFloat'><!-- DO NOT REMOVE --></div>";

		if($isloggedin == "yes"){

		// We are logged in.  Do we want to add this forum to our account?

  		$article_content = $article_content."<p><input name='addtoacct' type='checkbox' id='addtoacct' value='yes'/> 
    		<label for='addtoacct'>Add this forum to my account</label> <img class='tips' src='templates/icons/blueinfo.gif' alt='information' title='Checking the above checkbox will add this forum to your account and will make receiving support easier. This will also speed up the process of downloading forum backups if this option is enabled. This is an optional step, however it is highly recommended.'/></p>";
  		
		}

		$captcha = grabanysetting("verifymethod");

		if($captcha == "captcha" or $captcha == "emailandcaptcha"){

		// The recaptcha is enabled...

			require_once('recaptcha/recaptchalib.php');
			$publickey = grabanysetting("recaptchakey");

			// Show the captcha...

			$captchacode = recaptcha_get_html($publickey);

			$article_content = $article_content."<p>Please fill out the captcha below.  The captcha helps keep bots and other malicious automated systems off our servers.<br/></p>".$captchacode."";


		}

		$article_content = $article_content."<p>
    		<input name='tos' type='checkbox' id='tos' value='yes'/>
		<label for='tos'>I agree to the ".$sitename." <a href='tos.php'>Terms of Service</a> and am at least 13 years of age or have parental permission to access this site.</label></p>
  		<p>
    		<input type='submit' name='Submit' value='Create My Free Forum'/> 
  		</p>
		</div></form>";

		
	
	}
	else{

		// The forum name is not available or is reserved

		$article_title = "<img class='borderless' src='templates/icons/delete.gif' alt='Forum Name Not Available'/> Forum Name Not Available";
		$article_content = "We're sorry, but the access name <b>".$fname."</b> is not available.  It may have been taken by another user or it may be reserved.
		Please use the form below to choose another access name.<br/><br/>
		<form id='submitform' method='get' action='create.php'>
		<div id='checkavailform'>
  		<input name='fname' type='text' id='fname' maxlength='20'/>.".$domain."<br/><br/> 
  		<input type='submit' name='Submit' value='Check Availability'/>
		</div>
		</form>";	


	}

	
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

// Here we need to load multiple head codes...

if($fname != ""){

$templatevars[customheadtag] = "<script type='text/javascript' src='js/validate.js'></script>
<script type='text/javascript' src='js/validateonload.js'></script>";

}
else{

// The forum name is blank, so load the AJAX forum name checker...

$templatevars[customheadtag] = "<script type='text/javascript' src='js/checkfname.js'></script>";

}

// Now we send all this, plus the page name / type to the template system...
// Valid page types are: generic, admin, noads

$template = spittemplate("generic",$templatevars,$isloggedin);

echo $template;

// **********************************************************************
// End Template Definition
// **********************************************************************

?>