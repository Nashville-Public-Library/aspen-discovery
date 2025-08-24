<?php /** @noinspection PhpMissingFieldTypeInspection */

class TalpaData extends DataObject
{
	public $id;
	public $groupedRecordPermanentId;
	/** @noinspection PhpUnused */
	public $lt_workcode;
	public $checked;

	public $__table = 'talpa_ltwork_to_groupedwork';

	public function getNumericColumnNames() : array {
		return ['checked'];
	}

}