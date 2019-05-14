<?php

class Database {
	private $_db_name;
	private $_db_user;
	private $_db_passwd;
	private $_db_host;
	private $_pdo;

	public function __construct($db_name, $db_user = 'root', $db_passwd = 'root', $db_host = 'localhost') {
		$this->_db_name = $db_name;
		$this->_db_user = $db_user;
		$this->_db_passwd = $db_passwd;
		$this->_db_host = $db_host;
		unset ($this->_pdo);
		self::getPdo();
	}

	private function getPdo() {
		if (!isset($_pdo)) {
			$this->_pdo = new PDO('mysql:dbname=' . $this->_db_name . ';host=' . $this->_db_host, $this->_db_user, $this->_db_passwd);
			$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		return ($this->_pdo);
	}

	public function query($statement, $classname, $attr = array()) {
		$request = $this->getPdo()->prepare($statement);
		$request->execute($attr);
		return ($request->fetchAll(PDO::FETCH_NUM));
	}

	public function queryOne($statement, $classname, $attr = array()) {
		$request = $this->getPdo()->prepare($statement);
		$request->execute($attr);
		$request->setFetchMode(PDO::FETCH_NUM);
		return ($request->fetch());
	}

	public function lastInsertedId() {
		return ($this->getPdo()->lastInsertId());
	}

	public function create_insert_drop($statement, $attr = array()) {
		$request = $this->getPdo()->prepare($statement);
		return ($request->execute($attr));
	}

	public function update($statement, $attr = array()) {
		$request = $this->getPdo()->prepare($statement);
		return ($request->execute($attr));
	}

	public function delete($statement, $attr = array()) {
		$request = $this->getPdo()->prepare($statement);
		return ($request->execute($attr));
	}
}
