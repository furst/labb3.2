<?php

namespace login\controller;

require_once("./login/model/LoginModel.php");
require_once("./login/model/RegisterModel.php");
require_once("./login/view/LoginView.php");

class LoginController {
	
	/**
	 * @var \login\model\LoginModel
	 */
	private $model;

	private $registerModel;
	/**
	 * @var \login\view\LoginView
	 */
	private $view;

	private $registerView;
	
	/**
	 * @param loginviewLoginView $view
	 */
	public function __construct(\login\view\LoginView $view, \login\view\RegisterView $registerView) {
		$this->model = new \login\model\LoginModel();
		$this->registerModel = new \login\model\RegisterModel();
		$this->registerView = $registerView;
		$this->view = $view;
	}
	
	
	/**
	 * Facade
	 * @return boolean
	 */
	public function isLoggedIn() {
		return $this->model->isLoggedIn();
	}
	
	/** 
	 * Facade
	 * @return \login\model\UserCredentials
	 */
	public function getLoggedInUser() {
		return $this->model->getLoggedInUser();
	}

	public function tryToRegister() {
		return $this->registerView->register();
	}

	public function isRegistrated() {
		return $this->registerModel->isRegistrated();
	}

	/**
	 * Handle input
	 * Make sure to log statechanges
	 *
	 * note this has no output, output goes through views that are called seperately
	 */
	public function doToggleLogin() {
		if ($this->model->isLoggedIn()) {
			\Debug::log("We are logged in");
			if ($this->view->isLoggingOut() ) {
				$this->model->doLogout();
				$this->view->doLogout();
				\Debug::log("We logged out");
			}
		} else {
			\Debug::log("We are not logged in");
			if ($this->view->isLoggingIn()) {
				try {
					$credentials = $this->view->getUserCredentials();
					$this->model->doLogin($credentials, $this->view);
					\Debug::log("Login succeded");
				} catch (\Exception $e) {
					\Debug::log("Login failed", false, $e->getMessage());
					$this->view->LoginFailed();
				}
			} else if ($this->registerView->isRegistrating()) {
				try {
					$credentials = $this->registerView->getUserCredentials();
					$this->registerModel->doRegister($credentials);
					\Debug::log("Registration complete");
				} catch (\Exception $e) {
					\Debug::log("Login failed", false, $e->getMessage());
					$this->registerView->RegisterFailed($e->getMessage());
				}
			}
		}
	}
}





