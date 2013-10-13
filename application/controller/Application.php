<?php

namespace application\controller;

require_once("application/view/View.php");
require_once("login/controller/LoginController.php");
require_once("login/view/RegisterView.php");


/**
 * Main application controller
 */
class Application {
	/**
	 * \view\view
	 * @var [type]
	 */
	private $view;

	private $loginView;

	/**
	 * @var \login\controller\LoginController
	 */
	private $loginController;
	
	public function __construct() {
		$this->loginView = new \login\view\LoginView();
		$registerView = new \login\view\RegisterView();

		$this->loginController = new \login\controller\LoginController($this->loginView, $registerView);
		$this->view = new \application\view\View($this->loginView, $registerView);
	}
	
	/**
	 * @return \common\view\Page
	 */
	public function doFrontPage() {
		$this->loginController->doToggleLogin();
	
		if ($this->loginController->isLoggedIn()) {
			$loggedInUserCredentials = $this->loginController->getLoggedInUser();
			return $this->view->getLoggedInPage($loggedInUserCredentials);
		} else if($this->loginController->isRegistrated()) {
			return $this->view->getLoggedOutPage();
		} else if($this->loginController->tryToRegister()) {
			return $this->view->getRegisterUserPage();
		} else {
			return $this->view->getLoggedOutPage();
		}
	}
}
