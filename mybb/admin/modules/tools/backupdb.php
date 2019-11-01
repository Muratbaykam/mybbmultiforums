<?php
/**
 * MyBB 1.6
 * Copyright 2010 MyBB Group, All Rights Reserved
 *
 * Website: http://mybb.com
 * License: http://mybb.com/about/license
 *
 * $Id: backupdb.php 5409 2011-03-20 02:15:47Z jammerx2 $
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$page->output_header("Database Backups Disabled");
$page->output_alert("Database backups have been disabled through this interface.");
$page->output_footer();

?>