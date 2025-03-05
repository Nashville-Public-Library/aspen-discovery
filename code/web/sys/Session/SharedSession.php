<?php

require_once ROOT_DIR . '/sys/DB/DataObject.php';

class SharedSession extends DataObject {
	public $__table = 'shared_session';
	protected $id;
	protected $sessionId;
	protected $userId;
	protected $createdOn;

	public function isSessionStillValid() {
		return $this->createdOn < strtotime('+1 hour', $this->createdOn);
	}

	public function getSessionId(): mixed {
		return $this->sessionId;
	}

	public function setSessionId($sessionId): void {
		$this->sessionId = $sessionId;
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id): void {
		$this->id = $id;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function setUserId($userId): void {
		$this->userId = $userId;
	}

	public function getCreated() {
		return $this->createdOn;
	}

	public function setCreated($created): void {
		$this->createdOn = $created;
	}

	public function redirectUser(User $user, $returnTo, $id = null) {
		$page = '/MyAccount/' . $returnTo;
		if ($returnTo === 'GroupedWork' && $id) {
			$page = '/GroupedWork/' . $id . '/Home/';
		}elseif ($returnTo === 'Fines' || $returnTo === 'YearInReview') {
			$page = '/MyAccount/' . $returnTo;
		} else if (!empty($id)) {
			$page = "/$returnTo/$id" ;
		}

		global $configArray;
		$redirectTo = $configArray['Site']['url'] . $page . '?minimalInterface=true'; // set minimalInterface to hide some unnecessary elements that clutter the mobile UI
		if (UserAccount::loginWithAspen($user)) {
			global $timer;
			/** SessionInterface $session */ global $session;
			require_once ROOT_DIR . '/sys/Session/MySQLSession.php';
			session_name('aspen_session');
			$session = new MySQLSession();
			$session->init();

			$timer->logTime('Session initialization MySQLSession');

			$_SESSION['activeUserId'] = $user->id;
			$_SESSION['rememberMe'] = false;
			$_SESSION['loggedInViaSSO'] = true;

			header('Location: ' . $redirectTo);
		}
	}
}