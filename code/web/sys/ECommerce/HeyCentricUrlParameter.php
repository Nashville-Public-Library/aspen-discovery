<?php /** @noinspection PhpMissingFieldTypeInspection */

class HeyCentricUrlParameter extends DataObject {
	public $__table = 'heycentric_url_parameter';
	public $id;
	public $name;
	public $multiline;
	public $optional;
	public $defaultValue;

	static function getHeyCentricUrlParamFields() : array {
		$urlParamsArr = [];
		$urlParam = new HeyCentricUrlParameter();
		$urlParam = $urlParam->fetchAll();
		
		foreach($urlParam as $param) {
			$urlParamsArr[] = [
				'id' => $param->id,
				'property' => $param->name,
				'type' => 'section',
				'label' => $param->name,
				'description' => '',
				'maxLength' => 10,
				'properties' => HeyCentricUrlParameterSetting::getObjectStructure(),
			];
		}
		return $urlParamsArr;
	}
}
