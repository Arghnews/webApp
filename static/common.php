<?php

const SESSION_START_PATH = "/var/www/html/backend/sessionStart.php";
const SESSION_DESTROY_PATH = "/var/www/html/backend/sessionDestroy.php";

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
	p("Connecting to ".$dsn." ".$user." "."HIDDEN BUT PASSWORD HERE"." ".json_encode($opt));
	
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

// wrapper for fields
// example below, although it's fairly negative so don't look at it for too long
//{
//	success: false
//	text: "Could not create user" -- this text may/will be printed to user, make friendly
//	"data": {
//				success: false, // is O(n) call in number of fields, AND of all successes
//				fields: {
//					"username" => Field obj, ...
//				}
//			}
//}
// setting the success variable manually will override other settings
// useful if database error then just fail
class FieldList {
	private $success;
	private $text;
	private $fields; // array of Field objects
	private $keys; // array of strings that are keys
	
	public function setSuccess($bool) {
		$this->success = $bool;
	}

	public function setText($text) {
		$this->text = $text;
	}

	// convenient wrapper so can go $FieldListInstance->getValue("username")
	public function getValue($name) {
		return $this->getField($name)->getValue();
	}

	// boolean, meant to be all success values ANDed
	public function getSuccess() {
		p("Checking success status of all");
		// set false if error has occurred, can return false immediately
		if ( $this->success === false ) {
			return false;
		}
		foreach ($this->fields as $field) {
			if ( $field->getSuccess() === false) {
				$this->success = false;
				break;
			}
		}
		return $this->success;
	}

	// requires PHP >= 5.6 (which I (obviously) have)
	// where $fields is "username","password".. html name attribs
	// eg. new FieldList($_POST,"username","password")
	public function __construct($post) {
		$this->fields = array();
		$keys = array();
		foreach($post as $name => $value) {

			// adds keys (strings) to array, username, password, etc.
			$keys[] = $name;

			// yes this could print passwords in plaintext
			// but for debugging sanity I shall take this risk
			// should only ever see this serverside for debugging anyway
			p("Adding ".$name." ".$value);
			$this->fields += [ $name => (new Field($value)) ];
		}
		$this->keys = $keys;
		$this->success = true;
		$this->text = "";
	}

	public function getKeys() {
		return $this->keys;
	}

	public function hasKey($hasMe) {
		foreach ($this->keys as $key) {
			if ( $key === $hasMe ) {
				return true;
			}
		}
		return false;
	}

	// $name should be string that is field name
	// returns Field object for editting
	public function getField($name) {
		return $this->fields[$name];
	}

	// maybe can just use json_encode dur?
	public function toJson() {
		$data = array();
		foreach ($this->fields as $name => $field) {
			$data += [$name => $field->toJson()];
		}
		$top = array("success"=>$this->getSuccess(),"data"=>$data,"text"=>$this->text);
		return $top;
	}

	// before this called toJson which has $this->getSuccess() in
	// meaning printing would change the data - BAD
	public function __toString() {
		$data = array();
		foreach ($this->fields as $name => $field) {
			$data += [$name => $field->toJson()];
		}
		$top = array("success"=>$this->success,"data"=>$data,"text"=>$this->text);
		return json_encode($top);
	}
}

// these are created with success as FALSE by default
// this is because if using object assumed you want some checks
// corresponds to a field in a html form
// text var will be used in js on page to inform user of the problem
//{
//	"data": {
//				"success": false,
//				"text": "Username taken"
//				"value": "bill25"
//			}
//}

// this class should probably be private?
class Field {
	private $data;
	public function __construct($value) {
		$this->data = array("success"=>false,"text"=>"","value"=>$value);
	}

	// $name should be html form input attrib name, eg. "username"
	public function getValue() {
		return $this->data["value"];
	}

	public function setValue($value) {
		$this->data["value"] = $value;
	}
	
	// takes boolean
	public function setSuccess($success) {
		$this->data["success"] = $success;
	}

	// returns the value of success property
	// if used success() would call func "success"
	public function getSuccess() {
		return $this->data["success"];
	}

	public function appendText($text) {
		if ( $this->data["text"] !== "" ) {
			$this->data["text"] .= ", ";
		}
		$this->data["text"] .= $text;
	}
	
	// takes string, ie. failed because of db error
	public function setText($text) {
		$this->data["text"] = $text;
	}

	public function toJson() {
		return array("data"=>$this->data);
	}
	
	public function __toString() {
		return json_encode($this->toJson());
	}

}

// returns true if valid length 8-24
function passwordGood($pass) {
	// default encoding/encoding specified in php.ini for nginx's php fpm module
	// is 'UTF-8'
	$len = mb_strlen($pass);
	//$len = strlen($pass);
	// original code of ($len >= 8 && $len <= 24) doesn't seem to work since I think
	// when true these return 1, when false they seem to return nothing, when printed empty string
	// be careful, these seem to return nothing or they don't print properly
	// this does work though :P
	if ( ( $len < 8 ) || ( $len > 24 ) )  {
		return false;
	} else {
		return true;
	}
}

// checks if the username is taken in the database, returns true if not taken
function usernameTaken($username, $pdo) {
	// check if users exists already
	$query = "select count(*) as number from users where username=? limit 1";
	$stmt = $pdo->prepare($query);
	$stmt->execute([$username]);
	$result = $stmt->fetch();
	if ($result['number'] === 0) {
		return false;
	}
	return true;
}


?>
