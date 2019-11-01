<?php

// FREE VERSION ADMIN CONSOLE
// SOME FEATURES REMOVED

// Wake the sleeping giant

include("inc/functions.php");
include("inc/functions_template.php");
include("inc/functions_forms.php");
include("inc/config.php");

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

// We are an admin user who is logged in...
// Show the admin links...

$links = getadmlinks();

// Initialize Variables


$set = $_GET["set"];
$set = preg_replace("/[^a-zA-Z0-9s]/", "", $set);
$set = secure($set);

$do = $_GET["do"];
$do = preg_replace("/[^a-zA-Z0-9s]/", "", $do);
$do = secure($do);

$more = $_GET["more"];
$more = preg_replace("/[^a-zA-Z0-9\\040.]/", "", $more);
$more = secure($more);

//Start our settings switchout

if($set == ""){

//No setting selected...

$article_title = "Welcome to the MyBB Multiforums Admin Panel";
$article_content = "<p>Welcome to the admin panel.  This inteface allows you to administer your forum hosting site quickly and easily.<br/><br/>

<b>Forum Hosting Settings:</b><br/><br/>

<a href='admconsole.php?set=forums'>View Hosted Forums</a><br/>
<a href='admconsole.php?set=prune'>Prune Forums</a><br/>
<a href='admconsole.php?set=databases'>Manage Databases</a><br/>
<a href='admconsole.php?set=domains'>Manage Domains</a><br/>
<a href='admconsole.php?set=themes'>Manage Themes</a><br/>
<br/>

<b>Advertisement Settings:</b><br/><br/>
<a href='admconsole.php?set=ads'>Manage Advertisements</a><br/>
<a href='admconsole.php?set=ads&do=add'>Create a New Ad</a><br/><br/><br/>

<b>Site Content:</b><br/><br/>

<a href='admconsole.php?set=pages'>Edit CMS Pages</a><br/>
<a href='admconsole.php?set=pages&amp;do=new'>Add New Page to the CMS</a><br/><br/>

<br/><br/>


</p>";

}
else if($set == "pages"){

if($do == ""){

//Not working on a specific page...

$article_title = "CMS Page Editor";
$article_content = "<p>This page allows you to edit the pages that appear on your site's CMS system.  Use this interface to add, edit or delete pages.  For your convenience you can enter in HTML into pages you create.<br/><br/><a href='admconsole.php?set=pages&amp;do=new'>Add a new page</a> :: <a href='admconsole.php'>Back to Admin CP Main Menu</a></p>
<table width='575' border='1'>
  <tr>
    <td style='width:216px'><strong>Page URL: </strong></td>
    <td style='width:273px'><strong>Page Title: </strong></td>
    <td style='width:32px'><strong>Edit:</strong></td>
    <td style='width:51px'><strong>Delete:</strong></td>
  </tr>";

$query = "SELECT * FROM site_content";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

	while ($i < $num) {

	$ourpage=@mysql_result($result,$i,"page");
	$title=@mysql_result($result,$i,"title");

	$article_content = $article_content."<tr>
    <td>".$ourpage."</td>
    <td>".$title."</td>
    <td><a href='admconsole.php?set=pages&amp;do=edit&amp;more=".$ourpage."'><img class='cb' src='templates/icons/cog.gif' alt='Edit'/></a></td>
    <td><a href='admconsole.php?set=pages&amp;do=delete&amp;more=".$ourpage."'><img class='cb' src='templates/icons/delete.gif' alt='Delete'/></a></td>
  </tr>
";

	$i++;
	
	}


$article_content = $article_content."</table><br/>";

}
else if($do == "new"){

$article_title = "Create a New CMS Page";
$article_content = "<p>Using the form below you can create a new page on your site's CMS system.  If you create a new page it will be available to view at <b>/pages.php?page=yourpagename</b> off of the root of your domain.
You can also modify a PHP file to display a predefined page if you don't like the long URL.  For your convenience you can enter in HTML into the new page in the form below.</p><form id='submitform' method='post' action='admpost.php'><div id='newcmsform'>
  <p>Page URL: 
    <input name='pageurl' type='text' id='pageurl'/>
</p>
  <p>Page Title: 
    <input name='pagetitle' type='text' id='pagetitle'/>
</p>
  <p>Page Content: </p>
  <p>
    <textarea name='pagecontent' cols='45' rows='6' id='pagecontent'></textarea> 
  </p>
  <p>
    <input name='page' type='hidden' id='page' value='pages'/>
    <input name='type' type='hidden' id='type' value='newpage'/>
    <input type='submit' name='Submit' value='Create New Page'/>
  </p>
</div></form>
";

}
else if($do == "edit"){

$article_title = "Edit a Page";

if($more != ""){

$article_content = "<p>Here you can edit an existing page:</p><br/>";

//Select the page info from the database...

	$pageinfo = getsitecontent($more);
	$pagetitle = $pageinfo[title];
	$pagecontent = $pageinfo[content];

	$pagetitle = stripslashes($pagetitle);
	$pagecontent = stripslashes($pagecontent);


if($pagetitle != "" or $pagecontent != ""){


$article_content = $article_content="<p>This page allows you to edit a page on your site's CMS system.  For your convenience you may enter in HTML into the form below when creating your page.</p><form id='editform' method='post' action='admpost.php'><div id='editpage'>
  <p>Page URL: 
    <input name='pageurl' type='text' id='pageurl' value='".$more."' readonly='readonly'/>
</p>
  <p>Page Title: 
    <input name='pagetitle' type='text' id='pagetitle' value='".$pagetitle."'/>
</p>
  <p>Page Content: </p>
  <p>
    <textarea name='pagecontent' cols='45' rows='6' id='pagecontent'>".$pagecontent."</textarea> 
  </p>
  <p>
    <input name='page' type='hidden' id='page' value='pages'/>
    <input name='type' type='hidden' id='type' value='editpage'/>
    <input type='submit' name='Submit' value='Edit Page Content'/>
  </p>
</div></form>";
}
else{
$article_title = "Page does not exist";
$article_content = "<p>Page does not exist!</p>";
}
}
else{

$article_title = "Page does not exist";
$article_content = "<p>Page does not exist!</p>";

}


}
else if($do == "delete"){

//Delete a page...

	if($more != "index" and $more != "tos" and $more != "newsbar"){

	$query = "DELETE FROM site_content WHERE page='".$more."'";
	mysql_query($query);

	$article_title = "Page Deleted Successfully";
	$article_content = "<p>The page with the name <b>".$more."</b> has been deleted.<br/><br/><a href='admconsole.php'>ACP Home</a></p>";
	}
	else{
	$article_title = "Error";
	$article_content = "<p>The page you tried to delete is a special page and cannot be deleted. <br/><br/> <a href='admconsole.php?set=pages'>Return to the Pages Editor</a></p>";
	}


}
else{

}


}
else if($set == "forums"){

	if($do == ""){



		$query = "SELECT * FROM site_hosted_forums ORDER BY id DESC";
		$result = mysql_query($query);
		$num = mysql_numrows($result);

		$rowsperpage = 10;
		$totalpages = ceil($num / $rowsperpage);

		if(is_numeric($more) and $more != ""){
			$currentpage = $more;
		}
		else{
			$currentpage = 1;
		}

		if ($currentpage > $totalpages) {  
			$currentpage = $totalpages;  
		}

		if ($currentpage < 1) {   
			$currentpage = 1;  
		} 

		$offset = ($currentpage - 1) * $rowsperpage;  

		$query = "SELECT * FROM site_hosted_forums ORDER BY id DESC LIMIT $offset, $rowsperpage";
		$result = mysql_query($query);
		$num2 = mysql_numrows($result);

	// We are showing all the forums...
	// Begin by showing an about...

	$article_title = "Your Hosted Forums";
	$article_content = "This page shows you all of the forums hosted on your forum hosting service.  You can also use the search form to find a specific forum meeting your criteria.<br/><br/>
<form method='get' action='admconsole.php'>
<div id='forumsearchform'>
  <input name='set' type='hidden' id='set' value='forums'/>
  <input name='do' type='hidden' id='do' value='search'/>
  Find forums where forum 
  <select name='searchtype' id='searchtype'>
    <option value='name' selected='selected'>name</option>
    <option value='regemail'>registered email</option>
    <option value='ip'>IP address</option>
  </select>
  <select name='searchcriteria' id='searchcriteria'>
    <option value='exact' selected='selected'>Exactly Matches</option>
    <option value='begins'>Begins With</option>
    <option value='ends'>Ends With</option>
    <option value='contains'>Contains</option>
  </select> 
  <input name='searchterm' type='text' id='searchterm'/> 
  <input type='submit' name='Submit' value='Search Forums'/></div>
</form><br/>";

	$fl = "no";

	$article_content = $article_content."<p class = 'rightp'>";

	if($currentpage > 1) {
			$newpage = $currentpage - 1;
			$article_content = $article_content."<a href='admconsole.php?set=forums&more=".$newpage."'>Previous Page</a>";	

		$fl = "yes";		

		}

		if($currentpage < $totalpages) {
			$newpage = $currentpage + 1;

			if($fl == "no"){

			$article_content = $article_content."<a href='admconsole.php?set=forums&more=".$newpage."'>Next Page</a>";

			}

			else{

			$article_content = $article_content." :: <a href='admconsole.php?set=forums&more=".$newpage."'>Next Page</a>";

			}

		}

	$article_content = $article_content."</p><br/>";

$article_content = $article_content."<table width='600' border='1'>
 <tr>
    <td style='width:119px'><strong>Forum Name: </strong></td>
    <td style='width:117px'><strong>Created Date: </strong></td>
    <td style='width:109px'><strong>Email:</strong></td>
    <td style='width:65px'><strong>IP:</strong></td>
    <td style='width:44px'><strong>Ad Status: </strong></td>
    <td style='width:56px'><strong>Backups:</strong></td>
    <td style='width:56px'><strong>Edit:</strong></td>
    <td style='width:56px'><strong>Del:</strong></td>
  </tr>";

		//Loop out code
		$i=0;
		while ($i < $num2) {

			$id=@mysql_result($result,$i,"id");
			$created=@mysql_result($result,$i,"created");
			$ip=@mysql_result($result,$i,"fromip");
			$email=@mysql_result($result,$i,"email");
			$adstatus=@mysql_result($result,$i,"adstatus");
			$backupmode=@mysql_result($result,$i,"backupmode");
			$fname=@mysql_result($result,$i,"forumname");

		// OUR TABLE OUTPUT GOES HERE...

		if($adstatus == "runads"){

		$adstatus = "<a href='admconsole.php?set=forums&amp;do=disableads&amp;more=".$fname."'><img class = 'cb' src='templates/icons/yes.gif' alt='yes'/></a>";

		}
		else{

		$adstatus = "<a href='admconsole.php?set=forums&amp;do=enableads&amp;more=".$fname."'><img class='cb' src='templates/icons/no.gif' alt='no'/></a>";

		}

		$sitebackupmode = grabanysetting("backupmode");

		if($sitebackupmode == "free" or $backupmode == 2){

		$bstatus = "<a href='admconsole.php?set=forums&amp;do=disablebackup&amp;more=".$fname."'><img class='cb' src='templates/icons/yes.gif' alt='Backups are Enabled'/></a>";

		}
		else{

		$bstatus = "<a href='admconsole.php?set=forums&amp;do=enablebackup&amp;more=".$fname."'><img class='cb' src='templates/icons/no.gif' alt='Backups are Disabled'/></a>";

		}


		$article_content = $article_content."<tr>
    		<td><a href='http://www.".$fname.".".$domain."'>".$fname."</a></td>
    		<td>".$created."</td>
    		<td><a href='mailto:".$email."'>".$email."</a></td>
    		<td>".$ip."</td>
    		<td>".$adstatus."</td>
    		<td>".$bstatus."</td>
		<td><a href='admconsole.php?set=forums&amp;do=edit&amp;more=".$fname."'><img class='cb' src='templates/icons/cog.gif' alt='Edit'/></a></td>
    		<td><a href='admconsole.php?set=forums&amp;do=delete&amp;more=".$fname."'><img class='cb' src='templates/icons/delete.gif' alt='Delete'/></a></td>
  		</tr>";



		$i++;
		}



		$article_content = $article_content."</table><br/>";


		$article_content = $article_content."<p class='rightp'>";

	if($currentpage > 1) {
			$newpage = $currentpage - 1;
			$article_content = $article_content."<a href='admconsole.php?set=forums&amp;more=".$newpage."'>Previous Page</a>";	

		$fl = "yes";		

		}

		if($currentpage < $totalpages) {
			$newpage = $currentpage + 1;

			if($fl == "no"){

			$article_content = $article_content."<a href='admconsole.php?set=forums&amp;more=".$newpage."'>Next Page</a>";

			}

			else{

			$article_content = $article_content." :: <a href='admconsole.php?set=forums&amp;more=".$newpage."'>Next Page</a>";

			}

		}

	$article_content = $article_content."</p>";


	}
	else if($do == "search"){

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// BEGIN THE FORUM SEARCH CODE
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$searchtype = $_GET["searchtype"];
	$searchtype = preg_replace("/[^a-zA-Z0-9\\040.]/", "", $searchtype);
	$searchtype = secure($searchtype);

	$searchcriteria = $_GET["searchcriteria"];
	$searchcriteria = preg_replace("/[^a-zA-Z0-9\\040.]/", "", $searchcriteria);
	$searchcriteria = secure($searchcriteria);

	$searchterm = $_GET["searchterm"];
	$searchterm = secure($searchterm);

	// Check for blank search and reject...

	if($searchtype == "" or $searchcriteria == "" or $searchterm == ""){ //

	$article_title = "Search Error";
	$article_content = "A required field was left blank.  To search for forums please <a href='admconsole.php?set=forums'>go back</a> and fill in all of the search fields.";

	} //
	else{ //

	$flag = 0;

	// We are conducting a search here...
	// Let's start building our SQL query...

	$query = "SELECT * FROM site_hosted_forums WHERE ";

	

	if($searchtype == "name"){ //

	$query = $query."forumname ";

		if($searchcriteria == "exact"){ //

		$query = $query."= '".$searchterm."'";

		} //
		else if($searchcriteria == "begins"){ //

		$query = $query."LIKE '".$searchterm."%'";

		} //
		else if($searchcriteria == "ends"){ //

		$query = $query."LIKE '%".$searchterm."'";
	
		} //
		else if($searchcriteria == "contains"){ //

		$query = $query."LIKE '%".$searchterm."%'";

		} //
		else{ //

		$flag++;

		} //


	} //
	else if($searchtype == "regemail"){ //

		$query = $query."email ";

		if($searchcriteria == "exact"){ //

		$query = $query."= '".$searchterm."'";

		} //
		else if($searchcriteria == "begins"){ //

		$query = $query."LIKE '".$searchterm."%'";

		} //
		else if($searchcriteria == "ends"){ //

		$query = $query."LIKE '%".$searchterm."'";
	
		} //
		else if($searchcriteria == "contains"){ //

		$query = $query."LIKE '%".$searchterm."%'";

		} //
		else{ //

		$flag++;

		} //

	} //
	else if($searchtype == "ip"){ //

		$query = $query."fromip ";

		if($searchcriteria == "exact"){ //

		$query = $query."= '".$searchterm."'";

		} //
		else if($searchcriteria == "begins"){ //

		$query = $query."LIKE '".$searchterm."%'";

		} //
		else if($searchcriteria == "ends"){ //

		$query = $query."LIKE '%".$searchterm."'";
	
		} //
		else if($searchcriteria == "contains"){ //

		$query = $query."LIKE '%".$searchterm."%'";

		} //
		else{ //

		$flag++;

		} //

	} //
	else{ //

	// No valid search type selected...

	$flag++;

	} //

	} //

	if($flag == 0){ //

	// Run the search...

		$result = mysql_query($query);
		$num = mysql_numrows($result);
	

	if($num > 0){ //

	$article_title = "Search Results";
	$article_content = "Here are your search results...<br/><br/><table width='600' border='1'>
 <tr>
   <td style='width:119px'><strong>Forum Name: </strong></td>
    <td style='width:117px'><strong>Created Date: </strong></td>
    <td style='width:109px'><strong>Email:</strong></td>
    <td style='width:65px'><strong>IP:</strong></td>
    <td style='width:44px'><strong>Ad Status: </strong></td>
    <td style='width:56px'><strong>Backups:</strong></td>
    <td style='width:56px'><strong>Edit:</strong></td>
    <td style='width:56px'><strong>Del:</strong></td>
  </tr>";


		$i=0;
		while ($i < $num) { //

			$id=@mysql_result($result,$i,"id");
			$created=@mysql_result($result,$i,"created");
			$ip=@mysql_result($result,$i,"fromip");
			$email=@mysql_result($result,$i,"email");
			$adstatus=@mysql_result($result,$i,"adstatus");
			$backupmode=@mysql_result($result,$i,"backupmode");
			$fname=@mysql_result($result,$i,"forumname");

		// OUR TABLE OUTPUT GOES HERE...

		if($adstatus == "runads"){

		$adstatus = "<a href='admconsole.php?set=forums&amp;do=disableads&amp;more=".$fname."'><img class = 'cb' src='templates/icons/yes.gif' alt='yes'/></a>";

		}
		else{

		$adstatus = "<a href='admconsole.php?set=forums&amp;do=enableads&amp;more=".$fname."'><img class='cb' src='templates/icons/no.gif' alt='no'/></a>";

		}

		$sitebackupmode = grabanysetting("backupmode");

		if($sitebackupmode == "free" or $backupmode == 2){

		$bstatus = "<a href='admconsole.php?set=forums&amp;do=disablebackup&amp;more=".$fname."'><img class='cb' src='templates/icons/yes.gif' alt='Backups are Enabled'/></a>";

		}
		else{

		$bstatus = "<a href='admconsole.php?set=forums&amp;do=enablebackup&amp;more=".$fname."'><img class='cb' src='templates/icons/no.gif' alt='Backups are Disabled'/></a>";

		}



		$article_content = $article_content."<tr>
    		<td><a href='http://www.".$fname.".".$domain."'>".$fname."</a></td>
    		<td>".$created."</td>
    		<td><a href='mailto:".$email."'>".$email."</a></td>
    		<td>".$ip."</td>
    		<td>".$adstatus."</td>
    		<td>".$bstatus."</td>
		<td><a href='admconsole.php?set=forums&amp;do=edit&amp;more=".$fname."'><img class='cb' src='templates/icons/cog.gif' alt='Edit'/></a></td>
    		<td><a href='admconsole.php?set=forums&amp;do=delete&amp;more=".$fname."'><img class='cb' src='templates/icons/delete.gif' alt='Delete'/></a></td>
  		</tr>";



		$i++;
		} //



		$article_content = $article_content."</table><br/>";

		

	} //
	else{ //

	$article_title = "No Results Found";
	$article_content = "There were no results returned by this search.  <a href='admconsole.php?set=forums'>Click here</a> to search again.";


	} //


	} //
	else{ //

	$article_title = "Search Error";
	$article_content = "There was an error performing this search.  Please go back and try again.";

	}
	} // End the search setting do if
	else if($do == "delete"){

		// This option allows us to delete a forum.

		if($more != ""){

		// We are deleting a forum...

		$status = deleteforum($more);

		if($status == "success"){

		$article_title = "Forum Deleted Successfully";
		$article_content = "The forum was deleted from the database successfully.  <a href='admconsole.php?set=forums'>Click here</a> to manage the hosted forums.</a>";

		}
		else{

		$article_title = "Forum Deletion Error";
		$article_content = "The forum could not be deleted.  Either it does not exist or something went wrong.  Please attempt manual forum deletion.";


		}



		}
		else{
			
		$article_title = "No Forum Selected";
		$article_content = "You did not select a forum to delete.  Please <a href='admconsole.php?set=forums'>go back</a> and select a forum from the list to delete.";


		}

	
	} // End the delete a forum if statement
	else if($do == "edit"){

	if($more != ""){

		// Add some new post variables...

		$posting = $_GET["posting"];
		$posting = preg_replace("/[^a-zA-Z0-9\\040.]/", "", $posting);
		$posting = secure($posting);

		$forumisactivated = $_GET["forumisactivated"];
		$forumisactivated = preg_replace("/[^a-zA-Z0-9\\040.]/", "", $forumisactivated);
		$forumisactivated = secure($forumisactivated);

		if($posting == "yes"){

		// Update the settings...

		if($forumisactivated != "yes"){

		$actdigit = 0;

		}
		else{

		$actdigit = 1;

		}

		$query = "UPDATE site_hosted_forums SET actstatus=".$actdigit." WHERE forumname='".$more."'";
		mysql_query($query);

		$article_title = "Forum Updated Successfully";
		$article_content = "The new settings have been applied to the forum successfully.  <a href='admconsole.php?set=forums'>Click here</a> to return to editing the forums.";

		}
		else{

		// First let's see if the forum exists
		// Then get the forum's activation status

		$query = "SELECT * FROM site_hosted_forums WHERE forumname='".$more."'";
		$result = mysql_query($query);
		$num = mysql_numrows($result);

		if($num > 0){

			// The forum exists, so pull up the activation status...

			$i=0;
			while ($i < $num) {

			$actstatus=@mysql_result($result,$i,"actstatus");

			$i++;
			}

			if($actstatus == 1){

			$actcode = "<input name='forumisactivated' type='checkbox' id='forumisactivated' value='yes' checked='checked'/>";

			}
			else{

			$actcode = "<input name='forumisactivated' type='checkbox' id='forumisactivated' value='yes'/>";
	
			}

		$article_title = "Editing Forum";
		$article_content = "This page allows you to edit basic settings for this forum.<br/><br/>
		<form method='get' action='admconsole.php'>
		<div id = 'optionsform'>
  		<p>".$actcode."Forum is Activated (The forum has passed email activation)</p>
  		<p>
    		<input name='set' type='hidden' id='set' value='forums'/>
    		<input name='do' type='hidden' id='do' value='edit'/>
    		<input name='more' type='hidden' id='more' value='".$more."'/>
    		<input name='posting' type='hidden' id='posting' value='yes'/>
    		<br/><input type='submit' name='Submit' value='Submit Forum Edits'/></p>
  		</div></form>";
	

		}
		else{

		$article_title = "Forum does not exist";
		$article_content = "A forum with the name ".$more." does not exist.  Please go back and choose a valid forum to edit.";

		}
		

		}


	}
	else{

	$article_title = "No Forum Selected";
	$article_content = "Please select a forum to edit.";
	
	}

	
	}
	else if($do == "disableads"){

	// This section allows PREMIUM members to remove the ads from a forum...

	$article_title = "Feature Not In This Version";
	$article_content = "";
	
	}
	else if($do == "enableads"){

	$article_title = "Feature Not In This Version";
	$article_content = "";
	
	}
	else if($do == "disablebackup"){



	$article_title = "Feature Not In This Version";
	$article_content = "";
	

	
	}
	else if($do == "enablebackup"){
	
	$article_title = "Feature Not In This Version";
	$article_content = "";
	
	}
	else{

	$article_title = "The selected setting is invalid";
	$article_content = "The selected setting is invalid or does not exist.";

	}

}
else if($set == "prune"){

// This area allows us to prune forums based on certain criteria...

// This variable allows us to determine if we are running a real prune or just a dry run...

		$dryrun = $_GET["dryrun"];
		$dryrun = preg_replace("/[^a-zA-Z0-9\\040.]/", "", $dryrun);
		$dryrun = secure($dryrun);

// This variable takes in a parameter, such as user or post count...

		$count = $_GET["count"];
		$count = preg_replace("/[^a-zA-Z0-9\\040.]/", "", $count);
		$count = secure($count);

// Now we determine if we are just showing a menu or if we are actually pruning the forums...

	if($more != "email" and $more != "users" and $more != "posts"){

	// Show the forum prune menu

	$article_title = "Forum Pruner";
	$article_content = "This page allows you to prune (delete) forums from your forum hosting service that meet criteria you specify.
	It is recommended that you prune the forums occasionally to remove unused forums and free up database space.<br/>
	<br/><form method='get' action='admconsole.php'><div id='emailpruner' style='border:1px solid black;padding:4px;'>


    <b>Email Pruner - Prune forums that have not confirmed their email addresses.</b><br/><br/>  
    <input name='set' type='hidden' id='set' value='prune'/>
    <input name='more' type='hidden' id='more' value='email'/>
    <input name='dryrun' type='checkbox' id='dryrun' value='no'/> Prune the Forums (Leave unchecked to do a dry run)<br/><br/>
    <input type='submit' name='Submit' value='Run Email Based Pruner'/></div>
 
  
</form>";

/*
<hr>
<form name='form2' method='get' action='admconsole.php'>
  <p>
    <input name='set' type='hidden' id='set' value='prune'>
    <input name='more' type='hidden' id='more' value='users'>
Prune forums with less than 
<input name='count' type='text' id='count' size='6' maxlength='6'>
users.  </p>
  <p>
    <input name='dryrun' type='checkbox' id='dryrun' value='no'>
    Run This Forum Prune - Leave unchecked to tally number of removed forums only.<br/>
    <input type='submit' name='Submit' value='Prune Forums Based on User Count'>
  </p>
</form><hr>
<form name='form3' method='get' action='admconsole.php'>
  <p>
    <input name='set' type='hidden' id='set' value='prune'>
    <input name='more' type='hidden' id='more' value='posts'>
    Prune forums with less than
    <input name='count' type='text' id='count' size='6' maxlength='6'>
    posts. </p>
  <p>
    <input name='dryrun' type='checkbox' id='dryrun' value='no'>
    Run This Forum Prune - Leave unchecked to tally number of removed forums only.<br/>
    <input type='submit' name='Submit' value='Prune Forums Based on Post Count'>
  </p>
</form><br/><a href='admconsole.php'>Return to the Admin CP</a>";

*/
		

	}
	else{

	// Actually run a forum prune or forum prune dry run...

	$article_title = "Running Forum Pruner...";
	$article_content = "The forum pruner is now checking for forums that meet your prune requirements...<br/><br/>";

	$query = "";

	if($more == "email"){

	$query = "SELECT * FROM site_hosted_forums WHERE actstatus=0";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

			$i=0;
			while ($i < $num) {

			$forumname=@mysql_result($result,$i,"forumname");

			$article_content = $article_content."<img src='templates/icons/next.gif' alt='Arrow' class='borderless'/> Forum ".$forumname." has been marked for deletion...<br/>";

			if($dryrun == "no"){

			// We are deleting the forum for real...

			$stat = deleteforum($forumname);

			if($stat != "failure"){

			$article_content = $article_content."<img src='templates/icons/next.gif' alt='Arrow' class='borderless'/> Forum ".$forumname." has been deleted successfully...<br/>";

			}
			else{
			
			$article_content = $article_content."<img src='templates/icons/warning.gif' alt='Arrow' class='borderless'/> Forum ".$forumname." could not be deleted!<br/>";

			}

			}

			$i++;
			}	
	
	$article_content = $article_content."<br/>Forum Pruner Finished Run!<br/><br/>
	Forums Marked For Deletion: ".$num."<br/><br/>";

	if($dryrun != "no"){

	$article_content = $article_content."This was a dry run.  No forums were deleted.<br/><br/>";

	}
	
	$article_content = $article_content."<a href='admconsole.php'>Return to the Admin CP</a>";

	}
	else if($more == "users" or $more == "posts"){

	// We're going to have to do some fishing for the forum information forum by forum...

	$query = "SELECT * FROM site_hosted_forums";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

	$ourcount = 0;

			$i=0;
			while ($i < $num) {

			$actstatus=@mysql_result($result,$i,"actstatus");

			$i++;
			}

	}



	}


}
else if($set == "reserved"){

$article_title = "Placeholder";
$article_content = "Placeholder";

}
else if($set == "domain"){

$article_title = "Placeholder";
$article_content = "Placeholder";

}
else if($set == "databases"){

	$article_title = "Database Management Not Available";
	$article_content = "Sorry, but the database management functionality is reserved for users who have purchased the full version of the MyBB Multiforums Mod.
	For information on how to purchase, please visit <a href='http://www.rusnakweb.com/forum' target='_blank'>www.rusnakweb.com/forum</a>.<br/><br/>
	After purchasing, you will be able to upgrade your existing installation to use the new code.<br/><br/>
	In the full version of the script, this would be where you could add secondary databases to your forum hosting service, in order to host forums on multiple databases.";

}
else if($set == "forumsettings"){

$article_title = "Placeholder";
$article_content = "Placeholder";

}
else if($set == "settings"){

$article_title = "Placeholder";
$article_content = "Placeholder";

}
else if($set == "ads"){

	if($do == ""){
	
		// Show the ad grouping
		
		$article_title = "Manage Advertisements";
		$article_content = "This page allows you to manage the advertisements that will appear on your site.
		An ad unit can appear either on a forum you host or within the site's CMS system.<br/><br/>";
		
		// Show the ads currently running on the site
		
		$query = "SELECT * FROM ads";
		$result = mysql_query($query);
		$num = mysql_numrows($result);

		// We need some variables for displaying this information back...
		
			$forumads = "";
			$siteads = "";
		
		while ($i < $num) {

			$id=@mysql_result($result,$i,"id");
			$adname=@mysql_result($result,$i,"adname");

			$impressions=@mysql_result($result,$i,"impressions");
			$actualimpressions=@mysql_result($result,$i,"actualimpressions");
			$created=@mysql_result($result,$i,"created");
			$status=@mysql_result($result,$i,"status");
			$forumheader = @mysql_result($result,$i,"forumheader");
			$forumfooter = @mysql_result($result,$i,"forumfooter");
			$sitecms = @mysql_result($result,$i,"site");
			
			$forumads = $forumads."<div class='acpAdListing'>
				<p class='acpAdInfo'><strong>Ad Name:</strong> ".$adname."<br/>
				<strong>Status:</strong> ".$status."<br/>
				<strong>Forum Header:</strong> ".$forumheader."<br/>
				<strong>Forum Footer:</strong> ".$forumfooter."<br/>
				<strong>Site CMS:</strong> ".$sitecms."<br/>
				<strong>Max Impressions:</strong> ".$impressions."<br/>
				<strong>Actual Impressions:</strong> ".$actualimpressions."</br>
				</p>
				
				<p class='acpEditAd'><a href='admconsole.php?set=ads&do=edit&more=".$id."'>Edit Ad</a></p>
			
			</div>";

		$i++;
		
		}
		
			if($forumads == ""){
			
				$forumads = "You have no ads to display.<br/><br/>";
			
			}
			
		
			$article_content = $article_content."<u><b>Advertisements:</u></b><br/><br/>".$forumads;
			
	}
	else if($do == "add"){
	
		// Show the create a new ad page
		
		$article_title = "Create a new ad";
		$article_content = "This page allows you to create a new ad that will appear on your hosted forums
		or within the site's CMS system.<br/><br/>";
		
		$article_content = $article_content."".makeform("post", "admpost.php")."
		Ad Name: ".maketextfield("adname", "", "", "")."<br/><br/>
		Where should the ad be shown? 
		<p><input type='checkbox' name='forumheader' id='forumheader' value='1' /> Forum Header</p>
		<p><input type='checkbox' name='forumfooter' id='forumfooter' value='1' /> Forum Footer</p>
		<p><input type='checkbox' name='sitecms' id='sitecms' value='1' /> Site CMS</p>
		Ad HTML code:<br/><br/>".maketextbox("htmlcode", "")."<br/><br/>
		Max Impressions (0 for unlimited): ".maketextfield("maximpressions", "0", "6", "10")."<br/><br/>
		".makehiddenfield("page", "createad")."
		".makesubmit()."";
	
	}
	else if($do == "edit"){
		
		$article_title = "This is not available in the free version.";
		$article_content = "This functionality requires the full version of the MyBB Multiforums Mod from <a href='http://www.rusnakweb.com/forum' target='_blank'>RusnakWeb</a>.";
	
	}

}
else if($set == "domains"){

	$article_title = "Domain Management Not Available";
	$article_content = "Sorry, but the database management functionality is reserved for users who have purchased the full version of the MyBB Multiforums Mod.
	For information on how to purchase, please visit <a href='http://www.rusnakweb.com/forum' target='_blank'>www.rusnakweb.com/forum</a>.<br/><br/>
	After purchasing, you will be able to upgrade your existing installation to use the new code.<br/><br/>
	Domain management allows you to let forums sign up to use your service with their own domain name, rather than a free subdomain.";

}
else if($set == "themes"){

	if($do == "add"){
	
		$themeName = $_GET["themeName"];
		$themeName = secure($themeName);
		
		$themeURL = $_GET["themeURL"];
		$themeURL = secure($themeURL);
		
		if($themeName == "" or $themeURL == ""){
		
			$article_title = "Add a theme";
			$article_content = "This page allows you to add a MyBB theme that your site's users can install on their forums.
			To let users use the theme you will first need to download the theme and then upload the theme's images to your mybb master forum.
			Once the theme is uploaded, you must then put the theme's .xml file somewhere on your web server.  Using the form below you will allow
			users to import the .xml file from the URL that you will provide.<br/><br/>
			
			<form name='submitTheme' action='admconsole.php' method='get'>
				<input type='hidden' name='set' id='set' value='themes' />
				<input type='hidden' name='do' id='do' value='add' />
				
				<p>Theme Name: <input type='text' name='themeName' id='themeName' value='' style='margin-left: 2px;'/></p>
				<p>Theme URL: <input type='text' name='themeURL' id='themeURL' value='' style='margin-left: 15px;'/></p>
				
				<input type='submit' value='Submit' />
				
			</form>";
		
		}
		else{
		
			// Insert what the user submitted into the themes table
			// We're taking from the site owner, so we hope they're not a total moron where we have to check everything!
			
			$query = "INSERT INTO themes VALUES ('', '".$themeName."', '".$themeURL."')";
			mysql_query($query);
			$newid = mysql_insert_id();
			
			$article_title = "Theme added successfully";
			$article_content = "The theme ".$themeName." is now available to your users.  <a href='admconsole.php?set=themes'>Click here</a> to manage themes.";
		
		}
	
	}
	else if($do == "remove"){
	
		$query = "DELETE FROM themes WHERE id=".$more;
		$result = mysql_query($query);
		
		$article_title = "Theme Removed";
		$article_content = "The theme has been removed.  <a href='admconsole.php?set=themes'>Click here</a> to manage themes.";
	
	}
	else{
			
		$article_title = "Add or Remove MyBB Themes";
		$article_content = "You can use this page to add or remove MyBB themes for your forum hosting service.<br/><br/>
		<strong><a href='admconsole.php?set=themes&do=add'>Add a theme for forum use</a></strong><br/><br/>
		<u>Currently Installed Themes:</u><br/><br/>";
		
		$query = "SELECT * FROM themes";
		$result = mysql_query($query);
		$num = mysql_numrows($result);
		
		if($num > 0){

			while ($i < $num) {

				$themeID=@mysql_result($result,$i,"id");
				$themeName=@mysql_result($result,$i,"themeName");
				$themeURL=@mysql_result($result,$i,"themeURL");
				
				$article_content = $article_content."<div class='acpAdListing'><p><strong>Theme Name:</strong> ".$themeName."<br/>
				<strong>Theme URL:</strong> ".$themeURL."</p>
				<p><a href='admconsole.php?set=themes&do=remove&more=".$themeID."'>Remove Theme</a></p></div>";
				
				$i++;
				
			}
		}
		else{
		
			$article_content = $article_content."No themes installed!";
		
		}
	}
}
else if($set == "somethingelse"){

}
/*else{

$article_title = "Page does not exist";
$article_content = "<p>Page does not exist!</p>";

}
*/

//}




//}

		else{
		$article_title = "No Setting Selected";
		$article_content = "<p>No Setting Selected!  <a href='admconsole.php'>Admin CP Home</a></p>";
		}



	}
	else{
	$article_title = "Access Denied";
	$article_content = "<p>Access Denied</p>";
	}

}
else{
$article_title = "Access Denied";
$article_content = "<p>Access Denied</p>";

}


// **********************************************************************
// Begin Template Definition
// **********************************************************************

//Define our current theme
$file = $themeurl;

// No Ads
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
