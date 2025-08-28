<?php /** @noinspection PhpMissingFieldTypeInspection */

class VdxRequest extends DataObject {
	public $__table = 'user_vdx_request';
	public $id;
	public $userId;
	public $datePlaced;
	public $title;
	public $author;
	public $publisher;
	public $isbn;
	public $feeAccepted;
	public $maximumFeeAmount;
	public $catalogKey;
	public $status;
	public $note;
	public $pickupLocation;
	public $vdxId;
}