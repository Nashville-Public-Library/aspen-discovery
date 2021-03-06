<?php

require_once ROOT_DIR . '/Action.php';
require_once ROOT_DIR . '/services/Admin/ObjectEditor.php';
require_once ROOT_DIR . '/sys/OverDrive/OverDriveSetting.php';

class OverDrive_Settings extends ObjectEditor
{
	function getObjectType()
	{
		return 'OverDriveSetting';
	}

	function getToolName()
	{
		return 'Settings';
	}

	function getModule()
	{
		return 'OverDrive';
	}

	function getPageTitle()
	{
		return 'OverDrive Settings';
	}

	function getAllObjects()
	{
		$object = new OverDriveSetting();
		$object->find();
		$objectList = array();
		while ($object->fetch()) {
			$objectList[$object->id] = clone $object;
		}
		return $objectList;
	}

	function getObjectStructure()
	{
		return OverDriveSetting::getObjectStructure();
	}

	function getPrimaryKeyColumn()
	{
		return 'id';
	}

	function getIdKeyColumn()
	{
		return 'id';
	}

	function getAllowableRoles()
	{
		return array('opacAdmin', 'libraryAdmin', 'cataloging');
	}

	function canAddNew()
	{
		return UserAccount::userHasRole('opacAdmin');
	}

	function canDelete()
	{
		return UserAccount::userHasRole('opacAdmin');
	}

	function getAdditionalObjectActions($existingObject)
	{
		return [];
	}

	function getInstructions()
	{
		return '';
	}
}