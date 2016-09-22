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
				//var serialForm = formId.serialize();
				var serialForm = {};
				formId.children("input").each(function() {
					console.log("Kiddie - " + this.name + " " + this.value);
					if (this.name != "") {
						serialForm[this.name] = this.value;
					}
				});
				console.log(serialForm);
				console.log("Sending ajax request");
				$.ajax({
					url: actionUrl,
					dataType: 'json', // type of data we expect back
					data: serialForm,
					type: "POST"
				})
				.done(function( json ) {
					console.log(json)
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
	<form action="backend/createUser.php" method="post" id="createUserForm">
		Username <input type="text" name="username"><br>
		Password <input type="password" name="password"><br>
		<input type="submit" value="Submit"><br>
	</form>
</BODY>
</HTML>
