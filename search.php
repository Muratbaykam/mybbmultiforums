<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////
// BRANDON'S MYBB MULTIFORUMS MOD VERSION 3.0.0
// REDISTRIBUTION PROHIBITED WITHOUT WRITTEN PERMISSION
////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Wake the sleeping giant
// Add our file includes so that we can work with the rest of the script

include("inc/functions.php");
include("inc/functions_template.php");

// Pull the site links from the database.
// We can also add more links at a per page level here.

$links = getlinks();


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

$search = $_POST["search"];
$search = secure($search);

if($search != ""){

$article_title = "Search Results for ".$search.":";

// Do a query to see all matching results...

	$query = "SELECT * FROM site_content WHERE value LIKE '%".$search."%'";
	$result = mysql_query($query);
	$num = mysql_numrows($result);

	if($num > 0){




	$i = 0;
	$numres = 0;
	while ($i < $num) {

	$pageurl=@mysql_result($result,$i,"page");
	$title=@mysql_result($result,$i,"title");
	$text=@mysql_result($result,$i,"value");

	// Now we check within each page text for the search query...

	$text = strip_tags($text);

	$position = stripos($text, $search);

	// die("Position: ".$position);

	if($position > 0){

	

	// A result was found...

	// Now we get a displayable result ready...
	
	$offset = $position - 40;

	if($offset < 0){

	$offset = 0; // We can't start at a negative position...	

	}

	$back = $offset + 100;

	$output = substr($text, $offset, $back);

	$output = "...".$output."...";

	// Now we make a nice div for this...

	//$new = "<strong>".$search."</strong>";

	//$output = str_ireplace($search, $new, $output);

	$output = stripslashes($output);

	$filename = $pageurl.".php";

	if(file_exists($filename)){

	$outlink = $filename;	

	}
	else{

	$outlink = "pages.php?page=".$pageurl;

	}

	$search_content = $search_content."<div id='result".$i."' style='border:1px solid black;padding:4px;'>
	<img src='templates/icons/next.gif' class='borderless' alt='Result'/> <a href='".$outlink."'>".$title."</a><br/><br/>".$output."</div><br/>";
	
	$numres++;

	}
	

	$i++;
	}

	// $num = $num + 1;

	$article_content = "The term ".$search." was found on ".$numres." pages shown below...<br/><br/>".$search_content;

	}
	else{

	$article_content = "There were no results found for your search.";	

	}



}
else{

// We have an empty search...

$article_title = "Search This Site";
$article_content = "Here you can search this site.  Simply enter your search query in the box below.<br/><br/>
<form action='search.php' method='post' id='searchsite'>

			<div id='searchformmain'>
				
			
				<label for='search'>Site Search:</label>
				<input type='text' name='search' value='' />
				<input type='submit' value='Search'/>
				
			</div>		
			</form>";

	
}


// **********************************************************************
// Begin Template Definition
// See inc > functions_template.php for more on the template system.
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
