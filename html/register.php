<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
   <HEAD>
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
				$.ajax({
					url: actionUrl,
					datatype: 'json',
					data: formId.serializeArray(),
					success: function(data) {
						alert(data);
					}
				});
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
