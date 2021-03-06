<?php

require_once ROOT_DIR . '/Action.php';
require_once ROOT_DIR . '/services/Admin/ObjectEditor.php';
require_once ROOT_DIR . '/sys/Email/SendGridSetting.php';

class Admin_SendGridSettings extends ObjectEditor
{
    function getObjectType(){
        return 'SendGridSetting';
    }
    function getToolName(){
        return 'SendGridSettings';
    }
    function getModule(){
        return 'Admin';
    }
    function getPageTitle(){
        return 'SendGrid Settings';
    }
    function getAllObjects(){
        $object = new SendGridSetting();
        $object->find();
        $objectList = array();
        while ($object->fetch()){
            $objectList[$object->id] = clone $object;
        }
        return $objectList;
    }
    function getObjectStructure(){
        return SendGridSetting::getObjectStructure();
    }
    function getPrimaryKeyColumn(){
        return 'id';
    }
    function getIdKeyColumn(){
        return 'id';
    }
    function getAllowableRoles(){
        return array('opacAdmin');
    }
    function canAddNew(){
        return UserAccount::userHasRole('opacAdmin');
    }
    function canDelete(){
        return UserAccount::userHasRole('opacAdmin');
    }
    function getAdditionalObjectActions($existingObject){
        return [];
    }

    function getInstructions(){
        return '';
    }
}