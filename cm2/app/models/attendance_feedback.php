<?php
class AttendanceFeedback extends AppModel
{
	var $actsAs = array('Notifier' => array(
		'status_fields' => array('report'),
		'target_model' => 'Referrer'
	));
	
	var $name = 'AttendanceFeedback';
	var $useTable = 'attendance_feedback';
	
	var $belongsTo = array(
		'Attendance'
	);
	
	var $validate = array(
		'attendance_id' => array(
			'numeric' => array(
				'rule' => 'numeric'
			)
		)
	);
	
	var $referral_id = 0;

	function beforeSave() {
		$d = &$this->data[$this->alias];
		$d['created_by'] = CurrentUser::id();
		$d['created_at'] = date("Y-m-d");
		$d['updated_at'] = date("Y-m-d");
		
		return true;
	}
	
	function getReferralId($f_id) {
		$query = "SELECT a.referral_id FROM appointments AS a "
				. "LEFT JOIN attendance_feedback as f "
				. "ON a.attendance_id = f.attendance_id "
				. "WHERE f.id=$f_id LIMIT 1";
		$referral_id = $this->query($query);
		
		if($referral_id == '') {
			$this->log("No Referral was found for the AttendanceFeedback with id: " . $f_id);
		}
		
		return $referral_id[0]['a']['referral_id'];
	}
	
	function getReference() {
		if($this->referral_id == 0) {
			$this->referral_id = $this->getReferralId($this->id);
		}
		
		$query = "SELECT case_reference_number FROM referrals AS Referral "
				. "WHERE Referral.id=$this->referral_id LIMIT 1";
		$referral = $this->query($query);
		
		return "Case reference number: " . $referral[0]['Referral']['case_reference_number'];
	}
	
	function notifierLink() {
		if($this->referral_id == 0) {
			$this->referral_id = $this->getReferralId($this->id);
		}

		return Configure::read('CMX.view_urls.Referral') . $this->referral_id;
	}
		
	function targetIds() {
		if($this->referral_id == 0) {
			$this->referral_id = $this->getReferralId($this->id);
		}
		
		$referral = ClassRegistry::init('Referral');
		
		return $referral->getFollowers($this->referral_id);
	}
}
?>