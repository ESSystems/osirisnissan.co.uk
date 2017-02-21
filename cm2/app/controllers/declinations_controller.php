<?php
/**
 * @author stv
 * 
 * @property Declination $Declination
 *
 */
class DeclinationsController extends AppController 
{
	var $name = 'Declinations';
	
	var $directFormHandlers = array('direct_save');
	
	var $uses = array('Declination', 'Notification');
	
	function direct_save($filter = null) {
		if (!empty($this->data)) {
			if ($this->Declination->save($this->data)) {
				$this->Notification->notify('Referral', $this->data['Declination']['referral_id'], true);
				
				return array(
					'success' => true,
				);
			}
		}

		return array(
			'success'=>false, 
			'errors' =>Set::flatten(array('Declination' => $this->Declination->validationErrors))
		);
	}
	
}
?>