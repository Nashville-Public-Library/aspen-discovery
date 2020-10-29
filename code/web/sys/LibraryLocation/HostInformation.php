<?php


class HostInformation extends DataObject
{
	public $__table = 'host_information';
	public $id;
	public $host;
	public $libraryId;
	public $locationId;
	public $defaultPath;

	public static function getObjectStructure(){
		$libraryList = Library::getLibraryList();
		$locationList = [-1, 'Default, no location specified'];
		$locationList = array_merge($locationList, Location::getLocationList());
		return [
			'id' => array('property' => 'id', 'type' => 'label', 'label' => 'Id', 'description' => 'The unique id within the database'),
			'host' => array('property' => 'host', 'type' => 'text', 'label' => 'Host name', 'description' => 'The name of the host.  I.e. discover.library.org or www.library.org', 'maxLength' => 100, 'required' => true),
			'libraryId' => array('property' => 'libraryId', 'type'=>'enum','values'=>$libraryList,'label'=> 'Library', 'description' => 'The default library that will be active for this host'),
			'locationId' => array('property' => 'locationId', 'type'=>'enum','values'=>$locationList,'label'=> 'Location', 'description' => 'The default location that will be active for this host', 'default'=>-1),
			'defaultPath' => array('property' => 'defaultPath', 'type'=>'text', 'Default Path', 'description' => 'The default path that will be used if no path is provided', 'maxLength' => 50, 'default'=>'/Search/Home'),
		];
	}
}