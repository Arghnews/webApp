<?php
	require "common.php";
	#$GLOBALS['debug'] = true; // uncomment for debugging
	// for this page, don't want printout as it will break redirect?
	
	$pdo = connect();
	
	// apparently salt is all handled for you using this snazzy bcrypt build in library!
	$username = sanitise($_POST["username"]);
	$password = sanitise($_POST["password"]);
	$hash = password_hash($password,PASSWORD_BCRYPT); # always 72 chars long
	p($hash);
	$comment = sanitise("Comment here!");

	
	// if username not taken create user
	if ( !usernameTaken($username, $pdo) ) {
		// create user in db
		$query = "insert into users (username,hash,comment) values (?,?,?)";
		$stmt = $pdo->prepare($query);
		$stmt->execute([$username,$hash,$comment]);
		p("Would redirect now to other page - obviously if you're seeing this, it won't! Account creation success");
		//header("Location: /");
		//die();
	} else {
		p("Would redirect now to other page - obviously if you're seeing this, it won't! Username was taken");
		//header("Location: /register.php");
		//die();
	}
	
	// checks if the username is taken in the database, returns true if not taken
	function usernameTaken($username, $pdo) {
		// check if users exists already
		$query = "select count(*) as number from users where username=?";
		$stmt = $pdo->prepare($query);
		$stmt->execute([$username]);
		$result = $stmt->fetch();
		if ($result['number'] === 0) {
			return false;
		}
		return true;
	}
?>
