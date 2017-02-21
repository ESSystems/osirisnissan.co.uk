<?php
class ReferralReasonsController extends AppController
{
	var $name = 'ReferralReasons';
	
	/**
	 * @var ReferralReason
	 */
	var $ReferralReason;

	function direct_index()
	{
		$data = $this->ReferralReason->find('all', array('recursive'=>-1));
		$success = (boolean)$data;
		
		return compact('data', 'success');
	}
}
?>