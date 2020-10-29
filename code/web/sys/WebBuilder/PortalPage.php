<?php
require_once ROOT_DIR . '/sys/WebBuilder/PortalRow.php';

class PortalPage extends DataObject
{
	public $__table = 'web_builder_portal_page';
	public $id;
	public $title;
	public $urlAlias;

	private $_rows;

	static function getObjectStructure() {
		$portalRowStructure = PortalRow::getObjectStructure();
		return [
			'id' => array('property' => 'id', 'type' => 'label', 'label' => 'Id', 'description' => 'The unique id within the database'),
			'title' => array('property' => 'title', 'type' => 'text', 'label' => 'Title', 'description' => 'The title of the page', 'size' => '40', 'maxLength'=>100),
			'urlAlias' => array('property' => 'urlAlias', 'type' => 'text', 'label' => 'URL Alias (no domain)', 'description' => 'The url of the page (no domain name)', 'size' => '40', 'maxLength'=>100),

			'rows' => [
				'property'=>'rows',
				'type'=>'portalRow',
				'label'=>'Rows',
				'description'=>'Rows to show on the page',
				'keyThis' => 'id',
				'keyOther' => 'portalPageId',
				'subObjectType' => 'PortalRow',
				'structure' => $portalRowStructure,
				'sortable' => true,
				'storeDb' => true,
				'allowEdit' => true,
				'canEdit' => true,
				'hideInLists' => true
			],
		];
	}

	public function __get($name)
	{
		if ($name == 'rows') {
			return $this->getRows();
		} else {
			return $this->_data[$name];
		}
	}

	public function __set($name, $value)
	{
		if ($name == 'rows') {
			$this->_rows = $value;
		}else{
			$this->_data[$name] = $value;
		}
	}

	/**
	 * Override the update functionality to save related objects
	 *
	 * @see DB/DB_DataObject::update()
	 */
	public function update(){
		//Updates to properly update settings based on the ILS
		$ret = parent::update();
		if ($ret !== FALSE ){
			$this->saveRows();
		}

		return $ret;
	}

	/**
	 * Override the insert functionality to save the related objects
	 *
	 * @see DB/DB_DataObject::insert()
	 */
	public function insert(){
		$ret = parent::insert();
		if ($ret !== FALSE ){
			$this->saveRows();
		}
		return $ret;
	}

	public function saveRows(){
		if (isset ($this->_rows) && is_array($this->_rows)){
			$this->saveOneToManyOptions($this->_rows, 'portalPageId');
			unset($this->_rows);
		}
	}

	/** @return PortalRow[] */
	public function getRows()
	{
		if (!isset($this->_rows) && $this->id){
			$this->_rows = [];
			$obj = new PortalRow();
			$obj->portalPageId = $this->id;
			$obj->orderBy('weight ASC');
			$obj->find();
			while($obj->fetch()){
				$this->_rows[$obj->id] = clone $obj;
			}
		}
		return $this->_rows;
	}
}