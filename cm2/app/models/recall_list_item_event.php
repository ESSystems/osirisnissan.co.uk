<?php
class RecallListItemEvent extends AppModel
{
	var $name = 'RecallListItemEvent';
	
	var $belongsTo = array(
		'RecallListItem',
	    'Creator' => array(
            'className' => 'User',
	        'foreignKey' => 'created_by',
        )
	);
	
	/**
	 * @var RecallListItem
	 */
	var $RecallListItem;

	function afterSave()
	{
	    $this->RecallListItem->id = $this->data[$this->alias]['recall_list_item_id'];
	    $lastInviteId = $this->field('MAX(id)', array('recall_list_item_id'=>$this->RecallListItem->id));
	    
	    $this->RecallListItem->saveField('last_invite_id', $lastInviteId);
	}
	
	/**
	 * @param $id int
	 * @param $Attendance Attendance
	 */
	function onAttendance($id, $Attendance) {
		$d = $Attendance->data[$Attendance->alias];

		$recallListItemId = $this->field('recall_list_item_id',
			array('id' => $id)
		);
		if ($recallListItemId) {
			$ds = $this->getDataSource();
			if ($this->updateAll(
					array(
						'RecallListItemEvent.attended_date' => $ds->value($d['seen_at_time'], 'date'),
						'RecallListItemEvent.attendance_id' => $Attendance->id,
						'RecallListItem.last_attended_date' => $ds->value($d['seen_at_time'], 'date'),
					),
					array(
						'RecallListItemEvent.recall_list_item_id' => $recallListItemId,
						'RecallListItemEvent.attended_date' => null,
						'RecallListItem.id' => $recallListItemId
					)
				) ) {
				return true;
			}
		}
		
		return false;
	}
}
?>