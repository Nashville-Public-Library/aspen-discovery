<?php /** @noinspection PhpMissingFieldTypeInspection */

class ExpiredPasswordError extends AspenError {
	public $userId;
	public $expirationDate;
	public $resetToken;

	public function __construct($userId, $expirationDate, $resetToken) {
		parent::__construct('Your PIN has expired.');
		$this->userId = $userId;
		$this->expirationDate = $expirationDate;
		$this->resetToken = $resetToken;
	}
}