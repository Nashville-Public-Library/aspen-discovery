<?php /** @noinspection PhpMissingFieldTypeInspection */

class ClaimAuthorshipRequest extends DataObject {
	public $__table = 'claim_authorship_requests';
	public $id;
	public $name;
	public $phone;
	public $email;
	public $message;
	/** @noinspection PhpUnused */
	public $pid;
	public $dateRequested;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		$structure = [
			[
				'property' => 'name',
				'type' => 'text',
				'label' => 'Name',
				'description' => 'Name',
				'maxLength' => 100,
				'required' => true,
			],
			[
				'property' => 'phone',
				'type' => 'text',
				'label' => 'Phone',
				'description' => 'Phone',
				'maxLength' => 20,
				'required' => true,
			],
			[
				'property' => 'email',
				'type' => 'email',
				'label' => 'Email Address',
				'description' => 'Email Address',
				'maxLength' => 100,
				'required' => true,
			],
			[
				'property' => 'message',
				'type' => 'text',
				'label' => 'Additional Information',
				'description' => 'Additional information about your request for authorship',
				'maxLength' => 255,
				'required' => false,
			],
			'pid' => [
				'property' => 'pid',
				'type' => 'hidden',
				'label' => 'PID of Object',
				'description' => 'ID of the object in ',
				'maxLength' => 50,
				'required' => true,
			],
			'dateRequested' => [
				'property' => 'dateRequested',
				'type' => 'hidden',
				'label' => 'The date this request was made',
			],

		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	public function insert(string $context = '') : int|bool {
		$this->dateRequested = time();
		return parent::insert();
	}
}