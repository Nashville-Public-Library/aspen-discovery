<?php /** @noinspection PhpMissingFieldTypeInspection */


class ARSetting extends DataObject {
	public $__table = 'accelerated_reading_settings';
	public $id;
	/** @noinspection PhpUnused */
	public $indexSeries;
	/** @noinspection PhpUnused */
	public $indexSubjects;
	/** @noinspection PhpUnused */
	public $arExportPath;
	/** @noinspection PhpUnused */
	public $ftpServer;
	/** @noinspection PhpUnused */
	public $ftpUser;
	/** @noinspection PhpUnused */
	public $ftpPassword;
	/** @noinspection PhpUnused */
	public $lastFetched;
	/** @noinspection PhpUnused */
	public $updateOn;
	/** @noinspection PhpUnused */
	public $updateFrequency;

	static $_objectStructure = [];
	static function getObjectStructure(string $context = ''): array {
		if (isset(self::$_objectStructure[$context]) && self::$_objectStructure[$context] !== null) {
			return self::$_objectStructure[$context];
		}
		$structure = [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id',
			],
			'indexSeries' => [
				'property' => 'indexSeries',
				'type' => 'checkbox',
				'label' => 'Index Series',
				'description' => 'Whether or not series from the AR data should be indexed',
				'default' => 1,
				'forcesReindex' => true,
			],
			'indexSubjects' => [
				'property' => 'indexSubjects',
				'type' => 'checkbox',
				'label' => 'Index indexSubjects',
				'description' => 'Whether or not subjects from the AR data should be indexed',
				'default' => 1,
				'forcesReindex' => true,
			],
			'arExportPath' => [
				'property' => 'arExportPath',
				'type' => 'text',
				'label' => 'AR Export Path',
				'description' => 'The local path on the server where the Accelerated Reader data is stored',
				'forcesReindex' => true,
			],
			'ftpServer' => [
				'property' => 'ftpServer',
				'type' => 'text',
				'label' => 'FTP Server URL',
				'description' => 'The Name of the FTP Server',
				'forcesReindex' => true,
			],
			'ftpUser' => [
				'property' => 'ftpUser',
				'type' => 'text',
				'label' => 'FTP Username',
				'description' => 'The username to connect to the FTP Server',
				'forcesReindex' => true,
			],
			'ftpPassword' => [
				'property' => 'ftpPassword',
				'type' => 'storedPassword',
				'label' => 'FTP Password',
				'description' => 'The password to connect to the FTP Server',
				'hideInLists' => true,
				'forcesReindex' => true,
			],
			'updateOn' => [
				'property' => 'updateOn',
				'type' => 'enum',
				'values' => [
					0 => 'Friday night/Saturday morning',
					1 => 'Saturday night/Sunday morning',
				],
				'label' => 'Update On',
				'description' => 'Which day to run the update on',
				'default' => 0,
			],
			'updateFrequency' => [
				'property' => 'updateFrequency',
				'type' => 'enum',
				'values' => [
					0 => 'Weekly',
					1 => 'Bi-weekly',
					2 => 'Monthly',
				],
				'label' => 'Update Frequency',
				'description' => 'The frequency to update Accelerated Reader',
				'default' => 0,
			],
			'lastFetched' => [
				'property' => 'lastFetched',
				'type' => 'timestamp',
				'label' => 'Last Fetch from the FTP Server',
				'description' => 'The timestamp when the file was last fetched from the server',
				'default' => 0,
			],
		];

		self::$_objectStructure[$context] = $structure;
		return self::$_objectStructure[$context];
	}
}