<?php
	require "common.php";
	#$GLOBALS['debug'] = true; // uncomment for debugging
	// for this page, don't want printout as it will break redirect?
	
	$pdo = connect();

	p("Creating fieldlist");
	$fields = new FieldList($_POST,"username","password");
	p("Created fieldlist");
	
	p("Fieldlist object should all data received - printing it as json below");
	p($fields);


	// if username taken set error
	if ( usernameTaken($fields->getValue("username"), $pdo) === true ) {
		$fields->getField("username")->appendText("username taken");
		$fields->getField("username")->setSuccess(false);
	}
	if ( passwordLengthGood($fields->getValue("password")) !== true ) {
		$fields->getField("password")->appendText("please use a password between 8 and 24 characters");
		$fields->getField("password")->setSuccess(false);
	}

	// set password field value to hash
	$passObj = $fields->getField("password");
	$hash = password_hash($passObj->getValue(),PASSWORD_BCRYPT); # always 72 chars long
	$passObj->setValue($hash);

	$comment = "Comment field, unused currently!";

	// if this is true then has passed all validation
	if ( $fields->getSuccess() ) {
		// create user in db
		try {
			$query = "insert into users (username,hash,comment) values (?,?,?)";
			$stmt = $pdo->prepare($query);
			$stmt->execute([$fields->getValue("username"),$fields->getValue("password"),$comment]);
			p("User successfully created");
		} catch (Exception $e) { // obviously this could be PDOException
			p("Database error ".$e->getMessage());
			$fields->setText("Could not create user, database error");
			$fields->setSuccess(false);
		}
	}

	echo json_encode($fields->toJson());

?>
