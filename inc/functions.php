<?php

// File ID: functions.php
// Purpose: Provides basic sitewide functions

// Start our session...
session_start();

//Connect to the database first
connect();

//This function simply connects us to the database
function connect(){

	include("config.php");
	$conn = mysql_connect("localhost", $dbuser, $dbpass) or die ('Error connecting to MySQL');
	mysql_select_db($dbname);

}

//This function performs security checks on all incoming form data
function secure($data){

if(is_array($data)){
die("Hacking Attempt!");
}


//MySQL Real Escape String
$data = mysql_real_escape_string($data);

//Strip HTML tags
$data = strip_tags($data, '');

return $data;

}

function getsitecontent($page){
$query = "SELECT * FROM site_content WHERE page = '$page'";
$result = @mysql_query($query);
$num = @mysql_numrows($result);

//Loop out code
$i=0;
while ($i < 1) {

$title=@mysql_result($result,$i,"title");
$content=@mysql_result($result,$i,"value");

$title = stripslashes($title);
$content = stripslashes($content);

$i++;
}

$value[content] = $content;
$value[title] = $title;

return $value;
}

//This function replaces template values
function replace($old,$new,$template)
	{
	$template = str_replace($old, $new, $template);
	return $template;
	}

function logincheck(){

$pathtomybb = grabanysetting("pathtomybbfiles");
$fprefix = grabanysetting("uforumprefix");

include($pathtomybb."/inc/functions_user.php");
include("config.php");

if($_SESSION['is_logged_in'] == TRUE and $_SESSION['unique'] == $rsession){

// We have detected a session, so let's attempt to validate the user...

$username = secure($_SESSION['username']);

$query = "SELECT * FROM ".$fprefix."users WHERE username = '$username'";
$result = mysql_query($query);
$num = mysql_numrows($result);

if($num > 0){

	// The user exists, so they're probably logged in...
	// The only way this could break is if they changed their PW in MyBB
	// This would still work though because they could still be logged in with a new PW

$isloggedin = "yes";


}
else{

// The user doesn't exist in the DB, so destroy the session...

$isloggedin = "no";
session_destroy();

}


}
else if (isset($_COOKIE['rusername']) and isset($_COOKIE['rpass'])){

$username = $_COOKIE['rusername'];
$password = $_COOKIE['rpass'];

$username = secure($username);
$password = secure($password);

//Run login operation
$query = "SELECT * FROM ".$fprefix."users WHERE username = '$username'";
$result = mysql_query($query);
$num = mysql_numrows($result);

//Loop out code
$i=0;
while ($i < 1) {

$luser=@mysql_result($result,$i,"username");
$lpass=@mysql_result($result,$i,"password");

$i++;
}

	if($username == $luser and $password == $lpass){
		$isloggedin = "yes";
	}
	else{
	if (isset($_COOKIE['rusername'])){
	$past = time() - 10; 
	setcookie("rusername",$username,$past);
	}

	if (isset($_COOKIE['rpass'])){
	$past = time() - 10; 
	setcookie("rpass",$password,$past);
	}
	$isloggedin = "no";
	}

}
else
{
//User is not logged in
$isloggedin = "no";

} 

//Return our user data
$userdata[loginstatus] = $isloggedin;
$userdata[username] = $username;

return $userdata;

}

function getacctstatus($username){

//This function determines which MyBB usergroup the user belongs to, if any...

//Grab the forum prefix...
$fprefix = grabanysetting("uforumprefix");

$query = "SELECT * FROM ".$fprefix."users WHERE username = '$username'";
$result = mysql_query($query);
$num = mysql_numrows($result);

//Loop out code
$i=0;
while ($i < 1) {

$group=@mysql_result($result,$i,"usergroup");

$i++;
}

return $group;

}

function grabanysetting($where){

$query = "SELECT * FROM settings WHERE name = '".$where."'";
$result = @mysql_query($query);
$num = @mysql_numrows($result);

//Loop out code
$i=0;
while ($i < 1) {

$value=@mysql_result($result,$i,"value");
$value = stripslashes($value);
$i++;
}
return $value;
}

function getlinks(){


// We will be getting our links from the database...

$links = "";

	$query = "SELECT * FROM links ORDER BY id ASC";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

	//Loop out code
	$i=0;
	while ($i < $num) {

	$linktext=@mysql_result($result,$i,"linktext");
	$linkurl=@mysql_result($result,$i,"linkurl");

	$linktext = stripslashes($linktext);

	$links = $links."<li><a href='".$linkurl."'>".$linktext."</a></li>";

	$i++;
	}


return $links;

}

