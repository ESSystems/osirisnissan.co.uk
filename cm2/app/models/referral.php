<?php
/**
 * @author stv
 *
 * @property Document $Attachment
 * @property ReferralReason $ReferralReason
 * @property Declination $Declination
 * @property Referrer $Referrer
 * @property Person $Person
 * @property Appointment $Appointment
 */
class Referral extends AppModel
{
	var $actsAs = array('Notifier' => array(
		'status_fields' => array('state'),
		'target_model' => 'Referrer',
		'skip_values' => array(Referral::STATE_ACCEPTED)
	));

	var $name = 'Referral';

	var $belongsTo = array(
		'ReferralReason',
		'Person' => array(
			'foreignKey' => 'person_id',
		),
		'PatientStatus',
		'Referrer',
		'OperationalPriority',
	    'Creator' => array(
	        'className' => 'Person',
            'foreignKey' => 'created_by'
        ),
	    'Updater' => array(
	        'className' => 'Person',
            'foreignKey' => 'updated_by'
        ),
	);

	var $hasOne = array(
		//'Declination',
		//'Appointment',
	);

	var $hasMany = array(
		'Attachment' => array(
			'className' => 'Document',
			'foreignKey' => 'attachable_id',
			'conditions' => array(
				'Attachment.attachable_type' => 'Referral'
			)
		),
		'Appointment',
		'Declination'
	);

	var $hasAndBelongsToMany = array(
		'Follower' => array(
			'className' => 'Referrer',
			'forreignKey' => 'referral_id',
			'associationForeignKey' => 'referrer_id',
			'with' => 'ReferralsFollower'
		)
	);
	
	
	var $validate = array(
	    'person_id' => array(
	        'notempty' => array(
	            'rule' => 'notempty',
	            'message' => 'Please select person',
            )
        ),
	    'patient_status_id' => array(
	        'notempty' => array(
	            'rule' => 'notempty',
	            'message' => 'Please select status',
            )
        ),
	    'case_nature' => array(
	        'notempty' => array(
	            'rule' => 'notempty',
	            'message' => 'Please describe the case',
            )
        ),
	    'job_information' => array(
	        'notempty' => array(
	            'rule' => 'notempty',
	            'message' => 'Please provide job information',
            )
        ),
	    'history' => array(
	        'notempty' => array(
	            'rule' => 'notempty',
	            'message' => 'Please detail background to case',
            )
        ),
	    'referral_reason_id' => array(
	        'notempty' => array(
	            'rule' => 'notempty',
	            'message' => 'Please select reason',
            )
        ),
	    'operational_priority_id' => array(
	        'notempty' => array(
	            'rule' => 'notempty',
	            'message' => 'Please select operational priority',
            )
        ),
    );

	/**
	 * Referral states
	 */
	const STATE_NEW       = 'new';
	const STATE_ACCEPTED  = 'accepted';
	const STATE_CLOSED    = 'closed';
	const STATE_DECLINED  = 'declined';
	
	function beforeSave()
	{
	    $now = date('Y-m-d H:i:s');

	    // A call to exists() should be very cheap here, since exists() has already been called
	    if (!$this->exists()) {
	        $this->data[$this->alias]['created_at'] = $now;
	        $this->data[$this->alias]['created_by'] = CurrentUser::id();
	        $this->data[$this->alias]['state']      = static::STATE_NEW;
	    }
	    
	    $this->data[$this->alias]['updated_at'] = $now;
	    $this->data[$this->alias]['updated_by'] = CurrentUser::id();
	    
	    if (empty($this->data[$this->alias]['case_reference_number']) && !$this->exists()) {
	        $this->data[$this->alias]['case_reference_number'] = static::generate_case_reference_number();
	    }

	    return true;
	}

	/**
	 * Return an array with ids of people that are allowed to follow this referral
	 * @param integer $id
	 */
	function getFollowers($referral_id) {
		$ids = array();

		$query = "SELECT referrer_id FROM referrals AS Referral WHERE id = $referral_id";
		//$referrer = $this->read('referrer_id', $referral_id);
		$referrer = $this->query($query);

		$ids[] = $referrer[0]['Referral']['referrer_id'];

		$this->bindModel(array('hasOne' => array('ReferralsFollower')));
		$followers = $this->ReferralsFollower->find('all', array(
			'conditions' => array('referral_id' => $referral_id),
			'fields' => array('referrer_id')
		));
		foreach ($followers as $f) {
			$ids[] = $f['ReferralsFollower']['referrer_id'];
		}

		if(empty($ids)) {
			$this->log("No followers found for Referral with id: " . $referral_id);
		}

		return $ids;
	}

	function getReference() {
		$query = "SELECT case_reference_number FROM referrals AS Referral "
				. "WHERE Referral.id=$this->id LIMIT 1";
		$referral = $this->query($query);

		return "Case reference number: " . $referral[0]['Referral']['case_reference_number'];
	}

	function isPrivate($id) {
		$query = "SELECT private FROM referrals AS Referral "
				. "WHERE Referral.id=$id LIMIT 1";
		$referral = $this->query($query);

		return $referral[0]['Referral']['private'];
	}

	function makePrivate($id) {
		$private_success = false;
		$this->id = $id;
		$data = array(
			'Referral' => array(
				'id' => $id,
				'private' => true
			)
		);
		if($this->ReferralsFollower->deleteAll(array('referral_id' => $id), false) && $this->save($data)) {
			$private_success = true;
		}

		// Make related models private
		if($private_success) {
			$attendance_model = ClassRegistry::init('Attendance');
			$attendance_model->makePrivateByReferralId($id);
		}

		return $private_success;
	}

	function notifierLink() {
		return Configure::read('CMX.view_urls.Referral') . $this->id;
	}

	function setState($state, $ids) {
		if (!empty($ids)) {
		    if (!is_array($ids)) {
		        $ids = array($ids);
		    }

		    // Update all records in a loop, using save instead of using a single updateAll() call.
		    // The reason is that we want model callbacks to get called.
		    foreach ($ids as $id) {
		        // $this->create(); // allowing create will overwrite private attribute - it will always make it false
		        $this->id = $id;
		        if ($this->exists()) {
		            $this->save(
        				array(
        				    'id'    => $id,
        				    'state' => $state
    				    )
		            );
		        }
		    }
		    
		    return true;
		}

		return false;
	}

	function targetIds() {
		return $this->getFollowers($this->id);
	}
	
	protected static function generate_case_reference_number() {
	    $crn = String::uuid();
	    $crn = str_replace('-', '', $crn);
	    $crn = substr($crn, 0, 10);
	    $crn = strtoupper($crn);
	    
	    return $crn;
	}
}
?>