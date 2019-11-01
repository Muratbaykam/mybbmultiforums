<?php

// This file is responsible for allowing easy generation of HTML forms within the site and CMS system

function makeform($reqtype, $postto){

	// Output the HTML to begin a new web form...
	
		$form = "<form id='autogenform' name='autogenform' method='".$reqtype."' action='".$postto."'>";
	
return $form;

}

function maketextfield($name, $defaultvalue, $size, $maxlength){

	$form = "";

	if($name != ""){
	
		if($size != "" or $maxlength != ""){
			
			$form = "<input name='".$name."' type='text' id='".$name."' value='".$defaultvalue."' size='".$size."' maxlength='".$maxlength."'/>";
		
		}
		else{
		
			$form = "<input name='".$name."' type='text' id='".$name."' value='".$defaultvalue."'/>";
		
		}
	
	}
	
	
return $form;

}

function makehiddenfield($name, $value){

	$form = "<input name='".$name."' type='hidden' id='".$name."' value='".$value."'/>";
	
return $form;

}

function maketextbox($name, $defaultvalue){

	$form = "<textarea name='".$name."' id='".$name."' cols='45' rows='5'>".$defaultvalue."</textarea>";
	
return $form;

}

function makesubmit(){

	$form = "<p>
    <input type='submit' name='Submit' id='submit' value='Submit' />
    <input type='reset' name='Reset' id='reset' value='Reset' />
    <br />
	</p></form>";
	
return $form;

}

?>