<?php
/**
 * Database configuration
 *
 * Please see the MyBB Wiki for advanced
 * database configuration for larger installations
 * http://wiki.mybboard.net/
 */

///////////////////////////////////////////////////////////////////////////////////////
// FIRST WE REQUIRE OUR CONFIG.PHP FILE FROM OUR CMS WITH OUR DATABASE INFO          //
///////////////////////////////////////////////////////////////////////////////////////

if(file_exists(MYBB_ROOT."../../inc/config.php")){

require_once(MYBB_ROOT."../../inc/config.php");

}
else if(file_exists(MYBB_ROOT."../inc/config.php")){

require_once(MYBB_ROOT."../inc/config.php");

}
else{

die("Could not locate MyBBMF config.php!");

}

///////////////////////////////////////////////////////////////////////////////////////
// NOW WE TAKE THE DATABASE DETAILS FROM THAT FILE AND USE THEM HERE			 //
///////////////////////////////////////////////////////////////////////////////////////

$spath = $ourmybbpath;

$config['database']['type'] = 'mysql';
$config['database']['database'] = $dbname;

$config['database']['hostname'] = $dbhost;
$config['database']['username'] = $dbuser;
$config['database']['password'] = $dbpass;

///////////////////////////////////////////////////////////////////////////////////////
// NOW WE DETERMINE THROUGH MAGIC WHAT OUR TABLE PREFIX WILL BE 				 //
///////////////////////////////////////////////////////////////////////////////////////

$ref =  $_SERVER['HTTP_HOST'];

//Check if string is www, if it is then remove:
if(strstr($ref,'www.'))
{
$ref = substr($ref, 4); //Strip first 4 characters from the string
}

$customdomain = $ref;

$parts = explode(".",$ref);

$ref = $parts['0'];
//grab the first part 

//Let's clean things up and remove slashes and non alpha-numeric chars
$ref = preg_replace("/[^a-zA-Z0-9s]/", "", $ref);

if ($ref == $domainwithoutext) {
$config['database']['table_prefix'] = "mybb_";
$check123 = "mybb";
}
else {

// HERE WE CHECK IF THE FORUM IS USING A CUSTOM DOMAIN NAME...

//Forum Name
$fname = $ref;
$ref = $ref . "_";

$go = "no";

if(file_exists(MYBB_ROOT."../../inc/checkfordomain.php")){

require_once(MYBB_ROOT."../../inc/checkfordomain.php");

$go = "yes";

}
else if(file_exists(MYBB_ROOT."../inc/checkfordomain.php")){

require_once(MYBB_ROOT."../inc/checkfordomain.php");

$go = "yes";

}
else{

die("Could not load checkfordomain.php!");

}

if($go == "yes"){

// Check if the forum is using a custom domain...

$domainprefix = checkdomain($customdomain);

// Now we are going to check if the forum is on a database other than the main database...

$ondb = checkdatabase($fname);

if($ondb != $dbname){

// We are not on the same database as the main database...

$config['database']['database'] = $ondb;

}

// If ondb returns error, it most likely means the forum was not found...

if($ondb == "error"){

$config['database']['database'] = $dbname;

}

}

if($domainprefix == "no" or $domainprefix == ""){

// The forum is not using a custom domain...

$config['database']['table_prefix'] = $ref; //Clever, eh?  Table prefix is the requested directory name plus undescore.

}
else{

// The forum is using a custom domain, so set the prefix accordingly...

$config['database']['table_prefix'] = $domainprefix;

}

}

/**
 * Admin CP directory
 *  For security reasons, it is recommended you
 *  rename your Admin CP directory. You then need
 *  to adjust the value below to point to the
 *  new directory.
 */

$config['admin_dir'] = 'admin';

/**
 * Hide all Admin CP links
 *  If you wish to hide all Admin CP links
 *  on the front end of the board after
 *  renaming your Admin CP directory, set this
 *  to 1.
 */

$config['hide_admin_links'] = 0;

/**
 * Data-cache configuration
 *  The data cache is a temporary cache
 *  of the most commonly accessed data in MyBB.
 *  By default, the database is used to store this data.
 *
 *  If you wish to use the file system (cache/ directory), MemCache, xcache, or eAccelerator
 *  you can change the value below to 'files', 'memcache', 'xcache' or 'eaccelerator' from 'db'.
 */

$config['cache_store'] = 'db';

/**
 * Memcache configuration
 *  If you are using memcache as your data-cache,
 *  you need to configure the hostname and port
 *  of your memcache server below.
 *
 * If not using memcache, ignore this section.
 */

$config['memcache']['host'] = 'localhost';
$config['memcache']['port'] = 11211;

/**
 * Super Administrators
 *  A comma separated list of user IDs who cannot
 *  be edited, deleted or banned in the Admin CP.
 *  The administrator permissions for these users
 *  cannot be altered either.
 */

$config['super_admins'] = '1';

/**
 * Database Encoding
 *  If you wish to set an encoding for MyBB uncomment 
 *  the line below (if it isn't already) and change
 *  the current value to the mysql charset:
 *  http://dev.mysql.com/doc/refman/5.1/en/charset-mysql.html
 */

$config['database']['encoding'] = 'utf8';

/**
 * Automatic Log Pruning
 *  The MyBB task system can automatically prune
 *  various log files created by MyBB.
 *  To enable this functionality for the logs below, set the
 *  the number of days before each log should be pruned.
 *  If you set the value to 0, the logs will not be pruned.
 */

$config['log_pruning'] = array(
	'admin_logs' => 365, // Administrator logs
	'mod_logs' => 365, // Moderator logs
	'task_logs' => 30, // Scheduled task logs
	'mail_logs' => 180, // Mail error logs
	'user_mail_logs' => 180, // User mail logs
	'promotion_logs' => 180 // Promotion logs
);
 
?>