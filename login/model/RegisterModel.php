<?php

namespace login\model;

require_once("UserCredentials.php");
require_once("UserList.php");
require_once("LoginInfo.php");


class RegisterModel {
	/**
	 * Location in $_SESSION
	 * @var string
	 */
	private static $registratedUser = "RegisterModel::registratedUser";
	
	/**
	 * @var \model\UserList
	 */
	private $allUsers;
	
	
	public function __construct() {
		assert(isset($_SESSION));
		
		$this->allUsers = new UserList();
	}
	
	/**
	 * @param  UserCredentials $fromClient
	 * @param  LoginObserver   $observer 
	 *
	 * @throws  \Exception if login failed
	 */
	public function doRegister(UserCredentials $fromClient) {

		try {
			$this->allUsers->checkUsername($fromClient);

			//create new temporary password and save it
			$fromClient->newTemporaryPassword();

			//this user needs to be saved since temporary password changed
			$this->allUsers->update($fromClient);

			$this->registrationComplete();

		} catch (\Exception $e) {
			\Debug::log("Registration failed", false, $e->getMessage());
			throw $e;
		}
	}

	public function isRegistrated() {
		if (isset($_SESSION[self::$registratedUser])) {
			unset($_SESSION[self::$registratedUser]);
			return true;
		}
		return false;
	}

	public function registrationComplete() {
		$_SESSION[self::$registratedUser] = true;
	}
}
