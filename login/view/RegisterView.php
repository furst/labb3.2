<?php

namespace login\view;

class RegisterView {

	private static $REGISTER = "register";
	private static $USERNAME = "LoginView::UserName";
	private static $PASSWORD = "RegisterView::Password";
	private static $PASSWORD_REPEAT = "RegisterView::PasswordRepeat";

	private $message = "";

	public function getRegisterBox() {

		$user = \Common\Filter::sanitizeString($this->getUserName());

		$html = "
			<form action='?" . self::$REGISTER . "' method='post' enctype='multipart/form-data'>
				<fieldset>
					$this->message
					<legend>Registrera ny användare - Skriv in användarnamn och lösenord</legend>
					<label for='UserNameID' >Användarnamn :</label>
					<input type='text' size='20' name='" . self::$USERNAME . "' id='UserNameID' value='$user' />
					<label for='PasswordID' >Lösenord  :</label>
					<input type='password' size='20' name='" . self::$PASSWORD . "' id='PasswordID' value='' />
					<label for='PasswordRepeatID' >Repetera lösenord  :</label>
					<input type='password' size='20' name='" . self::$PASSWORD_REPEAT . "' id='PasswordRepeatID' value='' />
					<input type='submit' name='" . self::$REGISTER . "'  value='Registrera' />
				</fieldset>
			</form>";

		return $html;
	}

	public function isRegistrating() {
		return isset($_POST[self::$REGISTER]);
	}

	public function getUserCredentials() {
		return \login\model\UserCredentials::createFromClientData(new \login\model\UserName($this->getUserName()),
																	\login\model\Password::fromCleartextRepeat($this->getPassword(), $this->getPasswordRepeat()));
	}

	public function getRegisterButton() {
		return "<p><a href='?" . self::$REGISTER . "'>Registrera ny användare</a></p>";
	}

	public function getBackButton() {
		return "<p><a href='?'>Tillbaka</a></p>";
	}

	private function getUserName() {
		if (isset($_POST[self::$USERNAME])) {
			return $_POST[self::$USERNAME];
		} else
			return "";
	}

	private function getPassword() {
		if (isset($_POST[self::$PASSWORD]))
			return \Common\Filter::sanitizeString($_POST[self::$PASSWORD]);
		else
			return "";
	}

	private function getPasswordRepeat() {
		if (isset($_POST[self::$PASSWORD_REPEAT]))
			return \Common\Filter::sanitizeString($_POST[self::$PASSWORD_REPEAT]);
		else
			return "";
	}

	public function registerFailed($message) {

		if(\Common\Filter::hasTags($this->getUserName())) {
			$this->message .= "<p>Användarnamnet innehåller ogiltiga tecken</p>";
		} else if (strlen($this->getUserName()) < 3) {
			$this->message .= "<p>Användarnamnet har för få tecken. Minst 3 tecken</p>";
		} else if(strlen($this->getUserName()) > 9) {
			$this->message .= "<p>Användarnamnet har för många tecken. Max 9 tecken</p>";
		}

		if ($message == "Username is taken") {
			$this->message = "<p>Användarnamnet är upptaget</p>";
		}

		if (strlen($this->getPassword()) < 6) {
			$this->message .= "<p>Lösenorden har för få tecken. Minst 6 tecken</p>";
		} else if(strlen($this->getPassword()) > 16) {
			$this->message .= "<p>Lösenorden har för många tecken. Max 16 tecken</p>";
		} else if($this->getPassword() != $this->getPasswordRepeat()) {
			$this->message .= "<p>Lösenorden matchar inte</p>";
		}
	}

	public function register() {
		if (isset($_GET[self::$REGISTER])) {
			return true;
		}
		return false;
	}
}