<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////
// BRANDON'S MYBB MULTIFORUMS MOD VERSION 3.0.0
// REDISTRIBUTION PROHIBITED WITHOUT WRITTEN PERMISSION
////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Wake the sleeping giant

include("inc/functions.php");
include("inc/config.php");
include("inc/functions_template.php");

if($scriptpath != ""){

	$scriptpath = $scriptpath."/";

}

// Only needed for pre-1.6.4 versions of MyBB

/*
$path = $scriptpath."".$ourmybbpath."/inc/functions.php";
require_once($path);

$path = $scriptpath."".$ourmybbpath."/inc/functions_user.php";
require_once($path);
*/

$links = getlinks();

$pagecontent = getsitecontent("index");
$article_title = $pagecontent[title];
$article_content = $pagecontent[content];
$article_content = nl2br($article_content);

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

// This file is our workhorse and is designed to create new forums...
// Luckily most of this we can copy from the old mkforum.php :)

// Take in our form variables...

$fname = $_POST['fname']; //Forum Name
$email = $_POST['email']; //Admin and Forum Email
$desusername = $_POST['uname']; //Admin Username
$pass1 = $_POST['pass1']; //Admin Password
$pass2 = $_POST['pass2']; //Admin Password Confirm
$display = $_POST['forumname']; //Forum Name as it appears on forums
$tos = $_POST['tos']; // Terms of Service Acceptance
$addtoacct = $_POST['addtoacct']; // Add to account variable

// Now we secure our form variables
// This is very important...

$fname = preg_replace("/[^a-zA-Z0-9s]/", "", $fname);
$fname = secure($fname);

$email = preg_replace("/[^a-zA-Z0-9@._-]/", "", $email);
$email = secure($email);

$desusername = secure($desusername);
$pass1 = secure($pass1);
$pass2 = secure($pass2);

$display = secure($display);

$tos = secure($tos);
$addtoacct = secure($addtoacct);

// IP Address Based Settings
$ip = $_SERVER['REMOTE_ADDR'];
$ip = preg_replace("/[^a-zA-Z0-9.]/", "", $ip);
$ip = secure($ip);

// All done with securing our variables...

// Convert forum name to lower case because server cannot understand uppercase subdomains
$fname = strtolower($fname);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// DEFINE PATHS
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$pathtomybbmf = grabanysetting("pathtomybbfiles");

if($pathtomybbmf == ""){

// Without a path to MyBB the script will not work properly.

die("The path to the MyBB files could not be determined.  This script is either not installed correctly or the pathtomybbfiles setting in your Host Admin CP is incorrect.");

}

// Include these files so we can salt our password later on...

include($pathtomybbmf."/inc/functions.php");
//include($pathtomybbmf."/inc/functions_user.php");

$masterprefix = grabanysetting("mforumprefix");


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// END DEFINE PATHS
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Now we are going to do some checks to see that we have entered in correct information...

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// BEGIN ERROR CHECKING...
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$pass = "yes";
$error = "";

	$emailisvalid = "no";


	if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
	
	$emailisvalid = "yes";

	}

	if($emailisvalid != "yes"){

	$pass = "no";
	$error = "It appears that you did not enter in a valid email address.  A valid email address is required.  Please go back and enter in a valid email address.";
	
	}

	if($pass1 != $pass2) {
	
	$pass = "no";
	$error = "Your passwords do not match.  Please go back and make sure your passwords match.";
	
	}

	//Check that the passwords are the right length, otherwise give up
	$pwlen = strlen($pass1);

	if($pwlen < 4) {
	
	$pass = "no";
	$error = "Your password is too short.  Your password must be at least 4 characters long, however the longer it is the more secure it will be.";

	}

	$islong = strlen($fname); //Get the length

	if ($islong > 20) {
    	
	$pass = "no";
	$error = "Your forum access name is too long.  It must be between 6 and 20 characters.  Please go back and enter in a valid forum access name.";
	
	} 
	if ($islong < 6) {
	
	$pass = "no";
	$error = "Your forum access name is too short.  It must be between 6 and 20 characters.  Please go back and enter in a valid forum access name.";
	
	}

	// Users are stupid, so we have to run this check again...

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
	
	

	if($available != "yes"){

	$pass = "no";
	$error = "That forum access name is not available.  It has either been taken or it is reserved.  Please <a href='create.php'>go back</a> and enter in a new forum access name.";
	
	}

	// Check if the forum exists another way.  If the database connection file exists...

	
	$filename = $pathtomybbmf."/users/".$fname."/dbconn.php";

	if (file_exists($filename)) {
   
	$pass = "no";
	$error = "That forum access name is not available.  It has either been taken or it is reserved.  Please <a href='create.php'>go back</a> and enter in a new forum access name.";

	}

	if($tos != "yes"){

	$pass = "no";
	$error = "You did not agree to our <a href='tos.php'>Terms of Service</a>.  Please go back and agree to our TOS to use this site.";

	}

	// RECAPTCHA VALIDATION...

	$captcha = grabanysetting("verifymethod");

		if($captcha == "captcha" or $captcha == "emailandcaptcha"){

		// ReCaptcha is Enabled

		require_once('recaptcha/recaptchalib.php');

		$privatekey = grabanysetting("recaptchapkey"); // Grab the private key from the database

		$resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

			if (!$resp->is_valid) {
  			
			// The captcha code is incorrect...

			$pass = "no";
			$error = "The captcha code that was entered is incorrect.  Please go back and submit the form again with the correct captcha code.";

			}
		

		}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// END ERROR CHECKING
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// SHOW ANY ERRORS...
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ALLOW US TO USE MULTIPLE DATABASES...
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$usingdb = grabanysetting("newusedb");

