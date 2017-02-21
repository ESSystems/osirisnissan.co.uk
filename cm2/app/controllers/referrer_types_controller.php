<?php
class ReferrerTypesController extends AppController
{
	var $name = 'ReferrerTypes';
	
	/**
	 * @var ReferrerTypes
	 */
	var $ReferrerTypes;

	function direct_index()
	{
		$data = $this->ReferrerType->find('all', array('recursive'=>-1));
		$success = (boolean)$data;

		return compact('data', 'success');
	}
}
?>