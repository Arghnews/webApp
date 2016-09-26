<?php
	require_once "/var/www/static/common.php";
	#$GLOBALS['debug'] = true; // uncomment for debugging

	p($_POST["username"]." ".$_POST["password"]);

	$pdo = connect();

	p("Creating fieldlist");
	
	// $fields = new FieldList($_POST,"username","password");
	$fields = new FieldList($_POST);

	p("Created fieldlist");
	p($fields);

	// validation should be done here
	$fields->getField("username")->setSuccess(true);
	$fields->getField("password")->setSuccess(true);

	p($fields);

	// repeated for clarity here
	if ( $fields->getSuccess() ) {
		p("Going to db");

		// check user in db
		try {
			$query = "select username, hash from users where username=?";
			$stmt = $pdo->prepare($query);
			p("About to query");
			$stmt->execute([$fields->getValue("username")]);
			// should only ever return 1 row as users are unique
			p("Query done");

			$row = $stmt->fetch();
			// please note the password field is called 'hash' in the db
			
			p("Row:".$row["username"]." ".$row["hash"]);
			$user = ( $row["username"] === $fields->getValue("username") );
			p($user);
			
			p("Comparing user hash");
			$pass = ( password_verify($fields->getValue("password"),$row["hash"]) );
			if ( ( $user === true ) && ( $pass === true ) ) {
				p("Login success");
				// successful login
				$fields->setText("Login successful!");
				$fields->setSuccess(true);
				// start the session
				require_once SESSION_START_PATH;
				$_SESSION["username"] = $fields->getValue("username");
				header("Location: /");
			} else {
				p("Login failed");
				// don't do this by default, will override inner booleans
				$fields->setText("Login failed");
				$fields->setSuccess(false);
				header("Location: /");
			}
		} catch (Exception $e) { // obviously this could be PDOException
			p("Database error ".$e->getMessage());
			$fields->setText("Could not login user, database error");
			$fields->setSuccess(false);
		}
	}
	p("Returning:");
	echo json_encode($fields->toJson());

?>
