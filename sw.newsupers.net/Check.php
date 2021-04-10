<?php

require_once('Log.php');

class Check{

	Private $mysqli;
	Private $log;

	public function __construct() {
		global $mysqli;
		$this->mysqli = $mysqli;
		$this->log = new Log($mysqli);
	}

	public function CheckPost($text = '') {
		//nonpublic
		return true;
	}

	public function CheckflushAll() {
		//nonpublic
		return true;
	}

	public function CheckflushOne($seq) {
		//nonpublic
		return true;
	}
}

?>