function getads($page, $location){

/*

// Function to display site advertisements

if($page == "any"){
$page = "";
}

$query = "SELECT * FROM ads WHERE page = '".$page."' and status = 'active' ORDER BY RAND() LIMIT 1";
$result = @mysql_query($query);
$num = @mysql_numrows($result);

if($num > 0){

//Loop out code
$i=0;
while ($i < 1) {

$value=@mysql_result($result,$i,"text");
$value = stripslashes($value);
$aid=@mysql_result($result,$i,"id");
$actualimpressions=@mysql_result($result,$i,"actualimpressions");
$impressions=@mysql_result($result,$i,"impressions");
$i++;
}

if($impressions == ""){
$impressions = 0;
}

$actualimpressions = $actualimpressions + 1;

//Update the impressions count
$query = "UPDATE ads SET actualimpressions='".$actualimpressions."' WHERE id='".$aid."'";
mysql_query($query);

//Check that ad is not over max impressions...
if ($actualimpressions >= $impressions and $impressions != 0){
$query = "UPDATE ads SET status='inactive' WHERE id='".$aid."'";
mysql_query($query);
}


}
else{
$value = "";
}

*/

$value = "";
return $value;

}

function deleteforum($forumname){

// This function deletes a forum from the database...

$status = "success";

	$query = "SELECT * FROM site_hosted_forums WHERE forumname='".$forumname."'";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

	if($num > 0){

	while ($i < $num) {

	$isondb=@mysql_result($result,$i,"usesdbconn");
	
	$i++;
	}

	// Now that we have the forum's database connection, we can delete it from our site...
	// Run the database delete queries...

	$table = $forumname."_";

			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."adminlog");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."adminoptions");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."adminsessions");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."adminviews");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."banfilters");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."announcements");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."attachments");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."attachtypes");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."awaitingactivation");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."badwords");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."banned");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."calendars");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."calendarpermissions");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."captcha");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."forumsread");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."datacache");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."events");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."forumpermissions");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."forums");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."forumsubscriptions");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."groupleaders");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."helpdocs");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."helpsections");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."joinrequests");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."mailqueue");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."moderatorlog");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."moderators");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."modtools");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."mycode");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."polls");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."pollvotes");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."posts");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."privatemessages");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."profilefields");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."reportedposts");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."reputation");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."searchlog");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."sessions");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."settinggroups");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."settings");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."smilies");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."templategroups");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."templates");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."templatesets");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."themes");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."threadratings");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."threads");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."threadsread");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."userfields");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."usergroups");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."users");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."usertitles");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."icons");
			
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."massemails");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."mailerrors");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."maillogs");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."promotions");

			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."promotionlogs");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."spiders");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."stats");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."tasks");

			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."tasklog");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."themestylesheets");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."threadviews");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."warninglevels");

			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."warningtypes");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."warnings");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."isawaiting_activation");
			mysql_query("DROP TABLE IF EXISTS ".$isondb.".".$table."threadsubscriptions");

	// Now we are going to attempt to delete the files associated with this forum...
	// Include config.php so we can work with file paths...

	include("config.php");

	$access = $forumname; // Change naming convention as some of this is old code straight from the old script ;)

	// Delete the avatars directory for the forum...

	$avydir = $ourmybbpath."/users/".$access."/avatars";
	

	if(file_exists($avydir)){

	//First delete all files in the directory
	clean($avydir);

	//Now delete the directory
	rmdir($avydir);

	}      
	else{

	// echo "Warning!  Filepath not found for forum ".$forumname."!</br>";
	// die("Filepath Not Found");

	}

	// Delete the attachments directory for this forum, located in the users folder...
	// First we need to clean it...
	
	$attdir = $ourmybbpath."/users/".$access;
	if(file_exists($attdir)){

	//First delete all files in the directory
	clean($attdir);

	//Now delete the directory
	rmdir($attdir);

	}  

	// Now we are going to attempt to clean the cache directory for this forum...

	$cachedir = $ourmybbpath."/cache/".$access;
	if(file_exists($cachedir)){

	//First delete all files in the directory
	clean($cachedir);

	//Now delete the directory
	rmdir($cachedir);

	}  

	// Lastly delete the forum from the database completely...

	$query = "DELETE FROM domainmap WHERE forumname = '".$access."'";
	mysql_query($query);

	$query = "DELETE FROM site_hosted_forums WHERE forumname = '".$access."'";
	mysql_query($query);

	// All done.  That was a bitter pill to swallow, wasn't it? :)

	}
	else{

	$status = "failure";

	}


return $status;

}

//Function for deleting all files in the avatars directory or other directories...
function clean($avydir) {
                
                $directory = $avydir;

                if( !$dirhandle = opendir($directory) )
                        return;

                while( false !== ($filename = readdir($dirhandle)) ) {
                        if( $filename != "." && $filename != ".." ) {
                                $filename = $directory. "/". $filename;
					  unlink($filename);
                        }
                }

}

function getadmlinks() {

// This function gets the links for the Admin CP...

$links = "<li><a href='index.php'>Site Home</a></li><li><a href='admconsole.php'>Admin CP Home</a></li><li><a href='admconsole.php?set=forums'>View Hosted Forums</a></li><li><a href='admconsole.php?set=prune'>Prune Forums</a></li><li><a href='admconsole.php?set=pages'>Manage the CMS</a></li>";

return $links;

}



?>
