<?php

require_once ROOT_DIR . '/Action.php';
require_once ROOT_DIR . '/services/Admin/ObjectEditor.php';
require_once ROOT_DIR . '/sys/RBdigital/RBdigitalScope.php';

class RBdigital_Scopes extends ObjectEditor
{
	function getObjectType(){
		return 'RBdigitalScope';
	}
	function getToolName(){
		return 'Scopes';
	}
	function getModule(){
		return 'RBdigital';
	}
	function getPageTitle(){
		return 'RBdigital Scopes';
	}
	function getAllObjects(){
		$object = new RBdigitalScope();
		$object->orderBy('name');
		$object->find();
		$objectList = array();
		while ($object->fetch()){
			$objectList[$object->id] = clone $object;
		}
		return $objectList;
	}
	function getObjectStructure(){
		return RBdigitalScope::getObjectStructure();
	}
	function getPrimaryKeyColumn(){
		return 'id';
	}
	function getIdKeyColumn(){
		return 'id';
	}
	function getAllowableRoles(){
		return array('opacAdmin', 'libraryAdmin', 'cataloging');
	}
	function canAddNew(){
		return UserAccount::userHasRole('opacAdmin') || UserAccount::userHasRole('libraryAdmin') || UserAccount::userHasRole('cataloging');
	}
	function canDelete(){
		return UserAccount::userHasRole('opacAdmin') || UserAccount::userHasRole('libraryAdmin') || UserAccount::userHasRole('cataloging');
	}
	function getAdditionalObjectActions($existingObject){
		return [];
	}

	function getInstructions(){
		return '';
	}
}