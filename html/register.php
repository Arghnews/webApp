<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
   <HEAD>
		<meta charset="utf-8">
      <TITLE>
         Create account
      </TITLE>
   </HEAD>
<BODY>
	<script type="text/javascript" src="/static/jquery.js"></script>
	<script type="text/javascript" src="/static/common.js"></script>
	<script>

		function appendStatus( json ) {
			var fields = json["data"];
			$.each(fields, function(field, val) {
				var fieldData = val["data"];
				var problemChild = $( "input[name=" + field + "]");
				var statusText = "";
				if ( fieldData["success"] == false ) {
					statusText = fieldData["text"];
				}
				problemChild.closest("span").find("x-status").html(statusText);
			});
		}

		function sendCheck(serialForm, formId, doneFunc) {

			var formObj = $( "#" + formId );
			var actionUrl = formObj.attr("action");
			var formObj = $( "#" + formId );

			console.log("Sending this data in ajax -",serialForm);
			console.log("Sending ajax request");
			$.ajax({
				data: serialForm,
				dataType: "json", // type of data we expect back
				method: "POST",
				url: actionUrl,
			})
			.done(doneFunc)
			.fail(function( xhr, status, errorThrown ) {
				console.log("ERORR - ajax call failed");
				console.log("Error - " + errorThrown);
				console.log("Status- " + status);
				console.dir(xhr);
			})
			.always(function( xhr, status ) {
				console.log("Ajax request sent");
			});
			console.log("End of js");
		}

		$( document ).ready(function() {
			// basic form validation
			var formId = "createUserForm";
			var formObj = $( "#" + formId );

			// this gets all classes in this form that contain in the class
			// value 'validateRemote'

			$( "#" + formId+" [class*='validateRemote']").on("keyup", function() {
				var serialForm = $( "#" + formId + " input[class='validateRemote']").serialize();
				sendCheck(serialForm,formId, function( json ) {
					console.log("Received successful response!");
					console.dir(json);
					appendStatus(json);
				});
			});

			formObj.submit( function(e) {
				e.preventDefault();
				var serialForm = $( "#" + formId + " input" ).serialize();
				sendCheck(serialForm,formId,function( json ) {
					console.log("Received successful response!");
				 	appendStatus(json);
					if ( json["success"] == true ) {
						console.log("Passed validation");
						// redirect to homepage
						window.location.href = HOME;
					}
				});
			});
		});
	</script>
	<H1>Hi</H1>
	<!-- Currently for easier jquery stuff all input tags must be wrapped in spans -->
	<!-- Validate remote means input will be sent to server for validation on keypress -->
	<form action="backend/createUser.php" method="post" id="createUserForm">
		Username <span><input class="validateRemote" type="text" name="username"><x-status></x-status></span> <br>
		Password <span><input type="password" name="password"><x-status></x-status><br></span>
		<input type="submit" value="Submit"><br>
	</form>
</BODY>
</HTML>
