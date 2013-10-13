<?php

namespace login\model;


require_once("UserCredentials.php");
require_once("common/model/PHPFileStorage.php");

/**
 * represents All users in the system
 *
 */
class UserList {
	/**
	 * Temporary solution with only one user "Admin" in PHPFileStorage
 	 * You might want to use a database instead.
	 * @var \common\model\PHPFileStorage
	 */
	private $adminFile;

	/**
	 * We only have one user in the system right now.
	 * @var array of UserCredentials
	 */
	private $users;


	public function  __construct() {
		$this->users = array();
		
		$this->loadAdmin();
	}

	/**
	 * Do we have this user in this list?
	 * @throws  Exception if user provided is not in list
	 * @param  UserCredentials $fromClient
	 * @return UserCredentials from list
	 */
	public function findUser(UserCredentials $fromClient) {
		$this->loadUser($fromClient->getUserName());
		foreach($this->users as $user) {
			if ($user->isSame($fromClient) ) {
				\Debug::log("found User");
				return  $user;
			}
		}
		throw new \Exception("could not login, no matching user");
	}

	public function checkUsername(UserCredentials $fromClient) {

		try {
			$this->loadUser($fromClient->getUserName());
		} catch (\Exception $e) {
			return;
		}
		\Debug::log("Found user");
		throw new \Exception("Username is taken");
	}

	public function update(UserCredentials $changedUser) {
		//this user needs to be saved since temporary password changed
		$this->adminFile->writeItem($changedUser->getUserName(), $changedUser->toString());

		\Debug::log("wrote changed user to file", true, $changedUser);
		$this->users[$changedUser->getUserName()->__toString()] = $changedUser;
	}

	/**
	 * Temporary function to store "Admin" user in file "data/admin.php"
	 * If no file is found a new one is created.
	 * 
	 * @return [type] [description]
	 */
	private function loadAdmin() {
		
		$this->adminFile = new \common\model\PHPFileStorage("data/admin.php");
		try {
			//Read admin from file
			$adminUserString = $this->adminFile->readItem("Admin");
			$admin = UserCredentials::fromString($adminUserString);

		} catch (\Exception $e) {
			\Debug::log("Could not read file, creating new one", true, $e);

			//Create a new user
			$userName = new UserName("Admin");
			$password = Password::fromCleartext("Password");
			$admin = UserCredentials::create( $userName, $password);
			$this->update($admin);
		}

		$this->users[$admin->getUserName()->__toString()] = $admin;
	}

	private function loadUser($username) {

		$this->adminFile = new \common\model\PHPFileStorage("data/admin.php");

		//Read admin from file
		$adminUserString = $this->adminFile->readItem($username);
		$admin = UserCredentials::fromString($adminUserString);

		$this->users[$admin->getUserName()->__toString()] = $admin;
	}
}





