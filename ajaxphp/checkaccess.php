<?php

// This file checks if an access name is taken or not and then outputs the results back to the AJAX call...

include("../inc/config.php");
include("../inc/functions.php");

// Grab the forum name

$fname = $_GET["fname"];
$fname = preg_replace("/[^a-zA-Z0-9s]/", "", $fname);
$fname = secure($fname);

// Set a status, message and image variable
$status = "noGo";
$message = "";
$image = ""; // Images in templates => icons folder

// Check first the length...

if(strlen($fname) < 6){

// Too short...

	$message = "The forum name ".$fname." is too short.";
	$image = "warning.gif";

}
else if(strlen($fname) > 20){

	$message = "The forum name ".$fname." is too long.";
	$image = "warning.gif";

}
else{

// It's not too short or long, now check the db...

	$available = "yes";
	$num = 0;

	$query = "SELECT * FROM site_hosted_forums WHERE forumname='".$fname."'";
	$result = mysql_query($query);
	$num = mysql_num_rows($result);

	if($num > 0){

		$available = "no";
	
	}

	$query = "SELECT * FROM reserved_access_names WHERE forumname='".$fname."'";
	$result = mysql_query($query);
	$num = mysql_num_rows($result);

	if($num > 0){

		$available = "no";
	
	}

if($available != "yes"){

	$message =  "The forum name ".$fname." is not available.";
	$image = "delete.gif";
	
}
else{

	$message = "The forum name ".$fname." is available!";
	$status = "go";
	$image = "yes.gif";
	
}

}

// Prepare our response
$send = array('fname' => $fname, 'message'=> $message, 'status' => $status, 'image' => $image);
$send = json_encode($send);
echo $send;

?>