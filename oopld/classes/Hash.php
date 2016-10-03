<?php

class Hash {

	public static function make($string, $salt = '') {
		return hash('sha256', $string . $salt);
	}// a salt will add a randomly generated, secure string of characters to the end of a password to make it more difficult to forge, to look them up
	// two same passwords will not be stored in the same way in the database

	public static function salt($length) {
		return mcrypt_create_iv($length);
	}

	public static function unique() {
		return self::make(uniqid());
		//TODO use md5() function to generate the id, time is more predictable
	}

}//generate a one way hash with sha256