<?php
class RecallListItemEventsController extends AppController
{
	var $name = 'RecallListItemEvents';
	
	var $directFormHandlers = array('direct_save');
	
	/**
	 * @var RecallListItemEvent
	 */
	var $RecallListItemEvent;

	function direct_save() {
		if (empty($this->data['RecallListItemEvent']['created_by'])) {
			$this->data['RecallListItemEvent']['created_by'] = $this->Session->read('user.User.id');
		}
		if (empty($this->data['RecallListItemEvent']['invite_date'])) {
			$this->data['RecallListItemEvent']['invite_date'] = date('Y-m-d');
		}
		
		if (empty($this->data['RecallListItemEvent']['id'])) {
			$callNo = $this->RecallListItemEvent->field('MAX(call_no)', 
				array(
					'recall_list_item_id' => $this->data['RecallListItemEvent']['recall_list_item_id'],
					'attended_date' => null
				)
			);
			
			$this->data['RecallListItemEvent']['call_no'] = intval($callNo) + 1;
		}
		
		if ($this->RecallListItemEvent->save($this->data)) {
			return array('success'=>true, 'id' => $this->RecallListItemEvent->id);
		}
		
		return array(
			'success'=>false,
			'errors' => Set::flatten(
				array(
					'RecallListItemEvent' => $this->RecallListItemEvent->validationErrors
				)
			)
		);
	}
	
	function summary($personId)
	{
	    $data = $this->RecallListItemEvent->find('all',
    	    array(
    	        'contain' => array(
    	            'Creator.Person(first_name, last_name)',
    	            'RecallListItem',
    	            'RecallListItem.RecallList'
    	        ),
    	        'conditions' => array(
    	            'RecallListItem.person_id' => $personId
    	        ),
                'order' => 'due_date DESC',
    	    )
	    );
	     
	    if (!empty($this->params['requested'])) {
	        return $data;
	    }
	     
	}
}
?>