<?php /** @noinspection PhpMissingFieldTypeInspection */

class BlockPatronAccountLink extends DataObject {

	public $__table = 'user_link_blocks';
	public $id;
	public $primaryAccountId;
	public $blockedLinkAccountId; // A specific account primaryAccountId will not be linked to.
	public $blockLinking;         // Indicates primaryAccountId will not be linked to any other accounts.

	// Additional Info Not stored in table
	public $_primaryAccountBarCode;      //  The info the Admin user will see & input
	public $_blockedAccountBarCode;      //  The info the Admin user will see & input

	public function getNumericColumnNames(): array {
		return ['id', 'primaryAccountId', 'blockedLinkAccountId', 'blockLinking'];
	}

	/**
	 * Override the fetch functionality to fetch Account BarCodes
	 *
	 * @param bool $includeBarCodes short-circuit the fetching of barcodes when not needed.
	 * @return bool|DataObject|null
	 * @see DB/DB_DataObject::fetch()
	 */
	function fetch(bool $includeBarCodes = true): bool|DataObject|null {
		$return = parent::fetch();
		if (!is_null($return) & $includeBarCodes) {
			// Default values (clear out any previous values
			$this->_blockedAccountBarCode = null;
			$this->_primaryAccountBarCode = null;

			$user = new User();
			if ($user->get($this->primaryAccountId)) {
				$this->_primaryAccountBarCode = $user->ils_barcode;
			}
			if ($this->blockedLinkAccountId) {
				$user = new User();
				if ($user->get($this->blockedLinkAccountId)) {
					$this->_blockedAccountBarCode = $user->ils_barcode;
				}
			}
		}
		return $return;
	}

	/**
	 * Override the update functionality to store account ids rather than barcodes
	 *
	 * @see DB/DB_DataObject::update()
	 */
	public function update(string $context = '') : int|bool {
		$this->getAccountIds();
		if (!$this->primaryAccountId) {
			$this->setLastError("Could not find a user for the blocked barcode that was provided");
			return false;
		}  // require a primary account id
		if (!$this->blockedLinkAccountId && !$this->blockLinking) {
			$this->setLastError("Could not find a user for the non accessible barcode that was provided");
			return false;
		} // require at least one of these
		return parent::update();
	}

	/**
	 * Override the insert functionality to store account ids rather than barcodes
	 *
	 * @see DB/DB_DataObject::insert()
	 */
	public function insert(string $context = '') : int|bool {
		$this->getAccountIds();
		if (!$this->primaryAccountId) {
			$this->setLastError("Could not find a user for the blocked barcode that was provided");
			return false;
		}  // require a primary account id
		if (!$this->blockedLinkAccountId && !$this->blockLinking) {
			$this->setLastError("Could not find a user for the non accessible barcode that was provided");
			return false;
		} // require at least one of these
		return parent::insert();
	}

	private function getAccountIds() : void {
		// Get Account Ids for the barcodes
		if ($this->_primaryAccountBarCode) {
			$user = new User();
			$user->ils_barcode = $this->_primaryAccountBarCode;
			if ($user->find(true)) {
				$this->primaryAccountId = $user->id;
			}
		}
		if ($this->_blockedAccountBarCode) {
			$user = new User();
			$user->ils_barcode = $this->_blockedAccountBarCode;
			if ($user->find(true)) {
				$this->blockedLinkAccountId = $user->id;
			}
		}
	}

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		$structure = [
			[
				'property' => 'id',
				'type' => 'hidden',
				'label' => 'Id',
				'description' => 'The unique id of the blocking row in the database',
				'storeDb' => true,
				'primaryKey' => true,
			],
			[
				'property' => '_primaryAccountBarCode',
				'type' => 'text',
//				'size' => 36,
//				'maxLength' => 36,
				'label' => 'The following blocked barcode will not have access to the account below.',
				'description' => 'The account the blocking settings will be applied to.',
				'storeDb' => true,
//				'showDescription' => true,
				'required' => true,
			],
			[
				'property' => '_blockedAccountBarCode',
				'type' => 'text',
//				'size' => 36,
//				'maxLength' => 36,
				'label' => 'The following barcode will not be accessible by the blocked barcode above.',
				'description' => '',
//				'showDescription' => true,
				'storeDb' => true,
//				'required' => true,
			],
			[
				'property' => 'blockLinking',
				'type' => 'checkbox',
				'label' => 'Check this box to prevent the blocked barcode from accessing ANY linked accounts.',
				'description' => 'Prevent the blocked barcode from linking to any account.',
//				'showDescription' => true,
				'storeDb' => true,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}

	public function okToExport(array $selectedFilters): bool {
		return true;
	}
}