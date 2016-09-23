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

	// set password field value to hash
	$passObj = $fields->getField("password");
	$hash = password_hash($passObj->getValue(),PASSWORD_BCRYPT); # always 72 chars long
	$passObj->setValue($hash);

	$comment = "Comment field, unused currently!";
	// if username not taken create user
	if ( !usernameTaken($fields->getValue("username"), $pdo) ) {
		// create user in db
		try {
			$query = "insert into users (username,hash,comment) values (?,?,?)";
			$stmt = $pdo->prepare($query);
			$stmt->execute([$fields->getValue("username"),$fields->getValue("password"),$comment]);
			p("User successfully created");
		} catch (Exception $e) {
			p("Database error ".$e->getMessage());
			$fields->setText("Could not create user, database error");
			$fields->setSuccess(false);
		}
		
	} else {
		$fields->getField("username")->setText("username taken");
		$fields->getField("username")->setSuccess(false);
	}
	echo json_encode($fields->toJson());

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
		public function __construct($array, ...$names) {
			$this->fields = array();
			foreach($names as $name) {
				// yes this could print passwords in plaintext
				// but for debugging sanity I shall take this risk
				// should only ever see this serverside for debugging anyway
				p("Hi mum! ".$name." ".$array[$name]);
				$this->fields += [ $name => (new Field($array[$name])) ];
			}
			$this->success = true;
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
			$top = array("success"=>$this->getSuccess(),"data"=>$data);
			return $top;
		}

		public function __toString() {
			return json_encode($this->toJson());
		}
	}

	// these are created with success as true by default
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
			$this->data = array("success"=>true,"text"=>"","value"=>$value);
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
