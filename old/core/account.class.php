<?php
class userAccount {
	
	private static $authorizedUser = array(
		'id'	=> 0
	);
	
	static function loginUser($login, $password) {
		$res = db::query("SELECT * FROM `users` WHERE `login` = :login AND `password` = :password", array( 'login' => $login, 'password' => $password));
		
	}
	
	static function createUser($login, $password, $name, $info, $avatar) {
		// Check that the login is unique
	}
	
}