if($usingdb != $dbname){

	// We are not using the main database...

	mysql_select_db($usingdb);

}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// END MULTI DB CODE...
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if($pass == "yes"){

// No errors, so make the forum...

$table = $fname.'_'; //Add our underscore right away for DB tables

// Now we add the database tables from the MyBB files...

	// Add the table structure

	require_once "resources/mysql_db_tables.php";	

	foreach($tables as $val) //Some code borrowed from MyBB installer :wink wink:
	{
		$val = preg_replace('#mybb_(\S+?)([\s\.,]|$)#', $table.'\\1\\2', $val);
		preg_match('#CREATE TABLE (\S+) \(#i', $val, $match);
		//echo $val; //For Debugging		
		$result = mysql_query($val);
	}
	
	// Add the data

	require_once "resources/mysql_db_inserts.php";

	foreach($inserts as $val)
	{
		$val = preg_replace('#mybb_(\S+?)([\s\.,]|$)#', $table.'\\1\\2', $val);
		//echo $val; //For Debugging		
		$result = mysql_query($val);
	}

// Change back to our primary database...

if($usingdb != $dbname){

	// We are not using the main database...

	mysql_select_db($dbname);

}


// Now we are going to grab the master forum prefix and then copy the template data and other information to the hosted forums...



mysql_query("INSERT INTO ".$usingdb.".".$table."templates SELECT * FROM ".$dbname.".".$masterprefix."templates");
mysql_query("INSERT INTO ".$usingdb.".".$table."templatesets SELECT * FROM ".$dbname.".".$masterprefix."templatesets");
mysql_query("INSERT INTO ".$usingdb.".".$table."themes SELECT * FROM ".$dbname.".".$masterprefix."themes");
mysql_query("INSERT INTO ".$usingdb.".".$table."themestylesheets SELECT * FROM ".$dbname.".".$masterprefix."themestylesheets");
mysql_query("INSERT INTO ".$usingdb.".".$table."templategroups SELECT * FROM ".$dbname.".".masterprefix."templategroups");

mysql_query("DROP TABLE IF EXISTS ".$usingdb.".".$table."adminoptions");
mysql_query("CREATE TABLE ".$usingdb.".".$table."adminoptions SELECT * FROM ".$dbname.".".$masterprefix."adminoptions");
mysql_query("DROP TABLE IF EXISTS ".$usingdb.".".$table."adminviews");
mysql_query("CREATE TABLE ".$usingdb.".".$table."adminviews SELECT * FROM ".$dbname.".".$masterprefix."adminviews");

mysql_query("DROP TABLE IF EXISTS ".$usingdb.".".$table."tasks");
mysql_query("CREATE TABLE ".$usingdb.".".$table."tasks SELECT * FROM ".$dbname.".".$masterprefix."tasks");

//Copy the default user into the forums
//Make Password

$salt = random_str();
$loginkey = generate_loginkey();
$saltedpw = md5(md5($salt).md5($pass1));
	
//$mquery = ("INSERT INTO users (uid,username,password,salt,loginkey,email,postnum,avatar,avatardimensions,avatartype,usergroup,additionalgroups,displaygroup,usertitle,regdate,lastactive,lastvisit,lastpost,website,icq,aim,yahoo,msn,birthday,birthdayprivacy,signature,allownotices,hideemail,subscriptionmethod,invisible,receivepms,pmnotice,pmnotify,remember,threadmode,showsigs,showavatars,showquickreply,showredirect,ppp,tpp,daysprune,dateformat,timeformat,timezone,dst,dstcorrection,buddylist,ignorelist,style,away,awaydate,returndate,awayreason,pmfolders,notepad,referrer,reputation,regip,lastip,longregip,longlastip,language,timeonline,showcodebuttons,totalpms,unreadpms,warningpoints,moderateposts,moderationtime,suspendposting,suspensiontime,coppauser,classicpostbit) VALUES ('1','$desusername','$saltedpw','$salt','$loginkey','$email','0','','','0','4','','0','','1210031560','1210196142','1210119519','0','','','','','','','all','','1','0','0','0','1','1','1','1','','1','1','1','1','0','0','0','','','0','0','0','','','0','0','0','','','','','0','0','127.0.0.1','127.0.0.1','403520761','403520761','','38','1','0','0','0','0','0','0','0','0','0');");

$fname2 = $fname."_users";

$mquery = "INSERT INTO " . $fname2 . " (`uid`, `username`, `password`, `salt`, `loginkey`, `email`, `postnum`, `avatar`, `avatardimensions`, `avatartype`, `usergroup`, `additionalgroups`, `displaygroup`, `usertitle`, `regdate`, `lastactive`, `lastvisit`, `lastpost`, `website`, `icq`, `aim`, `yahoo`, `msn`, `birthday`, `birthdayprivacy`, `signature`, `allownotices`, `hideemail`, `subscriptionmethod`, `invisible`, `receivepms`, `receivefrombuddy`, `pmnotice`, `pmnotify`, `threadmode`, `showsigs`, `showavatars`, `showquickreply`, `showredirect`, `ppp`, `tpp`, `daysprune`, `dateformat`, `timeformat`, `timezone`, `dst`, `dstcorrection`, `buddylist`, `ignorelist`, `style`, `away`, `awaydate`, `returndate`, `awayreason`, `pmfolders`, `notepad`, `referrer`, `referrals`, `reputation`, `regip`, `lastip`, `longregip`, `longlastip`, `language`, `timeonline`, `showcodebuttons`, `totalpms`, `unreadpms`, `warningpoints`, `moderateposts`, `moderationtime`, `suspendposting`, `suspensiontime`, `suspendsignature`, `suspendsigtime`, `coppauser`, `classicpostbit`, `loginattempts`, `failedlogin`, `usernotes`) VALUES('', '$desusername', '$saltedpw', '$salt', '$loginkey', '$email', 0, '', '', '0', 4, '', 0, '', 1302114153, 1302228632, 1302114372, 0, '', '', '', '', '', '', 'all', '', 1, 0, 0, 0, 1, 0, 1, 1, '', 1, 1, 1, 1, 0, 0, 0, '', '', '0', 0, 0, '', '', 0, 0, 0, '', '', '', '', 0, 0, 0, '$ip', '$ip', -802359451, -802359451, '', 219, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, '');";


mysql_query($mquery);
mysql_query("INSERT INTO ".$usingdb.".".$table."adminoptions VALUES ('1','','','1','yes','yes','yes','yes','yes','yes','yes','yes','yes','yes','yes','yes','yes','yes','yes','yes','yes','yes')");

// COPY FORUM SETTINGS...

mysql_query("INSERT INTO ".$usingdb.".".$table."settinggroups SELECT * FROM ".$dbname.".".$masterprefix."settinggroups");
mysql_query("INSERT INTO ".$usingdb.".".$table."settings SELECT * FROM ".$dbname.".".$masterprefix."settings");
mysql_query("INSERT INTO ".$usingdb.".".$table."usergroups SELECT * FROM ".$dbname.".".$masterprefix."usergroups");

//Datacache fix
mysql_query("INSERT INTO ".$usingdb.".".$table."datacache SELECT * FROM ".$dbname.".".$masterprefix."datacache");

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// UPDATE FORUM SETTINGS...
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$newURL = "http://www.".$fname.".".$domain;

mysql_query("UPDATE ".$usingdb.".".$table."settings SET value='".$newURL."' WHERE name='bburl'");
mysql_query("UPDATE ".$usingdb.".".$table."settings SET value='".$email."' WHERE name='adminemail'");

$do = $fname.".".$domain;

mysql_query("UPDATE ".$usingdb.".".$table."settings SET value='.".$do."' WHERE name='cookiedomain'");

mysql_query("UPDATE ".$usingdb.".".$table."settings SET value='/' WHERE name='cookiepath'");

//Update the forum's name

mysql_query("UPDATE ".$usingdb.".".$table."settings SET value='".$display."' WHERE name='bbname'");

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// WRITE THE DATABASE CONFIG FILE...
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$pathtomybbmf = $ourmybbpath;

// Create our required files...

@mkdir($pathtomybbmf."/users/".$fname, 0777); //Make a new directory in users for our folder

//copy("resources/dbconn.php","../users/".$fname."/dbconn.php"); //Copy the dbconn.php file
//chmod($pathtomybbmf."/users/".$fname."/dbconn.php", 0777); //Chmod the file so it is writable

$ourFileName = $pathtomybbmf."/users/".$fname."/index.htm";
$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
fclose($ourFileHandle);


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// END WRITE...
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Make a custom disk cache location for forums...
// This will hopefully solve the weird theme issues people have been having...

//@mkdir($pathtomybbmf."/cache/".$fname."", 0777); //Make a new directory in user folder for our avatars
//chmod($pathtomybbmf."/cache/".$fname."", 0777); //Chmod the avatar folder so we can upload avatars to it
//$ourFileName = $pathtomybbmf."/cache/".$fname."/index.htm";
//$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
//fclose($ourFileHandle);

// Now we create the avatars directory...
// Make sure the directory does not already exist though

if(!file_exists($pathtomybbmf."/users/".$fname."/avatars")){
	@mkdir($pathtomybbmf."/users/".$fname."/avatars", 0755); //Make a new directory in user folder for our avatars
	@chmod($pathtomybbmf."/users/".$fname."/avatars", 0755); //Chmod the avatar folder so we can upload avatars to it
	$ourFileName = $pathtomybbmf."/users/".$fname."/avatars/index.htm";
	$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
	fclose($ourFileHandle);
}

$path = "./users/".$fname."/avatars";

//Update the SQL
mysql_query("UPDATE ".$usingdb.".".$table."settings SET value='".$path."' WHERE name='avataruploadpath'");

// Now we also modify the uploads directory

if(!file_exists($pathtomybbmf."/users/".$fname."/uploads")){
	@mkdir($pathtomybbmf."/users/".$fname."/uploads", 0755); //Make a new directory in user folder for our avatars
	@chmod($pathtomybbmf."/users/".$fname."/uploads", 0755); //Chmod the avatar folder so we can upload avatars to it
	$ourFileName = $pathtomybbmf."/users/".$fname."/uploads/index.htm";
	$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
	fclose($ourFileHandle);
}

$path = "./users/".$fname."/uploads";

mysql_query("UPDATE ".$usingdb.".".$table."settings SET value='".$path."' WHERE name='uploadspath'");

$act = random_str(); // Generate an activation code OTF

// Associate account...

if($addtoacct == "yes" and $isloggedin == "yes"){

$acctholder = $loggedinname;

}
else{

$acctholder = "";

}


// Do we have email activation?

$sendemailverify = grabanysetting("verifymethod");

if($sendemailverify == "email" or $sendemailverify == "emailandcaptcha"){

	$actnumber = 0;  // Require email activation / verification

}
else{

	$actnumber = 1;  // No further activation, so set the forum to activated status

}



// Update site_hosted_forums

$regdate = date('Y-m-d');
mysql_query("INSERT INTO site_hosted_forums(forumname,email,assocaccount,created,fromip,actcode,actstatus,backupmode,adstatus,adfreeimptotal,adfreeimpused,usesdbconn) VALUES ('$fname', '$email', '$acctholder', '$regdate', '$ip', '$act', '$actnumber', '2', 'runads', '0', '0', '$usingdb')");

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// WOW!  THAT WAS A LOT OF SETUP PROCEDURES!!!
// WE'RE DONE NOW, SO SHOW THE USER A PAGE...
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$article_title = "<img class='borderless' src='templates/icons/yes.gif' alt='Forum Created Successfully Checkmark'/> Success!  Your forum has been created!";
$article_content = "Your forum has been created successfully.  You can now view your forum at the following URL:<br/><br/>
<img class='borderless' src='templates/icons/next.gif' alt='arrow'/> <b><a href='http://www.".$fname.".".$domain."'>http://www.".$fname.".".$domain."</a></b><br/><br/>
You can log in to the forum using the username and password you specified during setup.  Enjoy your new free forum!";

// Let's see if we need to send the user their activation code via email...

if($sendemailverify == "email" or $sendemailverify == "emailandcaptcha"){

	// We are sending the user an activation code via email for their forum...

	$fromemail = grabanysetting("systememail");

	$message = "Hello,\n\nYour new forum, located at http://www.".$fname.".".$domain." is almost ready for your use.  In order to finish setting up your forum you need to activate it using the following activation code:  ".$act."\n\nYou can use the following link to activate your forums: http://www.".$domain."/activate.php?forum=".$fname."&code=".$act."\n\nUntil you activate your forum it may be deleted by the site admin at any time, so to avoid forum deletion you must activate the forum.";

	$headers = "From: ".$fromemail;

	mail($email, "Activation Code for your Free Forums", $message, $headers);


}


}
else{

// We've had some errors, so we cannot continue...

$article_title = "<img src='templates/icons/delete.gif' class='borderless' alt='error'/> There has been an error!";
$article_content = $error;

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

$templatevars[customheadtag] = ""; //  

// Now we send all this, plus the page name / type to the template system...
// Valid page types are: generic, admin, noads

$template = spittemplate("generic",$templatevars,$isloggedin);

echo $template;

// **********************************************************************
// End Template Definition
// **********************************************************************


?>