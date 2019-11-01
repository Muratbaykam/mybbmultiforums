<?php

// THIS FILE CHECKS IF A FORUM IS USING A CUSTOM DOMAIN...

include("config.php");
// include("functions.php");

function showads($adtype, $forumname){

	$adcode = "";

	// First, let's see if this forum can show ads
	// Get today's date
	$date = date("y-m-d"); 
	
	$query = "SELECT * FROM site_hosted_forums WHERE forumname='".$forumname."' AND adfreeuntil >= '".$date."' LIMIT 1";
	$result = mysql_query($query);
	$num = mysql_numrows($result);
	
	if($num == 0){
	
		// No results found, so let's do another check
		// We now need to check for ad free impressions
		
		$query = "select * from site_hosted_forums where forumname='".$forumname."' and adfreeimpused <= adfreeimptotal LIMIT 1";
		$result = mysql_query($query);
		$numRows = mysql_numrows($result);
		
		
		
		if($numRows > 0){
			
			
			
			// We can show ads on this forum!
			// Let's fetch an ad from the database
			
			$query = "select * from ads WHERE ".$adtype." = 1 and status='active' ORDER BY RAND() LIMIT 1";
			$result = mysql_query($query);
			$num = mysql_numrows($result);
			
			
			if($num > 0){
			
				// We found an ad!
				// Let's get the ad information
				
				
				
				$adcode = @mysql_result($result,$i,"text");
				$impressions = @mysql_result($result,$i,"impressions");
				$actualimpressions = @mysql_result($result,$i,"actualimpressions");
				$adID = @mysql_result($result,$i,"id");
				
				// Strip the slashes from the ad code 
				// $adcode = stripslashes($adcode);
				
				// Now we update the ad information
				$newImpressions = $actualimpressions + 1;
				
				$query = "UPDATE ads SET actualimpressions=".$newImpressions." WHERE id=".$adID;
				$result = mysql_query($query);
				
				// Check to see if ad should be disabled
				
				if($newImpressions > $impressions){
				
					if($impressions != 0){
					
						$query = "UPDATE ads SET status = 'disabled' WHERE id=".$adID;
						$result = mysql_query($query);
					
					}
				
				}
			
			}
			
		}
		else{
			
			// We cannot show ads on this forum
			// We need to mark this as an ad free impression
			$query = "UPDATE site_hosted_forums SET adfreeimpused = adfreeimpused+1 where forumname = '".$forumname."'";
			$result = mysql_query($query);
			
		}
	
	}
	
	return $adcode;

}

?>