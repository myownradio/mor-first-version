<?php

class myredis {
	private $handle = null;
	private static function init() {
		$this->handle = new Redis();
		$this->handle->connect('127.0.0.1', 6379);
	}
	static function set($key, $val) {
        if(self::$handle == null)
            self::init();
		self::$handle->set($key, $val);
        return self;
	}
	static function get($key) {
        if(self::$handle == null)
            self::init();
		return self::$handle->get($key);
	}
}