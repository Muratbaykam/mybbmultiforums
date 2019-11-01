// checkfname.js
// A simple Javascript class written in prototype for checking if a forum name is available

Event.observe(window, 'load', function() {

	var myChecker = new Checker();
	Checker.instance = myChecker;

});

var Checker = Class.create({

	initialize: function() {
	
		/*****************************************/
		/* USER CONFIG VARS **********************/
		/*****************************************/
		
		this.imagepath = "templates/icons/";
		this.image = "warning.gif";
		this.warning = "Please check that the forum name you wish to use is available.";
		this.lastForumNameChecked = "";
		this.status = "noGo";
		this.serverResponse = "";
		
	
	},
	checkName: function(){
	
		// Checks to see if the forum name is available
		
		if($('fname').value != ""){
		
			var checkerAJAX = new Ajax.Request('ajaxphp/checkaccess.php',
			{
			method: 'get',	
			parameters: { fname: $('fname').value },
			onSuccess: function(transport) {
			
				this.response = transport.responseText.evalJSON(true);
				Checker.instance.image = this.response.image;
				Checker.instance.message = this.response.message;
				Checker.instance.status = this.response.status;
				Checker.instance.lastForumNameChecked = this.response.fname;

				// Take our response and show to the user
				//$('fnamecheck').update("<img src='" + Checker.instance.imagepath + "" + this.response.image + "' alt='Status Image'/> " + this.instance.message);
				$('fnamecheck').update("<img src='" + Checker.instance.imagepath + "" + Checker.instance.image + "' style='border: 0; margin-right: 2px;'/> " + Checker.instance.message + "");
	
				
	
			}
			});
		}
		else{
		
			// The forum name is blank
			$('fnamecheck').update("<img src='" + Checker.instance.imagepath + "warning.gif' alt='ERROR'/> Please enter in a forum name.");
		
		}
	
	},
	checkCanCreate: function(){
		
		// Check if we can create the forum or not
		if(this.status == "noGo"){
			
			if(this.lastForumNameChecked != $('fname').value){
				$('fnamecheck').update("<img src='" + Checker.instance.imagepath + "warning.gif' alt='ERROR'/> Please check that your forum name is available.");
			}
			else{
				$('fnamecheck').update("<img src='" + Checker.instance.imagepath + "warning.gif' alt='ERROR'/> We cannot create a forum with this name.");
			}
			
			return false;
		}
		else{
		
			// We could be valid, or we could still be invalid
			// If the user changed the forum name and did not check it for validity, then it is invalid
			// until they check again.
			
			if(this.lastForumNameChecked == $('fname').value){
				return true;
			}
			else{	
				$('fnamecheck').update("<img src='" + Checker.instance.imagepath + "warning.gif' alt='ERROR'/> Please check that your forum name is available.");
				return false;
			}
		
		}
		
	}
});	  // END CLASS