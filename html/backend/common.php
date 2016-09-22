<?php
// set true for debug print outs -- may break header redirection pages remember!
if ( !isset($GLOBALS['debug']) ) {
	$GLOBALS['debug'] = false;
}

// println, puts br in for html, only prints if global debug var is set
function p($p) {
	if ( $GLOBALS['debug'] === true ) {
		echo $p."\t<br>\n";
	}
}

// function to sanitise all input
function sanitise($str) {
	return $str;
}

// for now all hard coded, should be fine
function connect() {
	// mysql credentials/port
	$host = '127.0.0.1:3306';
	$dbname = 'webApp';
	$user = 'webApp';
	$pass = 'l3mp$tAck';
	$charset = 'utf8';
	p("End of vars");
	// address to connect to
	$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
	$opt = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false 
	];
	p("Connecting to ".$dsn." ".$user." ".$pass." ".$opt);
	
	try {
		$pdo = new PDO($dsn, $user, $pass, $opt);
		// since we're not exactly high security setting errors on
		ini_set('display_errors',1);
		p("Connected");
		return $pdo;
	} catch (PDOException $e) {
		p("Error - Could not connect - ".$e->getMessage());
		return null;
	}
	p("Connected");
	
}

?>
