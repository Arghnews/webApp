<?php
	require_once "/var/www/static/common.php";
	#$GLOBALS['debug'] = true; // uncomment for debugging
	// for this page, don't want printout as it will break redirect?
	
	$pdo = connect();

	p("Creating fieldlist");
	
	// $fields = new FieldList($_POST,"username","password");
	$fields = new FieldList($_POST);

	p("Created fieldlist");
	
	p("Fieldlist object should all data received - printing it as json below");
	p($fields);


	checkUsername($fields,$pdo);

	checkPassword($fields);

	// checks if username == password
	if ( ( $fields->hasKey("username") ) && ( $fields->hasKey("password") ) ) {
		if ( $fields->getValue("username") === $fields->getValue("password") ) {
			$fields->getField("username")->appendText("username and password cannot be the same");
			$fields->getField("username")->setSuccess(false);
		}
	}

	// repeated for clarity here
	if ( ( $fields->hasKey("username") ) && ( $fields->hasKey("password") ) ) {
		// if this is true then has passed all validation

		if ( $fields->getSuccess() ) {
			p("Success - going to put value into db");
			// set password field value to hash
			$passObj = $fields->getField("password");
			$hash = password_hash($passObj->getValue(),PASSWORD_BCRYPT); # always 72 chars long
			$passObj->setValue($hash);

			$comment = "Comment field, unused currently!";

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
	}

	echo json_encode($fields->toJson());

	function checkUsername($fields,$pdo) {
		if ( $fields->hasKey("username") ) {
			// username cannot be empty
			if ( $fields->getValue("username") === "" ) {
				$fields->getField("username")->setText("username may not be empty");
				$fields->getField("username")->setSuccess(false);
			} elseif ( usernameTaken($fields->getValue("username"), $pdo) === true ) {
				$fields->getField("username")->appendText("username taken");
				$fields->getField("username")->setSuccess(false);
			} else {
				$fields->getField("username")->setSuccess(true);
			}
		}
	}

	function checkPassword($fields) {
		// checks if password between 8 and 24 chars
		if ( $fields->hasKey("password") ) {
			if ( passwordGood($fields->getValue("password")) !== true ) {
				$fields->getField("password")->appendText("please use a password between 8 and 24 characters");
				$fields->getField("password")->setSuccess(false);
			} else {
				$fields->getField("password")->setSuccess(true);
			}
		}
	}

?>
