<?php /** @noinspection PhpMissingFieldTypeInspection */

require_once ROOT_DIR . '/sys/DB/DataObject.php';

class BadWord extends DataObject {
	public $__table = 'bad_words';    // table name
	public $id;                      //int(11)
	public $word;                    //varchar(50)

	/** @noinspection PhpUnusedParameterInspection */
	public static function getObjectStructure($context = ''): array {
		return [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'word' => [
				'property' => 'word',
				'type' => 'text',
				'label' => 'Word',
				'description' => 'The word to be censored',
				'maxLength' => 50,
				'required' => true,
			],
		];
	}

	static ?array $_badWordExpressions = null;
	/**
	 * @return string[]
	 */
	function getBadWordExpressions(): array {
		if (self::$_badWordExpressions == null) {
			global $timer;
			self::$_badWordExpressions = [];
			$this->find();
			if ($this->getNumResults()) {
				while ($this->fetch()) {
					$quotedWord = preg_quote(trim($this->word));
					//$badWordExpression = '/^(?:.*\W)?(' . preg_quote(trim($badWord->word)) . ')(?:\W.*)?$/';
					self::$_badWordExpressions[] = "/^$quotedWord(?=\W)|(?<=\W)$quotedWord(?=\W)|(?<=\W)$quotedWord$|^$quotedWord$/i";
				}
			}
			$timer->logTime("Loaded bad words");
		}

		return self::$_badWordExpressions;
	}

	function censorBadWords(?string $search, string $replacement = '***'): ?string {
		if (empty($search)) {
			return $search;
		}
		$badWordsList = $this->getBadWordExpressions();
		return preg_replace($badWordsList, $replacement, $search);
	}

	function hasBadWords($search): bool {
		$badWordsList = $this->getBadWordExpressions();
		foreach ($badWordsList as $badWord) {
			if (preg_match($badWord, $search)) {
				return true;
			}
		}
		return false;
	}

}