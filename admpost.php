<?php

// Wake the sleeping giant

include("inc/functions.php");
include("inc/functions_template.php");

$links = getlinks();

// **********************************************************************
// We do all our prepwork here
// **********************************************************************

$loginstatus = logincheck();
$isloggedin = $loginstatus[loginstatus];
$loggedinname = $loginstatus[username];

// **********************************************************************
// End Prepwork - Output the page to the user
// **********************************************************************

if($isloggedin == "yes"){

$status = getacctstatus($loggedinname);

if($status == 4 or $status == "4"){

//We are an admin user who is logged in...

$links = getadmlinks();

//Initialize Variables

//Start our settings switchout

$page = $_POST["page"];
$page = preg_replace("/[^a-zA-Z0-9s]/", "", $page);
$page = secure($page);

if($page == "pages"){

	//BEGIN PAGE EDIT CODE HERE...

	if($_POST["type"] == "editpage"){
	//We are editing an existing page...
	//Secure our variables...	
	$title = $_POST["pagetitle"];
	$content = $_POST["pagecontent"];
	$pagename = $_POST["pageurl"];

	$title = secure($title);
	$content = mysql_real_escape_string($content);
	$pagename = secure($pagename);

	//Check that page does indeed exist...
	$query = "SELECT * FROM site_content WHERE page='".$pagename."'";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

	if($num > 0){
	$query = "UPDATE site_content SET value='".$content."' WHERE page='".$pagename."'";
	mysql_query($query);

	$query = "UPDATE site_content SET title='".$title."' WHERE page='".$pagename."'";
	mysql_query($query);

	$article_title = "Page Updated Successfully";
	$article_content = "<p>Your page has been updated.  <a href='admconsole.php?set=pages'>Click Here</a> to manage pages.</p>";
	
	
	}
	else{
	$article_title = "Page Does Not Exist!";
	$article_content = "<p>Error</p>";
	}

	}
	else if($_POST["type"] == "newpage"){

	//Secure our variables...	
	$title = $_POST["pagetitle"];
	$content = $_POST["pagecontent"];
	$pagename = $_POST["pageurl"];

	$title = secure($title);
	$content = mysql_real_escape_string($content);
	$pagename = secure($pagename);
	$pagename = preg_replace("/[^a-zA-Z0-9s]/", "", $pagename);

	if($pagename != ""){

	//Check that page doesn't already exist

	$query = "SELECT * FROM site_content WHERE page='".$pagename."'";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

	if($num > 0){
	$article_title = "Error - Page Already Exists!";
	$article_content = "<p>Error.  The page already exists, thus logic suggests we cannot make the page again.  You can, however,
	<a href='admconsole.php?set=pages&amp;do=edit&amp;more=".$pagename."'>Edit This Page</a>.</p>";
	
	}
	else{
	//Page doesn't exist, so we will create it from scratch.....	

	mysql_query("INSERT INTO site_content VALUES ('', '$pagename', '$title', '$content')");

	$article_title = "Page Created Successfully";
	$article_content = "<p>Your page has been created successfully.  <a href='admconsole.php?set=pages'>Return to the pages listing.</a></p>";

	}
	}
	else{

	// Making a blank page is not allowed
	$article_title = "Page Creation Error";
	$article_content = "You are not allowed to create a page with a blank URL.  Please <a href='admconsole.php?set=pages'>go back</a> and try again.";


	}
	

	}
}
else if($page == "createad"){

	$adname = $_POST["adname"];
	$adname = secure($adname);
	
	$forumheader = $_POST["forumheader"];
	$forumheader = secure($forumheader);
	
	$forumfooter = $_POST["forumfooter"];
	$forumfooter = secure($forumfooter);
	
	$sitecms = $_POST["sitecms"];
	$sitecms = secure($sitecms);
	
	$htmlcode = $_POST["htmlcode"];
	$htmlcode = mysql_real_escape_string($htmlcode);
	
	$maximpressions = $_POST["maximpressions"];
	$maximpressions = secure($maximpressions);
	
	$date = date("y-m-d");  
	
	if($forumheader != 1){
		$forumheader = 0;
	}
	if($forumfooter != 1){
		$forumfooter = 0;
	}
	if($sitecms != 1){
		$sitecms = 0;
	}
	
	if($adname == ""){
	
		$article_title = "Ad Name Missing";
		$article_content = "You must give the ad a name before you can submit it!";
	
	}
	else{
	
		if(!is_numeric($maximpressions)){
		
			$article_title = "Max Impressions must be a numeric value";
			$article_content = "Please go back and correct this error.";
			
		}
		else{
		
			// The ad worked!
			
			mysql_query("INSERT INTO ads VALUES ('', '$adname', '$htmlcode', '$maximpressions', '0', '$date', 'active', '$forumheader', '$forumfooter', '$sitecms', '')");
			
			$article_title = "Ad Created Successfully";
			$article_content = "The ad was created successfully.  <a href='admconsole.php?set=ads'>Click here</a> to manage ads.";
		
		}
	
	}
	
}
else if($page == "editad"){

	// Update an ad in the database
	$adActive = secure($_POST["active"]);
	$maxImpressions = secure($_POST["maximp"]);
	$adContent = mysql_real_escape_string($_POST["adcontent"]);
	$adID = secure($_POST["id"]);
	
	if($adActive != "active"){
		$adActive = "inactive";
	}
	
	if(!is_numeric($maxImpressions)){
		$maxImpressions = 0;
	}
	
	$query = "UPDATE ads SET status='".$adActive."' WHERE id=".$adID;
	mysql_query($query);
	
	$query = "UPDATE ads SET impressions='".$maxImpressions."' WHERE id=".$adID;
	mysql_query($query);
	
	$query = "UPDATE ads SET text='".$adContent."' WHERE id=".$adID;
	mysql_query($query);
	
	$article_title = "Ad Edited Successfully";
	$article_content = "The ad was edited successfully.  <a href='admconsole.php?set=ads'>Click here</a> to manage ads.";

}
else{
$article_title = "Access Denied";
$article_content = "<p>Access Denied - Resource does not exist!</p><br/>
Page: ".$page;
}

}
	else{
	$article_title = "Access Denied";
	$article_content = "<p>Access Denied</p>";
	}
	
}else{
$article_title = "Access Denied";
$article_content = "<p>Access Denied</p>";

}


// **********************************************************************
// Begin Template Definition
// **********************************************************************

// Disable Ads for a special page...

$ads = "";

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
