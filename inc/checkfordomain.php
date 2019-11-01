<?php

// THIS FILE CHECKS IF A FORUM IS USING A CUSTOM DOMAIN...

include("config.php");
include("functions.php");

function checkdomain($customdomain){

$ourdomain = secure($customdomain);

// Run a lookup query to see if the domain exists in a special table...

	$query = "SELECT * FROM domainmap WHERE domain='".$ourdomain."' LIMIT 1";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

	if($num > 0){
	
		$ourprefix=@mysql_result($result,$i,"forumname");
		$ourprefix = $ourprefix."_";
		return $ourprefix;
		
	}
	else{
		return "no";
	}
}

function checkdatabase($forumname){

// This function determines which database a forum is on...

	$query = "SELECT * FROM site_hosted_forums WHERE forumname='".$forumname."' LIMIT 1";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

	if($num > 0){

		$weusedb=@mysql_result($result,$i,"usesdbconn");
		return $weusedb;

	}
	else{
		return "error";
	}

}

?>