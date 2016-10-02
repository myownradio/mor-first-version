<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/core/db.class.php");



class RadioManager {

	private $uid, $db, $conf;

	function __construct ($uid = null) {
		$this->conf = require_once($_SERVER['DOCUMENT_ROOT'] . "/core/config.inc.php");
		$this->uid = $uid;
		$this->db = new db($this->conf->db->host, $this->conf->db->base, $this->conf->db->user, $this->conf->db->pass);
	}

	function getUID() {
		return $this->uid;
	}
	
	function getDatabaseTime() {
		return $this->db->query_single_col("SELECT NOW()");
	}
	
	function getStreamList() {
		
	}
}

