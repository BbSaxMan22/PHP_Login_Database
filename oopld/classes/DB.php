<?php

//core of application
//access store info
//database wrapper
//abstracts functionality
//built to be useful outside of this application

//dont want to connect to database too much, do it on the fly
// _ denotes provate or protected varibles

class DB {

	private static $_instance = null;//store instance of database if available
	private $_pdo,
			$_query,
			$_error = false,
			$_results,
			$_count = 0;
	//store pdo object when instantiated in pdo
	//query will store last query executed
	//error to indicate a successful query
	//results will store result set (ex: all users named alex)
	//count the number of results, are there any?

	private function __construct() {

		try {
			$this->_pdo = new PDO ('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
		} catch (PDOException $e) {// kill the application if this fails
			die($e->getMessage());
		}

	}// constructor, connect to the database

	public static function getInstance() {

		if(!isset(self::$_instance)) {
			self::$_instance = new DB();// new DB() will run the constructor
		}//this block wont run twice on the same page
		return self::$_instance;
	}//check if object has been created, if not make one, then return the object

	// above to establish a connection

	// 

	public function query($sql, $params = array()) {
		$this->_error = false;//reset error in case previous query goofed
		if($this->_query = $this->_pdo->prepare($sql)) {
			if(count($params)) {
				$x=1;
				foreach($params as $param) {
					$this->_query->bindValue($x, $param);
					$x++;
				}
			}// query prepared

			if($this->_query->execute()) {
				$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_count = $this->_query->rowCount();
			} else {// query executed
				$this->_error = true;
			}//query not executed
		}

		return $this;//return current object being worked with
	}//query and check for the user efficiently

	public function action($action, $table, $where = array()) {
		if(count($where) === 3) {//need a field operator and value, from specific values
			$operators = array('=', '>', '<', '>=', '<=');

			$field = $where[0];
			$operator = $where[1];
			$value = $where[2];

			if(in_array($operator, $operators)) {
				$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
				if(!$this->query($sql, array($value))->error()) {
					return $this;
				}
			}
		}
		return false;
	}

	public function get($table, $where) {//shortcut to using action
		return $this->action('SELECT *', $table, $where);
	}

	public function delete($table, $where) {
		return $this->action('DELETE', $table, $where);
	}

	public function insert($table, $fields = array()) {
		if(count($fields)) {
			$keys = array_keys($fields);
			$values = '';
			$x = 1;

			foreach($fields as $field) {
				$values .= '?';
				if($x < count($fields)) {
					$values .= ', ';
				}
				$x++;
			}

			$sql = "INSERT INTO {$table} (`" . implode('` , `', $keys) . "`) VALUES ({$values})";
			if(!$this->query($sql, $fields)->error()) {
				return true;
			}
		}

		return false;
	}

	public function update($table, $id, $fields) {
		$set = '';
		$x = 1;

		foreach($fields as $name => $value) {
			$set .= "{$name} = ?";
			if($x < count($fields)) {
				$set .= ',';
			}
			$x++;
		}

		$sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";

		if ($this->query($sql, $fields)->error()) {
			return true;
		}

		return false;
	}

	/* used in the following manner
		$user = DB::getInstance()->update('users', 3, array(
			'password' => 'newpassword',
			'name => 'Dale Garett'
		));

		// this will update the id of dale to 3, change his password, and update his name
	*/

	public function results() {
		return $this->_results;
	}

	public function first() {
		return $this->results()[0];
	}

	public function error() {
		return $this->_error;
	}

	public function count() {
		return $this->_count;
	}

}

