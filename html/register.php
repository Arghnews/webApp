<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
   <HEAD>
		<meta charset="utf-8">
      <TITLE>
         Create account
      </TITLE>
   </HEAD>
<BODY>
	<script type="text/javascript" src="jquery.js"></script>
	<script>
		$( document ).ready(function() {
			// basic form validation
			formId = $( "#createUserForm" );
			formId.submit( function(e) {
				e.preventDefault();
				var actionUrl = e.currentTarget.action;
				console.log("Sending ajax request to" + actionUrl);
				var serialForm = formId.serialize();
				
				console.log("Sending this data in ajax -",serialForm);
				console.log("Sending ajax request");
				$.ajax({
					data: serialForm,
					dataType: "json", // type of data we expect back
					method: "POST",
					url: actionUrl,
				})
				.done(function( json ) {
	//{
	//	success: false
	//	text: "Could not create user" -- this text may/will be printed to user, make friendly
	//	"data": {
	//				success: false, // is O(n) call in number of fields, AND of all successes
	//				fields: {
	//					"username" => // this is a field object
								//	"data": {
								//				"success": false,
								//				"text": "Username taken"
								//				"value": "bill25"
								//			}
	//					"password" => ...
	//				}
	//			}
	//}
					console.log("Received successful response!");
					console.log(json);
					if ( json["success"] == true ) {
						console.log("success");
						// redirect to homepage
					} else {
						console.log("not success");
						var fields = json["data"];
						console.log(fields);
		// Assuming html form elements look like this				
		// Username <span><input type="text" name="username"><x-error></x-error></span> <br>
		// this sets the text for each to whatever the text returned by the backend is
						$.each(fields, function(field, val) {
							var fieldData = val["data"];
							var problemChild = $( "input[name=" + field + "]");
							var statusText = "";
							if ( fieldData["success"] == false ) {
								statusText = fieldData["text"];
							}
							problemChild.closest("span").find("x-error").html(statusText);
						});
					}
					
				})
				.fail(function( xhr, status, errorThrown ) {
					
				})
				.always(function( xhr, status ) {
				});
				console.log("End of js");
			});
		});
	</script>
	<H1>Hi</H1>
	<!-- Currently for easier jquery stuff all input tags must be wrapped in spans -->
	<form action="backend/createUser.php" method="post" id="createUserForm">
		Username <span><input type="text" name="username"><x-error></x-error></span> <br>
		Password <span><input type="password" name="password"><x-error></x-error><br></span>
		<input type="submit" value="Submit"><br>
	</form>
</BODY>
</HTML>
