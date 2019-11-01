<?php

// This file checks if an access name is taken or not and then outputs the results back to the AJAX call...

include("../inc/config.php");
include("../inc/functions.php");

	$query = "SELECT * FROM themes";
	$result = mysql_query($query);
	$num = mysql_numrows($result);
	
	if($num > 0){

		while ($i < $num) {

			$themeID=@mysql_result($result,$i,"id");
			$themeName=@mysql_result($result,$i,"themeName");
			$themeURL=@mysql_result($result,$i,"themeURL");
			
			echo '<option value="'.$themeURL.'">'.$themeName.'</option>';
			
			$i++;
			
		}
	}

?>